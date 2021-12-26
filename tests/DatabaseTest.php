<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database;

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
use Framework\Log\Logger;
use mysqli_sql_exception;

final class DatabaseTest extends TestCase
{
    public function testConnection() : void
    {
        $database = new Database(
            \getenv('DB_USERNAME'), // @phpstan-ignore-line
            \getenv('DB_PASSWORD'), // @phpstan-ignore-line
            \getenv('DB_SCHEMA'), // @phpstan-ignore-line
            \getenv('DB_HOST'), // @phpstan-ignore-line
            // @phpstan-ignore-next-line
            \getenv('DB_PORT')
        );
        self::assertInstanceOf(Database::class, $database);
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
        self::assertInstanceOf(Database::class, $database);
    }

    public function testConnectionFail() : void
    {
        $this->expectException(mysqli_sql_exception::class);
        new Database([
            'username' => 'error-1',
            'password' => \getenv('DB_PASSWORD'),
            'schema' => \getenv('DB_SCHEMA'),
            'host' => \getenv('DB_HOST'),
            'port' => \getenv('DB_PORT'),
        ]);
    }

    public function testConnectionFailWithLogger() : void
    {
        $directory = '/tmp/logs';
        if ( ! \is_dir($directory)) {
            \mkdir($directory);
        }
        $logger = new Logger($directory);
        $config = [
            'username' => 'error-1',
            'password' => \getenv('DB_PASSWORD'),
            'schema' => \getenv('DB_SCHEMA'),
            'host' => \getenv('DB_HOST'),
            'port' => \getenv('DB_PORT'),
        ];
        try {
            new Database($config, logger: $logger);
        } catch (mysqli_sql_exception) {
            self::assertSame(
                "Database: Connection failed for 'error-1'@'{$config['host']}'",
                $logger->getLastLog()->message
            );
        }
        $config['failover'] = [
            [
                'username' => 'error-2',
            ],
        ];
        try {
            new Database($config, logger: $logger);
        } catch (mysqli_sql_exception) {
            self::assertSame(
                "Database: Connection failed for 'error-2'@'{$config['host']}' (failover: 0)",
                $logger->getLastLog()->message
            );
        }
    }

