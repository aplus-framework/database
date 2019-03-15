<?php namespace Framework\Database\Definition\DataTypes\Dates;

/**
 * Class DatetimeColumn.
 *
 * @see https://mariadb.com/kb/en/library/datetime/
 */
class DatetimeColumn extends TimeColumn
{
	protected $type = 'datetime';
}
