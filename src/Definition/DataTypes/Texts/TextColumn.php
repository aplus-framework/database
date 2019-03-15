<?php namespace Framework\Database\Definition\DataTypes\Texts;

/**
 * Class TextColumn.
 *
 * @see https://mariadb.com/kb/en/library/text/
 */
class TextColumn extends TextDataType
{
	protected $type = 'TEXT';
	protected $minLength = 0;
	protected $maxLength = 65535;

	public function length(int $length)
	{
		$this->length = $length;
		return $this;
	}
}
