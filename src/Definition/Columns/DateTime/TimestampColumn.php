<?php namespace Framework\Database\Definition\Columns\DateTime;

/**
 * Class TimestampColumn.
 *
 * @see https://mariadb.com/kb/en/library/timestamp/
 */
class TimestampColumn extends TimeColumn
{
	protected $type = 'timestamp';
}
