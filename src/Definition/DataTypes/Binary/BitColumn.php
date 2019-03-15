<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class BitColumn.
 *
 * @see https://mariadb.com/kb/en/library/bit/
 */
class BitColumn extends BinaryColumn
{
	protected $type = 'bit';
}
