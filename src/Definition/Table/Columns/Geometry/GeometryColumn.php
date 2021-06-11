<?php namespace Framework\Database\Definition\Table\Columns\Geometry;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class GeometryColumn.
 *
 * @see https://mariadb.com/kb/en/geometry/
 */
final class GeometryColumn extends Column
{
	protected string $type = 'geometry';
}
