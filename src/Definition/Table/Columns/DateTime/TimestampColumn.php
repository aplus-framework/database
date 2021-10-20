<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns\DateTime;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class TimestampColumn.
 *
 * @see https://mariadb.com/kb/en/timestamp/
 *
 * @package database
 */
final class TimestampColumn extends Column
{
    protected string $type = 'timestamp';
}
