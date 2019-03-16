<?php namespace Framework\Database\Definition\DataTypes\Keys;

use Framework\Database\Definition\DataTypes\Key;

class PrimaryKey extends Key
{
	use Traits\Constraint;
	protected $type = 'PRIMARY KEY';
}
