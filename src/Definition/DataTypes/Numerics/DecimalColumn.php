<?php namespace Framework\Database\Definition\DataTypes\Numerics;

class DecimalColumn extends FloatColumn
{
	protected $type = 'decimal';
	protected $maxLength = 11;
	protected $decimal;
}
