<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class BinaryColumn.
 *
 * @see https://mariadb.com/kb/en/library/binary/
 */
class BinaryColumn extends BinaryDataType
{
	protected $type = 'BINARY';
	protected $minLength = 0;
	protected $maxLength = 65535;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
