<?php declare(strict_types = 1);

namespace RegistryAres\Ares\Vo;

use RegistryAres\Ares\Exception\IncorrectReturnedDataException;
use RegistryAres\Ares\Exception\InvalidArgumentException;
use SimpleXMLElement;

final class AresVo extends Vo
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

    /** @var FieldOfActivity[] */
    protected $_fieldOfActivities;

    /** @throws IncorrectReturnedDataException|InvalidArgumentException */
    public static function createFromXmlElement(
        string $companyId, SimpleXMLElement $companyInfoXmlElement, SimpleXMLElement $metaInfoXmlElement,
        SimpleXMLElement $adressInfoXmlElement
    ): self {
        $element = new self();
        $element->_companyId = (string) $companyInfoXmlElement->ICO;

        if ($element->_companyId !== $companyId) {
            throw new IncorrectReturnedDataException('Returned data are bad');
        }

        $element->_companyName = (string) $companyInfoXmlElement->OF;
        $element->_vatNumber = (string) $companyInfoXmlElement->DIC;
        $element->_meta = MetaVo::createFromXmlElement($metaInfoXmlElement);
        $element->_address = AddressVo::createFromXmlElement($adressInfoXmlElement);

        foreach ($adressInfoXmlElement->Obory_cinnosti as $fieldOfActivityXmlElements) {
            foreach ($fieldOfActivityXmlElements as $fieldOfActivityXmlElement) {
                $element->_fieldOfActivities[] = FieldOfActivity::createFromXmlElement($fieldOfActivityXmlElement);
            }
        }

        $element->validate();

        return $element;
    }

    /** @return array<string,string|array> */
    public function toArray(): array
    {
        return [
            'companyName' => $this->_companyName,
            'vatNumber' => $this->_vatNumber,
            'meta' => $this->_meta->toArray(),
            'address' => $this->_address->toArray(),
            'fieldOfActivities' => array_map(
                static function (FieldOfActivity $item) {
                    return $item->toArray();
                }, $this->_fieldOfActivities,
            ),
        ];
    }

    /** @throws InvalidArgumentException */
    protected function validate(): void
    {
        if (8 !== strlen($this->_companyId)) {
            throw new InvalidArgumentException('Company ID must have 8 letters');
        }
    }

}
