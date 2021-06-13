<?php namespace Tests\Database\Manipulation\Traits;

use Tests\Database\TestCase;

final class OrderByTest extends TestCase
{
	protected OrderByMock $statement;

	public function setup() : void
	{
		$this->statement = new OrderByMock(static::$database);
	}

	public function testOrderBy() : void
	{
		self::assertNull($this->statement->renderOrderBy());
		$this->statement->orderBy('c1');
		self::assertSame(' ORDER BY `c1`', $this->statement->renderOrderBy());
		$this->statement->orderBy(static function () {
			return 'select c2';
		});
		self::assertSame(' ORDER BY `c1`, (select c2)', $this->statement->renderOrderBy());
		$this->statement->orderBy(static function () {
			return 'select c3';
		}, 'c4');
		self::assertSame(
			' ORDER BY `c1`, (select c2), (select c3), `c4`',
			$this->statement->renderOrderBy()
		);
	}

	public function testOrderByAsc() : void
	{
		$this->statement->orderByAsc('c1');
		self::assertSame(' ORDER BY `c1` ASC', $this->statement->renderOrderBy());
		$this->statement->orderByAsc('c2', 'c3');
		self::assertSame(
			' ORDER BY `c1` ASC, `c2` ASC, `c3` ASC',
			$this->statement->renderOrderBy()
		);
	}

	public function testOrderByDesc() : void
	{
		$this->statement->orderByDesc('c1');
		self::assertSame(' ORDER BY `c1` DESC', $this->statement->renderOrderBy());
		$this->statement->orderByDesc('c2', 'c3');
		self::assertSame(
			' ORDER BY `c1` DESC, `c2` DESC, `c3` DESC',
			$this->statement->renderOrderBy()
		);
	}

	public function testOrderByMixed() : void
	{
		$this->statement->orderBy('c1');
		$this->statement->orderByAsc('c2');
		$this->statement->orderByDesc('c3');
		$this->statement->orderBy('a', 'b');
		$this->statement->orderByAsc('c', 'D');
		$this->statement->orderByDesc('e', static function () {
			return 'select "f"';
		});
		self::assertSame(
			' ORDER BY `c1`, `c2` ASC, `c3` DESC, `a`, `b`, `c` ASC, `D` ASC, `e` DESC, (select "f") DESC',
			$this->statement->renderOrderBy()
		);
	}

	public function testInvalidExpressionDataType() : void
	{
		$this->expectException(\TypeError::class);
		$this->statement->orderBy([]); // @phpstan-ignore-line
	}
}
