<?php declare(strict_types = 1);

namespace registryAres\src\Ares;

use GuzzleHttp\Client;
use RuntimeException;
use TypeError;
use function preg_match;

class Ares
{

    private const ARES_URL = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi';

    /** @var \GuzzleHttp\Client */
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

        $ares = new AresVO();
        $ares->companyId = $companyId;
        $ares->company = (string)$el->OF;
        $ares->city = (string)$el->AA->N;
        $ares->state = TRUE;
        $ares->zip = (string)$el->AA->PSC;
        $ares->street = (string)$el->AA->NU;
        $ares->streetNo1 = (string)$el->AA->CD;
        $ares->streetNo2 = (string)$el->AA->CO;
        $ares->partOfCity = (string)$el->AA->NCO;
        $ares->country = (string)$el->AA->NS;
        $ares->vatNumber = (string)$el->DIC;

        return $ares;
    }

	private function checkRequiredInput(string $companyId): void
    {
        if (!preg_match('-^\d{8}$-', $companyId)) {
            throw new TypeError('Company id must be 8 integers');
        }
    }

}
