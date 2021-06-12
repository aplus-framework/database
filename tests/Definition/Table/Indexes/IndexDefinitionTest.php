<?php namespace Tests\Database\Definition\Table\Indexes;

use Framework\Database\Definition\Table\Indexes\IndexDefinition;
use Framework\Database\Definition\Table\Indexes\Keys\ForeignKey;
use Framework\Database\Definition\Table\Indexes\Keys\FulltextKey;
use Framework\Database\Definition\Table\Indexes\Keys\Key;
use Framework\Database\Definition\Table\Indexes\Keys\PrimaryKey;
use Framework\Database\Definition\Table\Indexes\Keys\SpatialKey;
use Framework\Database\Definition\Table\Indexes\Keys\UniqueKey;
use Tests\Database\TestCase;

class IndexDefinitionTest extends TestCase
{
	protected IndexDefinition $definition;

	protected function setUp() : void
	{
		$this->definition = new IndexDefinition(static::$database);
	}

	public function testInstances() : void
	{
		$this->assertInstanceOf(Key::class, $this->definition->key('id'));
		$this->assertInstanceOf(PrimaryKey::class, $this->definition->primaryKey('id'));
		$this->assertInstanceOf(UniqueKey::class, $this->definition->uniqueKey('id'));
		$this->assertInstanceOf(FulltextKey::class, $this->definition->fulltextKey('id'));
		$this->assertInstanceOf(ForeignKey::class, $this->definition->foreignKey('id'));
		$this->assertInstanceOf(SpatialKey::class, $this->definition->spatialKey('id'));
	}

	public function testSql() : void
	{
		$this->definition->primaryKey('id');
		$this->definition->uniqueKey('email');
		$this->assertEquals(
			' UNIQUE KEY (`email`)',
			$this->definition->sql()
		);
	}

	public function testBadMethod() : void
	{
		$this->expectException(\BadMethodCallException::class);
		$this->expectExceptionMessage('Method not found or not allowed: foo');
		$this->definition->foo();
	}

	public function testEmptyKeyType() : void
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Key type not set in index');
		$this->definition->sql();
	}
}
