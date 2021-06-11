<?php namespace Framework\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Index;

/**
 * Class FulltextKey.
 *
 * @see https://mariadb.com/kb/en/library/full-text-index-overview/
 */
final class FulltextKey extends Index
{
	protected string $type = 'FULLTEXT KEY';
}
