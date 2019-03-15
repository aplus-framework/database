<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class LongblobColumn.
 *
 * @see https://mariadb.com/kb/en/library/longblob/
 */
class LongblobColumn extends BinaryDataType
{
	protected $type = 'LONGBLOB';
}
