<?php namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class LongblobColumn.
 *
 * @see https://mariadb.com/kb/en/library/longblob/
 */
final class LongblobColumn extends Column
{
	protected string $type = 'longblob';
}
