<?php namespace Framework\Database\Definition\DataTypes\Texts;

/**
 * Class JsonColumn.
 *
 * @see https://mariadb.com/kb/en/library/json-data-type/
 */
class JsonColumn extends LongtextColumn
{
	protected $type = 'json';
}
