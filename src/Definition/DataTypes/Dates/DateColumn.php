<?php namespace Framework\Database\Definition\DataTypes\Dates;

/**
 * Class EnumColumn.
 *
 * @see https://mariadb.com/kb/en/library/date/
 */
class DateColumn extends DateDataType
{
	protected $type = 'date';
}
