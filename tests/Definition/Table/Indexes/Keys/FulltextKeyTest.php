<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\FulltextKey;
use Tests\Database\TestCase;

final class FulltextKeyTest extends TestCase
{
    public function testType() : void
    {
        $index = new FulltextKey(static::$database, null, 'id');
        self::assertSame(
            ' FULLTEXT KEY (`id`)',
            $index->sql()
        );
    }
}
