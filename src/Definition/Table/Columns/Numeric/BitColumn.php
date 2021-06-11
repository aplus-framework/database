<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class BitColumn.
 *
 * @see https://mariadb.com/kb/en/library/bit/
 */
final class BitColumn extends Column
{
	protected string $type = 'bit';
}
