<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class VarbinaryColumn.
 *
 * @see https://mariadb.com/kb/en/varbinary/
 *
 * @package database
 */
final class VarbinaryColumn extends Column
{
    protected string $type = 'varbinary';
    protected int $maxLength = 65535;
}
