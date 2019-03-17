<?php namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class MediumblobColumn.
 *
 * @see https://mariadb.com/kb/en/library/mediumblob/
 */
class MediumblobColumn extends Column
{
	protected $type = 'mediumblob';
}
