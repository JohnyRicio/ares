<?php declare(strict_types = 1);

namespace unit\src\Ares\VO;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RegistryAres\src\Ares\Vo\MetaVo;

class MetaVOTest extends TestCase
{

    public function testExceptionMeta(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Datetime must be correctly defined');
        $xml = simplexml_load_string(file_get_contents(__DIR__ . '/../data/InvalidMetaDatetimeFakeData.xml'));
        $ns = $xml->getDocNamespaces();
        $data = $xml->children($ns['are']);
        MetaVo::createFromXmlElement($data->children($ns['D'])->UVOD);
    }

}
