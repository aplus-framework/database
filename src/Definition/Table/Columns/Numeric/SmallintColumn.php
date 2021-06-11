<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

/**
 * Class SmallintColumn.
 *
 * @see https://mariadb.com/kb/en/library/smallint/
 */
final class SmallintColumn extends NumericDataType
{
	protected string $type = 'smallint';
	protected int $maxLength = 127;
}
