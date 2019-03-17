<?php namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Traits\ListLength;

/**
 * Class SetColumn.
 *
 * @see https://mariadb.com/kb/en/library/set-data-type/
 */
class SetColumn extends StringDataType
{
	use ListLength;
	protected $type = 'set';
}
