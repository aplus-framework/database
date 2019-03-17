<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class CharColumn.
 *
 * @see https://mariadb.com/kb/en/library/char/
 */
class CharColumn extends StringDataType
{
	protected $type = 'char';
	protected $minLength = 0;
	protected $maxLength = 255;
}
