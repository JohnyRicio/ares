<?php declare(strict_types = 1);

namespace RegistryAres\src\Ares\Vo;

class AresVO extends Vo
{

	/** @var string */
	public $companyId;

	/** @var string */
	public $vatNumber;

	/** @var string */
	public $companyName;

	/** @var bool */
	public $status = FALSE;

	/** @var AddressVo */
	public $address;

	public function __construct()
	{
		$this->address = new AddressVo();
	}

}
