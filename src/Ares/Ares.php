<?php declare(strict_types = 1);

namespace RegistryAres\Ares;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RegistryAres\Ares\Exception\ExternalAresException;
use RegistryAres\Ares\Exception\InvalidArgumentException;
use RegistryAres\Ares\Vo\AresVo;
use function preg_match;

require_once __DIR__.'/../../vendor/autoload.php';

class Ares
{

	private const ARES_URL = 'https://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi';

	/** @var Client */
	private $client;

	/**
	 * Ares constructor.
	 */
	public function __construct(Client $client)
	{
		$this->client = $client;
	}

    /** @throws ExternalAresException | InvalidArgumentException | Exception\IncorrectReturnedDataException */
    public function getByCompanyId(string $companyId): AresVo
	{
		$this->checkRequiredInput($companyId);

		try {
            $response = $this->client->request('GET', self::ARES_URL . '?' . http_build_query(['ico' => $companyId]));
        }catch (GuzzleException $e) {
            throw new ExternalAresException($e->getMessage(), $e->getCode(), $e);
        }

        if (200 !== $response->getStatusCode()) {
			throw new ExternalAresException('Problem with connection', $response->getStatusCode());
		}

		$data = $response->getBody()->getContents();
		$xml = simplexml_load_string($data);

		if (!$xml) {
			throw new ExternalAresException('Problem with xml parser');
		}

		/** @var array<string,string> $ns */
		$ns = $xml->getDocNamespaces();
		$data = $xml->children($ns['are']);

		if (!empty($data->children($ns['D'])->E->EK)) {
			throw new ExternalAresException('Problem in ARES: ' . $data->children($ns['D'])->E->ET);
		}

		return AresVo::createFromXmlElement(
			$companyId, $data->children($ns['D'])->VBAS, $data->children($ns['D'])->UVOD,
			$data->children($ns['D'])->VBAS,
		);
	}

    /** @throws InvalidArgumentException */
	private function checkRequiredInput(string $companyId): void
	{
		if (!preg_match('-^\d{8}$-', $companyId)) {
			throw new InvalidArgumentException('Company id must be 8 integers');
		}
	}

}
