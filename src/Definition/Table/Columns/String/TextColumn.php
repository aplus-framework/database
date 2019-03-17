<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class TextColumn.
 *
 * @see https://mariadb.com/kb/en/library/text/
 */
class TextColumn extends StringDataType
{
	protected $type = 'text';
	protected $minLength = 0;
	protected $maxLength = 65535;
}
