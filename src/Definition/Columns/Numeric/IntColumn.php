<?php namespace Framework\Database\Definition\Columns\Numeric;

class IntColumn extends NumericDataType
{
	protected $type = 'int';
	protected $maxLength = 11;

	public function length(int $maximum)
	{
		return $this->setLength($maximum);
	}
}
