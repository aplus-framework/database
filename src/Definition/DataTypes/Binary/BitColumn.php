<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class BitColumn.
 *
 * @see https://mariadb.com/kb/en/library/bit/
 */
class BitColumn extends BinaryDataType
{
	protected $type = 'BIT';
	protected $minLength = 0;
	protected $maxLength = 65535;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
