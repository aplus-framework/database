<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

class TinyintColumn extends NumericDataType
{
	protected $type = 'tinyint';
	protected $maxLength = 127;
}
