<?php declare(strict_types = 1);

namespace RegistryAres\src\Ares;

use GuzzleHttp\Client;
use RegistryAres\src\Ares\Vo\AddressVo;
use RegistryAres\src\Ares\Vo\AresVO;
use RuntimeException;
use TypeError;
use function preg_match;

class Ares
{

    private const ARES_URL = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi';

    /** @var Client */
    private $client;

	/**
	 * Ares constructor.
     */
	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
     * @param $companyId
     * @throws /Exception
     */
    public function getByCompanyId(string $companyId): AresVO
    {
        $this->checkRequiredInput($companyId);

        $response = $this->client->request('GET', self::ARES_URL . '?' . http_build_query(['ico' => $companyId]));

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Problem with connection', $response->getStatusCode());
        }

        $data = $response->getBody()->getContents();
        $xml = simplexml_load_string($data);

        if (!$xml) {
            throw new RuntimeException('Problem with xml parser');
        }

        $ns = $xml->getDocNamespaces();
        $data = $xml->children($ns['are']);

        if (!empty($data->children($ns['D'])->E->EK)) {
            throw new RuntimeException('Problem in ARES: '.$data->children($ns['D'])->E->ET);
        }

        $el = $data->children($ns['D'])->VBAS;

        if ((string)$el->ICO !== $companyId) {
            throw new RuntimeException('Returned data are bad');
        }

		$address = new AddressVo();
		$address->city = (string)$el->AA->N;
		$address->zip = (string)$el->AA->PSC;
		$address->street = (string)$el->AA->NU;
		$address->streetNo1 = (string)$el->AA->CD;
		$address->streetNo2 = (string)$el->AA->CO;
		$address->district = (string)$el->AA->NCO;
		$address->country = (string)$el->AA->NS;

        $ares = new AresVO();
        $ares->companyId = $companyId;
        $ares->companyName = (string)$el->OF;
		$ares->vatNumber = (string)$el->DIC;
		$ares->status = TRUE;
		$ares->address = $address;

        return $ares;
    }

	private function checkRequiredInput(string $companyId): void
    {
        if (!preg_match('-^\d{8}$-', $companyId)) {
            throw new TypeError('Company id must be 8 integers');
        }
    }

}
