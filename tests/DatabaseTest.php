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

final class DatabaseTest extends TestCase
{
	public function testConnection() : void
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

	public function testConnectionWithArray() : void
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

	public function testConnectionFail() : void
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

	public function testConnectionWithSSL() : void
	{
		if (\getenv('DB_HOST') === 'mariadb') {
			$this->expectException(\mysqli_sql_exception::class);
			$this->expectExceptionMessage('MySQL server has gone away');
		}
		$database = new Database([
			'username' => \getenv('DB_USERNAME'),
			'password' => \getenv('DB_PASSWORD'),
			'schema' => \getenv('DB_SCHEMA'),
			'host' => \getenv('DB_HOST'),
			'port' => \getenv('DB_PORT'),
			'ssl' => [
				'enabled' => true,
			],
		]);
		$this->assertInstanceOf(Database::class, $database);
		$this->cipherStatus($database);
	}

	protected function cipherStatus(Database $database) : void
	{
		$status = $database->query("SHOW STATUS LIKE 'ssl_cipher'")->fetchArray();
		$this->assertEquals([
			'Variable_name' => 'Ssl_cipher',
			'Value' => 'TLS_AES_256_GCM_SHA384',
		], $status);
	}

	public function testConnectionWithSSLNotVerified() : void
	{
		if (\getenv('DB_HOST') === 'mariadb') {
			$this->expectException(\mysqli_sql_exception::class);
			$this->expectExceptionMessage('MySQL server has gone away');
		}
		$database = new Database([
			'username' => \getenv('DB_USERNAME'),
			'password' => \getenv('DB_PASSWORD'),
			'schema' => \getenv('DB_SCHEMA'),
			'host' => \getenv('DB_HOST'),
			'port' => \getenv('DB_PORT'),
			'ssl' => [
				'enabled' => true,
				'verify' => false,
			],
		]);
		$this->assertInstanceOf(Database::class, $database);
		$this->cipherStatus($database);
	}

	public function testConnectionWithFailover() : void
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

	public function testConnectionFailWithfailover() : void
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

	public function testProtectIdentifier() : void
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

	public function testQuote() : void
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
		$this->expectException(\TypeError::class);
		static::$database->quote([]);
	}

	public function testDefinitionInstances() : void
	{
		$this->assertInstanceOf(CreateSchema::class, static::$database->createSchema());
		$this->assertInstanceOf(DropSchema::class, static::$database->dropSchema());
		$this->assertInstanceOf(AlterSchema::class, static::$database->alterSchema());
		$this->assertInstanceOf(CreateTable::class, static::$database->createTable());
		$this->assertInstanceOf(DropTable::class, static::$database->dropTable());
		$this->assertInstanceOf(AlterTable::class, static::$database->alterTable());
	}

	public function testDefinitionInstancesWithParams() : void
	{
		$this->assertInstanceOf(CreateSchema::class, static::$database->createSchema('foo'));
		$this->assertInstanceOf(DropSchema::class, static::$database->dropSchema('foo'));
		$this->assertInstanceOf(AlterSchema::class, static::$database->alterSchema('foo'));
		$this->assertInstanceOf(CreateTable::class, static::$database->createTable('foo'));
		$this->assertInstanceOf(DropTable::class, static::$database->dropTable('foo'));
		$this->assertInstanceOf(AlterTable::class, static::$database->alterTable('foo'));
	}

	public function testManipulationInstances() : void
	{
		$this->assertInstanceOf(Delete::class, static::$database->delete());
		$this->assertInstanceOf(Insert::class, static::$database->insert());
		$this->assertInstanceOf(LoadData::class, static::$database->loadData());
		$this->assertInstanceOf(Replace::class, static::$database->replace());
		$this->assertInstanceOf(Select::class, static::$database->select());
		$this->assertInstanceOf(Update::class, static::$database->update());
		$this->assertInstanceOf(With::class, static::$database->with());
	}

	public function testManipulationInstancesWithParams() : void
	{
		$this->assertInstanceOf(Delete::class, static::$database->delete('foo'));
		$this->assertInstanceOf(Insert::class, static::$database->insert('foo'));
		$this->assertInstanceOf(LoadData::class, static::$database->loadData('foo'));
		$this->assertInstanceOf(Replace::class, static::$database->replace('foo'));
		$this->assertInstanceOf(Select::class, static::$database->select('foo'));
		$this->assertInstanceOf(Update::class, static::$database->update('foo'));
		$this->assertInstanceOf(With::class, static::$database->with());
	}

	public function testExec() : void
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

	public function testQuery() : void
	{
		$this->createDummyData();
		$this->assertInstanceOf(Result::class, static::$database->query('SELECT * FROM `t1`'));
	}

	public function testQueryNoResult() : void
	{
		$this->createDummyData();
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage(
			'Statement does not return result: INSERT INTO `t1` SET `c2` = "a"'
		);
		static::$database->query('INSERT INTO `t1` SET `c2` = "a"');
	}

	public function testPrepare() : void
	{
		$this->assertInstanceOf(
			PreparedStatement::class,
			static::$database->prepare('SELECT * FROM `t1` WHERE `c1` = ?')
		);
	}

	public function testInsertId() : void
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

	public function testTransaction() : void
	{
		$this->createDummyData();
		static::$database->transaction(static function (Database $db) : void {
			$db->exec('INSERT INTO `t1` SET `c1` = 100, `c2` = "tr"');
		});
		$this->assertEquals(
			'tr',
			static::$database->query('SELECT `c2` FROM `t1` WHERE `c1` = 100')->fetch()->c2
		);
	}

	public function testTransactionInTransaction() : void
	{
		$this->createDummyData();
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Transaction already is active');
		static::$database->transaction(static function (Database $db) : void {
			$db->transaction(static function (Database $db) : void {
				$db->exec('INSERT INTO `t1` SET `c2` = "a"');
			});
		});
	}

	public function testTransactionRollback() : void
	{
		$this->createDummyData();
		$this->assertEquals(5, static::$database->exec('SELECT * FROM `t1`'));
		static::$database->transaction(static function (Database $db) : void {
			$db->exec('INSERT INTO `t1` SET `c2` = "a"');
			$db->exec('INSERT INTO `t1` SET `c2` = "a"');
		});
		$this->assertEquals(7, static::$database->exec('SELECT * FROM `t1`'));
		try {
			static::$database->transaction(static function (Database $db) : void {
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

	public function testUse() : void
	{
		static::$database->use(\getenv('DB_SCHEMA'));
		$this->expectException(\mysqli_sql_exception::class);
		$this->expectExceptionMessage("Unknown database 'Foo'");
		static::$database->use('Foo');
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testErrors() : void
	{
		$this->assertEquals([], static::$database->errors());
		$this->assertNull(static::$database->error());
		try {
			static::$database->use('Foo');
		} catch (\mysqli_sql_exception $e) {
			//
		}
		try {
			static::$database->use('Bar');
		} catch (\mysqli_sql_exception $e) {
			//
		}
		$this->assertEquals([
			[
				'errno' => 1049,
				'sqlstate' => '42000',
				'error' => "Unknown database 'Bar'",
			],
		], static::$database->errors());
		$this->assertEquals("Unknown database 'Bar'", static::$database->error());
	}

	public function testWarnings() : void
	{
		$this->assertEquals(0, static::$database->warnings());
	}

	public function testLastQuery() : void
	{
		$sql = 'SELECT COUNT(*) FROM t1';
		static::$database->query($sql);
		$this->assertEquals($sql, static::$database->lastQuery());
		static::$database->exec($sql);
		$this->assertEquals($sql, static::$database->lastQuery());
	}
}
