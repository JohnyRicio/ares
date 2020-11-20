<?php declare(strict_types = 1);

namespace RegistryAres\Ares\Vo;

use SimpleXMLElement;

class FieldOfActivity extends Vo
{

    /** @var string */
    protected $code;

    /** @var string */
    protected $title;

    public static function createFromXmlElement(SimpleXMLElement $FieldOfActivityXmlElement): self
    {
        $element = new self();
        $element->code = (string) $FieldOfActivityXmlElement->K;
        $element->title = (string) $FieldOfActivityXmlElement->T;

        return $element;
    }

    /** @return array<string,string> */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'title' => $this->title,
        ];
    }

}
