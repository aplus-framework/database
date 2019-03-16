<?php namespace Tests\Database\Definition\Indexes;

use Framework\Database\Definition\Indexes\IndexDefinition;
use Framework\Database\Definition\Indexes\Keys\ForeignKey;
use Framework\Database\Definition\Indexes\Keys\FulltextKey;
use Framework\Database\Definition\Indexes\Keys\Key;
use Framework\Database\Definition\Indexes\Keys\PrimaryKey;
use Framework\Database\Definition\Indexes\Keys\SpatialKey;
use Framework\Database\Definition\Indexes\Keys\UniqueKey;
use Tests\Database\TestCase;

class IndexDefinitionTest extends TestCase
{
	/**
	 * @var IndexDefinition
	 */
	protected $definition;

	protected function setUp()
	{
		$this->definition = new IndexDefinition($this->database);
	}

	public function testInstances()
	{
		$this->assertInstanceOf(Key::class, $this->definition->key('id'));
		$this->assertInstanceOf(PrimaryKey::class, $this->definition->primaryKey('id'));
		$this->assertInstanceOf(UniqueKey::class, $this->definition->uniqueKey('id'));
		$this->assertInstanceOf(FulltextKey::class, $this->definition->fulltextKey('id'));
		$this->assertInstanceOf(ForeignKey::class, $this->definition->foreignKey('id'));
		$this->assertInstanceOf(SpatialKey::class, $this->definition->spatialKey('id'));
	}

	public function testSql()
	{
		$this->assertEquals('', $this->definition->sql());
		$this->definition->primaryKey('id');
		$this->definition->uniqueKey('email');
		$this->assertEquals(
			"  PRIMARY KEY (`id`),\n  UNIQUE KEY (`email`)",
			$this->definition->sql()
		);
	}

	public function testBadMethod()
	{
		$this->expectException(\BadMethodCallException::class);
		$this->expectExceptionMessage('Method not found: foo');
		$this->definition->foo();
	}
}
