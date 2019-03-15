<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class BlobColumn.
 *
 * @see https://mariadb.com/kb/en/library/blob/
 */
class BlobColumn extends BinaryDataType
{
	protected $type = 'BLOB';
	protected $minLength = 0;
	protected $maxLength = 65535;

	public function length(int $maximum)
	{
		$this->length = $maximum;
		return $this;
	}
}
