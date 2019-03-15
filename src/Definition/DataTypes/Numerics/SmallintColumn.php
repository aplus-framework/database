<?php namespace Framework\Database\Definition\DataTypes\Numerics;

/**
 * Class SmallintColumn.
 *
 * @see https://mariadb.com/kb/en/library/smallint/
 */
class SmallintColumn extends NumericDataType
{
	protected $type = 'SMALLINT';
	protected $maxLength = 127;

	public function length($length)
	{
		$this->length = $length;
		return $this;
	}
}
