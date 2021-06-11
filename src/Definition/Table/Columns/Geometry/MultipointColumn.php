<?php namespace Framework\Database\Definition\Table\Columns\Geometry;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class MultipointColumn.
 *
 * @see https://mariadb.com/kb/en/multipoint/
 */
final class MultipointColumn extends Column
{
	protected string $type = 'multipoint';
}
