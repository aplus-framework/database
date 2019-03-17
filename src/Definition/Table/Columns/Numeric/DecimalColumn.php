<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

use Framework\Database\Definition\Table\Columns\Traits\DecimalLength;

class DecimalColumn extends NumericDataType
{
	use DecimalLength;
	protected $type = 'decimal';
	protected $maxLength = 11;
	protected $decimal;
}
