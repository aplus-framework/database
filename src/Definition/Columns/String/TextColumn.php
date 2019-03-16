<?php namespace Framework\Database\Definition\Columns\String;

/**
 * Class TextColumn.
 *
 * @see https://mariadb.com/kb/en/library/text/
 */
class TextColumn extends CharColumn
{
	protected $type = 'text';
	protected $minLength = 0;
	protected $maxLength = 65535;
}
