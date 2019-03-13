<?php namespace Tests\Database\Manipulation\Traits;

use Framework\Database\Manipulation\Traits\OrderBy;
use Tests\Database\Manipulation\StatementMock;

class OrderByMock extends StatementMock
{
	use OrderBy {
		renderOrderBy as public;
	}
}
