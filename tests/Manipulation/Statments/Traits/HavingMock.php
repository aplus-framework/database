<?php namespace Tests\Database\Manipulation\Statments\Traits;

use Framework\Database\Database;
use Framework\Database\Manipulation\Manipulation;
use Framework\Database\Manipulation\Statements\Statement;
use Framework\Database\Manipulation\Statements\Traits\Having;

class HavingMock extends Statement
{
	use Having;

	public function __construct()
	{
		parent::__construct(new Manipulation(new Database()));
	}

	public function render() : ?string
	{
		return $this->renderHaving();
	}

	public function sql() : string
	{
	}
}
