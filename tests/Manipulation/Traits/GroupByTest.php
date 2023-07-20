<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation\Traits;

use Tests\Database\TestCase;

final class GroupByTest extends TestCase
{
    protected GroupByMock $statement;

    public function setup() : void
    {
        $this->statement = new GroupByMock(static::$database);
    }

    public function testGroupBy() : void
    {
        self::assertNull($this->statement->renderGroupBy());
        $this->statement->groupBy('c1');
        self::assertSame(' GROUP BY `c1`', $this->statement->renderGroupBy());
        $this->statement->groupBy(static fn () => 'select c2');
        self::assertSame(' GROUP BY `c1`, (select c2)', $this->statement->renderGroupBy());
        $this->statement->groupBy(static fn () => 'select c3', 'c4');
        self::assertSame(
            ' GROUP BY `c1`, (select c2), (select c3), `c4`',
            $this->statement->renderGroupBy()
        );
    }

    public function testGroupByAsc() : void
    {
        $this->statement->groupByAsc('c1');
        self::assertSame(' GROUP BY `c1` ASC', $this->statement->renderGroupBy());
        $this->statement->groupByAsc('c2', 'c3');
        self::assertSame(
            ' GROUP BY `c1` ASC, `c2` ASC, `c3` ASC',
            $this->statement->renderGroupBy()
        );
    }

    public function testGroupByDesc() : void
    {
        $this->statement->groupByDesc('c1');
        self::assertSame(' GROUP BY `c1` DESC', $this->statement->renderGroupBy());
        $this->statement->groupByDesc('c2', 'c3');
        self::assertSame(
            ' GROUP BY `c1` DESC, `c2` DESC, `c3` DESC',
            $this->statement->renderGroupBy()
        );
    }

    public function testGroupByMixed() : void
    {
        $this->statement->groupBy('c1');
        $this->statement->groupByAsc('c2');
        $this->statement->groupByDesc('c3');
        $this->statement->groupBy('a', 'b');
        $this->statement->groupByAsc('c', 'D');
        $this->statement->groupByDesc('e', static fn () => 'select "f"');
        self::assertSame(
            ' GROUP BY `c1`, `c2` ASC, `c3` DESC, `a`, `b`, `c` ASC, `D` ASC, `e` DESC, (select "f") DESC',
            $this->statement->renderGroupBy()
        );
    }

    public function testInvalidExpressionDataType() : void
    {
        $this->expectException(\TypeError::class);
        $this->statement->groupBy([]); // @phpstan-ignore-line
    }
}
