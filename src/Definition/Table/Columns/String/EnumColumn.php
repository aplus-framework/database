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

use Framework\Database\Definition\Table\Columns\Traits\ListLength;

/**
 * Class EnumColumn.
 *
 * @see https://mariadb.com/kb/en/enum/
 *
 * @package database
 */
final class EnumColumn extends StringDataType
{
    use ListLength;
    protected string $type = 'enum';
}
