<?php namespace Framework\Database\Definition\Columns\Geometry;

use Framework\Database\Definition\Columns\Column;

/**
 * Class GeometryCollectionColumn.
 *
 * @see https://mariadb.com/kb/en/geometrycollection/
 */
class GeometryCollectionColumn extends Column
{
	protected $type = 'geometrycollection';
}
