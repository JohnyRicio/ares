<?php declare(strict_types = 1);

namespace RegistryAres\src\Ares\Vo;

use SimpleXMLElement;

class AddressVo extends Vo
{

	/** @var string */
	protected $_street;

	/** @var string */
	protected $_streetNo1;

	/** @var string */
	protected $_streetNo2;

	/** @var string */
	protected $_city;

	/** @var string */
	protected $_zip;

	/** @var string */
	protected $_country;

	/** @var string */
	protected $_district;

	public static function createFromXmlElement(SimpleXMLElement $addressXmlElement): self
	{
		$element = new self();
		$element->_city = (string)$addressXmlElement->AA->N;
		$element->_zip = (string)$addressXmlElement->AA->PSC;
		$element->_street = (string)$addressXmlElement->AA->NU;
		$element->_streetNo1 = (string)$addressXmlElement->AA->CD;
		$element->_streetNo2 = (string)$addressXmlElement->AA->CO;
		$element->_district = (string)$addressXmlElement->AA->NCO;
		$element->_country = (string)$addressXmlElement->AA->NS;
		$element->validate();

		return $element;
	}

	public function toArray(): array
	{
		return [
			'city' => $this->_city,
			'zip' => $this->_zip,
			'street' => $this->_street,
			'streetNo1' => $this->_streetNo1,
			'streetNo2' => $this->_streetNo2,
			'district' => $this->_district,
			'country' => $this->_country,
		];
	}

}
