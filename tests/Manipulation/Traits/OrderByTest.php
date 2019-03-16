<?php namespace Tests\Database\Manipulation\Traits;

use Tests\Database\TestCase;

class OrderByTest extends TestCase
{
	/**
	 * @var OrderByMock
	 */
	protected $statement;

	public function setup()
	{
		$this->statement = new OrderByMock(static::$database);
	}

	public function testOrderBy()
	{
		$this->assertNull($this->statement->renderOrderBy());
		$this->statement->orderBy('c1');
		$this->assertEquals(' ORDER BY `c1`', $this->statement->renderOrderBy());
		$this->statement->orderBy(function () {
			return 'select c2';
		});
		$this->assertEquals(' ORDER BY `c1`, (select c2)', $this->statement->renderOrderBy());
		$this->statement->orderBy(function () {
			return 'select c3';
		}, 'c4');
		$this->assertEquals(
			' ORDER BY `c1`, (select c2), (select c3), `c4`',
			$this->statement->renderOrderBy()
		);
	}

	public function testOrderByAsc()
	{
		$this->statement->orderByAsc('c1');
		$this->assertEquals(' ORDER BY `c1` ASC', $this->statement->renderOrderBy());
		$this->statement->orderByAsc('c2', 'c3');
		$this->assertEquals(
			' ORDER BY `c1` ASC, `c2` ASC, `c3` ASC',
			$this->statement->renderOrderBy()
		);
	}

	public function testOrderByDesc()
	{
		$this->statement->orderByDesc('c1');
		$this->assertEquals(' ORDER BY `c1` DESC', $this->statement->renderOrderBy());
		$this->statement->orderByDesc('c2', 'c3');
		$this->assertEquals(
			' ORDER BY `c1` DESC, `c2` DESC, `c3` DESC',
			$this->statement->renderOrderBy()
		);
	}

	public function testOrderByMixed()
	{
		$this->statement->orderBy('c1');
		$this->statement->orderByAsc('c2');
		$this->statement->orderByDesc('c3');
		$this->statement->orderBy('a', 'b');
		$this->statement->orderByAsc('c', 'D');
		$this->statement->orderByDesc('e', function () {
			return 'select "f"';
		});
		$this->assertEquals(
			' ORDER BY `c1`, `c2` ASC, `c3` DESC, `a`, `b`, `c` ASC, `D` ASC, `e` DESC, (select "f") DESC',
			$this->statement->renderOrderBy()
		);
	}

	public function testInvalidExpressionDataType()
	{
		$this->statement->orderBy([]);
		$this->expectException(\TypeError::class);
		$this->statement->renderOrderBy();
	}
}
