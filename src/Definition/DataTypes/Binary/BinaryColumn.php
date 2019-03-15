<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class BinaryColumn.
 *
 * @see https://mariadb.com/kb/en/library/binary/
 */
class BinaryColumn extends BinaryDataType
{
	protected $type = 'binary';
	protected $minLength = 0;
	protected $maxLength = 255;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
