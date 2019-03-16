<?php namespace Framework\Database\Definition\Columns\String;

use Framework\Database\Definition\Columns\Column;

/**
 * Class LongblobColumn.
 *
 * @see https://mariadb.com/kb/en/library/longblob/
 */
class LongblobColumn extends Column
{
	protected $type = 'longblob';
}
