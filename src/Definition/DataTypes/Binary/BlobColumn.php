<?php namespace Framework\Database\Definition\DataTypes\Binary;

/**
 * Class BlobColumn.
 *
 * @see https://mariadb.com/kb/en/library/blob/
 */
class BlobColumn extends BinaryColumn
{
	protected $type = 'blob';
}
