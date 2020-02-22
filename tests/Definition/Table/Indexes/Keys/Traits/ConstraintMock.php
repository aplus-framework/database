<?php namespace Tests\Database\Definition\Table\Indexes\Keys\Traits;

use Framework\Database\Definition\Table\Indexes\Keys\Traits\Constraint;
use Tests\Database\Definition\Table\Indexes\IndexMock;

class ConstraintMock extends IndexMock
{
	use Constraint;
	public string $type = 'constraint_mock';
}
