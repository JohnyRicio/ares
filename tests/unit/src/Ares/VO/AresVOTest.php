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
        $xml = simplexml_load_string(file_get_contents(__DIR__ . '/../data/EmptyCompanyIdFakeData.xml'));
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
        $xml = simplexml_load_string(file_get_contents(__DIR__ . '/../data/InvalidCompanyIdFakeData.xml'));
        $ns = $xml->getDocNamespaces();
        $data = $xml->children($ns['are']);
        AresVO::createFromXmlElement(
            '1231231', $data->children($ns['D'])->VBAS, $data->children($ns['D'])->UVOD,
            $data->children($ns['D'])->VBAS,
        );
    }

}
