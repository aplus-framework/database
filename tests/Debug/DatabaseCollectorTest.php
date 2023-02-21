<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Debug;

use Framework\Database\Database;
use Framework\Database\Debug\DatabaseCollector;
use Tests\Database\TestCase;

final class DatabaseCollectorTest extends TestCase
{
    protected DatabaseCollector $collector;

    protected function setUp() : void
    {
        $this->collector = new DatabaseCollector();
    }

    protected function makeDatabase() : Database
    {
        return self::$database->setDebugCollector($this->collector);
    }

    public function testNoDatabase() : void
    {
        self::assertStringContainsString(
            'This collector has not been added to a Database instance',
            $this->collector->getContents()
        );
    }

    public function testNoStatements() : void
    {
        $this->makeDatabase();
        self::assertStringContainsString(
            'Did not run statements',
            $this->collector->getContents()
        );
    }

    public function testStatements() : void
    {
        $database = $this->makeDatabase();
        $database->query('SELECT 1');
        self::assertStringContainsString(
            'Ran 1 statement',
            $this->collector->getContents()
        );
        $database->query('SELECT 1');
        self::assertStringContainsString(
            'Ran 2 statements',
            $this->collector->getContents()
        );
        $database->exec('SELECT 1');
        self::assertStringContainsString(
            'Ran 3 statements',
            $this->collector->getContents()
        );
    }

    public function testActivities() : void
    {
        $database = $this->makeDatabase();
        self::assertEmpty($this->collector->getActivities());
        $database->query('SELECT 1');
        self::assertSame(
            [
                'collector',
                'class',
                'description',
                'start',
                'end',
            ],
            \array_keys($this->collector->getActivities()[0]) // @phpstan-ignore-line
        );
    }

    protected function makeDatabaseWithSocket() : Database
    {
        return new Database([
            'username' => \getenv('DB_USERNAME'),
            'password' => \getenv('DB_PASSWORD'),
            'schema' => \getenv('DB_SCHEMA'),
            'host' => \getenv('DB_HOST'),
            'port' => \getenv('DB_PORT'),
            'socket' => '/var/run/mysqld/mysqld.sock',
        ]);
    }

    public function testPort() : void
    {
        $collector = new class() extends DatabaseCollector {
            protected function getHostInfo() : string
            {
                return '127.0.0.1 via TCP/IP';
            }
        };
        $database = $this->makeDatabaseWithSocket();
        $database->setDebugCollector($collector);
        self::assertStringNotContainsString('Socket:', $collector->getContents());
        self::assertStringContainsString('Port:', $collector->getContents());
    }

    public function testSocket() : void
    {
        $collector = new class() extends DatabaseCollector {
            protected function getHostInfo() : string
            {
                return 'Localhost via UNIX socket';
            }
        };
        $database = $this->makeDatabaseWithSocket();
        $database->setDebugCollector($collector);
        self::assertStringContainsString('Socket:', $collector->getContents());
        self::assertStringNotContainsString('Port:', $collector->getContents());
    }

    public function testFinalizeAddToDebug() : void
    {
        try {
            $this->makeDatabase()->query('Foo Bar');
        } catch (\Exception) {
        }
        $data = $this->collector->getData();
        $data = $data[\array_key_last($data)];
        self::assertSame('Foo Bar', $data['statement']);
        self::assertSame('error', $data['rows']);
        $contents = $this->collector->getContents();
        self::assertStringContainsString('error', $contents);
        self::assertStringContainsString(
            \htmlentities($data['description']),
            $contents
        );
    }
}
