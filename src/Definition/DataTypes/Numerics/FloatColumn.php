<?php namespace Framework\Database\Definition\DataTypes\Numerics;

class FloatColumn extends NumericDataType
{
	protected $type = 'FLOAT';
	protected $maxLength = 11;
	protected $decimal;

	public function length(int $length, int $decimal)
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
