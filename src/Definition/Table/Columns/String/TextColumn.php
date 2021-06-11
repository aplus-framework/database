<?php namespace Framework\Database\Definition\Table\Columns\String;

/**
 * Class TextColumn.
 *
 * @see https://mariadb.com/kb/en/library/text/
 */
final class TextColumn extends StringDataType
{
	protected string $type = 'text';
	protected int $minLength = 0;
	protected int $maxLength = 65535;
}
