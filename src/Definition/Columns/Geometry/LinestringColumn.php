<?php namespace Framework\Database\Definition\Columns\Geometry;

use Framework\Database\Definition\Columns\Column;

/**
 * Class LinestringColumn.
 *
 * @see https://mariadb.com/kb/en/linestring/
 */
class LinestringColumn extends Column
{
	protected $type = 'linestring';
}
