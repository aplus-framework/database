<?php namespace Framework\Database\Definition\DataTypes\Lists;

/**
 * Class SetColumn.
 *
 * @see https://mariadb.com/kb/en/library/set-data-type/
 */
class SetColumn extends ListDataType
{
	protected $type = 'ENUM';
}
