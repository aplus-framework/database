<?php namespace Tests\Database\Manipulation\Traits;

use Framework\Database\Manipulation\Traits\Having;
use Tests\Database\Manipulation\StatementMock;

class HavingMock extends StatementMock
{
	use Having {
		renderHaving as public;
	}
}
