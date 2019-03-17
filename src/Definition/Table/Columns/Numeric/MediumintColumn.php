<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

/**
 * Class MediumintColumn.
 *
 * @see https://mariadb.com/kb/en/library/mediumint/
 */
class MediumintColumn extends NumericDataType
{
	protected $type = 'MEDIUMINT';
	protected $maxLength = 127;
}
