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

/**
 * Class UniqueKey.
 *
 * @see https://mariadb.com/docs/server/mariadb-quickstart-guides/mariadb-indexes-guide#unique-index
 *
 * @package database
 */
final class UniqueKey extends ConstraintKey
{
    protected string $type = 'UNIQUE KEY';
}
