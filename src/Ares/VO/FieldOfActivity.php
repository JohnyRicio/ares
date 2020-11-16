<?php declare(strict_types = 1);

namespace RegistryAres\src\Ares\Vo;

use SimpleXMLElement;

class FieldOfActivity extends Vo
{

    /** @var string */
    protected $code;

    /** @var string */
    protected $title;

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'title' => $this->title,
        ];
    }

    public static function createFromXmlElement(SimpleXMLElement $FieldOfActivityXmlElement): Vo
    {
        $element = new self();
        $element->code = (string) $FieldOfActivityXmlElement->K;
        $element->title = (string) $FieldOfActivityXmlElement->T;

        return $element;
    }

}
