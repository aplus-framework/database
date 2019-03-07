<?php namespace Tests\Database\Manipulation\Statements\Traits;

use Framework\Database\Database;
use Framework\Database\Manipulation\Manipulation;
use Framework\Database\Manipulation\Statements\Statement;
use Framework\Database\Manipulation\Statements\Traits\OrderBy;

class OrderByMock extends Statement
{
	use OrderBy {
		renderOrderBy as public;
	}

	public function __construct()
	{
		parent::__construct(new Manipulation(new Database()));
	}

	public function sql() : string
	{
	}
}
