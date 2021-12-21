<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Indexes\Keys;

use Tests\Database\TestCase;

final class ConstraintKeyTest extends TestCase
{
    public function testConstraint() : void
    {
        $index = new ConstraintKeyMock(static::$database, null, 'id');
        self::assertSame(
            ' constraint_mock (`id`)',
            $index->sql()
        );
        $index->constraint('foo');
        self::assertSame(
            ' CONSTRAINT `foo` constraint_mock (`id`)',
            $index->sql()
        );
    }
}
