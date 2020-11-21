<?php declare(strict_types = 1);

namespace RegistryAres\Ares\Vo;

use RegistryAres\Ares\Exception\PropertyException;

abstract class Vo
{

    /** @return array<string,string|array> */
    abstract public function toArray(): array;

    protected function validate(): void {
        // Default behavior
    }

    /**
     * @return mixed
     * @throws PropertyException
     */
	public function __get(string $name)
	{
		if (!property_exists($this, '_'.$name)) {
			throw new PropertyException('Property \''.$name.'\' in object \''.self::class.'\' not exists');
		}

		return $this->{'_' . $name};
	}

}
