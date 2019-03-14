<?php namespace Framework\Database\Definition\DataTypes\Texts;

class LongtextColumn extends TextDataType
{
	protected $type = 'LONGTEXT';
	protected $minLength = 0;
	protected $maxLength = 65535;
}
