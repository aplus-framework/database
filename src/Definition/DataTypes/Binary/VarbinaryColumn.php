<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class VarbinaryColumn.
 *
 * @see https://mariadb.com/kb/en/library/varbinary/
 */
class VarbinaryColumn extends BinaryDataType
{
	protected $type = 'VARBINARY';
	protected $minLength = 0;
	protected $maxLength = 65535;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
