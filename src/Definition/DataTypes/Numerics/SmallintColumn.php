<?php namespace Framework\Database\Definition\DataTypes\Numerics;

/**
 * Class SmallintColumn.
 *
 * @see https://mariadb.com/kb/en/library/smallint/
 */
class SmallintColumn extends IntColumn
{
	protected $type = 'smallint';
	protected $maxLength = 127;
}
