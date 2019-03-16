<?php namespace Framework\Database\Definition\Indexes\Keys;

use Framework\Database\Definition\Indexes\Index;

class PrimaryKey extends Index
{
	use Traits\Constraint;
	protected $type = 'PRIMARY KEY';
}
