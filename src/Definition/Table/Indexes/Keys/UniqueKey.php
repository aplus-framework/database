<?php namespace Framework\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Index;

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
