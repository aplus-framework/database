<?php namespace Framework\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Index;

class PrimaryKey extends Index
{
	use Traits\Constraint;
	protected string $type = 'PRIMARY KEY';
}
