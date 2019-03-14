<?php namespace Framework\Database\Definition\DataTypes\Numerics;

class DecimalColumn extends NumericDataType
{
	protected $type = 'DECIMAL';
	protected $maxLength = 11;
	protected $decimal;

	public function length(int $length, int $decimal = 0)
	{
		$this->length = $length;
		$this->decimal = $decimal;
		return $this;
	}

	protected function renderLength() : ?string
	{
		if ( ! isset($this->length)) {
			return null;
		}
		return "({$this->length},{$this->decimal})";
	}
}
