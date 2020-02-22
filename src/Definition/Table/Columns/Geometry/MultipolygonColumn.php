<?php namespace Framework\Database\Definition\Table\Columns\Geometry;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class MultipolygonColumn.
 *
 * @see https://mariadb.com/kb/en/multipolygon/
 */
class MultipolygonColumn extends Column
{
	protected string $type = 'multipolygon';
}
