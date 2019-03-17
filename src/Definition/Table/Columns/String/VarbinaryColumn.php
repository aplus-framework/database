<?php namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class VarbinaryColumn.
 *
 * @see https://mariadb.com/kb/en/library/varbinary/
 */
class VarbinaryColumn extends Column
{
	protected $type = 'varbinary';
	protected $maxLength = 65535;
}
