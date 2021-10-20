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

/**
 * Class TextColumn.
 *
 * @see https://mariadb.com/kb/en/text/
 *
 * @package database
 */
final class TextColumn extends StringDataType
{
    protected string $type = 'text';
    protected int $minLength = 0;
    protected int $maxLength = 65535;
}
