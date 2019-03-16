<?php namespace Framework\Database\Definition\Columns\String;

/**
 * Class LongtextColumn.
 *
 * @see https://mariadb.com/kb/en/library/longtext/
 */
class LongtextColumn extends StringDataType
{
	protected $type = 'longtext';
}
