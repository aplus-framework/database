<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class VarcharColumn.
 *
 * @see https://mariadb.com/kb/en/library/varchar/
 */
class VarcharColumn extends StringDataType
{
	protected string $type = 'varchar';
	protected int $maxLength = 65535;
}
