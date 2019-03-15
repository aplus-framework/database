<?php namespace Framework\Database\Definition\DataTypes\Strings;

/**
 * Class VarcharColumn.
 *
 * @see https://mariadb.com/kb/en/library/varchar/
 */
class VarcharColumn extends StringDataType
{
	protected $type = 'VARCHAR';
	protected $minLength = 0;
	protected $maxLength = 65535;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
