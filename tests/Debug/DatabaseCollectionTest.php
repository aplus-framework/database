<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Debug;

use Framework\Database\Debug\DatabaseCollection;
use PHPUnit\Framework\TestCase;

final class DatabaseCollectionTest extends TestCase
{
    protected DatabaseCollection $collection;

    protected function setUp() : void
    {
        $this->collection = new DatabaseCollection('Database');
    }

    public function testIcon() : void
    {
        self::assertStringContainsString('<svg ', $this->collection->getIcon());
    }
}
