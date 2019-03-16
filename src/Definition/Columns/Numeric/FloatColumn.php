<?php namespace Framework\Database\Definition\Columns\Numeric;

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
		$maximum = $this->database->protectIdentifier($this->length);
		$decimal = $this->database->protectIdentifier($this->decimal);
		return "({$maximum},{$decimal})";
	}
}
