<?php declare(strict_types = 1);

namespace RegistryAres\Ares\Vo;

use DateTime;
use InvalidArgumentException;
use SimpleXMLElement;

class MetaVo extends Vo
{

    /** @var DateTime */
    protected $_datetime;

    public static function createFromXmlElement(SimpleXMLElement $metaInfoXmlElement): self
    {
        $element = new self();
        $dateTime = DateTime::createFromFormat(
            'Y-m-d H:i:s', (string)$metaInfoXmlElement->DVY . " " . (string)$metaInfoXmlElement->CAS,
        );

        if(false === $dateTime) {
            throw new InvalidArgumentException('Datetime must be correctly defined');
        }

        $element->_datetime = $dateTime;
        $element->validate();

        return $element;
    }

    /** @return array<string,string> */
    public function toArray(): array
    {
        return [
            'datetime' => $this->_datetime->format('Y-m-d H:i:s'),
        ];
    }

}
