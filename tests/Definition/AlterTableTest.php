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

    public function testIfExists() : void
    {
        self::assertSame(
            "ALTER TABLE IF EXISTS `t1`\n ADD COLUMN `c1` int NOT NULL",
            $this->prepare()->ifExists()->sql()
        );
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

    public function testAddEmpty() : void
    {
        $sql = $this->alterTable->table('t1')
            ->add(static function (TableDefinition $definition) : void {
            });
        self::assertSame(
            "ALTER TABLE `t1`\n",
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

    public function testChangeEmpty() : void
    {
        $sql = $this->alterTable->table('t1')
            ->change(static function (TableDefinition $definition) : void {
            });
        self::assertSame(
            "ALTER TABLE `t1`\n",
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

    public function testModifyEmpty() : void
    {
        $sql = $this->alterTable->table('t1')
            ->modify(static function (TableDefinition $definition) : void {
            });
        self::assertSame(
            "ALTER TABLE `t1`\n",
            $sql->sql()
        );
    }

    public function testDropColumnIfExists() : void
    {
        $alterTable = $this->alterTable->table('t1')->dropColumnIfExists('foo');
        self::assertSame(
            "ALTER TABLE `t1`\n DROP COLUMN IF EXISTS `foo`",
            $alterTable->sql()
        );
    }

    public function testDropColumns() : void
    {
        $alterTable = $this->alterTable->table('t1')->dropColumn('foo');
        self::assertSame(
            "ALTER TABLE `t1`\n DROP COLUMN `foo`",
            $alterTable->sql()
        );
        $alterTable->dropColumn('bar', true);
        self::assertSame(
            "ALTER TABLE `t1`\n DROP COLUMN `foo`,\n DROP COLUMN IF EXISTS `bar`",
            $alterTable->sql()
        );
    }

    public function testDropPrimaryKey() : void
    {
        $alterTable = $this->alterTable->table('t1')->dropPrimaryKey();
        self::assertSame(
            "ALTER TABLE `t1`\n DROP PRIMARY KEY",
            $alterTable->sql()
        );
    }

    public function testDropKeyIfExists() : void
    {
        $alterTable = $this->alterTable->table('t1')->dropKeyIfExists('foo');
        self::assertSame(
            "ALTER TABLE `t1`\n DROP KEY IF EXISTS `foo`",
            $alterTable->sql()
        );
    }

    public function testDropKeys() : void
    {
        $alterTable = $this->alterTable->table('t1')->dropKey('foo');
        self::assertSame(
            "ALTER TABLE `t1`\n DROP KEY `foo`",
            $alterTable->sql()
        );
        $alterTable->dropKey('bar', true);
        self::assertSame(
            "ALTER TABLE `t1`\n DROP KEY `foo`,\n DROP KEY IF EXISTS `bar`",
            $alterTable->sql()
        );
    }

    public function testDropForeignKeyIfExists() : void
    {
        $alterTable = $this->alterTable->table('t1')->dropForeignKeyIfExists('foo');
        self::assertSame(
            "ALTER TABLE `t1`\n DROP FOREIGN KEY IF EXISTS `foo`",
            $alterTable->sql()
        );
    }

    public function testDropForeignKeys() : void
    {
        $alterTable = $this->alterTable->table('t1')->dropForeignKey('foo');
        self::assertSame(
            "ALTER TABLE `t1`\n DROP FOREIGN KEY `foo`",
            $alterTable->sql()
        );
        $alterTable->dropForeignKey('bar', true);
        self::assertSame(
            "ALTER TABLE `t1`\n DROP FOREIGN KEY `foo`,\n DROP FOREIGN KEY IF EXISTS `bar`",
            $alterTable->sql()
        );
    }

    public function testDropConstraintIfExists() : void
    {
        $alterTable = $this->alterTable->table('t1')->dropConstraintIfExists('foo');
        self::assertSame(
            "ALTER TABLE `t1`\n DROP CONSTRAINT IF EXISTS `foo`",
            $alterTable->sql()
        );
    }

    public function testDropConstraints() : void
    {
        $alterTable = $this->alterTable->table('t1')->dropConstraint('foo');
        self::assertSame(
            "ALTER TABLE `t1`\n DROP CONSTRAINT `foo`",
            $alterTable->sql()
        );
        $alterTable->dropConstraint('bar', true);
        self::assertSame(
            "ALTER TABLE `t1`\n DROP CONSTRAINT `foo`,\n DROP CONSTRAINT IF EXISTS `bar`",
            $alterTable->sql()
        );
    }

    public function testDisableKeys() : void
    {
        $alterTable = $this->alterTable->table('t1')->disableKeys();
        self::assertSame(
            "ALTER TABLE `t1`\n DISABLE KEYS",
            $alterTable->sql()
        );
    }

    public function testEnableKeys() : void
    {
        $alterTable = $this->alterTable->table('t1')->enableKeys();
        self::assertSame(
            "ALTER TABLE `t1`\n ENABLE KEYS",
            $alterTable->sql()
        );
    }

    public function testRenameTo() : void
    {
        $alterTable = $this->alterTable->table('t1')->renameTo('foo');
        self::assertSame(
            "ALTER TABLE `t1`\n RENAME TO `foo`",
            $alterTable->sql()
        );
    }

    public function testOrderBy() : void
    {
        $alterTable = $this->alterTable->table('t1')->orderBy('c1');
        self::assertSame(
            "ALTER TABLE `t1`\n ORDER BY `c1`",
            $alterTable->sql()
        );
        $alterTable->orderBy('c2', 'c3');
        self::assertSame(
            "ALTER TABLE `t1`\n ORDER BY `c1`, `c2`, `c3`",
            $alterTable->sql()
        );
    }

    public function testRenameColumns() : void
    {
        $alterTable = $this->alterTable->table('t1')->renameColumn('c1', 'foo');
        self::assertSame(
            "ALTER TABLE `t1`\n RENAME COLUMN `c1` TO `foo`",
            $alterTable->sql()
        );
        $alterTable->renameColumn('c2', 'bar');
        self::assertSame(
            "ALTER TABLE `t1`\n RENAME COLUMN `c1` TO `foo`,\n RENAME COLUMN `c2` TO `bar`",
            $alterTable->sql()
        );
    }

    public function testRenameKeys() : void
    {
        $alterTable = $this->alterTable->table('t1')->renameKey('k1', 'foo');
        self::assertSame(
            "ALTER TABLE `t1`\n RENAME KEY `k1` TO `foo`",
            $alterTable->sql()
        );
        $alterTable->renameKey('k2', 'bar');
        self::assertSame(
            "ALTER TABLE `t1`\n RENAME KEY `k1` TO `foo`,\n RENAME KEY `k2` TO `bar`",
            $alterTable->sql()
        );
    }

    public function testConvertToCharset() : void
    {
        $alterTable = $this->alterTable->table('t1')->convertToCharset('utf8mb4');
        self::assertSame(
            "ALTER TABLE `t1`\n CONVERT TO CHARACTER SET 'utf8mb4'",
            $alterTable->sql()
        );
        $alterTable->convertToCharset('utf8', 'utf8_general_ci');
        self::assertSame(
            "ALTER TABLE `t1`\n CONVERT TO CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'",
            $alterTable->sql()
        );
    }

    public function testCharset() : void
    {
        $alterTable = $this->alterTable->table('t1')->charset('utf8mb4');
        self::assertSame(
            "ALTER TABLE `t1`\n CHARACTER SET = 'utf8mb4'",
            $alterTable->sql()
        );
        $alterTable->charset(null);
        self::assertSame(
            "ALTER TABLE `t1`\n DEFAULT CHARACTER SET",
            $alterTable->sql()
        );
        $alterTable->charset('Default');
        self::assertSame(
            "ALTER TABLE `t1`\n DEFAULT CHARACTER SET",
            $alterTable->sql()
        );
    }

    public function testCollate() : void
    {
        $alterTable = $this->alterTable->table('t1')->collate('utf8_general_ci');
        self::assertSame(
            "ALTER TABLE `t1`\n COLLATE = 'utf8_general_ci'",
            $alterTable->sql()
        );
        $alterTable->collate(null);
        self::assertSame(
            "ALTER TABLE `t1`\n DEFAULT COLLATE",
            $alterTable->sql()
        );
        $alterTable->collate('Default');
        self::assertSame(
            "ALTER TABLE `t1`\n DEFAULT COLLATE",
            $alterTable->sql()
        );
    }

    public function testLock() : void
    {
        $alterTable = $this->alterTable->table('t1')->lock('default');
        self::assertSame(
            "ALTER TABLE `t1`\n LOCK = DEFAULT",
            $alterTable->sql()
        );
        $alterTable->lock('Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid LOCK value: Foo');
        $alterTable->sql();
    }

    public function testForce() : void
    {
        $alterTable = $this->alterTable->table('t1')->force();
        self::assertSame(
            "ALTER TABLE `t1`\n FORCE",
            $alterTable->sql()
        );
    }

    public function testAlgorithm() : void
    {
        $alterTable = $this->alterTable->table('t1')->algorithm('default');
        self::assertSame(
            "ALTER TABLE `t1`\n ALGORITHM = DEFAULT",
            $alterTable->sql()
        );
        $alterTable->algorithm('Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ALGORITHM value: Foo');
        $alterTable->sql();
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

    public function testNoWait() : void
    {
        $alterTable = $this->prepare()->noWait();
        self::assertSame(
            "ALTER TABLE `t1`\n NOWAIT\n ADD COLUMN `c1` int NOT NULL",
            $alterTable->sql()
        );
        $alterTable->wait(10);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('WAIT and NOWAIT can not be used together');
        $alterTable->sql();
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
                $definition->column('foo')->varchar(100)->default('Foo Bar');
                $definition->column('bar')->int()->null();
            })->renameColumn('c1', 'id')->dropColumn('c2');
        self::assertSame(
            "ALTER TABLE `t1`\n" .
            " ADD COLUMN `foo` varchar(100) NOT NULL DEFAULT 'Foo Bar',\n" .
            " ADD COLUMN `bar` int NULL,\n" .
            " DROP COLUMN `c2`,\n" .
            ' RENAME COLUMN `c1` TO `id`',
            $statement->sql()
        );
        self::assertSame(0, $statement->run());
        static::$database->exec('INSERT INTO `t1` SET `id` = 123');
        self::assertSame(
            [
                'id' => 123,
                'foo' => 'Foo Bar',
                'bar' => null,
            ],
            static::$database->query('SELECT * FROM `t1` WHERE `id` = 123')->fetchArray()
        );
    }
}
