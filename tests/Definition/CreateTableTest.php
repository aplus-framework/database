<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\Columns\ColumnDefinition;
use Framework\Database\Definition\CreateTable;
use Framework\Database\Definition\Indexes\IndexDefinition;
use Tests\Database\TestCase;

class CreateTableTest extends TestCase
{
	/**
	 * @var CreateTable
	 */
	protected $createTable;

	protected function setUp()
	{
		$this->createTable = new CreateTable($this->database);
	}

	protected function prepare()
	{
		return $this->createTable->table('t1')
			->columns(function (ColumnDefinition $definition) {
				$definition->int('c1');
			});
	}

	public function testEmptyTable()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('TABLE name must be set');
		$this->createTable->sql();
	}

	public function testEmptyColumns()
	{
		$this->createTable->table('t1');
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Columns must be defined');
		$this->createTable->sql();
	}

	public function testColumns()
	{
		$sql = $this->createTable->table('t1')
			->columns(function (ColumnDefinition $definition) {
				$definition->int('c1', 11);
				$definition->varchar('c2', 255);
			});
		$this->assertEquals(
			"CREATE TABLE `t1` (\n  `c1` int(11),\n  `c2` varchar(255)\n)",
			$sql->sql()
		);
	}

	public function testIndexes()
	{
		$sql = $this->prepare()
			->indexes(function (IndexDefinition $definition) {
				$definition->primaryKey('c1');
			});
		$this->assertEquals(
			"CREATE TABLE `t1` (\n  `c1` int,\n  PRIMARY KEY (`c1`)\n)",
			$sql->sql()
		);
	}

	public function testIfNotExists()
	{
		$this->assertEquals(
			"CREATE TABLE IF NOT EXISTS `t1` (\n  `c1` int\n)",
			$this->prepare()->ifNotExists()->sql()
		);
	}

	public function testOrReplace()
	{
		$this->assertEquals(
			"CREATE OR REPLACE TABLE `t1` (\n  `c1` int\n)",
			$this->prepare()->orReplace()->sql()
		);
	}

	public function testIfNotExistsConflictsWithOrReplace()
	{
		$this->prepare()->orReplace()->ifNotExists();
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'Clauses OR REPLACE and IF NOT EXISTS can not be used together'
		);
		$this->createTable->sql();
	}

	public function testTemporary()
	{
		$this->assertEquals(
			"CREATE TEMPORARY TABLE `t1` (\n  `c1` int\n)",
			$this->prepare()->temporary()->sql()
		);
	}

	public function testRun()
	{
		$this->dropDummyData();
		$statement = $this->createTable->table('t1')
			->columns(function (ColumnDefinition $definition) {
				$definition->int('c1', 11);
			});
		$this->assertEquals(0, $statement->run());
		$this->expectException(\mysqli_sql_exception::class);
		$this->expectExceptionMessage("Table 't1' already exists");
		$statement->run();
	}
}
