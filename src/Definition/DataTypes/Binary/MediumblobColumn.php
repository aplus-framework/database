<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class MediumblobColumn.
 *
 * @see https://mariadb.com/kb/en/library/mediumblob/
 */
class MediumblobColumn extends BinaryDataType
{
	protected $type = 'MEDIUMBLOB';
}