    public function testConnectionWithSSL() : void
    {
        if (\getenv('DB_IMAGE') === 'mariadb') {
            $this->expectException(mysqli_sql_exception::class);
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
        self::assertInstanceOf(Database::class, $database);
        $this->cipherStatus($database);
    }

    protected function cipherStatus(Database $database) : void
    {
        $status = $database->query("SHOW STATUS LIKE 'ssl_cipher'")->fetchArray();
        self::assertSame([
            'Variable_name' => 'Ssl_cipher',
            'Value' => 'TLS_AES_256_GCM_SHA384',
        ], $status);
    }

    public function testConnectionWithSSLNotVerified() : void
    {
        if (\getenv('DB_IMAGE') === 'mariadb') {
            $this->expectException(mysqli_sql_exception::class);
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
        self::assertInstanceOf(Database::class, $database);
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
        self::assertInstanceOf(Database::class, $database);
    }

    public function testConnectionFailWithFailover() : void
    {
        $this->expectException(mysqli_sql_exception::class);
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

    /**
     * @runInSeparateProcess
     */
    public function testClose() : void
    {
        self::assertTrue(static::$database->close());
        self::assertTrue(static::$database->close());
    }

    public function testPing() : void
    {
        self::assertTrue(static::$database->ping());
    }

    public function testReconnect() : void
    {
        self::assertInstanceOf(Database::class, static::$database->reconnect());
    }

    public function testConfig() : void
    {
        self::assertSame(\getenv('DB_USERNAME'), static::$database->getConfig()['username']);
        self::assertSame('+00:00', static::$database->getConfig()['timezone']);
    }

    public function testOptions() : void
    {
        $this->createDummyData();
        $config = [
            'username' => \getenv('DB_USERNAME'),
            'password' => \getenv('DB_PASSWORD'),
            'schema' => \getenv('DB_SCHEMA'),
            'host' => \getenv('DB_HOST'),
            'port' => \getenv('DB_PORT'),
        ];
        $database = new Database($config);
        self::assertSame(1, $database->query('SELECT `c1` FROM `t1` LIMIT 1')->fetch()->c1);
        $config['options'][\MYSQLI_OPT_INT_AND_FLOAT_NATIVE] = false;
        $database = new Database($config);
        self::assertSame('1', $database->query('SELECT `c1` FROM `t1` LIMIT 1')->fetch()->c1);
    }

    public function testProtectIdentifier() : void
    {
        self::assertSame('*', static::$database->protectIdentifier('*'));
        self::assertSame('`foo`', static::$database->protectIdentifier('foo'));
        self::assertSame('```foo```', static::$database->protectIdentifier('`foo`'));
        self::assertSame('`foo ``bar`', static::$database->protectIdentifier('foo `bar'));
        self::assertSame('`foo`.`bar`', static::$database->protectIdentifier('foo.bar'));
        self::assertSame('`foo`.*', static::$database->protectIdentifier('foo.*'));
        self::assertSame('```foo```.*', static::$database->protectIdentifier('`foo`.*'));
        self::assertSame('`db`.`table`.*', static::$database->protectIdentifier('db.table.*'));
    }

    public function testQuote() : void
    {
        self::assertSame(0, static::$database->quote(0));
        self::assertSame(1, static::$database->quote(1));
        self::assertSame(-1, static::$database->quote(-1));
        self::assertSame(.0, static::$database->quote(.0));
        self::assertSame(1.1, static::$database->quote(1.1));
        self::assertSame(-1.1, static::$database->quote(-1.1));
        self::assertSame("'0'", static::$database->quote('0'));
        self::assertSame("'-1'", static::$database->quote('-1'));
        self::assertSame("'abc'", static::$database->quote('abc'));
        self::assertSame("'ab\\'c'", static::$database->quote("ab'c"));
        self::assertSame("'ab\\'cd\\'\\''", static::$database->quote("ab'cd''"));
        self::assertSame('\'ab\"cd\"\"\'', static::$database->quote('ab"cd""'));
        self::assertSame('NULL', static::$database->quote(null));
        self::assertSame('TRUE', static::$database->quote(true));
        self::assertSame('FALSE', static::$database->quote(false));
        $this->expectException(\TypeError::class);
        static::$database->quote([]); // @phpstan-ignore-line
    }

    public function testDefinitionInstances() : void
    {
        self::assertInstanceOf(CreateSchema::class, static::$database->createSchema());
        self::assertInstanceOf(DropSchema::class, static::$database->dropSchema());
        self::assertInstanceOf(AlterSchema::class, static::$database->alterSchema());
        self::assertInstanceOf(CreateTable::class, static::$database->createTable());
        self::assertInstanceOf(DropTable::class, static::$database->dropTable());
        self::assertInstanceOf(AlterTable::class, static::$database->alterTable());
    }

    public function testDefinitionInstancesWithParams() : void
    {
        self::assertInstanceOf(CreateSchema::class, static::$database->createSchema('foo'));
        self::assertInstanceOf(DropSchema::class, static::$database->dropSchema('foo'));
        self::assertInstanceOf(AlterSchema::class, static::$database->alterSchema('foo'));
        self::assertInstanceOf(CreateTable::class, static::$database->createTable('foo'));
        self::assertInstanceOf(DropTable::class, static::$database->dropTable('foo'));
        self::assertInstanceOf(AlterTable::class, static::$database->alterTable('foo'));
    }

    public function testManipulationInstances() : void
    {
        self::assertInstanceOf(Delete::class, static::$database->delete());
        self::assertInstanceOf(Insert::class, static::$database->insert());
        self::assertInstanceOf(LoadData::class, static::$database->loadData());
        self::assertInstanceOf(Replace::class, static::$database->replace());
        self::assertInstanceOf(Select::class, static::$database->select());
        self::assertInstanceOf(Update::class, static::$database->update());
        self::assertInstanceOf(With::class, static::$database->with());
    }

    public function testManipulationInstancesWithParams() : void
    {
        self::assertInstanceOf(Delete::class, static::$database->delete('foo'));
        self::assertInstanceOf(Insert::class, static::$database->insert('foo'));
        self::assertInstanceOf(LoadData::class, static::$database->loadData('foo'));
        self::assertInstanceOf(Replace::class, static::$database->replace('foo'));
        self::assertInstanceOf(Select::class, static::$database->select('foo'));
        self::assertInstanceOf(Update::class, static::$database->update('foo'));
        self::assertInstanceOf(With::class, static::$database->with());
    }

    public function testExec() : void
    {
        $this->createDummyData();
        self::assertSame(1, static::$database->exec(
            'INSERT INTO `t1` SET `c2` = "a"'
        ));
        self::assertSame(3, static::$database->exec(
            'INSERT INTO `t1` (`c2`) VALUES ("a"),("a"),("a")'
        ));
        self::assertSame(9, static::$database->exec('SELECT * FROM `t1`'));
    }

    public function testQuery() : void
    {
        $this->createDummyData();
        self::assertInstanceOf(Result::class, static::$database->query('SELECT * FROM `t1`'));
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
        self::assertInstanceOf(
            PreparedStatement::class,
            static::$database->prepare('SELECT * FROM `t1` WHERE `c1` = ?')
        );
    }

    public function testInsertId() : void
    {
        $this->createDummyData();
        self::assertSame(1, static::$database->insertId());
        static::$database->exec(
            'INSERT INTO `t1` SET `c2` = "a"'
        );
        self::assertSame(6, static::$database->insertId());
        static::$database->exec(
            'INSERT INTO `t1` (`c2`) VALUES ("a"),("a"),("a")'
        );
        self::assertSame(7, static::$database->insertId());
        static::$database->exec(
            'INSERT INTO `t1` SET `c2` = "a"'
        );
        self::assertSame(10, static::$database->insertId());
    }

    public function testTransaction() : void
    {
        $this->createDummyData();
        static::$database->transaction(static function (Database $db) : void {
            $db->exec('INSERT INTO `t1` SET `c1` = 100, `c2` = "tr"');
        });
        self::assertSame(
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
        self::assertSame(5, static::$database->exec('SELECT * FROM `t1`'));
        static::$database->transaction(static function (Database $db) : void {
            $db->exec('INSERT INTO `t1` SET `c2` = "a"');
            $db->exec('INSERT INTO `t1` SET `c2` = "a"');
        });
        self::assertSame(7, static::$database->exec('SELECT * FROM `t1`'));
        try {
            static::$database->transaction(static function (Database $db) : void {
                $db->exec('INSERT INTO `t1` SET `c2` = "a"');
                $db->exec('INSERT INTO `t1` SET `c2` = "a"');
                $db->exec('INSERT INTO `t1000` SET `c2` = "a"');
            });
        } catch (\Exception $exception) {
            $schema = \getenv('DB_SCHEMA');
            self::assertInstanceOf(\mysqli_sql_exception::class, $exception);
            self::assertSame("Table '{$schema}.t1000' doesn't exist", $exception->getMessage());
        }
        self::assertSame(7, static::$database->exec('SELECT * FROM `t1`'));
    }

    public function testUse() : void
    {
        static::$database->use(\getenv('DB_SCHEMA')); // @phpstan-ignore-line
        $this->expectException(\mysqli_sql_exception::class);
        $this->expectExceptionMessage("Unknown database 'Foo'");
        static::$database->use('Foo');
    }

    public function testErrors() : void
    {
        $this->resetDatabase();
        self::assertSame([], static::$database->errors());
        self::assertNull(static::$database->error());
        try {
            static::$database->use('Bar');
        } catch (\mysqli_sql_exception) {
            //
        }
        self::assertSame([
            [
                'errno' => 1049,
                'sqlstate' => '42000',
                'error' => "Unknown database 'Bar'",
            ],
        ], static::$database->errors());
        self::assertSame("Unknown database 'Bar'", static::$database->error());
    }

    public function testWarnings() : void
    {
        self::assertSame(0, static::$database->warnings());
    }

    public function testLastQuery() : void
    {
        $sql = 'SELECT COUNT(*) FROM `t1`';
        static::$database->query($sql);
        self::assertSame($sql, static::$database->lastQuery());
        static::$database->exec($sql);
        self::assertSame($sql, static::$database->lastQuery());
    }
}
