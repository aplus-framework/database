<?php namespace Tests\Database\Manipulation\Statements\Traits;

use Framework\Database\Database;
use Framework\Database\Manipulation\Manipulation;
use Framework\Database\Manipulation\Statements\Statement;
use Framework\Database\Manipulation\Statements\Traits\Join;

class JoinMock extends Statement
{
	use Join {
		hasFrom as public;
		renderFrom as public;
		renderJoin as public;
	}

	public function __construct()
	{
		parent::__construct(new Manipulation(new Database()));
	}

	public function sql() : string
	{
	}
}
