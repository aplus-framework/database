<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\AlterTable;
use Framework\Database\Definition\Table\TableDefinition;
use Tests\Database\TestCase;

class AlterTableTest extends TestCase
{
	/**
	 * @var AlterTable
	 */
	protected $alterTable;

	protected function setUp()
	{
		$this->alterTable = new AlterTable(static::$database);
	}

	protected function prepare()
	{
		return $this->alterTable->table('t1')
			->add(function (TableDefinition $definition) {
				$definition->column('c1')->int();
			});
	}

	public function testEmptyTable()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('TABLE name must be set');
		$this->alterTable->sql();
	}

	public function testAdd()
	{
		$sql = $this->alterTable->table('t1')
			->add(function (TableDefinition $definition) {
				$definition->column('c1')->int();
				$definition->index()->primaryKey('c1');
				$definition->index('Foo')->uniqueKey('c2');
			});
		$this->assertEquals(
			"ALTER TABLE `t1`\n ADD COLUMN `c1` int,\n ADD PRIMARY KEY (`c1`),\n ADD UNIQUE KEY `Foo` (`c2`)",
			$sql->sql()
		);
	}

	public function testChange()
	{
		$sql = $this->alterTable->table('t1')
			->change(function (TableDefinition $definition) {
				$definition->column('c1', 'c5')->bigint();
			});
		$this->assertEquals(
			"ALTER TABLE `t1`\n CHANGE COLUMN `c1` `c5` bigint",
			$sql->sql()
		);
	}

	public function testModify()
	{
		$sql = $this->alterTable->table('t1')
			->modify(function (TableDefinition $definition) {
				$definition->column('c1')->smallint()->notNull();
			});
		$this->assertEquals(
			"ALTER TABLE `t1`\n MODIFY COLUMN `c1` smallint NOT NULL",
			$sql->sql()
		);
	}

	public function testWait()
	{
		$this->assertEquals(
			"ALTER TABLE `t1`\n WAIT 10\n ADD COLUMN `c1` int",
			$this->prepare()->wait(10)->sql()
		);
	}

	public function testInvalidWait()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Invalid WAIT value: -1');
		$this->prepare()->wait(-1)->sql();
	}

	public function testOnline()
	{
		$this->assertEquals(
			"ALTER ONLINE TABLE `t1`\n ADD COLUMN `c1` int",
			$this->prepare()->online()->sql()
		);
	}

	public function testIgnore()
	{
		$this->assertEquals(
			"ALTER IGNORE TABLE `t1`\n ADD COLUMN `c1` int",
			$this->prepare()->ignore()->sql()
		);
	}

	public function testRun()
	{
		$this->createDummyData();
		$statement = $this->alterTable->table('t1')
			->add(function (TableDefinition $definition) {
				$definition->column('c3')->varchar(100)->default('Foo Bar');
			});
		$this->assertEquals(0, $statement->run());
		static::$database->exec('INSERT INTO `t1` SET `c1` = 123, `c2` = "z"');
		$this->assertEquals(
			'Foo Bar',
			static::$database->query('SELECT * FROM `t1` WHERE `c1` = 123')->fetch()->c3
		);
	}
}
