<?php namespace Framework\Database\Definition\Columns\Numeric;

/**
 * Class MediumintColumn.
 *
 * @see https://mariadb.com/kb/en/library/mediumint/
 */
class MediumintColumn extends IntColumn
{
	protected $type = 'MEDIUMINT';
	protected $maxLength = 127;
}
