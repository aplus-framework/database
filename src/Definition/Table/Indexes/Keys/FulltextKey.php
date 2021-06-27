<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Indexes\Keys;

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
