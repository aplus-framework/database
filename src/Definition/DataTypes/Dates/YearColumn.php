<?php namespace Framework\Database\Definition\DataTypes\Dates;

/**
 * Class TimestampColumn.
 *
 * @see https://mariadb.com/kb/en/library/year-data-type/
 */
class YearColumn extends DateDataType
{
	protected $type = 'year';
}
