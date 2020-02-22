<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

class TinyintColumn extends NumericDataType
{
	protected string $type = 'tinyint';
	protected int $maxLength = 127;
}
