<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Columns\Traits;

use Tests\Database\TestCase;

final class DecimalLengthTest extends TestCase
{
    public function testLength() : void
    {
        $column = new DecimalLengthMock(static::$database);
        self::assertSame(
            ' mock NOT NULL',
            $column->sql()
        );
        $column = new DecimalLengthMock(static::$database, 12);
        self::assertSame(
            ' mock(12) NOT NULL',
            $column->sql()
        );
        $column = new DecimalLengthMock(static::$database, 16, 4);
        self::assertSame(
            ' mock(16,4) NOT NULL',
            $column->sql()
        );
    }
}
