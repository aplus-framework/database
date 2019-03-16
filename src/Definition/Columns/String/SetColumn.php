<?php namespace Framework\Database\Definition\Columns\String;

/**
 * Class SetColumn.
 *
 * @see https://mariadb.com/kb/en/library/set-data-type/
 */
class SetColumn extends EnumColumn
{
	protected $type = 'set';
}
