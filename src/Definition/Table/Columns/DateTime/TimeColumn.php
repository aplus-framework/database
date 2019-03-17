<?php namespace Framework\Database\Definition\Table\Columns\DateTime;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class TimeColumn.
 *
 * @see https://mariadb.com/kb/en/library/time/
 */
class TimeColumn extends Column
{
	protected $type = 'time';
}
