<?php namespace Tests\Database\Manipulation\Statements\Traits;

use Framework\Database\Manipulation\Statements\Traits\Having;
use Tests\Database\Manipulation\Statements\StatementMock;

class HavingMock extends StatementMock
{
	use Having {
		renderHaving as public;
	}
}
