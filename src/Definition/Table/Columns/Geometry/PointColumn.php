<?php namespace Framework\Database\Definition\Table\Columns\Geometry;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class PointColumn.
 *
 * @see https://mariadb.com/kb/en/point/
 */
class PointColumn extends Column
{
	protected $type = 'point';
}
