<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class TinyblobColumn.
 *
 * @see https://mariadb.com/kb/en/library/tinyblob/
 */
class TinyblobColumn extends BinaryDataType
{
	protected $type = 'TINYBLOB';
}
