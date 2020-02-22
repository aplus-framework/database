<?php namespace Framework\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Index;

/**
 * Class SpatialKey.
 *
 * @see https://mariadb.com/kb/en/library/spatial-index/
 */
class SpatialKey extends Index
{
	protected string $type = 'SPATIAL KEY';
}
