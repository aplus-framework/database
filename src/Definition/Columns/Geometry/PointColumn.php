<?php namespace Framework\Database\Definition\Columns\Geometry;

use Framework\Database\Definition\Columns\Column;

/**
 * Class PointColumn.
 *
 * @see https://mariadb.com/kb/en/point/
 */
class PointColumn extends Column
{
	protected $type = 'point';
}
