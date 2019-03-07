<?php namespace Tests\Database\Manipulation\Statments\Traits;

use Framework\Database\Manipulation\Manipulation;
use PHPUnit\Framework\TestCase;

class WhereTest extends TestCase
{
	/**
	 * @var WhereMock
	 */
	protected $statement;

	public function setup()
	{
		$this->statement = new WhereMock();
	}

	public function testWhere()
	{
		$this->assertNull($this->statement->render());
		$this->statement->where('id', '=', 10);
		$this->assertEquals(' WHERE `id` = 10', $this->statement->render());
		$this->statement->where('name', '=', "'foo");
		$this->assertEquals(" WHERE `id` = 10 AND `name` = '\\'foo'", $this->statement->render());
		$this->statement->orWhere('created_at', '>', function () {
			return 'NOW() - 60';
		});
		$this->assertEquals(
			" WHERE `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60)",
			$this->statement->render()
		);
		$this->statement->where(function (Manipulation $manipulation) {
			return $manipulation->database->protectIdentifier('random_table');
		}, '!=', 'bar');
		$this->assertEquals(
			" WHERE `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60) AND (`random_table`) != 'bar'",
			$this->statement->render()
		);
	}

	public function testOperatorWithoutRequiredArgument()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator = must receive 1 parameter');
		$this->statement->where('email', '=')->render();
	}

	public function testOperatorWithTooManyArguments()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator = must receive only 1 parameter');
		$this->statement->where('email', '=', 1, 2)->render();
	}

	public function testOperatorInWithoutRequiredArgument()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator NOT IN must receive at least 1 parameter');
		$this->statement->where('email', 'not in')->render();
	}

	public function testOperatorBetweenWithoutRequiredArguments()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator BETWEEN must receive exactly 2 parameters');
		$this->statement->where('email', 'between', 1)->render();
	}

	public function testOperatorBetweenWithTooManyArguments()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator BETWEEN must receive exactly 2 parameters');
		$this->statement->where('email', 'between', 1, 5, 15)->render();
	}

	public function testOperatorIsNullWithArguments()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator IS NULL must not receive parameters');
		$this->statement->where('email', 'is null', 1)->render();
	}

	public function testEqual()
	{
		$this->statement->whereEqual('email', 'user@mail.com');
		$this->assertEquals(" WHERE `email` = 'user@mail.com'", $this->statement->render());
		$this->statement->orWhereEqual('name', 'foo');
		$this->assertEquals(
			" WHERE `email` = 'user@mail.com' OR `name` = 'foo'",
			$this->statement->render()
		);
		$this->statement->whereEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" WHERE `email` = 'user@mail.com' OR `name` = 'foo' AND (id) = (10)",
			$this->statement->render()
		);
	}

	public function testNotEqual()
	{
		$this->statement->whereNotEqual('email', 'user@mail.com');
		$this->assertEquals(" WHERE `email` != 'user@mail.com'", $this->statement->render());
		$this->statement->orWhereNotEqual('name', 'foo');
		$this->assertEquals(
			" WHERE `email` != 'user@mail.com' OR `name` != 'foo'",
			$this->statement->render()
		);
		$this->statement->whereNotEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" WHERE `email` != 'user@mail.com' OR `name` != 'foo' AND (id) != (10)",
			$this->statement->render()
		);
	}

	public function testNullSafeEqual()
	{
		$this->statement->whereNullSafeEqual('email', 'user@mail.com');
		$this->assertEquals(" WHERE `email` <=> 'user@mail.com'", $this->statement->render());
		$this->statement->orWhereNullSafeEqual('name', null);
		$this->assertEquals(
			" WHERE `email` <=> 'user@mail.com' OR `name` <=> NULL",
			$this->statement->render()
		);
		$this->statement->whereNullSafeEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" WHERE `email` <=> 'user@mail.com' OR `name` <=> NULL AND (id) <=> (10)",
			$this->statement->render()
		);
	}

	public function testLessThan()
	{
		$this->statement->whereLessThan('count', 5);
		$this->assertEquals(" WHERE `count` < 5", $this->statement->render());
		$this->statement->orWhereLessThan('name', 'foo');
		$this->assertEquals(
			" WHERE `count` < 5 OR `name` < 'foo'",
			$this->statement->render()
		);
		$this->statement->whereLessThan(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" WHERE `count` < 5 OR `name` < 'foo' AND (id) < (10)",
			$this->statement->render()
		);
	}

	public function testLessThanOrEqual()
	{
		$this->statement->whereLessThanOrEqual('count', 5);
		$this->assertEquals(" WHERE `count` <= 5", $this->statement->render());
		$this->statement->orWhereLessThanOrEqual('name', 'foo');
		$this->assertEquals(
			" WHERE `count` <= 5 OR `name` <= 'foo'",
			$this->statement->render()
		);
		$this->statement->whereLessThanOrEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" WHERE `count` <= 5 OR `name` <= 'foo' AND (id) <= (10)",
			$this->statement->render()
		);
	}

	public function testGreaterThan()
	{
		$this->statement->whereGreaterThan('count', 5);
		$this->assertEquals(" WHERE `count` > 5", $this->statement->render());
		$this->statement->orWhereGreaterThan('name', 'foo');
		$this->assertEquals(
			" WHERE `count` > 5 OR `name` > 'foo'",
			$this->statement->render()
		);
		$this->statement->whereGreaterThan(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" WHERE `count` > 5 OR `name` > 'foo' AND (id) > (10)",
			$this->statement->render()
		);
	}

	public function testGreaterThanOrEqual()
	{
		$this->statement->whereGreaterThanOrEqual('count', 5);
		$this->assertEquals(" WHERE `count` >= 5", $this->statement->render());
		$this->statement->orWhereGreaterThanOrEqual('name', 'foo');
		$this->assertEquals(
			" WHERE `count` >= 5 OR `name` >= 'foo'",
			$this->statement->render()
		);
		$this->statement->whereGreaterThanOrEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" WHERE `count` >= 5 OR `name` >= 'foo' AND (id) >= (10)",
			$this->statement->render()
		);
	}

	public function testLike()
	{
		$this->statement->whereLike('email', '%@mail.com');
		$this->assertEquals(" WHERE `email` LIKE '%@mail.com'", $this->statement->render());
		$this->statement->orWhereLike('name', 'foo%');
		$this->assertEquals(
			" WHERE `email` LIKE '%@mail.com' OR `name` LIKE 'foo%'",
			$this->statement->render()
		);
		$this->statement->whereLike(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" WHERE `email` LIKE '%@mail.com' OR `name` LIKE 'foo%' AND (id) LIKE (10)",
			$this->statement->render()
		);
	}

	public function testNotLike()
	{
		$this->statement->whereNotLike('email', '%@mail.com');
		$this->assertEquals(" WHERE `email` NOT LIKE '%@mail.com'", $this->statement->render());
		$this->statement->orWhereNotLike('name', 'foo%');
		$this->assertEquals(
			" WHERE `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%'",
			$this->statement->render()
		);
		$this->statement->whereNotLike(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" WHERE `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%' AND (id) NOT LIKE (10)",
			$this->statement->render()
		);
	}

	public function testIn()
	{
		$this->statement->whereIn('id', 1, 2, 8);
		$this->assertEquals(' WHERE `id` IN (1, 2, 8)', $this->statement->render());
		$this->statement->orWhereIn('code', 'abc', 'def');
		$this->assertEquals(
			" WHERE `id` IN (1, 2, 8) OR `code` IN ('abc', 'def')",
			$this->statement->render()
		);
		$this->statement->whereIn(function () {
			return 'id';
		}, function () {
			return 'SELECT * FROM foo';
		});
		$this->assertEquals(
			" WHERE `id` IN (1, 2, 8) OR `code` IN ('abc', 'def') AND (id) IN ((SELECT * FROM foo))",
			$this->statement->render()
		);
	}

	public function testNotIn()
	{
		$this->statement->whereNotIn('id', 1, 2, 8);
		$this->assertEquals(' WHERE `id` NOT IN (1, 2, 8)', $this->statement->render());
		$this->statement->orWhereNotIn('code', 'abc', 'def');
		$this->assertEquals(
			" WHERE `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def')",
			$this->statement->render()
		);
		$this->statement->whereNotIn(function () {
			return 'id';
		}, function () {
			return 'SELECT * FROM foo';
		});
		$this->assertEquals(
			" WHERE `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def') AND (id) NOT IN ((SELECT * FROM foo))",
			$this->statement->render()
		);
	}

	public function testBetween()
	{
		$this->statement->whereBetween('id', 1, 10);
		$this->assertEquals(' WHERE `id` BETWEEN 1 AND 10', $this->statement->render());
		$this->statement->orWhereBetween('code', 'abc', 'def');
		$this->assertEquals(
			" WHERE `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def'",
			$this->statement->render()
		);
		$this->statement->whereBetween(function () {
			return 'id';
		}, function () {
			return 'SELECT * FROM foo';
		}, function () {
			return 'SELECT * FROM bar';
		});
		$this->assertEquals(
			" WHERE `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def' AND (id) BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
			$this->statement->render()
		);
	}

	public function testNotBetween()
	{
		$this->statement->whereNotBetween('id', 1, 10);
		$this->assertEquals(' WHERE `id` NOT BETWEEN 1 AND 10', $this->statement->render());
		$this->statement->orWhereNotBetween('code', 'abc', 'def');
		$this->assertEquals(
			" WHERE `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def'",
			$this->statement->render()
		);
		$this->statement->whereNotBetween(function () {
			return 'id';
		}, function () {
			return 'SELECT * FROM foo';
		}, function () {
			return 'SELECT * FROM bar';
		});
		$this->assertEquals(
			" WHERE `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def' AND (id) NOT BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
			$this->statement->render()
		);
	}

	public function testIsNull()
	{
		$this->statement->whereIsNull('email');
		$this->assertEquals(' WHERE `email` IS NULL', $this->statement->render());
		$this->statement->orWhereIsNull('name');
		$this->assertEquals(
			' WHERE `email` IS NULL OR `name` IS NULL',
			$this->statement->render()
		);
		$this->statement->whereIsNull(function () {
			return 'id';
		});
		$this->assertEquals(
			' WHERE `email` IS NULL OR `name` IS NULL AND (id) IS NULL',
			$this->statement->render()
		);
	}

	public function testIsNotNull()
	{
		$this->statement->whereIsNotNull('email');
		$this->assertEquals(' WHERE `email` IS NOT NULL', $this->statement->render());
		$this->statement->orWhereIsNotNull('name');
		$this->assertEquals(
			' WHERE `email` IS NOT NULL OR `name` IS NOT NULL',
			$this->statement->render()
		);
		$this->statement->whereIsNotNull(function () {
			return 'id';
		});
		$this->assertEquals(
			' WHERE `email` IS NOT NULL OR `name` IS NOT NULL AND (id) IS NOT NULL',
			$this->statement->render()
		);
	}

	public function testOperators()
	{
		$this->statement
			->where('=', '=', 1)
			->orWhere('<=>', '<=>', 1)
			->where('!=', '!=', 1)
			->orWhere('<>', '<>', 1)
			->where('>', '>', 1)
			->orWhere('>=', '>=', 1)
			->where('<', '<', 1)
			->orWhere('<=', '<=', 1)
			->where('like', 'Like', 1)
			->orWhere('not like', 'Not Like', 1)
			->where('in', 'In', 1)
			->orWhere('not in', 'Not In', 1)
			->where('between', 'Between', 1, 5)
			->orWhere('not between', 'Not Between', 1, 5)
			->where('is null', 'Is Null')
			->orWhere('is not null', 'Is Not Null');
		$this->assertEquals(
			' WHERE `=` = 1'
			. ' OR `<=>` <=> 1'
			. ' AND `!=` != 1'
			. ' OR `<>` <> 1'
			. ' AND `>` > 1'
			. ' OR `>=` >= 1'
			. ' AND `<` < 1'
			. ' OR `<=` <= 1'
			. ' AND `like` LIKE 1'
			. ' OR `not like` NOT LIKE 1'
			. ' AND `in` IN (1)'
			. ' OR `not in` NOT IN (1)'
			. ' AND `between` BETWEEN 1 AND 5'
			. ' OR `not between` NOT BETWEEN 1 AND 5'
			. ' AND `is null` IS NULL'
			. ' OR `is not null` IS NOT NULL',
			$this->statement->render()
		);
		$this->statement->where('any', 'foo');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid comparison operator: foo');
		$this->statement->render();
	}
}
