<?php namespace Tests\Database\Manipulation\Traits;

use Framework\Database\Manipulation\Traits\Where;
use Tests\Database\Manipulation\StatementMock;

class WhereMock extends StatementMock
{
	use Where {
		renderWhere as public;
	}
}
