<?php namespace Tests\Database\Manipulation\Statements\Traits;

use Framework\Database\Manipulation\Statements\Traits\Where;
use Tests\Database\Manipulation\Statements\StatementMock;

class WhereMock extends StatementMock
{
	use Where {
		renderWhere as public;
	}
}
