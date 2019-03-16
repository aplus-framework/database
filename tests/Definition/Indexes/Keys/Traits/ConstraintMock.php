<?php namespace Tests\Database\Definition\Indexes\Keys\Traits;

use Framework\Database\Definition\Indexes\Keys\Traits\Constraint;
use Tests\Database\Definition\Indexes\IndexMock;

class ConstraintMock extends IndexMock
{
	use Constraint;
	public $type = 'constraint_mock';
}
