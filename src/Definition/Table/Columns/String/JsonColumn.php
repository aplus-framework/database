<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class JsonColumn.
 *
 * @see https://mariadb.com/kb/en/library/json-data-type/
 */
final class JsonColumn extends StringDataType
{
	protected string $type = 'json';
}
