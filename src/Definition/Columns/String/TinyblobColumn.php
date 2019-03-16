<?php namespace Framework\Database\Definition\Columns\String;

use Framework\Database\Definition\Columns\Column;

/**
 * Class TinyblobColumn.
 *
 * @see https://mariadb.com/kb/en/library/tinyblob/
 */
class TinyblobColumn extends Column
{
	protected $type = 'tinyblob';
}
