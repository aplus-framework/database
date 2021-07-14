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

use Framework\Database\Definition\Table\Indexes\Keys\Key;
use Tests\Database\TestCase;

final class KeyTest extends TestCase
{
    public function testType() : void
    {
        $index = new Key(static::$database, null, 'id');
        self::assertSame(
            ' KEY (`id`)',
            $index->sql()
        );
    }
}
