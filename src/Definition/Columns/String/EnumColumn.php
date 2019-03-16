<?php namespace Framework\Database\Definition\Columns\String;

/**
 * Class EnumColumn.
 *
 * @see https://mariadb.com/kb/en/library/enum/
 */
class EnumColumn extends StringDataType
{
	protected $type = 'enum';

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
