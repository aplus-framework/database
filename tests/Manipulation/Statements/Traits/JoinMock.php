<?php namespace Tests\Database\Manipulation\Statements\Traits;

use Framework\Database\Manipulation\Statements\Traits\Join;
use Tests\Database\Manipulation\Statements\StatementMock;

class JoinMock extends StatementMock
{
	use Join {
		hasFrom as public;
		renderFrom as public;
		renderJoin as public;
	}
}
