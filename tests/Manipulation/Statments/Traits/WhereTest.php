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
