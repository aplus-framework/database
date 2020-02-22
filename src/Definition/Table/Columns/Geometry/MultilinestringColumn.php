<?php namespace Framework\Database\Definition\Table\Columns\Geometry;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class MultilinestringColumn.
 *
 * @see https://mariadb.com/kb/en/multilinestring/
 */
class MultilinestringColumn extends Column
{
	protected string $type = 'multilinestring';
}
