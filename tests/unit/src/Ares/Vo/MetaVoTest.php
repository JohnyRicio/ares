<?php declare(strict_types = 1);

namespace RegistryAres\Tests\Ares\Vo;

use PHPUnit\Framework\TestCase;
use RegistryAres\Ares\Exception\InvalidArgumentException;
use RegistryAres\Ares\Vo\MetaVo;

class MetaVoTest extends TestCase
{

    public function testExceptionMeta(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Datetime must be correctly defined');
        $xmlData = file_get_contents(__DIR__ . '/../data/InvalidMetaDatetimeFakeData.xml');

        if (false === $xmlData) {
            throw new InvalidArgumentException('File is not readable');
        }

        $xml = simplexml_load_string($xmlData);

        if (false === $xml) {
            throw new InvalidArgumentException('Bad input xml!');
        }

        /** @var array<string,string> $ns */
        $ns = $xml->getDocNamespaces();
        $data = $xml->children($ns['are']);
        MetaVo::createFromXmlElement($data->children($ns['D'])->UVOD);
    }

}
