<?php namespace Tests\Database\Manipulation\Statments\Traits;

use Framework\Database\Manipulation\Manipulation;
use PHPUnit\Framework\TestCase;

class HavingTest extends TestCase
{
	/**
	 * @var HavingMock
	 */
	protected $statement;

	public function setup()
	{
		$this->statement = new HavingMock();
	}

	public function testHaving()
	{
		$this->assertNull($this->statement->render());
		$this->statement->having('id', '=', 10);
		$this->assertEquals(' HAVING `id` = 10', $this->statement->render());
		$this->statement->having('name', '=', "'foo");
		$this->assertEquals(" HAVING `id` = 10 AND `name` = '\\'foo'", $this->statement->render());
		$this->statement->orHaving('created_at', '>', function () {
			return 'NOW() - 60';
		});
		$this->assertEquals(
			" HAVING `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60)",
			$this->statement->render()
		);
		$this->statement->having(function (Manipulation $manipulation) {
			return $manipulation->database->protectIdentifier('random_table');
		}, '!=', 'bar');
		$this->assertEquals(
			" HAVING `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60) AND (`random_table`) != 'bar'",
			$this->statement->render()
		);
	}

	public function testEqual()
	{
		$this->statement->havingEqual('email', 'user@mail.com');
		$this->assertEquals(" HAVING `email` = 'user@mail.com'", $this->statement->render());
		$this->statement->orHavingEqual('name', 'foo');
		$this->assertEquals(
			" HAVING `email` = 'user@mail.com' OR `name` = 'foo'",
			$this->statement->render()
		);
		$this->statement->havingEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` = 'user@mail.com' OR `name` = 'foo' AND (id) = (10)",
			$this->statement->render()
		);
	}

	public function testNotEqual()
	{
		$this->statement->havingNotEqual('email', 'user@mail.com');
		$this->assertEquals(" HAVING `email` != 'user@mail.com'", $this->statement->render());
		$this->statement->orHavingNotEqual('name', 'foo');
		$this->assertEquals(
			" HAVING `email` != 'user@mail.com' OR `name` != 'foo'",
			$this->statement->render()
		);
		$this->statement->havingNotEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` != 'user@mail.com' OR `name` != 'foo' AND (id) != (10)",
			$this->statement->render()
		);
	}

	public function testNullSafeEqual()
	{
		$this->statement->havingNullSafeEqual('email', 'user@mail.com');
		$this->assertEquals(" HAVING `email` <=> 'user@mail.com'", $this->statement->render());
		$this->statement->orHavingNullSafeEqual('name', null);
		$this->assertEquals(
			" HAVING `email` <=> 'user@mail.com' OR `name` <=> NULL",
			$this->statement->render()
		);
		$this->statement->havingNullSafeEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` <=> 'user@mail.com' OR `name` <=> NULL AND (id) <=> (10)",
			$this->statement->render()
		);
	}

	public function testLessThan()
	{
		$this->statement->havingLessThan('count', 5);
		$this->assertEquals(' HAVING `count` < 5', $this->statement->render());
		$this->statement->orHavingLessThan('name', 'foo');
		$this->assertEquals(
			" HAVING `count` < 5 OR `name` < 'foo'",
			$this->statement->render()
		);
		$this->statement->havingLessThan(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `count` < 5 OR `name` < 'foo' AND (id) < (10)",
			$this->statement->render()
		);
	}

	public function testLessThanOrEqual()
	{
		$this->statement->havingLessThanOrEqual('count', 5);
		$this->assertEquals(' HAVING `count` <= 5', $this->statement->render());
		$this->statement->orHavingLessThanOrEqual('name', 'foo');
		$this->assertEquals(
			" HAVING `count` <= 5 OR `name` <= 'foo'",
			$this->statement->render()
		);
		$this->statement->havingLessThanOrEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `count` <= 5 OR `name` <= 'foo' AND (id) <= (10)",
			$this->statement->render()
		);
	}

	public function testGreaterThan()
	{
		$this->statement->havingGreaterThan('count', 5);
		$this->assertEquals(' HAVING `count` > 5', $this->statement->render());
		$this->statement->orHavingGreaterThan('name', 'foo');
		$this->assertEquals(
			" HAVING `count` > 5 OR `name` > 'foo'",
			$this->statement->render()
		);
		$this->statement->havingGreaterThan(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `count` > 5 OR `name` > 'foo' AND (id) > (10)",
			$this->statement->render()
		);
	}

	public function testGreaterThanOrEqual()
	{
		$this->statement->havingGreaterThanOrEqual('count', 5);
		$this->assertEquals(' HAVING `count` >= 5', $this->statement->render());
		$this->statement->orHavingGreaterThanOrEqual('name', 'foo');
		$this->assertEquals(
			" HAVING `count` >= 5 OR `name` >= 'foo'",
			$this->statement->render()
		);
		$this->statement->havingGreaterThanOrEqual(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `count` >= 5 OR `name` >= 'foo' AND (id) >= (10)",
			$this->statement->render()
		);
	}

	public function testLike()
	{
		$this->statement->havingLike('email', '%@mail.com');
		$this->assertEquals(" HAVING `email` LIKE '%@mail.com'", $this->statement->render());
		$this->statement->orHavingLike('name', 'foo%');
		$this->assertEquals(
			" HAVING `email` LIKE '%@mail.com' OR `name` LIKE 'foo%'",
			$this->statement->render()
		);
		$this->statement->havingLike(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` LIKE '%@mail.com' OR `name` LIKE 'foo%' AND (id) LIKE (10)",
			$this->statement->render()
		);
	}

	public function testNotLike()
	{
		$this->statement->havingNotLike('email', '%@mail.com');
		$this->assertEquals(" HAVING `email` NOT LIKE '%@mail.com'", $this->statement->render());
		$this->statement->orHavingNotLike('name', 'foo%');
		$this->assertEquals(
			" HAVING `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%'",
			$this->statement->render()
		);
		$this->statement->havingNotLike(function () {
			return 'id';
		}, function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%' AND (id) NOT LIKE (10)",
			$this->statement->render()
		);
	}

	public function testIn()
	{
		$this->statement->havingIn('id', 1, 2, 8);
		$this->assertEquals(' HAVING `id` IN (1, 2, 8)', $this->statement->render());
		$this->statement->orHavingIn('code', 'abc', 'def');
		$this->assertEquals(
			" HAVING `id` IN (1, 2, 8) OR `code` IN ('abc', 'def')",
			$this->statement->render()
		);
		$this->statement->havingIn(function () {
			return 'id';
		}, function () {
			return 'SELECT * FROM foo';
		});
		$this->assertEquals(
			" HAVING `id` IN (1, 2, 8) OR `code` IN ('abc', 'def') AND (id) IN ((SELECT * FROM foo))",
			$this->statement->render()
		);
	}

	public function testNotIn()
	{
		$this->statement->havingNotIn('id', 1, 2, 8);
		$this->assertEquals(' HAVING `id` NOT IN (1, 2, 8)', $this->statement->render());
		$this->statement->orHavingNotIn('code', 'abc', 'def');
		$this->assertEquals(
			" HAVING `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def')",
			$this->statement->render()
		);
		$this->statement->havingNotIn(function () {
			return 'id';
		}, function () {
			return 'SELECT * FROM foo';
		});
		$this->assertEquals(
			" HAVING `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def') AND (id) NOT IN ((SELECT * FROM foo))",
			$this->statement->render()
		);
	}

	public function testBetween()
	{
		$this->statement->havingBetween('id', 1, 10);
		$this->assertEquals(' HAVING `id` BETWEEN 1 AND 10', $this->statement->render());
		$this->statement->orHavingBetween('code', 'abc', 'def');
		$this->assertEquals(
			" HAVING `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def'",
			$this->statement->render()
		);
		$this->statement->havingBetween(function () {
			return 'id';
		}, function () {
			return 'SELECT * FROM foo';
		}, function () {
			return 'SELECT * FROM bar';
		});
		$this->assertEquals(
			" HAVING `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def' AND (id) BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
			$this->statement->render()
		);
	}

	public function testNotBetween()
	{
		$this->statement->havingNotBetween('id', 1, 10);
		$this->assertEquals(' HAVING `id` NOT BETWEEN 1 AND 10', $this->statement->render());
		$this->statement->orHavingNotBetween('code', 'abc', 'def');
		$this->assertEquals(
			" HAVING `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def'",
			$this->statement->render()
		);
		$this->statement->havingNotBetween(function () {
			return 'id';
		}, function () {
			return 'SELECT * FROM foo';
		}, function () {
			return 'SELECT * FROM bar';
		});
		$this->assertEquals(
			" HAVING `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def' AND (id) NOT BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
			$this->statement->render()
		);
	}

	public function testIsNull()
	{
		$this->statement->havingIsNull('email');
		$this->assertEquals(' HAVING `email` IS NULL', $this->statement->render());
		$this->statement->orHavingIsNull('name');
		$this->assertEquals(
			' HAVING `email` IS NULL OR `name` IS NULL',
			$this->statement->render()
		);
		$this->statement->havingIsNull(function () {
			return 'id';
		});
		$this->assertEquals(
			' HAVING `email` IS NULL OR `name` IS NULL AND (id) IS NULL',
			$this->statement->render()
		);
	}

	public function testIsNotNull()
	{
		$this->statement->havingIsNotNull('email');
		$this->assertEquals(' HAVING `email` IS NOT NULL', $this->statement->render());
		$this->statement->orHavingIsNotNull('name');
		$this->assertEquals(
			' HAVING `email` IS NOT NULL OR `name` IS NOT NULL',
			$this->statement->render()
		);
		$this->statement->havingIsNotNull(function () {
			return 'id';
		});
		$this->assertEquals(
			' HAVING `email` IS NOT NULL OR `name` IS NOT NULL AND (id) IS NOT NULL',
			$this->statement->render()
		);
	}
}
