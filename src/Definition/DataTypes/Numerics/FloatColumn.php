<?php namespace Framework\Database\Definition\DataTypes\Numerics;

class FloatColumn extends NumericDataType
{
	protected $type = 'float';
	protected $maxLength = 11;
	protected $decimal;

	public function length(int $maximum, int $decimal)
	{
		$this->length = $maximum;
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
