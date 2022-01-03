<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation;

use Framework\Database\Manipulation\Insert;
use Framework\Database\Manipulation\Select;
use Tests\Database\TestCase;

final class InsertTest extends TestCase
{
    protected Insert $insert;

    public function setup() : void
    {
        $this->insert = new Insert(static::$database);
    }

    protected function prepare() : void
    {
        $this->insert->into('t1');
    }

    public function testIntoOnly() : void
    {
        $this->insert->into('t1');
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'The INSERT INTO must be followed by VALUES, SET or SELECT statement'
        );
        $this->insert->sql();
    }

    public function testRenderWithoutInto() : void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('INTO table must be set');
        $this->insert->sql();
    }

    public function testOptions() : void
    {
        $this->insert->into('t1')->set(['id' => 1]);
        $this->insert->options($this->insert::OPT_DELAYED);
        self::assertSame(
            "INSERT\n DELAYED\n INTO `t1`\n SET `id` = 1\n",
            $this->insert->sql()
        );
        $this->insert->options($this->insert::OPT_IGNORE);
        self::assertSame(
            "INSERT\n IGNORE\n INTO `t1`\n SET `id` = 1\n",
            $this->insert->sql()
        );
        $this->insert->options($this->insert::OPT_DELAYED, $this->insert::OPT_IGNORE);
        self::assertSame(
            "INSERT\n DELAYED IGNORE\n INTO `t1`\n SET `id` = 1\n",
            $this->insert->sql()
        );
    }

    public function testInvalidOption() : void
    {
        $this->prepare();
        $this->insert->set(['id' => 1]);
        $this->insert->options('foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid option: foo');
        $this->insert->sql();
    }

    public function testOptionsConflict() : void
    {
        $this->prepare();
        $this->insert->options($this->insert::OPT_DELAYED, $this->insert::OPT_LOW_PRIORITY);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Options LOW_PRIORITY, DELAYED or HIGH_PRIORITY can not be used together'
        );
        $this->insert->sql();
    }

    public function testValues() : void
    {
        $this->prepare();
        $this->insert->columns('id', 'name', 'email');
        $this->insert->values(1, 'Foo', 'foo@baz.com');
        self::assertSame(
            "INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com')\n",
            $this->insert->sql()
        );
        $this->insert->values(2, 'Bar', 'bar@baz.com');
        self::assertSame(
            "INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com'),\n (2, 'Bar', 'bar@baz.com')\n",
            $this->insert->sql()
        );
        $this->insert->values(10, 'Baz', static fn () => 'select email from foo');
        self::assertSame(
            "INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com'),\n (2, 'Bar', 'bar@baz.com'),\n (10, 'Baz', (select email from foo))\n",
            $this->insert->sql()
        );
    }

    public function testValuesWithArrayAsFirstArgument() : void
    {
        $this->prepare();
        $this->insert->columns('id', 'name', 'email');
        $this->insert->values([[1, 'Foo', 'foo@baz.com']]);
        self::assertSame(
            "INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com')\n",
            $this->insert->sql()
        );
        $this->insert->values([
            [2, 'Bar', 'bar@baz.com'],
            [10, 'Baz', static fn () => 'select email from foo'],
        ]);
        self::assertSame(
            "INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com'),\n (2, 'Bar', 'bar@baz.com'),\n (10, 'Baz', (select email from foo))\n",
            $this->insert->sql()
        );
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'The method ' . $this->insert::class . '::values'
            . ' must have only one argument when the first parameter is passed as array'
        );
        $this->insert->values([], null);
    }

    public function testSet() : void
    {
        $this->prepare();
        $this->insert->set([
            'id' => 1,
            'name' => 'Foo',
            'other' => static fn () => "CONCAT('Foo', ' ', 1)",
        ]);
        self::assertSame(
            "INSERT\n INTO `t1`\n SET `id` = 1, `name` = 'Foo', `other` = (CONCAT('Foo', ' ', 1))\n",
            $this->insert->sql()
        );
    }

    public function testSetWithColumns() : void
    {
        $this->prepare();
        $this->insert->columns('id');
        $this->insert->set(['id' => 1]);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SET clause is not allowed when columns are set');
        $this->insert->sql();
    }

    public function testSetWithValues() : void
    {
        $this->prepare();
        $this->insert->values('id');
        $this->insert->set(['id' => 1]);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SET clause is not allowed when VALUES is set');
        $this->insert->sql();
    }

    public function testSelect() : void
    {
        $this->prepare();
        $this->insert->select(
            static fn (Select $s) => $s->columns('*')->from('t2')
        );
        self::assertSame(
            "INSERT\n INTO `t1`\n SELECT\n *\n FROM `t2`\n\n",
            $this->insert->sql()
        );
    }

    public function testSelectWithValues() : void
    {
        $this->prepare();
        $this->insert->values('id');
        $this->insert->select(
            static fn (Select $s) => $s->columns('*')->from('t2')
        );
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SELECT statement is not allowed when VALUES is set');
        $this->insert->sql();
    }

    public function testSelectWithSet() : void
    {
        $this->prepare();
        $this->insert->set(['id' => 1]);
        $this->insert->select(
            static fn (Select $s) => $s->columns('*')->from('t2')
        );
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SELECT statement is not allowed when SET is set');
        $this->insert->sql();
    }

    public function testOnDuplicateKeyUpdate() : void
    {
        $this->prepare();
        $this->insert->set((object) ['id' => 1]);
        $this->insert->onDuplicateKeyUpdate((object) [
            'id' => null,
            'name' => 'Foo',
            'other' => static fn () => "CONCAT('Foo', 'id')",
        ]);
        self::assertSame(
            "INSERT\n INTO `t1`\n SET `id` = 1\n ON DUPLICATE KEY UPDATE `id` = NULL, `name` = 'Foo', `other` = (CONCAT('Foo', 'id'))\n",
            $this->insert->sql()
        );
    }

    public function testRun() : void
    {
        $this->createDummyData();
        $this->prepare();
        self::assertSame(
            1,
            $this->insert->set(['c2' => 'foo'])->run()
        );
    }
}
