<?php namespace Framework\Database\Definition\Table\Columns\Traits;

trait DecimalLength
{
	protected function renderLength() : ?string
	{
		if ( ! isset($this->length[0])) {
			return null;
		}
		$maximum = $this->database->quote($this->length[0]);
		if ($this->length[1] !== null) {
			$decimals = $this->database->quote($this->length[1]);
			$maximum .= ",{$decimals}";
		}
		return "({$maximum})";
	}
}
