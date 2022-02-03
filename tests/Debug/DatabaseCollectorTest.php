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
            \array_keys($this->collector->getActivities()[0])
        );
    }
}
