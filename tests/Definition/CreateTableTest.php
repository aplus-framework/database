<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\CreateTable;
use Framework\Database\Definition\Table\TableDefinition;
use Tests\Database\TestCase;

final class CreateTableTest extends TestCase
{
	protected CreateTable $createTable;

	protected function setUp() : void
	{
		$this->createTable = new CreateTable(static::$database);
	}

	protected function prepare()
	{
		return $this->createTable->table('t1')
			->definition(static function (TableDefinition $definition) : void {
				$definition->column('c1')->int();
			});
	}

	public function testEmptyTable() : void
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('TABLE name must be set');
		$this->createTable->sql();
	}

	public function testEmptyColumns() : void
	{
		$this->createTable->table('t1');
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Table definition must be set');
		$this->createTable->sql();
	}

	public function testColumns() : void
	{
		$sql = $this->createTable->table('t1')
			->definition(static function (TableDefinition $definition) : void {
				$definition->column('c1')->int(11);
				$definition->column('c2')->varchar(255);
			});
		$this->assertEquals(
			"CREATE TABLE `t1` (\n  `c1` int(11) NOT NULL,\n  `c2` varchar(255) NOT NULL\n)",
			$sql->sql()
		);
	}

	public function testIndexes() : void
	{
		$sql = $this->createTable->table('t1')
			->definition(static function (TableDefinition $definition) : void {
				$definition->column('c1')->int();
				$definition->index()->primaryKey('c1');
			});
		$this->assertEquals(
			"CREATE TABLE `t1` (\n  `c1` int NOT NULL,\n  PRIMARY KEY (`c1`)\n)",
			$sql->sql()
		);
	}

	public function testIfNotExists() : void
	{
		$this->assertEquals(
			"CREATE TABLE IF NOT EXISTS `t1` (\n  `c1` int NOT NULL\n)",
			$this->prepare()->ifNotExists()->sql()
		);
	}

	public function testOrReplace() : void
	{
		$this->assertEquals(
			"CREATE OR REPLACE TABLE `t1` (\n  `c1` int NOT NULL\n)",
			$this->prepare()->orReplace()->sql()
		);
	}

	public function testIfNotExistsConflictsWithOrReplace() : void
	{
		$this->prepare()->orReplace()->ifNotExists();
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'Clauses OR REPLACE and IF NOT EXISTS can not be used together'
		);
		$this->createTable->sql();
	}

	public function testTemporary() : void
	{
		$this->assertEquals(
			"CREATE TEMPORARY TABLE `t1` (\n  `c1` int NOT NULL\n)",
			$this->prepare()->temporary()->sql()
		);
	}

	public function testRun() : void
	{
		$this->dropDummyData();
		$statement = $this->createTable->table('t1')
			->definition(static function (TableDefinition $definition) : void {
				$definition->column('c1')->int(11);
			});
		$this->assertEquals(0, $statement->run());
		$this->resetDatabase();
		$this->expectException(\mysqli_sql_exception::class);
		$this->expectExceptionMessage("Table 't1' already exists");
		$statement->run();
	}
}
