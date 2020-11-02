<?php declare(strict_types = 1);

namespace RegistryAres\src\Ares;

use GuzzleHttp\Client;
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
			throw new RuntimeException('Problem in ARES: ' . $data->children($ns['D'])->E->ET);
		}

		return AresVO::createFromElement(
			$companyId, $data->children($ns['D'])->VBAS, $data->children($ns['D'])->UVOD,
			$data->children($ns['D'])->VBAS,
		);
	}

	private function checkRequiredInput(string $companyId): void
	{
		if (!preg_match('-^\d{8}$-', $companyId)) {
			throw new TypeError('Company id must be 8 integers');
		}
	}

}
