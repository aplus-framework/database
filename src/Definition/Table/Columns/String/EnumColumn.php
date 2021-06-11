<?php namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Traits\ListLength;

/**
 * Class EnumColumn.
 *
 * @see https://mariadb.com/kb/en/library/enum/
 */
final class EnumColumn extends StringDataType
{
	use ListLength;
	protected string $type = 'enum';
}
