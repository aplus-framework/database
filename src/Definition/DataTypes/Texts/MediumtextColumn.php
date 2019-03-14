<?php namespace Framework\Database\Definition\DataTypes\Texts;

class MediumtextColumn extends TextDataType
{
	protected $type = 'MEDIUMTEXT';
	protected $minLength = 0;
	protected $maxLength = 65535;
}
