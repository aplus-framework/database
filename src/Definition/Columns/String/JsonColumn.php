<?php namespace Framework\Database\Definition\Columns\String;

/**
 * Class JsonColumn.
 *
 * @see https://mariadb.com/kb/en/library/json-data-type/
 */
class JsonColumn extends StringDataType
{
	protected $type = 'json';
}
