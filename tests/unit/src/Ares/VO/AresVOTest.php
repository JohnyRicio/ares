<?php declare(strict_types = 1);

namespace RegistryAres\Tests\Ares\VO;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RegistryAres\Ares\Vo\AresVO;

class AresVOTest extends TestCase
{

    public function testAresVoEmptyCompanyId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Company ID is required argument');
        $xmlData = file_get_contents(__DIR__ . '/../data/EmptyCompanyIdFakeData.xml');

        if (false === $xmlData) {
            throw new InvalidArgumentException('File is not readable');
        }

        $xml = simplexml_load_string($xmlData);

        if (false === $xml) {
            throw new InvalidArgumentException('Bad input xml!');
        }

        $ns = $xml->getDocNamespaces();
        $data = $xml->children($ns['are']);
        AresVO::createFromXmlElement(
            '', $data->children($ns['D'])->VBAS, $data->children($ns['D'])->UVOD,
            $data->children($ns['D'])->VBAS,
        );
    }

    public function testAresVoInvalidCompanyId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Company ID must have 8 letters');
        $xmlData = file_get_contents(__DIR__ . '/../data/InvalidCompanyIdFakeData.xml');

        if (false === $xmlData) {
            throw new InvalidArgumentException('File is not readable');
        }

        $xml = simplexml_load_string($xmlData);

        if (false === $xml) {
            throw new InvalidArgumentException('Bad input xml!');
        }

        $ns = $xml->getDocNamespaces();
        $data = $xml->children($ns['are']);
        AresVO::createFromXmlElement(
            '1231231', $data->children($ns['D'])->VBAS, $data->children($ns['D'])->UVOD,
            $data->children($ns['D'])->VBAS,
        );
    }

}
