<?php namespace Framework\Database\Definition\DataTypes\Numerics;

class IntColumn extends NumericDataType
{
	protected $type = 'INT';
	protected $maxLength = 11;

	public function length($length)
	{
		$this->length = $length;
		return $this;
	}
}
