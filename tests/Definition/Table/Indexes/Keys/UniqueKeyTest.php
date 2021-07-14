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

use Framework\Database\Definition\Table\Indexes\Keys\UniqueKey;
use Tests\Database\TestCase;

final class UniqueKeyTest extends TestCase
{
    public function testType() : void
    {
        $index = new UniqueKey(static::$database, null, 'id');
        self::assertSame(
            ' UNIQUE KEY (`id`)',
            $index->sql()
        );
    }
}
