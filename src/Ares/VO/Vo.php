<?php declare(strict_types = 1);

namespace RegistryAres\Ares\Vo;

use Exception;

abstract class Vo
{

    /** @return array<string,string|array> */
    abstract public function toArray(): array;

    protected function validate(): void {
        // Default behavior
    }

    /** @return mixed */
	public function __get(string $name)
	{
		if (!property_exists($this, '_'.$name)) {
			throw new Exception('Property \''.$name.'\' in object \''.self::class.'\' not exists');
		}

		return $this->{'_' . $name};
	}

}
