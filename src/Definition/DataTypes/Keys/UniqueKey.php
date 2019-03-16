<?php namespace Framework\Database\Definition\DataTypes\Keys;

use Framework\Database\Definition\DataTypes\Key;

class UniqueKey extends Key
{
	use Traits\Constraint;
	protected $type = 'UNIQUE KEY';
}
