<?php namespace Framework\Database\Definition\Columns\String;

use Framework\Database\Definition\Columns\Column;

/**
 * Class BinaryColumn.
 *
 * @see https://mariadb.com/kb/en/library/binary/
 */
class BinaryColumn extends Column
{
	protected $type = 'binary';
	protected $minLength = 0;
	protected $maxLength = 255;

	public function length(int $maximum)
	{
		return $this->setLength($maximum);
	}
}
