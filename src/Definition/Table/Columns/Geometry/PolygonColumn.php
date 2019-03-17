<?php namespace Framework\Database\Definition\Table\Columns\Geometry;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class PolygonColumn.
 *
 * @see https://mariadb.com/kb/en/polygon/
 */
class PolygonColumn extends Column
{
	protected $type = 'polygon';
}
