<?php namespace Framework\Database\Definition\DataTypes\Strings;

/**
 * Class CharColumn.
 *
 * @see https://mariadb.com/kb/en/library/char/
 */
class CharColumn extends StringDataType
{
	protected $type = 'CHAR';
	protected $minLength = 0;
	protected $maxLength = 65535;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
