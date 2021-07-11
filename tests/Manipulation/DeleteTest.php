<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation;

use Framework\Database\Manipulation\Delete;
use Tests\Database\TestCase;

final class DeleteTest extends TestCase
{
    protected Delete $delete;

    public function setup() : void
    {
        $this->delete = new Delete(static::$database);
    }

    public function testOptions() : void
    {
        $this->delete->from('t1');
        $this->delete->options($this->delete::OPT_LOW_PRIORITY);
        self::assertSame(
            "DELETE\n LOW_PRIORITY\n FROM `t1`\n",
            $this->delete->sql()
        );
        $this->delete->options($this->delete::OPT_IGNORE);
        self::assertSame(
            "DELETE\n IGNORE\n FROM `t1`\n",
            $this->delete->sql()
        );
        $this->delete->options($this->delete::OPT_LOW_PRIORITY, $this->delete::OPT_IGNORE);
        self::assertSame(
            "DELETE\n LOW_PRIORITY IGNORE\n FROM `t1`\n",
            $this->delete->sql()
        );
    }

    public function testInvalidOption() : void
    {
        $this->delete->from('t1');
        $this->delete->options('foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid option: foo');
        $this->delete->sql();
    }

    public function testLimit() : void
    {
        $this->delete->from('t1');
        $this->delete->limit(1);
        self::assertSame(
            "DELETE\n FROM `t1`\n LIMIT 1\n",
            $this->delete->sql()
        );
        $this->delete->limit('235'); // @phpstan-ignore-line
        self::assertSame(
            "DELETE\n FROM `t1`\n LIMIT 235\n",
            $this->delete->sql()
        );
    }

    public function testWhere() : void
    {
        $this->delete->from('t1');
        $this->delete->whereEqual('id', 1);
        self::assertSame(
            "DELETE\n FROM `t1`\n WHERE `id` = 1\n",
            $this->delete->sql()
        );
    }

    public function testOrderBy() : void
    {
        $this->delete->from('t1');
        $this->delete->orderByAsc('id');
        self::assertSame(
            "DELETE\n FROM `t1`\n ORDER BY `id` ASC\n",
            $this->delete->sql()
        );
    }

    public function testJoin() : void
    {
        $this->delete->table('t1', 't2')
            ->from('t1')
            ->innerJoinOn('t2', static function () {
                return 't2.ref = t1.id';
            });
        self::assertSame(
            "DELETE\n `t1`, `t2`\n FROM `t1`\n INNER JOIN `t2` ON (t2.ref = t1.id)\n",
            $this->delete->sql()
        );
    }

    public function testRun() : void
    {
        $this->createDummyData();
        self::assertSame(
            3,
            $this->delete->from('t1')->whereIn('c1', 1, 2, 3)->run()
        );
    }
}
