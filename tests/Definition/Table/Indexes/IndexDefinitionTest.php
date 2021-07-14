<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Indexes;

use Framework\Database\Definition\Table\Indexes\IndexDefinition;
use Framework\Database\Definition\Table\Indexes\Keys\ForeignKey;
use Framework\Database\Definition\Table\Indexes\Keys\FulltextKey;
use Framework\Database\Definition\Table\Indexes\Keys\Key;
use Framework\Database\Definition\Table\Indexes\Keys\PrimaryKey;
use Framework\Database\Definition\Table\Indexes\Keys\SpatialKey;
use Framework\Database\Definition\Table\Indexes\Keys\UniqueKey;
use Tests\Database\TestCase;

final class IndexDefinitionTest extends TestCase
{
    protected IndexDefinition $definition;

    protected function setUp() : void
    {
        $this->definition = new IndexDefinition(static::$database);
    }

    public function testInstances() : void
    {
        self::assertInstanceOf(Key::class, $this->definition->key('id'));
        self::assertInstanceOf(PrimaryKey::class, $this->definition->primaryKey('id'));
        self::assertInstanceOf(UniqueKey::class, $this->definition->uniqueKey('id'));
        self::assertInstanceOf(FulltextKey::class, $this->definition->fulltextKey('id'));
        self::assertInstanceOf(ForeignKey::class, $this->definition->foreignKey('id'));
        self::assertInstanceOf(SpatialKey::class, $this->definition->spatialKey('id'));
    }

    public function testSql() : void
    {
        $this->definition->primaryKey('id');
        $this->definition->uniqueKey('email');
        self::assertSame(
            ' UNIQUE KEY (`email`)',
            $this->definition->sql()
        );
    }

    public function testBadMethod() : void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method not found or not allowed: foo');
        $this->definition->foo(); // @phpstan-ignore-line
    }

    public function testEmptyKeyType() : void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Key type not set in index');
        $this->definition->sql();
    }
}
