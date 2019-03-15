<?php namespace Framework\Database\Definition\DataTypes\Dates;

/**
 * Class TimeColumn.
 *
 * @see https://mariadb.com/kb/en/library/time/
 */
class TimeColumn extends DateDataType
{
	protected $type = 'TIME';

	public function length($microsecond_precision)
	{
		$this->length = $microsecond_precision;
		return $this;
	}
}
