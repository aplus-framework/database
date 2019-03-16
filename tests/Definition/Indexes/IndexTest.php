<?php namespace Tests\Database\Definition\Indexes;

use Tests\Database\TestCase;

class IndexTest extends TestCase
{
	/**
	 * @var IndexMock
	 */
	protected $index;

	protected function setUp()
	{
		$this->index = new IndexMock($this->database, 'id');
	}

	public function testEmptyType()
	{
		$this->index->type = null;
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Key type is empty');
		$this->index->sql();
	}

	public function testMultiColumns()
	{
		$index = new IndexMock($this->database, 'id', 'email', 'foo');
		$this->assertEquals(
			' index_mock (`id`, `email`, `foo`)',
			$index->sql()
		);
	}

	public function testName()
	{
		$this->index->name('foo');
		$this->assertEquals(
			' index_mock `foo` (`id`)',
			$this->index->sql()
		);
	}

	public function testBadMethod()
	{
		$this->expectException(\BadMethodCallException::class);
		$this->expectExceptionMessage('Method not found: foo');
		$this->index->foo();
	}
}
