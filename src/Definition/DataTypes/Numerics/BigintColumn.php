<?php namespace Framework\Database\Definition\DataTypes\Numerics;

class BigintColumn extends NumericDataType
{
	protected $type = 'BIGINT';
	protected $maxLength = 21;

	public function length($length)
	{
		$this->length = $length;
		return $this;
	}
}
