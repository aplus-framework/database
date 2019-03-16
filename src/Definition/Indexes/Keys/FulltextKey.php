<?php namespace Framework\Database\Definition\Indexes\Keys;

use Framework\Database\Definition\Indexes\Index;

/**
 * Class FulltextKey.
 *
 * @see https://mariadb.com/kb/en/library/full-text-index-overview/
 */
class FulltextKey extends Index
{
	protected $type = 'FULLTEXT KEY';
}
