<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class LongtextColumn.
 *
 * @see https://mariadb.com/kb/en/library/longtext/
 */
class LongtextColumn extends StringDataType
{
	protected string $type = 'longtext';
}
