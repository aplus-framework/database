<?php namespace Framework\Database\Definition\Table\Columns\DateTime;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class TimestampColumn.
 *
 * @see https://mariadb.com/kb/en/library/year-data-type/
 */
class YearColumn extends Column
{
	protected $type = 'year';
}
