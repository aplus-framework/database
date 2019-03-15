<?php namespace Framework\Database\Definition\DataTypes\Lists;

use Framework\Database\Definition\DataTypes\Strings\StringDataType;

/**
 * Class ListDataType.
 */
abstract class ListDataType extends StringDataType
{
	public function length(string $value, string ...$values)
	{
		$this->length = $values ? \array_merge([$value], $values) : [$value];
		return $this;
	}

	protected function renderLength() : ?string
	{
		if ( ! isset($this->length)) {
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
