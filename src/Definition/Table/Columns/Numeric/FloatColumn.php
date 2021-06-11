<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

use Framework\Database\Definition\Table\Columns\Traits\DecimalLength;

final class FloatColumn extends NumericDataType
{
	use DecimalLength;
	protected string $type = 'float';
	protected int $maxLength = 11;
	protected float $decimal;
}
