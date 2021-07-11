<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database;

use Framework\Database\Database;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected static ?Database $database = null;

    public function __construct(mixed ...$params)
    {
        $this->setDatabase();
        parent::__construct(...$params);
    }

    protected function setDatabase() : Database
    {
        if (static::$database === null) {
            static::$database = new Database([
                'username' => \getenv('DB_USERNAME'),
                'password' => \getenv('DB_PASSWORD'),
                'schema' => \getenv('DB_SCHEMA'),
                'host' => \getenv('DB_HOST'),
                'port' => \getenv('DB_PORT'),
            ]);
        }
        return static::$database;
    }

    protected function resetDatabase() : void
    {
        static::$database = null;
        $this->setDatabase();
    }

    protected function dropDummyData() : void
    {
        static::$database->exec('DROP TABLE IF EXISTS `t1`');
        static::$database->exec('DROP TABLE IF EXISTS `t2`');
    }

    protected function createDummyData() : void
    {
        $this->dropDummyData();
        static::$database->exec(
            <<<'SQL'
                CREATE TABLE `t1` (
                  `c1` INT(11) AUTO_INCREMENT PRIMARY KEY,
                  `c2` VARCHAR(255)
                )
                SQL
        );
        static::$database->exec(
            <<<'SQL'
                CREATE TABLE `t2` (
                  `c1` INT(11) AUTO_INCREMENT PRIMARY KEY,
                  `c2` VARCHAR(255)
                )
                SQL
        );
        static::$database->exec(
            "INSERT INTO `t1` (`c2`) VALUES ('a'), ('b'), ('c'), ('d'), ('e')"
        );
    }
}
