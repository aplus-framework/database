<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

class IntColumn extends NumericDataType
{
	protected string $type = 'int';
	protected int $maxLength = 11;
}
