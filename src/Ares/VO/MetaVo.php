<?php declare(strict_types=1);

namespace RegistryAres\src\Ares\Vo;

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
		$element->_datetime = DateTime::createFromFormat(
			'Y-m-d H:i:s', (string)$metaInfoXmlElement->DVY . " " . (string)$metaInfoXmlElement->CAS,
		);
		$element->validate();
		return $element;
	}

	protected function validate() : void {
		if (!$this->_datetime instanceof DateTime) {
			throw new InvalidArgumentException('Datetime must be correctly defined');
		}
	}

	public function toArray(): array
	{
		return [
			'datetime' => $this->_datetime->format('Y-m-d H:i:s')
		];
	}
}
