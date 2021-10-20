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
 * Class CharColumn.
 *
 * @see https://mariadb.com/kb/en/char/
 *
 * @package database
 */
final class CharColumn extends StringDataType
{
    protected string $type = 'char';
    protected int $minLength = 0;
    protected int $maxLength = 255;
}
