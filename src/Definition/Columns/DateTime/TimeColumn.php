<?php namespace Framework\Database\Definition\Columns\DateTime;

use Framework\Database\Definition\Columns\Column;

/**
 * Class TimeColumn.
 *
 * @see https://mariadb.com/kb/en/library/time/
 */
class TimeColumn extends Column
{
	protected $type = 'time';

	public function length($microsecond_precision)
	{
		$this->length = $microsecond_precision;
		return $this;
	}
}
