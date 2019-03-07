<?php namespace Tests\Database\Manipulation\Statments\Traits;

use Framework\Database\Database;
use Framework\Database\Manipulation\Manipulation;
use Framework\Database\Manipulation\Statements\Statement;
use Framework\Database\Manipulation\Statements\Traits\Where;

class WhereMock extends Statement
{
	use Where;

	public function __construct()
	{
		parent::__construct(new Manipulation(new Database()));
	}

	public function render() : ?string
	{
		return $this->renderWhere();
	}

	public function sql() : string
	{
	}
}
