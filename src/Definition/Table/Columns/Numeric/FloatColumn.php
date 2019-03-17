<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

use Framework\Database\Definition\Table\Columns\Traits\DecimalLength;

class FloatColumn extends NumericDataType
{
	use DecimalLength;
	protected $type = 'float';
	protected $maxLength = 11;
	protected $decimal;
}
