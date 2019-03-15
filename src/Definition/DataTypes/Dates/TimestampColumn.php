<?php namespace Framework\Database\Definition\DataTypes\Dates;

/**
 * Class TimestampColumn.
 *
 * @see https://mariadb.com/kb/en/library/timestamp/
 */
class TimestampColumn extends DateDataType
{
	protected $type = 'TIMESTAMP';

	public function length($microsecond_precision)
	{
		$this->length = $microsecond_precision;
		return $this;
	}
}
