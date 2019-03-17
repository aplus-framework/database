<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class JsonColumn.
 *
 * @see https://mariadb.com/kb/en/library/json-data-type/
 */
class JsonColumn extends StringDataType
{
	protected $type = 'json';
}
