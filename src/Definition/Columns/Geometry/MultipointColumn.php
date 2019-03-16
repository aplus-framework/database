<?php namespace Framework\Database\Definition\Columns\Geometry;

use Framework\Database\Definition\Columns\Column;

/**
 * Class MultipointColumn.
 *
 * @see https://mariadb.com/kb/en/multipoint/
 */
class MultipointColumn extends Column
{
	protected $type = 'multipoint';
}
