<?php namespace Framework\Database\Definition\DataTypes\Texts;

/**
 * Class TinytextColumn.
 *
 * @see https://mariadb.com/kb/en/library/tinytext/
 */
class TinytextColumn extends TextDataType
{
	protected $type = 'TINYTEXT';
	protected $minLength = 0;
	protected $maxLength = 65535;
}
