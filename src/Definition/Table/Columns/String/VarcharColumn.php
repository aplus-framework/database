<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class VarcharColumn.
 *
 * @see https://mariadb.com/kb/en/library/varchar/
 */
class VarcharColumn extends StringDataType
{
	protected $type = 'varchar';
	protected $maxLength = 65535;
}
