<?php namespace Framework\Database\Definition\DataTypes\Numerics;

class IntColumn extends NumericDataType
{
	protected $type = 'int';
	protected $maxLength = 11;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
