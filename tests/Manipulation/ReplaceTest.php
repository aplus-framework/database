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

use Framework\Database\Manipulation\Replace;
use Framework\Database\Manipulation\Select;
use Tests\Database\TestCase;

final class ReplaceTest extends TestCase
{
    protected Replace $replace;

    public function setup() : void
    {
        $this->replace = new Replace(static::$database);
    }

    protected function prepare() : void
    {
        $this->replace->into('t1');
    }

    public function testIntoOnly() : void
    {
        $this->replace->into('t1');
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'The REPLACE INTO must be followed by VALUES, SET or SELECT statement'
        );
        $this->replace->sql();
    }

    public function testRenderWithoutInto() : void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('INTO table must be set');
        $this->replace->sql();
    }

    public function testOptions() : void
    {
        $this->replace->into('t1')->set(['id' => 1]);
        $this->replace->options($this->replace::OPT_DELAYED);
        self::assertSame(
            "REPLACE\n DELAYED\n INTO `t1`\n SET `id` = 1\n",
            $this->replace->sql()
        );
    }

    public function testInvalidOption() : void
    {
        $this->prepare();
        $this->replace->set(['id' => 1]);
        $this->replace->options('foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid option: foo');
        $this->replace->sql();
    }

    public function testOptionsConflict() : void
    {
        $this->prepare();
        $this->replace->options($this->replace::OPT_DELAYED, $this->replace::OPT_LOW_PRIORITY);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Options LOW_PRIORITY and DELAYED can not be used together'
        );
        $this->replace->sql();
    }

    public function testValues() : void
    {
        $this->prepare();
        $this->replace->columns('id', 'name', 'email');
        $this->replace->values(1, 'Foo', 'foo@baz.com');
        self::assertSame(
            "REPLACE\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com')\n",
            $this->replace->sql()
        );
        $this->replace->values(2, 'Bar', 'bar@baz.com');
        self::assertSame(
            "REPLACE\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com'),\n (2, 'Bar', 'bar@baz.com')\n",
            $this->replace->sql()
        );
        $this->replace->values(10, 'Baz', static fn () => 'select email from foo');
        self::assertSame(
            "REPLACE\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com'),\n (2, 'Bar', 'bar@baz.com'),\n (10, 'Baz', (select email from foo))\n",
            $this->replace->sql()
        );
    }

    public function testValuesWithArrayAsFirstArgument() : void
    {
        $this->prepare();
        $this->replace->columns('id', 'name', 'email');
        $this->replace->values([[1, 'Foo', 'foo@baz.com']]);
        self::assertSame(
            "REPLACE\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com')\n",
            $this->replace->sql()
        );
        $this->replace->values([
            [2, 'Bar', 'bar@baz.com'],
            [10, 'Baz', static fn () => 'select email from foo'],
        ]);
        self::assertSame(
            "REPLACE\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com'),\n (2, 'Bar', 'bar@baz.com'),\n (10, 'Baz', (select email from foo))\n",
            $this->replace->sql()
        );
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'The method ' . $this->replace::class . '::values'
            . ' must have only one argument when the first parameter is passed as array'
        );
        $this->replace->values([], null);
    }

    public function testSet() : void
    {
        $this->prepare();
        $this->replace->set([
            'id' => 1,
            'name' => 'Foo',
            'other' => static fn () => "CONCAT('Foo', ' ', 1)",
        ]);
        self::assertSame(
            "REPLACE\n INTO `t1`\n SET `id` = 1, `name` = 'Foo', `other` = (CONCAT('Foo', ' ', 1))\n",
            $this->replace->sql()
        );
    }

    public function testSetWithColumns() : void
    {
        $this->prepare();
        $this->replace->columns('id');
        $this->replace->set(['id' => 1]);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SET clause is not allowed when columns are set');
        $this->replace->sql();
    }

    public function testSetWithValues() : void
    {
        $this->prepare();
        $this->replace->values('id');
        $this->replace->set(['id' => 1]);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SET clause is not allowed when VALUES is set');
        $this->replace->sql();
    }

    public function testSelect() : void
    {
        $this->prepare();
        $this->replace->select(static fn (Select $s) => $s->columns('*')->from('t2'));
        self::assertSame(
            "REPLACE\n INTO `t1`\n SELECT\n *\n FROM `t2`\n\n",
            $this->replace->sql()
        );
    }

    public function testSelectWithValues() : void
    {
        $this->prepare();
        $this->replace->values('id');
        $this->replace->select(static fn (Select $s) => $s->columns('*')->from('t2'));
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SELECT statement is not allowed when VALUES is set');
        $this->replace->sql();
    }

    public function testSelectWithSet() : void
    {
        $this->prepare();
        $this->replace->set(['id' => 1]);
        $this->replace->select(static fn (Select $s) => $s->columns('*')->from('t2'));
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SELECT statement is not allowed when SET is set');
        $this->replace->sql();
    }

    public function testRun() : void
    {
        $this->createDummyData();
        $this->prepare();
        self::assertSame(
            1,
            $this->replace->set(['c2' => 'foo'])->run()
        );
    }
}
