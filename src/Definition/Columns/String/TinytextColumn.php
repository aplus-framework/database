<?php namespace Framework\Database\Definition\Columns\String;

/**
 * Class TinytextColumn.
 *
 * @see https://mariadb.com/kb/en/library/tinytext/
 */
class TinytextColumn extends StringDataType
{
	protected $type = 'tinytext';
}
