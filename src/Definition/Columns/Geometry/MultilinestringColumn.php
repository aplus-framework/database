<?php namespace Framework\Database\Definition\Columns\Geometry;

use Framework\Database\Definition\Columns\Column;

/**
 * Class MultilinestringColumn.
 *
 * @see https://mariadb.com/kb/en/multilinestring/
 */
class MultilinestringColumn extends Column
{
	protected $type = 'multilinestring';
}
