<?php namespace Framework\Database\Definition\DataTypes\Texts;

class TextColumn extends TextDataType
{
	protected $type = 'TEXT';
	protected $minLength = 0;
	protected $maxLength = 65535;
}
