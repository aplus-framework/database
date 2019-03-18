<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

/**
 * Class MediumintColumn.
 *
 * @see https://mariadb.com/kb/en/library/mediumint/
 */
class MediumintColumn extends NumericDataType
{
	protected $type = 'mediumint';
	protected $maxLength = 127;
}
