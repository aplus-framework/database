<?php namespace Tests\Database\Definition\Table\Indexes;

use Tests\Database\TestCase;

final class IndexTest extends TestCase
{
	protected IndexMock $index;

	protected function setUp() : void
	{
		$this->index = new IndexMock(static::$database, null, 'id');
	}

	public function testEmptyType() : void
	{
		$this->index->type = '';
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Key type is empty');
		$this->index->sql();
	}

	public function testMultiColumns() : void
	{
		$index = new IndexMock(static::$database, null, 'id', 'email', 'foo');
		$this->assertEquals(
			' index_mock (`id`, `email`, `foo`)',
			$index->sql()
		);
	}

	public function testName() : void
	{
		$index = new IndexMock(static::$database, 'foo', 'id');
		$this->assertEquals(
			' index_mock `foo` (`id`)',
			$index->sql()
		);
	}

	public function testBadMethod() : void
	{
		$this->expectException(\BadMethodCallException::class);
		$this->expectExceptionMessage('Method not found or not allowed: foo');
		$this->index->foo();
	}
}
