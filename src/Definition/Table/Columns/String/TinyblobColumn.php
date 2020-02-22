<?php namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class TinyblobColumn.
 *
 * @see https://mariadb.com/kb/en/library/tinyblob/
 */
class TinyblobColumn extends Column
{
	protected string $type = 'tinyblob';
}
