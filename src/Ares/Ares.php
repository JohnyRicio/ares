<?php declare(strict_types = 1);

namespace RegistryAres\src\Ares;

use DateTime;
use GuzzleHttp\Client;
use RegistryAres\src\Ares\Vo\AresVO;
use RuntimeException;
use SimpleXMLElement;
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
			throw new RuntimeException('Problem in ARES: ' . $data->children($ns['D'])->E->ET);
		}

		$ares = new AresVO();

		$this->_parseMeta($ares, $data->children($ns['D'])->UVOD);

		$this->_parseCompanyInfo($ares, $data->children($ns['D'])->VBAS);

		if ($ares->companyId !== $companyId) {
			throw new RuntimeException('Returned data are bad');
		}

		$ares->status = TRUE;
		$this->_parseAddress($ares, $data->children($ns['D'])->VBAS);

		return $ares;
	}

	private function checkRequiredInput(string $companyId): void
	{
		if (!preg_match('-^\d{8}$-', $companyId)) {
			throw new TypeError('Company id must be 8 integers');
		}
	}

	private function _parseAddress(AresVO $ares, SimpleXMLElement $addres): void
	{
		$ares->address->city = (string)$addres->AA->N;
		$ares->address->zip = (string)$addres->AA->PSC;
		$ares->address->street = (string)$addres->AA->NU;
		$ares->address->streetNo1 = (string)$addres->AA->CD;
		$ares->address->streetNo2 = (string)$addres->AA->CO;
		$ares->address->district = (string)$addres->AA->NCO;
		$ares->address->country = (string)$addres->AA->NS;
	}

	private function _parseCompanyInfo(AresVO $ares, SimpleXMLElement $companyInfo): void
	{
		$ares->companyId = (string)$companyInfo->ICO;
		$ares->companyName = (string)$companyInfo->OF;
		$ares->vatNumber = (string)$companyInfo->DIC;
	}

	private function _parseMeta(AresVO $ares, SimpleXMLElement $meta): void
	{
		$ares->meta->datetime = DateTime::createFromFormat(
			'Y-m-d H:i:s', (string)$meta->DVY . " " . (string)$meta->CAS,
		);
	}

}
