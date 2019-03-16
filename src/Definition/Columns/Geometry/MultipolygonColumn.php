<?php namespace Framework\Database\Definition\Columns\Geometry;

use Framework\Database\Definition\Columns\Column;

/**
 * Class MultipolygonColumn.
 *
 * @see https://mariadb.com/kb/en/multipolygon/
 */
class MultipolygonColumn extends Column
{
	protected $type = 'multipolygon';
}
