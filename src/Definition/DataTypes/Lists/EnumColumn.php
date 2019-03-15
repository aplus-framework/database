<?php namespace Framework\Database\Definition\DataTypes\Lists;

/**
 * Class EnumColumn.
 *
 * @see https://mariadb.com/kb/en/library/enum/
 */
class EnumColumn extends ListDataType
{
	protected $type = 'enum';
}
