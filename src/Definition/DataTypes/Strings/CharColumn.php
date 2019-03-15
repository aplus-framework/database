<?php namespace Framework\Database\Definition\DataTypes\Strings;

/**
 * Class CharColumn.
 *
 * @see https://mariadb.com/kb/en/library/char/
 */
class CharColumn extends StringDataType
{
	protected $type = 'char';
	protected $minLength = 0;
	protected $maxLength = 255;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
