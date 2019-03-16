<?php namespace Framework\Database\Definition\Columns\String;

/**
 * Class VarbinaryColumn.
 *
 * @see https://mariadb.com/kb/en/library/varbinary/
 */
class VarbinaryColumn extends BinaryColumn
{
	protected $type = 'varbinary';
	protected $maxLength = 65535;
}
