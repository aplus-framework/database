<?php namespace Framework\Database\Definition\Table\Columns\Traits;

trait ListLength
{
	protected function renderLength() : ?string
	{
		if (empty($this->length)) {
			return null;
		}
		$values = [];
		foreach ($this->length as $length) {
			$values[] = $this->database->quote($length);
		}
		$values = \implode(', ', $values);
		return "({$values})";
	}
}
