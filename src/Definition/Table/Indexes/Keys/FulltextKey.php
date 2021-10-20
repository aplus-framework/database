<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
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
 * @see https://mariadb.com/kb/en/full-text-index-overview/
 *
 * @package database
 */
final class FulltextKey extends Index
{
    protected string $type = 'FULLTEXT KEY';
}
