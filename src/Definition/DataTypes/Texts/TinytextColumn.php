<?php namespace Framework\Database\Definition\DataTypes\Texts;

class TinytextColumn extends TextDataType
{
	protected $type = 'TINYTEXT';
	protected $minLength = 0;
	protected $maxLength = 65535;
}
