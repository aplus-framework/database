<?php namespace Framework\Database\Definition\Columns\String;

use Framework\Database\Definition\Columns\Column;

/**
 * Class MediumblobColumn.
 *
 * @see https://mariadb.com/kb/en/library/mediumblob/
 */
class MediumblobColumn extends Column
{
	protected $type = 'mediumblob';
}
