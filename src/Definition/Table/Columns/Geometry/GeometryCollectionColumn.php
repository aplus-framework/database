<?php namespace Framework\Database\Definition\Table\Columns\Geometry;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class GeometryCollectionColumn.
 *
 * @see https://mariadb.com/kb/en/geometrycollection/
 */
final class GeometryCollectionColumn extends Column
{
	protected string $type = 'geometrycollection';
}
