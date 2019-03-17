<?php namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class BinaryColumn.
 *
 * @see https://mariadb.com/kb/en/library/binary/
 */
class BinaryColumn extends Column
{
	protected $type = 'binary';
	protected $minLength = 0;
	protected $maxLength = 255;
}
