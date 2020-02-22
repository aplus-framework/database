<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class TinytextColumn.
 *
 * @see https://mariadb.com/kb/en/library/tinytext/
 */
class TinytextColumn extends StringDataType
{
	protected string $type = 'tinytext';
}
