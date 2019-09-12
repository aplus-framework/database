<?php namespace Tests\Database;

use Framework\Database\Database;
use Framework\Database\Definition\AlterSchema;
use Framework\Database\Definition\AlterTable;
use Framework\Database\Definition\CreateSchema;
use Framework\Database\Definition\CreateTable;
use Framework\Database\Definition\DropSchema;
use Framework\Database\Definition\DropTable;
use Framework\Database\Manipulation\Delete;
use Framework\Database\Manipulation\Insert;
use Framework\Database\Manipulation\LoadData;
use Framework\Database\Manipulation\Replace;
use Framework\Database\Manipulation\Select;
use Framework\Database\Manipulation\Update;
use Framework\Database\Manipulation\With;
use Framework\Database\PreparedStatement;
use Framework\Database\Result;

class DatabaseTest extends TestCase
{
	public function testConnection()
	{
		$database = new Database(
			\getenv('DB_USERNAME'),
			\getenv('DB_PASSWORD'),
			\getenv('DB_SCHEMA'),
			\getenv('DB_HOST'),
			\getenv('DB_PORT')
		);
		$this->assertInstanceOf(Database::class, $database);
	}

	public function testConnectionWithArray()
	{
		$database = new Database([
			'username' => \getenv('DB_USERNAME'),
			'password' => \getenv('DB_PASSWORD'),
			'schema' => \getenv('DB_SCHEMA'),
			'host' => \getenv('DB_HOST'),
			'port' => \getenv('DB_PORT'),
		]);
		$this->assertInstanceOf(Database::class, $database);
	}

	public function testConnectionFail()
	{
		$this->expectException(\mysqli_sql_exception::class);
		//$this->expectExceptionMessageRegExp("#^Access denied for user 'error-1'@'#");
		new Database([
			'username' => 'error-1',
			'password' => \getenv('DB_PASSWORD'),
			'schema' => \getenv('DB_SCHEMA'),
			'host' => \getenv('DB_HOST'),
			'port' => \getenv('DB_PORT'),
		]);
	}

	public function testConnectionWithFailover()
	{
		$database = new Database([
			'username' => 'error-1',
			'password' => \getenv('DB_PASSWORD'),
			'schema' => \getenv('DB_SCHEMA'),
			'host' => \getenv('DB_HOST'),
			'port' => \getenv('DB_PORT'),
			'failover' => [
				[
					'username' => 'error-3',
					'password' => 'error-2',
				],
				[
					'username' => \getenv('DB_USERNAME'),
					'password' => \getenv('DB_PASSWORD'),
				],
			],
		]);
		$this->assertInstanceOf(Database::class, $database);
	}

	public function testConnectionFailWithfailover()
	{
		$this->expectException(\mysqli_sql_exception::class);
		//$this->expectExceptionMessageRegExp("#^Access denied for user 'error-3'@'#");
		new Database([
			'username' => 'error-1',
			'password' => \getenv('DB_PASSWORD'),
			'schema' => \getenv('DB_SCHEMA'),
			'host' => \getenv('DB_HOST'),
			'port' => \getenv('DB_PORT'),
			'failover' => [
				[
					'username' => 'error-3',
					'password' => 'error-2',
				],
				[
					'password' => \getenv('DB_PASSWORD'),
				],
			],
		]);
	}

	public function testProtectIdentifier()
	{
		$this->assertEquals('*', static::$database->protectIdentifier('*'));
		$this->assertEquals('`foo`', static::$database->protectIdentifier('foo'));
		$this->assertEquals('```foo```', static::$database->protectIdentifier('`foo`'));
		$this->assertEquals('`foo ``bar`', static::$database->protectIdentifier('foo `bar'));
		$this->assertEquals('`foo`.`bar`', static::$database->protectIdentifier('foo.bar'));
		$this->assertEquals('`foo`.*', static::$database->protectIdentifier('foo.*'));
		$this->assertEquals('```foo```.*', static::$database->protectIdentifier('`foo`.*'));
		$this->assertEquals('`db`.`table`.*', static::$database->protectIdentifier('db.table.*'));
	}

	public function testQuote()
	{
		$this->assertEquals(0, static::$database->quote(0));
		$this->assertEquals(1, static::$database->quote(1));
		$this->assertEquals(-1, static::$database->quote(-1));
		$this->assertEquals(.0, static::$database->quote(.0));
		$this->assertEquals(1.1, static::$database->quote(1.1));
		$this->assertEquals(-1.1, static::$database->quote(-1.1));
		$this->assertEquals("'0'", static::$database->quote('0'));
		$this->assertEquals("'-1'", static::$database->quote('-1'));
		$this->assertEquals("'abc'", static::$database->quote('abc'));
		$this->assertEquals("'ab\\'c'", static::$database->quote("ab'c"));
		$this->assertEquals("'ab\\'cd\\'\\''", static::$database->quote("ab'cd''"));
		$this->assertEquals('\'ab\"cd\"\"\'', static::$database->quote('ab"cd""'));
		$this->assertEquals('NULL', static::$database->quote(null));
		$this->assertEquals('TRUE', static::$database->quote(true));
		$this->assertEquals('FALSE', static::$database->quote(false));
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid value type: array');
		static::$database->quote([]);
	}

