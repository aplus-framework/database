<?php namespace Framework\Database\Definition\Columns\DateTime;

use Framework\Database\Definition\Columns\Column;

/**
 * Class TimestampColumn.
 *
 * @see https://mariadb.com/kb/en/library/year-data-type/
 */
class YearColumn extends Column
{
	protected $type = 'year';
}
