<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation\Traits;

use Framework\Database\Database;
use Tests\Database\TestCase;

final class WhereTest extends TestCase
{
	protected WhereMock $statement;

	public function setup() : void
	{
		$this->statement = new WhereMock(static::$database);
	}

	public function testWhere() : void
	{
		self::assertNull($this->statement->renderWhere());
		$this->statement->where('id', '=', 10);
		self::assertSame(' WHERE `id` = 10', $this->statement->renderWhere());
		$this->statement->where('name', '=', "'foo");
		self::assertSame(
			" WHERE `id` = 10 AND `name` = '\\'foo'",
			$this->statement->renderWhere()
		);
		$this->statement->orWhere('created_at', '>', static function () {
			return 'NOW() - 60';
		});
		self::assertSame(
			" WHERE `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60)",
			$this->statement->renderWhere()
		);
		$this->statement->where(static function (Database $database) {
			return $database->protectIdentifier('random_table');
		}, '!=', 'bar');
		self::assertSame(
			" WHERE `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60) AND (`random_table`) != 'bar'",
			$this->statement->renderWhere()
		);
	}

	public function testOperatorWithoutRequiredArgument() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator = must receive exactly 1 parameter');
		$this->statement->where('email', '=')->renderWhere();
	}

	public function testOperatorWithTooManyArguments() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator = must receive exactly 1 parameter');
		$this->statement->where('email', '=', 1, 2)->renderWhere();
	}

	public function testOperatorInWithoutRequiredArgument() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator NOT IN must receive at least 1 parameter');
		$this->statement->where('email', 'not in')->renderWhere();
	}

	public function testOperatorBetweenWithoutRequiredArguments() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator BETWEEN must receive exactly 2 parameters');
		$this->statement->where('email', 'between', 1)->renderWhere();
	}

	public function testOperatorBetweenWithTooManyArguments() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator BETWEEN must receive exactly 2 parameters');
		$this->statement->where('email', 'between', 1, 5, 15)->renderWhere();
	}

	public function testOperatorIsNullWithArguments() : void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Operator IS NULL must not receive parameters');
		$this->statement->where('email', 'is null', 1)->renderWhere();
	}

	public function testMatch() : void
	{
		$this->statement->whereMatch(['title'], 'foo');
		self::assertSame(
			" WHERE MATCH (`title`) AGAINST ('foo')",
			$this->statement->renderWhere()
		);
		$this->statement->orWhereMatch(['content', 'description'], ['bar', "ba'z"]);
		self::assertSame(
			" WHERE MATCH (`title`) AGAINST ('foo') OR MATCH (`content`, `description`) AGAINST ('bar, ba\\'z')",
			$this->statement->renderWhere()
		);
	}

	public function testMatchWithStringParams() : void
	{
		$this->statement->whereMatch('title', 'foo');
		self::assertSame(
			" WHERE MATCH (`title`) AGAINST ('foo')",
			$this->statement->renderWhere()
		);
	}

	public function testMatchWithClosureParams() : void
	{
		$this->statement->whereMatch(static function () {
			return 'title';
		}, static function () {
			return "'foo'";
		});
		self::assertSame(
			" WHERE MATCH ((title)) AGAINST (('foo'))",
			$this->statement->renderWhere()
		);
	}

	public function testMatchWithQueryExpansion() : void
	{
		$this->statement->whereMatchWithQueryExpansion(['title'], 'foo');
		self::assertSame(
			" WHERE MATCH (`title`) AGAINST ('foo' WITH QUERY EXPANSION)",
			$this->statement->renderWhere()
		);
		$this->statement->orWhereMatchWithQueryExpansion(
			['content', 'description'],
			['bar', "ba'z"]
		);
		self::assertSame(
			" WHERE MATCH (`title`) AGAINST ('foo' WITH QUERY EXPANSION) OR MATCH (`content`, `description`) AGAINST ('bar, ba\\'z' WITH QUERY EXPANSION)",
			$this->statement->renderWhere()
		);
	}

	public function testWhereMatchInBooleanMode() : void
	{
		$this->statement->whereMatchInBooleanMode(['title'], 'foo');
		self::assertSame(
			" WHERE MATCH (`title`) AGAINST ('foo' IN BOOLEAN MODE)",
			$this->statement->renderWhere()
		);
		$this->statement->orWhereMatchInBooleanMode(['content', 'description'], ['+bar', "-ba'z"]);
		self::assertSame(
			" WHERE MATCH (`title`) AGAINST ('foo' IN BOOLEAN MODE) OR MATCH (`content`, `description`) AGAINST ('+bar, -ba\\'z' IN BOOLEAN MODE)",
			$this->statement->renderWhere()
		);
	}

	public function testEqual() : void
	{
		$this->statement->whereEqual('email', 'user@mail.com');
		self::assertSame(" WHERE `email` = 'user@mail.com'", $this->statement->renderWhere());
		$this->statement->orWhereEqual('name', 'foo');
		self::assertSame(
			" WHERE `email` = 'user@mail.com' OR `name` = 'foo'",
			$this->statement->renderWhere()
		);
		$this->statement->whereEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		self::assertSame(
			" WHERE `email` = 'user@mail.com' OR `name` = 'foo' AND (id) = (10)",
			$this->statement->renderWhere()
		);
	}

	public function testNotEqual() : void
	{
		$this->statement->whereNotEqual('email', 'user@mail.com');
		self::assertSame(" WHERE `email` != 'user@mail.com'", $this->statement->renderWhere());
		$this->statement->orWhereNotEqual('name', 'foo');
		self::assertSame(
			" WHERE `email` != 'user@mail.com' OR `name` != 'foo'",
			$this->statement->renderWhere()
		);
		$this->statement->whereNotEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		self::assertSame(
			" WHERE `email` != 'user@mail.com' OR `name` != 'foo' AND (id) != (10)",
			$this->statement->renderWhere()
		);
	}

	public function testNullSafeEqual() : void
	{
		$this->statement->whereNullSafeEqual('email', 'user@mail.com');
		self::assertSame(" WHERE `email` <=> 'user@mail.com'", $this->statement->renderWhere());
		$this->statement->orWhereNullSafeEqual('name', null);
		self::assertSame(
			" WHERE `email` <=> 'user@mail.com' OR `name` <=> NULL",
			$this->statement->renderWhere()
		);
		$this->statement->whereNullSafeEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		self::assertSame(
			" WHERE `email` <=> 'user@mail.com' OR `name` <=> NULL AND (id) <=> (10)",
			$this->statement->renderWhere()
		);
	}

	public function testLessThan() : void
	{
		$this->statement->whereLessThan('count', 5);
		self::assertSame(' WHERE `count` < 5', $this->statement->renderWhere());
		$this->statement->orWhereLessThan('name', 'foo');
		self::assertSame(
			" WHERE `count` < 5 OR `name` < 'foo'",
			$this->statement->renderWhere()
		);
		$this->statement->whereLessThan(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		self::assertSame(
			" WHERE `count` < 5 OR `name` < 'foo' AND (id) < (10)",
			$this->statement->renderWhere()
		);
	}

	public function testLessThanOrEqual() : void
	{
		$this->statement->whereLessThanOrEqual('count', 5);
		self::assertSame(' WHERE `count` <= 5', $this->statement->renderWhere());
		$this->statement->orWhereLessThanOrEqual('name', 'foo');
		self::assertSame(
			" WHERE `count` <= 5 OR `name` <= 'foo'",
			$this->statement->renderWhere()
		);
		$this->statement->whereLessThanOrEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		self::assertSame(
			" WHERE `count` <= 5 OR `name` <= 'foo' AND (id) <= (10)",
			$this->statement->renderWhere()
		);
	}

	public function testGreaterThan() : void
	{
		$this->statement->whereGreaterThan('count', 5);
		self::assertSame(' WHERE `count` > 5', $this->statement->renderWhere());
		$this->statement->orWhereGreaterThan('name', 'foo');
		self::assertSame(
			" WHERE `count` > 5 OR `name` > 'foo'",
			$this->statement->renderWhere()
		);
		$this->statement->whereGreaterThan(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		self::assertSame(
			" WHERE `count` > 5 OR `name` > 'foo' AND (id) > (10)",
			$this->statement->renderWhere()
		);
	}

	public function testGreaterThanOrEqual() : void
	{
		$this->statement->whereGreaterThanOrEqual('count', 5);
		self::assertSame(' WHERE `count` >= 5', $this->statement->renderWhere());
		$this->statement->orWhereGreaterThanOrEqual('name', 'foo');
		self::assertSame(
			" WHERE `count` >= 5 OR `name` >= 'foo'",
			$this->statement->renderWhere()
		);
		$this->statement->whereGreaterThanOrEqual(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		self::assertSame(
			" WHERE `count` >= 5 OR `name` >= 'foo' AND (id) >= (10)",
			$this->statement->renderWhere()
		);
	}

	public function testLike() : void
	{
		$this->statement->whereLike('email', '%@mail.com');
		self::assertSame(" WHERE `email` LIKE '%@mail.com'", $this->statement->renderWhere());
		$this->statement->orWhereLike('name', 'foo%');
		self::assertSame(
			" WHERE `email` LIKE '%@mail.com' OR `name` LIKE 'foo%'",
			$this->statement->renderWhere()
		);
		$this->statement->whereLike(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		self::assertSame(
			" WHERE `email` LIKE '%@mail.com' OR `name` LIKE 'foo%' AND (id) LIKE (10)",
			$this->statement->renderWhere()
		);
	}

	public function testNotLike() : void
	{
		$this->statement->whereNotLike('email', '%@mail.com');
		self::assertSame(
			" WHERE `email` NOT LIKE '%@mail.com'",
			$this->statement->renderWhere()
		);
		$this->statement->orWhereNotLike('name', 'foo%');
		self::assertSame(
			" WHERE `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%'",
			$this->statement->renderWhere()
		);
		$this->statement->whereNotLike(static function () {
			return 'id';
		}, static function () {
			return 10;
		});
		self::assertSame(
			" WHERE `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%' AND (id) NOT LIKE (10)",
			$this->statement->renderWhere()
		);
	}

	public function testIn() : void
	{
		$this->statement->whereIn('id', 1, 2, 8);
		self::assertSame(' WHERE `id` IN (1, 2, 8)', $this->statement->renderWhere());
		$this->statement->orWhereIn('code', 'abc', 'def');
		self::assertSame(
			" WHERE `id` IN (1, 2, 8) OR `code` IN ('abc', 'def')",
			$this->statement->renderWhere()
		);
		$this->statement->whereIn(static function () {
			return 'id';
		}, static function () {
			return 'SELECT * FROM foo';
		});
		self::assertSame(
			" WHERE `id` IN (1, 2, 8) OR `code` IN ('abc', 'def') AND (id) IN ((SELECT * FROM foo))",
			$this->statement->renderWhere()
		);
	}

	public function testNotIn() : void
	{
		$this->statement->whereNotIn('id', 1, 2, 8);
		self::assertSame(' WHERE `id` NOT IN (1, 2, 8)', $this->statement->renderWhere());
		$this->statement->orWhereNotIn('code', 'abc', 'def');
		self::assertSame(
			" WHERE `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def')",
			$this->statement->renderWhere()
		);
		$this->statement->whereNotIn(static function () {
			return 'id';
		}, static function () {
			return 'SELECT * FROM foo';
		});
		self::assertSame(
			" WHERE `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def') AND (id) NOT IN ((SELECT * FROM foo))",
			$this->statement->renderWhere()
		);
	}

	public function testBetween() : void
	{
		$this->statement->whereBetween('id', 1, 10);
		self::assertSame(' WHERE `id` BETWEEN 1 AND 10', $this->statement->renderWhere());
		$this->statement->orWhereBetween('code', 'abc', 'def');
		self::assertSame(
			" WHERE `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def'",
			$this->statement->renderWhere()
		);
		$this->statement->whereBetween(static function () {
			return 'id';
		}, static function () {
			return 'SELECT * FROM foo';
		}, static function () {
			return 'SELECT * FROM bar';
		});
		self::assertSame(
			" WHERE `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def' AND (id) BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
			$this->statement->renderWhere()
		);
	}

	public function testNotBetween() : void
	{
		$this->statement->whereNotBetween('id', 1, 10);
		self::assertSame(' WHERE `id` NOT BETWEEN 1 AND 10', $this->statement->renderWhere());
		$this->statement->orWhereNotBetween('code', 'abc', 'def');
		self::assertSame(
			" WHERE `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def'",
			$this->statement->renderWhere()
		);
		$this->statement->whereNotBetween(static function () {
			return 'id';
		}, static function () {
			return 'SELECT * FROM foo';
		}, static function () {
			return 'SELECT * FROM bar';
		});
		self::assertSame(
			" WHERE `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def' AND (id) NOT BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
			$this->statement->renderWhere()
		);
	}

	public function testIsNull() : void
	{
		$this->statement->whereIsNull('email');
		self::assertSame(' WHERE `email` IS NULL', $this->statement->renderWhere());
		$this->statement->orWhereIsNull('name');
		self::assertSame(
			' WHERE `email` IS NULL OR `name` IS NULL',
			$this->statement->renderWhere()
		);
		$this->statement->whereIsNull(static function () {
			return 'id';
		});
		self::assertSame(
			' WHERE `email` IS NULL OR `name` IS NULL AND (id) IS NULL',
			$this->statement->renderWhere()
		);
	}

	public function testIsNotNull() : void
	{
		$this->statement->whereIsNotNull('email');
		self::assertSame(' WHERE `email` IS NOT NULL', $this->statement->renderWhere());
		$this->statement->orWhereIsNotNull('name');
		self::assertSame(
			' WHERE `email` IS NOT NULL OR `name` IS NOT NULL',
			$this->statement->renderWhere()
		);
		$this->statement->whereIsNotNull(static function () {
			return 'id';
		});
		self::assertSame(
			' WHERE `email` IS NOT NULL OR `name` IS NOT NULL AND (id) IS NOT NULL',
			$this->statement->renderWhere()
		);
	}

	public function testOperators() : void
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
		self::assertSame(
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
			$this->statement->renderWhere()
		);
		$this->statement->where('any', 'foo');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid comparison operator: foo');
		$this->statement->renderWhere();
	}

	public function testInvalidValueType() : void
	{
		$this->statement->where('id', '=', []);
		$this->expectException(\TypeError::class);
		$this->statement->renderWhere();
	}
}
