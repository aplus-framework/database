<?php namespace Framework\Database\Definition\Columns\String;

/**
 * Class VarcharColumn.
 *
 * @see https://mariadb.com/kb/en/library/varchar/
 */
class VarcharColumn extends CharColumn
{
	protected $type = 'varchar';
	protected $maxLength = 65535;
}
