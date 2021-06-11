<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\DropTable;
use Tests\Database\TestCase;

class DropTableTest extends TestCase
{
	protected DropTable $dropTable;

	protected function setUp() : void
	{
		$this->dropTable = new DropTable(static::$database);
	}

	public function testEmptyTable() : void
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Table names can not be empty');
		$this->dropTable->sql();
	}

	public function testSchema() : void
	{
		$this->assertSame(
			"DROP TABLE `t1`\n",
			$this->dropTable->table('t1')->sql()
		);
	}

	public function testIfExists() : void
	{
		$this->assertSame(
			"DROP TABLE IF EXISTS `t1`\n",
			$this->dropTable->ifExists()->table('t1')->sql()
		);
	}

	public function testWait() : void
	{
		$this->assertSame(
			"DROP TABLE `t1`\n WAIT 10\n",
			$this->dropTable->table('t1')->wait(10)->sql()
		);
	}

	public function testInvalidWait() : void
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Invalid WAIT value: -1');
		$this->dropTable->table('t1')->wait(-1)->sql();
	}

	public function testCommentToSave() : void
	{
		$this->assertSame(
			"DROP TABLE /* Oops! * /; */ `t1`\n",
			$this->dropTable->table('t1')->commentToSave('Oops! */;')->sql()
		);
	}

	public function testTemporary() : void
	{
		$this->assertSame(
			"DROP TEMPORARY TABLE `t1`\n",
			$this->dropTable->table('t1')->temporary()->sql()
		);
	}

	public function testRun() : void
	{
		$statement = $this->dropTable->table('t1');
		$this->createDummyData();
		$this->assertSame(0, $statement->run());
		$this->resetDatabase();
		$this->expectException(\mysqli_sql_exception::class);
		$schema = \getenv('DB_SCHEMA');
		$this->expectExceptionMessage("Unknown table '{$schema}.t1'");
		$statement->run();
	}
}
