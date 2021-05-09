<?php namespace Tests\Database\Manipulation\Traits;

use Framework\Database\Database;
use Tests\Database\TestCase;

class HavingTest extends TestCase
{
	/**
	 * @var HavingMock
	 */
	protected $statement;

	public function setup() : void
	{
		$this->statement = new HavingMock(static::$database);
	}

	public function testHaving()
	{
		$this->assertNull($this->statement->renderHaving());
		$this->statement->having('id', '=', 10);
		$this->assertEquals(' HAVING `id` = 10', $this->statement->renderHaving());
		$this->statement->having('name', '=', "'foo");
		$this->assertEquals(
			" HAVING `id` = 10 AND `name` = '\\'foo'",
			$this->statement->renderHaving()
		);
		$this->statement->orHaving('created_at', '>', static function () {
			return 'NOW() - 60';
		});
		$this->assertEquals(
			" HAVING `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60)",
			$this->statement->renderHaving()
		);
		$this->statement->having(static function (Database $database) {
			return $database->protectIdentifier('random_table');
		}, '!=', 'bar');
		$this->assertEquals(
			" HAVING `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60) AND (`random_table`) != 'bar'",
			$this->statement->renderHaving()
		);
	}

	public function testEqual()
	{
		$this->statement->havingEqual('email', 'user@mail.com');
		$this->assertEquals(" HAVING `email` = 'user@mail.com'", $this->statement->renderHaving());
		$this->statement->orHavingEqual('name', 'foo');
		$this->assertEquals(
			" HAVING `email` = 'user@mail.com' OR `name` = 'foo'",
			$this->statement->renderHaving()
		);
		$this->statement->havingEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` = 'user@mail.com' OR `name` = 'foo' AND (id) = (10)",
			$this->statement->renderHaving()
		);
	}

	public function testNotEqual()
	{
		$this->statement->havingNotEqual('email', 'user@mail.com');
		$this->assertEquals(" HAVING `email` != 'user@mail.com'", $this->statement->renderHaving());
		$this->statement->orHavingNotEqual('name', 'foo');
		$this->assertEquals(
			" HAVING `email` != 'user@mail.com' OR `name` != 'foo'",
			$this->statement->renderHaving()
		);
		$this->statement->havingNotEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` != 'user@mail.com' OR `name` != 'foo' AND (id) != (10)",
			$this->statement->renderHaving()
		);
	}

	public function testNullSafeEqual()
	{
		$this->statement->havingNullSafeEqual('email', 'user@mail.com');
		$this->assertEquals(
			" HAVING `email` <=> 'user@mail.com'",
			$this->statement->renderHaving()
		);
		$this->statement->orHavingNullSafeEqual('name', null);
		$this->assertEquals(
			" HAVING `email` <=> 'user@mail.com' OR `name` <=> NULL",
			$this->statement->renderHaving()
		);
		$this->statement->havingNullSafeEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` <=> 'user@mail.com' OR `name` <=> NULL AND (id) <=> (10)",
			$this->statement->renderHaving()
		);
	}

	public function testLessThan()
	{
		$this->statement->havingLessThan('count', 5);
		$this->assertEquals(' HAVING `count` < 5', $this->statement->renderHaving());
		$this->statement->orHavingLessThan('name', 'foo');
		$this->assertEquals(
			" HAVING `count` < 5 OR `name` < 'foo'",
			$this->statement->renderHaving()
		);
		$this->statement->havingLessThan(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `count` < 5 OR `name` < 'foo' AND (id) < (10)",
			$this->statement->renderHaving()
		);
	}

	public function testLessThanOrEqual()
	{
		$this->statement->havingLessThanOrEqual('count', 5);
		$this->assertEquals(' HAVING `count` <= 5', $this->statement->renderHaving());
		$this->statement->orHavingLessThanOrEqual('name', 'foo');
		$this->assertEquals(
			" HAVING `count` <= 5 OR `name` <= 'foo'",
			$this->statement->renderHaving()
		);
		$this->statement->havingLessThanOrEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `count` <= 5 OR `name` <= 'foo' AND (id) <= (10)",
			$this->statement->renderHaving()
		);
	}

	public function testGreaterThan()
	{
		$this->statement->havingGreaterThan('count', 5);
		$this->assertEquals(' HAVING `count` > 5', $this->statement->renderHaving());
		$this->statement->orHavingGreaterThan('name', 'foo');
		$this->assertEquals(
			" HAVING `count` > 5 OR `name` > 'foo'",
			$this->statement->renderHaving()
		);
		$this->statement->havingGreaterThan(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `count` > 5 OR `name` > 'foo' AND (id) > (10)",
			$this->statement->renderHaving()
		);
	}

	public function testGreaterThanOrEqual()
	{
		$this->statement->havingGreaterThanOrEqual('count', 5);
		$this->assertEquals(' HAVING `count` >= 5', $this->statement->renderHaving());
		$this->statement->orHavingGreaterThanOrEqual('name', 'foo');
		$this->assertEquals(
			" HAVING `count` >= 5 OR `name` >= 'foo'",
			$this->statement->renderHaving()
		);
		$this->statement->havingGreaterThanOrEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `count` >= 5 OR `name` >= 'foo' AND (id) >= (10)",
			$this->statement->renderHaving()
		);
	}

	public function testLike()
	{
		$this->statement->havingLike('email', '%@mail.com');
		$this->assertEquals(" HAVING `email` LIKE '%@mail.com'", $this->statement->renderHaving());
		$this->statement->orHavingLike('name', 'foo%');
		$this->assertEquals(
			" HAVING `email` LIKE '%@mail.com' OR `name` LIKE 'foo%'",
			$this->statement->renderHaving()
		);
		$this->statement->havingLike(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` LIKE '%@mail.com' OR `name` LIKE 'foo%' AND (id) LIKE (10)",
			$this->statement->renderHaving()
		);
	}

	public function testNotLike()
	{
		$this->statement->havingNotLike('email', '%@mail.com');
		$this->assertEquals(
			" HAVING `email` NOT LIKE '%@mail.com'",
			$this->statement->renderHaving()
		);
		$this->statement->orHavingNotLike('name', 'foo%');
		$this->assertEquals(
			" HAVING `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%'",
			$this->statement->renderHaving()
		);
		$this->statement->havingNotLike(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		$this->assertEquals(
			" HAVING `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%' AND (id) NOT LIKE (10)",
			$this->statement->renderHaving()
		);
	}

	public function testIn()
	{
		$this->statement->havingIn('id', 1, 2, 8);
		$this->assertEquals(' HAVING `id` IN (1, 2, 8)', $this->statement->renderHaving());
		$this->statement->orHavingIn('code', 'abc', 'def');
		$this->assertEquals(
			" HAVING `id` IN (1, 2, 8) OR `code` IN ('abc', 'def')",
			$this->statement->renderHaving()
		);
		$this->statement->havingIn(static function () {
			return 'id';
		}, static function () {
			return 'SELECT * FROM foo';
		});
		$this->assertEquals(
			" HAVING `id` IN (1, 2, 8) OR `code` IN ('abc', 'def') AND (id) IN ((SELECT * FROM foo))",
			$this->statement->renderHaving()
		);
	}

	public function testNotIn()
	{
		$this->statement->havingNotIn('id', 1, 2, 8);
		$this->assertEquals(' HAVING `id` NOT IN (1, 2, 8)', $this->statement->renderHaving());
		$this->statement->orHavingNotIn('code', 'abc', 'def');
		$this->assertEquals(
			" HAVING `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def')",
			$this->statement->renderHaving()
		);
		$this->statement->havingNotIn(static function () {
			return 'id';
		}, static function () {
			return 'SELECT * FROM foo';
		});
		$this->assertEquals(
			" HAVING `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def') AND (id) NOT IN ((SELECT * FROM foo))",
			$this->statement->renderHaving()
		);
	}

	public function testBetween()
	{
		$this->statement->havingBetween('id', 1, 10);
		$this->assertEquals(' HAVING `id` BETWEEN 1 AND 10', $this->statement->renderHaving());
		$this->statement->orHavingBetween('code', 'abc', 'def');
		$this->assertEquals(
			" HAVING `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def'",
			$this->statement->renderHaving()
		);
		$this->statement->havingBetween(static function () {
			return 'id';
		}, static function () {
			return 'SELECT * FROM foo';
		}, static function () {
			return 'SELECT * FROM bar';
		});
		$this->assertEquals(
			" HAVING `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def' AND (id) BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
			$this->statement->renderHaving()
		);
	}

	public function testNotBetween()
	{
		$this->statement->havingNotBetween('id', 1, 10);
		$this->assertEquals(' HAVING `id` NOT BETWEEN 1 AND 10', $this->statement->renderHaving());
		$this->statement->orHavingNotBetween('code', 'abc', 'def');
		$this->assertEquals(
			" HAVING `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def'",
			$this->statement->renderHaving()
		);
		$this->statement->havingNotBetween(static function () {
			return 'id';
		}, static function () {
			return 'SELECT * FROM foo';
		}, static function () {
			return 'SELECT * FROM bar';
		});
		$this->assertEquals(
			" HAVING `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def' AND (id) NOT BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
			$this->statement->renderHaving()
		);
	}

	public function testIsNull()
	{
		$this->statement->havingIsNull('email');
		$this->assertEquals(' HAVING `email` IS NULL', $this->statement->renderHaving());
		$this->statement->orHavingIsNull('name');
		$this->assertEquals(
			' HAVING `email` IS NULL OR `name` IS NULL',
			$this->statement->renderHaving()
		);
		$this->statement->havingIsNull(static function () {
			return 'id';
		});
		$this->assertEquals(
			' HAVING `email` IS NULL OR `name` IS NULL AND (id) IS NULL',
			$this->statement->renderHaving()
		);
	}

	public function testIsNotNull()
	{
		$this->statement->havingIsNotNull('email');
		$this->assertEquals(' HAVING `email` IS NOT NULL', $this->statement->renderHaving());
		$this->statement->orHavingIsNotNull('name');
		$this->assertEquals(
			' HAVING `email` IS NOT NULL OR `name` IS NOT NULL',
			$this->statement->renderHaving()
		);
		$this->statement->havingIsNotNull(static function () {
			return 'id';
		});
		$this->assertEquals(
			' HAVING `email` IS NOT NULL OR `name` IS NOT NULL AND (id) IS NOT NULL',
			$this->statement->renderHaving()
		);
	}
}
