<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition;

use Framework\Database\Definition\AlterTable;
use Framework\Database\Definition\Table\TableDefinition;
use Tests\Database\TestCase;

final class AlterTableTest extends TestCase
{
    protected AlterTable $alterTable;

    protected function setUp() : void
    {
        $this->alterTable = new AlterTable(static::$database);
    }

    protected function prepare() : AlterTable
    {
        return $this->alterTable->table('t1')
            ->add(static function (TableDefinition $definition) : void {
                $definition->column('c1')->int();
            });
    }

    public function testEmptyTable() : void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('TABLE name must be set');
        $this->alterTable->sql();
    }

    public function testOptions() : void
    {
        self::assertSame(
            "ALTER TABLE `t1`\n ENGINE = MyISAM,\n ADD COLUMN `c1` int NOT NULL",
            $this->prepare()->option('engine', 'myisam')->sql()
        );
    }

    public function testAdd() : void
    {
        $sql = $this->alterTable->table('t1')
            ->add(static function (TableDefinition $definition) : void {
                $definition->column('c1')->int();
                $definition->index()->primaryKey('c1');
                $definition->index('Foo')->uniqueKey('c2');
            });
        self::assertSame(
            "ALTER TABLE `t1`\n ADD COLUMN `c1` int NOT NULL,\n ADD PRIMARY KEY (`c1`),\n ADD UNIQUE KEY `Foo` (`c2`)",
            $sql->sql()
        );
    }

    public function testChange() : void
    {
        $sql = $this->alterTable->table('t1')
            ->change(static function (TableDefinition $definition) : void {
                $definition->column('c1', 'c5')->bigint();
            });
        self::assertSame(
            "ALTER TABLE `t1`\n CHANGE COLUMN `c1` `c5` bigint NOT NULL",
            $sql->sql()
        );
    }

    public function testModify() : void
    {
        $sql = $this->alterTable->table('t1')
            ->modify(static function (TableDefinition $definition) : void {
                $definition->column('c1')->smallint()->notNull();
            });
        self::assertSame(
            "ALTER TABLE `t1`\n MODIFY COLUMN `c1` smallint NOT NULL",
            $sql->sql()
        );
    }

    public function testWait() : void
    {
        self::assertSame(
            "ALTER TABLE `t1`\n WAIT 10\n ADD COLUMN `c1` int NOT NULL",
            $this->prepare()->wait(10)->sql()
        );
    }

    public function testInvalidWait() : void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid WAIT value: -1');
        $this->prepare()->wait(-1)->sql();
    }

    public function testOnline() : void
    {
        self::assertSame(
            "ALTER ONLINE TABLE `t1`\n ADD COLUMN `c1` int NOT NULL",
            $this->prepare()->online()->sql()
        );
    }

    public function testIgnore() : void
    {
        self::assertSame(
            "ALTER IGNORE TABLE `t1`\n ADD COLUMN `c1` int NOT NULL",
            $this->prepare()->ignore()->sql()
        );
    }

    public function testRun() : void
    {
        $this->createDummyData();
        $statement = $this->alterTable->table('t1')
            ->add(static function (TableDefinition $definition) : void {
                $definition->column('c3')->varchar(100)->default('Foo Bar');
            });
        self::assertSame(0, $statement->run());
        static::$database->exec('INSERT INTO `t1` SET `c1` = 123, `c2` = "z"');
        self::assertSame(
            'Foo Bar',
            static::$database->query('SELECT * FROM `t1` WHERE `c1` = 123')->fetch()->c3
        );
    }
}
