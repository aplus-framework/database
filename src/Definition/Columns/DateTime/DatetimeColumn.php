<?php namespace Framework\Database\Definition\Columns\DateTime;

/**
 * Class DatetimeColumn.
 *
 * @see https://mariadb.com/kb/en/library/datetime/
 */
class DatetimeColumn extends TimeColumn
{
	protected $type = 'datetime';
}
