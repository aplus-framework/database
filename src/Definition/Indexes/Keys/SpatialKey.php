<?php namespace Framework\Database\Definition\Indexes\Keys;

use Framework\Database\Definition\Indexes\Index;

/**
 * Class SpatialKey.
 *
 * @see https://mariadb.com/kb/en/library/spatial-index/
 */
class SpatialKey extends Index
{
	protected $type = 'SPATIAL KEY';
}
