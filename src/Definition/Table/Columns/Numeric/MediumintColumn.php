<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

/**
 * Class MediumintColumn.
 *
 * @see https://mariadb.com/kb/en/library/mediumint/
 */
final class MediumintColumn extends NumericDataType
{
	protected string $type = 'mediumint';
	protected int $maxLength = 127;
}
