<?php namespace Framework\Database\Definition\Table\Columns\DateTime;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class DatetimeColumn.
 *
 * @see https://mariadb.com/kb/en/library/datetime/
 */
class DatetimeColumn extends Column
{
	protected string $type = 'datetime';
}
