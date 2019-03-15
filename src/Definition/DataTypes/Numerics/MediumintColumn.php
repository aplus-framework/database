<?php namespace Framework\Database\Definition\DataTypes\Numerics;

/**
 * Class MediumintColumn.
 *
 * @see https://mariadb.com/kb/en/library/mediumint/
 */
class MediumintColumn extends NumericDataType
{
	protected $type = 'MEDIUMINT';
	protected $maxLength = 127;

	public function length($length)
	{
		$this->length = $length;
		return $this;
	}
}
