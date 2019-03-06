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

	public function testSimpleWhere()
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
}
