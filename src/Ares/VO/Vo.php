<?php declare(strict_types = 1);

namespace RegistryAres\src\Ares\Vo;

use Exception;

abstract class Vo
{
	public function __get($name)
	{
		if (!property_exists($this, '_'.$name)) {
			throw new Exception('Property \''.$name.'\' in object \''.self::class.'\' not exists');
		}
		return $this->{'_' . $name};
	}


	abstract public function toArray(): array;

	protected function validate(): void	{

	}

}
