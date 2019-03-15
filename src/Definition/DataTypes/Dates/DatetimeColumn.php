<?php namespace Framework\Database\Definition\DataTypes\Dates;

/**
 * Class DatetimeColumn.
 *
 * @see https://mariadb.com/kb/en/library/datetime/
 */
class DatetimeColumn extends DateDataType
{
	protected $type = 'DATETIME';

	public function length($microsecond_precision)
	{
		$this->length = $microsecond_precision;
		return $this;
	}
}
