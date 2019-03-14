<?php namespace Framework\Database\Definition\DataTypes\Strings;

class CharColumn extends StringDataType
{
	protected $type = 'CHAR';
	protected $minLength = 0;
	protected $maxLength = 65535;
}
