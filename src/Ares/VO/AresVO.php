<?php declare(strict_types = 1);

namespace RegistryAres\src\Ares\Vo;

use InvalidArgumentException;
use RuntimeException;
use SimpleXMLElement;

final class AresVO extends Vo
{

    /** @var string */
    protected $_companyId;

    /** @var string */
    protected $_vatNumber;

    /** @var string */
    protected $_companyName;

    /** @var AddressVo */
    protected $_address;

    /** @var MetaVo */
    protected $_meta;

    public static function createFromElement(
        string $companyId, SimpleXMLElement $companyInfoXmlElement, SimpleXMLElement $metaInfoXmlElement,
        SimpleXMLElement $adressInfoXmlElement
    ): self {
        $element = new self();
        $element->_companyId = (string) $companyInfoXmlElement->ICO;

        if ($element->_companyId !== $companyId) {
            throw new RuntimeException('Returned data are bad');
        }

        $element->_companyName = (string) $companyInfoXmlElement->OF;
        $element->_vatNumber = (string) $companyInfoXmlElement->DIC;
        $element->_meta = MetaVo::createFromXmlElement($metaInfoXmlElement);
        $element->_address = AddressVo::createFromXmlElement($adressInfoXmlElement);
        $element->validate();

        return $element;
    }

    public function toArray(): array {
        return [
            'companyName' => $this->_companyName,
            'vatNumber' => $this->_vatNumber,
            'meta' => $this->_meta->toArray(),
            'address' => $this->_address->toArray(),
        ];
    }

    protected function validate(): void {
        if (!$this->_companyId) {
            throw new InvalidArgumentException('Company ID is required argument');
        }

        if (!$this->_meta) {
            throw new InvalidArgumentException('Meta information is required argument');
        }

        if (!$this->_address) {
            throw new InvalidArgumentException('Address is required argument');
        }
    }

}
