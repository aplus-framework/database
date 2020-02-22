<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class MediumtextColumn.
 *
 * @see https://mariadb.com/kb/en/library/mediumtext/
 */
class MediumtextColumn extends StringDataType
{
	protected string $type = 'mediumtext';
}