	public function testDefinitionInstances()
	{
		$this->assertInstanceOf(CreateSchema::class, static::$database->createSchema());
		$this->assertInstanceOf(DropSchema::class, static::$database->dropSchema());
		$this->assertInstanceOf(AlterSchema::class, static::$database->alterSchema());
		$this->assertInstanceOf(CreateTable::class, static::$database->createTable());
		$this->assertInstanceOf(DropTable::class, static::$database->dropTable());
		$this->assertInstanceOf(AlterTable::class, static::$database->alterTable());
	}

	public function testManipulationInstances()
	{
		$this->assertInstanceOf(Delete::class, static::$database->delete());
		$this->assertInstanceOf(Insert::class, static::$database->insert());
		$this->assertInstanceOf(LoadData::class, static::$database->loadData());
		$this->assertInstanceOf(Replace::class, static::$database->replace());
		$this->assertInstanceOf(Select::class, static::$database->select());
		$this->assertInstanceOf(Update::class, static::$database->update());
		$this->assertInstanceOf(With::class, static::$database->with());
	}

	public function testExec()
	{
		$this->createDummyData();
		$this->assertEquals(1, static::$database->exec(
			'INSERT INTO `t1` SET `c2` = "a"'
		));
		$this->assertEquals(3, static::$database->exec(
			'INSERT INTO `t1` (`c2`) VALUES ("a"),("a"),("a")'
		));
		$this->assertEquals(9, static::$database->exec('SELECT * FROM `t1`'));
	}

	public function testQuery()
	{
		$this->createDummyData();
		$this->assertInstanceOf(Result::class, static::$database->query('SELECT * FROM `t1`'));
	}

	public function testQueryNoResult()
	{
		$this->createDummyData();
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage(
			'Statement does not return result: INSERT INTO `t1` SET `c2` = "a"'
		);
		static::$database->query('INSERT INTO `t1` SET `c2` = "a"');
	}

	public function testPrepare()
	{
		$this->assertInstanceOf(
			PreparedStatement::class,
			static::$database->prepare('SELECT * FROM `t1` WHERE `c1` = ?')
		);
	}

	public function testInsertId()
	{
		$this->createDummyData();
		$this->assertEquals(1, static::$database->insertId());
		static::$database->exec(
			'INSERT INTO `t1` SET `c2` = "a"'
		);
		$this->assertEquals(6, static::$database->insertId());
		static::$database->exec(
			'INSERT INTO `t1` (`c2`) VALUES ("a"),("a"),("a")'
		);
		$this->assertEquals(7, static::$database->insertId());
		static::$database->exec(
			'INSERT INTO `t1` SET `c2` = "a"'
		);
		$this->assertEquals(10, static::$database->insertId());
	}

	public function testTransaction()
	{
		$this->createDummyData();
		static::$database->transaction(function (Database $db) {
			$db->exec('INSERT INTO `t1` SET `c1` = 100, `c2` = "tr"');
		});
		$this->assertEquals(
			'tr',
			static::$database->query('SELECT `c2` FROM `t1` WHERE `c1` = 100')->fetch()->c2
		);
	}

	public function testTransactionInTransaction()
	{
		$this->createDummyData();
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Transaction already is active');
		static::$database->transaction(function (Database $db) {
			$db->transaction(function (Database $db) {
				$db->exec('INSERT INTO `t1` SET `c2` = "a"');
			});
		});
	}

	public function testTransactionRollback()
	{
		$this->createDummyData();
		$this->assertEquals(5, static::$database->exec('SELECT * FROM `t1`'));
		static::$database->transaction(function (Database $db) {
			$db->exec('INSERT INTO `t1` SET `c2` = "a"');
			$db->exec('INSERT INTO `t1` SET `c2` = "a"');
		});
		$this->assertEquals(7, static::$database->exec('SELECT * FROM `t1`'));
		try {
			static::$database->transaction(function (Database $db) {
				$db->exec('INSERT INTO `t1` SET `c2` = "a"');
				$db->exec('INSERT INTO `t1` SET `c2` = "a"');
				$db->exec('INSERT INTO `t1000` SET `c2` = "a"');
			});
		} catch (\Exception $exception) {
			$schema = \getenv('DB_SCHEMA');
			$this->assertInstanceOf(\mysqli_sql_exception::class, $exception);
			$this->assertEquals("Table '{$schema}.t1000' doesn't exist", $exception->getMessage());
		}
		$this->assertEquals(7, static::$database->exec('SELECT * FROM `t1`'));
	}
}
