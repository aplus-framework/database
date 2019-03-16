<?php namespace Framework\Database\Definition\Indexes\Keys;

use Framework\Database\Definition\Indexes\Index;

/**
 * Class UniqueKey.
 *
 * @see https://mariadb.com/kb/en/library/getting-started-with-indexes/#unique-index
 */
class UniqueKey extends Index
{
	use Traits\Constraint;
	protected $type = 'UNIQUE KEY';
}
