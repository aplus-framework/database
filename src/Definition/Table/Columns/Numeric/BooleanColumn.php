<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

/**
 * Class BooleanColumn.
 *
 * @see https://mariadb.com/kb/en/library/boolean/
 */
class BooleanColumn extends NumericDataType
{
	protected string $type = 'boolean';
}
