<?php namespace Framework\Database\Definition\DataTypes\Dates;

/**
 * Class TimestampColumn.
 *
 * @see https://mariadb.com/kb/en/library/timestamp/
 */
class TimestampColumn extends TimeColumn
{
	protected $type = 'timestamp';
}
