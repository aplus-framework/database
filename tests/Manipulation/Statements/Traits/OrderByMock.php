<?php namespace Tests\Database\Manipulation\Statements\Traits;

use Framework\Database\Manipulation\Statements\Traits\OrderBy;
use Tests\Database\Manipulation\Statements\StatementMock;

class OrderByMock extends StatementMock
{
	use OrderBy {
		renderOrderBy as public;
	}
}
