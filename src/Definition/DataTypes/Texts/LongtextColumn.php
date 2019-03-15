<?php namespace Framework\Database\Definition\DataTypes\Texts;

/**
 * Class LongtextColumn.
 *
 * @see https://mariadb.com/kb/en/library/longtext/
 */
class LongtextColumn extends TextDataType
{
	protected $type = 'longtext';
}
