<?php namespace Tests\Database\Manipulation\Traits;

use Framework\Database\Manipulation\Traits\Join;
use Tests\Database\Manipulation\StatementMock;

class JoinMock extends StatementMock
{
	use Join {
		hasFrom as public;
		renderFrom as public;
		renderJoin as public;
	}
}
