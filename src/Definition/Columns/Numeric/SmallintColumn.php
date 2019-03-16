<?php namespace Framework\Database\Definition\Columns\Numeric;

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
