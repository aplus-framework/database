<?php namespace Framework\Database\Definition\DataTypes\Texts;

/**
 * Class TextColumn.
 *
 * @see https://mariadb.com/kb/en/library/text/
 */
class TextColumn extends TextDataType
{
	protected $type = 'text';
	protected $minLength = 0;
	protected $maxLength = 65535;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
