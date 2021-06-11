<?php namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class BlobColumn.
 *
 * @see https://mariadb.com/kb/en/library/blob/
 */
final class BlobColumn extends Column
{
	protected string $type = 'blob';
}
