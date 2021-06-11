<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

use Framework\Database\Definition\Table\Columns\Traits\DecimalLength;

final class DecimalColumn extends NumericDataType
{
	use DecimalLength;
	protected string $type = 'decimal';
	protected int $maxLength = 11;
	protected float $decimal;
}
