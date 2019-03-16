<?php namespace Framework\Database\Definition\Columns\Numeric;

use Framework\Database\Definition\Columns\Column;

/**
 * Class BitColumn.
 *
 * @see https://mariadb.com/kb/en/library/bit/
 */
class BitColumn extends Column
{
	protected $type = 'bit';

	public function length(int $maximum)
	{
		return $this->setLength($maximum);
	}
}
