<?php namespace Framework\Database\Definition\Table\Columns\DateTime;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class EnumColumn.
 *
 * @see https://mariadb.com/kb/en/library/date/
 */
class DateColumn extends Column
{
	protected $type = 'date';
}
