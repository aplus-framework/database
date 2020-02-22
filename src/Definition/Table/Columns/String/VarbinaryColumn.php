<?php namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class VarbinaryColumn.
 *
 * @see https://mariadb.com/kb/en/library/varbinary/
 */
class VarbinaryColumn extends Column
{
	protected string $type = 'varbinary';
	protected int $maxLength = 65535;
}
