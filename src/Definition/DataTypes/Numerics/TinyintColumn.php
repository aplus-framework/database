<?php namespace Framework\Database\Definition\DataTypes\Numerics;

class TinyintColumn extends NumericDataType
{
	protected $type = 'TINYINT';
	protected $maxLength = 127;

	public function length($length)
	{
		$this->length = $length;
		return $this;
	}
}
