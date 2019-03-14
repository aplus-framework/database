<?php namespace Framework\Database\Definition\DataTypes\Strings;

class VarcharColumn extends StringDataType
{
	protected $type = 'VARCHAR';
	protected $minLength = 0;
	protected $maxLength = 65535;
}
