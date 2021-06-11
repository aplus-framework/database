<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

final class TinyintColumn extends NumericDataType
{
	protected string $type = 'tinyint';
	protected int $maxLength = 127;
}
