<?php namespace Tests\Database\Manipulation\Statments\Traits;

use Framework\Database\Database;
use Framework\Database\Manipulation\Manipulation;
use Framework\Database\Manipulation\Statements\Statement;
use Framework\Database\Manipulation\Statements\Traits\Where;
use PHPUnit\Framework\TestCase;

class WhereTest extends TestCase
{
	protected $statement;

	public function setup()
	{
		$this->statement = new class() extends Statement {
			use Where;

			public function __construct()
			{
				parent::__construct(new Manipulation(new Database()));
			}

			public function render() : ?string
			{
				return $this->renderWhere();
			}

			public function sql() : string
			{
			}
		};
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

	public function testWhereLike()
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

	public function testWhereNotLike()
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
}
