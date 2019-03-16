<?php namespace Framework\Database\Definition\Columns\Geometry;

use Framework\Database\Definition\Columns\Column;

/**
 * Class GeometryColumn.
 *
 * @see https://mariadb.com/kb/en/geometry/
 */
class GeometryColumn extends Column
{
	protected $type = 'geometry';
}
