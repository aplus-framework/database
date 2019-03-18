<?php namespace Framework\Database\Definition\Table\Columns\Traits;

trait DecimalLength
{
	protected function renderLength() : ?string
	{
		if ( ! isset($this->length[0])) {
			return null;
		}
		$maximum = $this->database->quote($this->length[0]);
		if (isset($this->length[1])) {
			$decimals = $this->database->quote($this->length[1]);
			$maximum .= ",{$decimals}";
		}
		return "({$maximum})";
	}
}
