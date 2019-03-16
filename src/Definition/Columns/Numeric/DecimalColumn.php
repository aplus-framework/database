<?php namespace Framework\Database\Definition\Columns\Numeric;

class DecimalColumn extends FloatColumn
{
	protected $type = 'decimal';
	protected $maxLength = 11;
	protected $decimal;
}
