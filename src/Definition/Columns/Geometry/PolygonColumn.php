<?php namespace Framework\Database\Definition\Columns\Geometry;

use Framework\Database\Definition\Columns\Column;

/**
 * Class PolygonColumn.
 *
 * @see https://mariadb.com/kb/en/polygon/
 */
class PolygonColumn extends Column
{
	protected $type = 'polygon';
}
