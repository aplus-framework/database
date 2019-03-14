<?php namespace Tests\Database;

use Framework\Database\Database;
use Framework\Database\Definition\AlterSchema;
use Framework\Database\Definition\CreateSchema;
use Framework\Database\Definition\DropSchema;
use Framework\Database\Definition\DropTable;
use Framework\Database\Manipulation\Insert;
use Framework\Database\Manipulation\LoadData;
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
			\getenv('GITLAB_CI') ? 'mariadb' : \getenv('DB_HOST'),
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
			'host' => \getenv('GITLAB_CI') ? 'mariadb' : \getenv('DB_HOST'),
			'port' => \getenv('DB_PORT'),
		]);
		$this->assertInstanceOf(Database::class, $database);
	}

	public function testConnectionFail()
	{
		$this->expectException(\mysqli_sql_exception::class);
		$this->expectExceptionMessageRegExp("#^Access denied for user 'error-1'@'#");
		new Database([
			'username' => 'error-1',
			'password' => \getenv('DB_PASSWORD'),
			'schema' => \getenv('DB_SCHEMA'),
			'host' => \getenv('GITLAB_CI') ? 'mariadb' : \getenv('DB_HOST'),
			'port' => \getenv('DB_PORT'),
		]);
	}

	public function testConnectionWithFailover()
	{
		$database = new Database([
			'username' => 'error-1',
			'password' => \getenv('DB_PASSWORD'),
			'schema' => \getenv('DB_SCHEMA'),
			'host' => \getenv('GITLAB_CI') ? 'mariadb' : \getenv('DB_HOST'),
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
		$this->expectExceptionMessageRegExp("#^Access denied for user 'error-3'@'#");
		new Database([
			'username' => 'error-1',
			'password' => \getenv('DB_PASSWORD'),
			'schema' => \getenv('DB_SCHEMA'),
			'host' => \getenv('GITLAB_CI') ? 'mariadb' : \getenv('DB_HOST'),
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
		$this->assertEquals('`foo`', $this->database->protectIdentifier('foo'));
		$this->assertEquals('```foo```', $this->database->protectIdentifier('`foo`'));
		$this->assertEquals('`foo ``bar`', $this->database->protectIdentifier('foo `bar'));
		$this->assertEquals('`foo`.`bar`', $this->database->protectIdentifier('foo.bar'));
		$this->assertEquals('`foo`.*', $this->database->protectIdentifier('foo.*'));
		$this->assertEquals('```foo```.*', $this->database->protectIdentifier('`foo`.*'));
		$this->assertEquals('`db`.`table`.*', $this->database->protectIdentifier('db.table.*'));
	}

	public function testQuote()
	{
		$this->assertEquals(0, $this->database->quote(0));
		$this->assertEquals(1, $this->database->quote(1));
		$this->assertEquals(-1, $this->database->quote(-1));
		$this->assertEquals(.0, $this->database->quote(.0));
		$this->assertEquals(1.1, $this->database->quote(1.1));
		$this->assertEquals(-1.1, $this->database->quote(-1.1));
		$this->assertEquals("'0'", $this->database->quote('0'));
		$this->assertEquals("'-1'", $this->database->quote('-1'));
		$this->assertEquals("'abc'", $this->database->quote('abc'));
		$this->assertEquals("'ab\\'c'", $this->database->quote("ab'c"));
		$this->assertEquals("'ab\\'cd\\'\\''", $this->database->quote("ab'cd''"));
		$this->assertEquals('\'ab\"cd\"\"\'', $this->database->quote('ab"cd""'));
		$this->assertEquals('NULL', $this->database->quote(null));
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid value type: array');
		$this->database->quote([]);
	}

	public function testDefinitionInstances()
	{
		$this->assertInstanceOf(CreateSchema::class, $this->database->createSchema());
		$this->assertInstanceOf(DropSchema::class, $this->database->dropSchema());
		$this->assertInstanceOf(AlterSchema::class, $this->database->alterSchema());
		$this->assertInstanceOf(DropTable::class, $this->database->dropTable());
	}

	public function testManipulationInstances()
	{
		$this->assertInstanceOf(Insert::class, $this->database->insert());
		$this->assertInstanceOf(LoadData::class, $this->database->loadData());
		$this->assertInstanceOf(Select::class, $this->database->select());
		$this->assertInstanceOf(Update::class, $this->database->update());
		$this->assertInstanceOf(With::class, $this->database->with());
	}

	public function testExec()
	{
		$this->createDummyData();
		$this->assertEquals(1, $this->database->exec(
			'INSERT INTO `t1` SET `c2` = "a"'
		));
		$this->assertEquals(3, $this->database->exec(
			'INSERT INTO `t1` (`c2`) VALUES ("a"),("a"),("a")'
		));
		$this->assertEquals(9, $this->database->exec('SELECT * FROM `t1`'));
	}

	public function testQuery()
	{
		$this->createDummyData();
		$this->assertInstanceOf(Result::class, $this->database->query('SELECT * FROM `t1`'));
	}

	public function testQueryNoResult()
	{
		$this->createDummyData();
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage(
			'Statement does not return result: INSERT INTO `t1` SET `c2` = "a"'
		);
		$this->database->query('INSERT INTO `t1` SET `c2` = "a"');
	}

	public function testPrepare()
	{
		$this->assertInstanceOf(
			PreparedStatement::class,
			$this->database->prepare('SELECT * FROM `t1` WHERE `c1` = ?')
		);
	}

	public function testInsertId()
	{
		$this->createDummyData();
		$this->assertEquals(1, $this->database->insertId());
		$this->database->exec(
			'INSERT INTO `t1` SET `c2` = "a"'
		);
		$this->assertEquals(6, $this->database->insertId());
		$this->database->exec(
			'INSERT INTO `t1` (`c2`) VALUES ("a"),("a"),("a")'
		);
		$this->assertEquals(7, $this->database->insertId());
		$this->database->exec(
			'INSERT INTO `t1` SET `c2` = "a"'
		);
		$this->assertEquals(10, $this->database->insertId());
	}

	public function testTransaction()
	{
		$this->createDummyData();
		$this->database->transaction(function (Database $db) {
			$db->exec('INSERT INTO `t1` SET `c1` = 100, `c2` = "tr"');
		});
		$this->assertEquals(
			'tr',
			$this->database->query('SELECT `c2` FROM `t1` WHERE `c1` = 100')->fetch()->c2
		);
	}

	public function testTransactionInTransaction()
	{
		$this->createDummyData();
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Transaction already is active');
		$this->database->transaction(function (Database $db) {
			$db->transaction(function (Database $db) {
				$db->exec('INSERT INTO `t1` SET `c2` = "a"');
			});
		});
	}

	public function testTransactionRollback()
	{
		$this->createDummyData();
		$this->assertEquals(5, $this->database->exec('SELECT * FROM `t1`'));
		$this->database->transaction(function (Database $db) {
			$db->exec('INSERT INTO `t1` SET `c2` = "a"');
			$db->exec('INSERT INTO `t1` SET `c2` = "a"');
		});
		$this->assertEquals(7, $this->database->exec('SELECT * FROM `t1`'));
		try {
			$this->database->transaction(function (Database $db) {
				$db->exec('INSERT INTO `t1` SET `c2` = "a"');
				$db->exec('INSERT INTO `t1` SET `c2` = "a"');
				$db->exec('INSERT INTO `t1000` SET `c2` = "a"');
			});
		} catch (\Exception $exception) {
			$schema = \getenv('DB_SCHEMA');
			$this->assertInstanceOf(\mysqli_sql_exception::class, $exception);
			$this->assertEquals("Table '{$schema}.t1000' doesn't exist", $exception->getMessage());
		}
		$this->assertEquals(7, $this->database->exec('SELECT * FROM `t1`'));
	}
}
