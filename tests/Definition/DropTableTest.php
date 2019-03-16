<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\DropTable;
use Tests\Database\TestCase;

class DropTableTest extends TestCase
{
	/**
	 * @var DropTable
	 */
	protected $dropTable;

	protected function setUp()
	{
		$this->dropTable = new DropTable(static::$database);
	}

	public function testEmptyTable()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Table names can not be empty');
		$this->dropTable->sql();
	}

	public function testSchema()
	{
		$this->assertEquals(
			"DROP TABLE `t1`\n",
			$this->dropTable->table('t1')->sql()
		);
	}

	public function testIfExists()
	{
		$this->assertEquals(
			"DROP TABLE IF EXISTS `t1`\n",
			$this->dropTable->ifExists()->table('t1')->sql()
		);
	}

	public function testWait()
	{
		$this->assertEquals(
			"DROP TABLE `t1`\n WAIT 10\n",
			$this->dropTable->table('t1')->wait(10)->sql()
		);
	}

	public function testInvalidWait()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Invalid WAIT value: -1');
		$this->dropTable->table('t1')->wait(-1)->sql();
	}

	public function testCommentToSave()
	{
		$this->assertEquals(
			"DROP TABLE /* Oops! * /; */ `t1`\n",
			$this->dropTable->table('t1')->commentToSave('Oops! */;')->sql()
		);
	}

	public function testTemporary()
	{
		$this->assertEquals(
			"DROP TEMPORARY TABLE `t1`\n",
			$this->dropTable->table('t1')->temporary()->sql()
		);
	}

	public function testRun()
	{
		$statement = $this->dropTable->table('t1');
		$this->createDummyData();
		$this->assertEquals(0, $statement->run());
		$this->resetDatabase();
		$this->expectException(\mysqli_sql_exception::class);
		$schema = \getenv('DB_SCHEMA');
		$this->expectExceptionMessage("Unknown table '{$schema}.t1'");
		$statement->run();
	}
}
