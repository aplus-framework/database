<?php namespace Framework\Database\Definition\Table\Columns\Geometry;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class LinestringColumn.
 *
 * @see https://mariadb.com/kb/en/linestring/
 */
class LinestringColumn extends Column
{
	protected string $type = 'linestring';
}
