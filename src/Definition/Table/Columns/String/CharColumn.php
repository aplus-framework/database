<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class CharColumn.
 *
 * @see https://mariadb.com/kb/en/library/char/
 */
final class CharColumn extends StringDataType
{
	protected string $type = 'char';
	protected int $minLength = 0;
	protected int $maxLength = 255;
}
