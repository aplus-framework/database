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

use Closure;
use Framework\Database\Manipulation\Select;
use Framework\Database\Result;
use Tests\Database\TestCase;

final class SelectTest extends TestCase
{
    protected Select $select;

    public function setup() : void
    {
        $this->select = new Select(static::$database);
    }

    /**
     * @param array<string,Closure|string>|Closure|string ...$from
     *
     * @return string
     */
    protected function selectAllFrom(array | Closure | string ...$from) : string
    {
        return $this->select->columns('*')->from(...$from)->sql();
    }

    protected function renderSelectAllFrom() : string
    {
        return $this->select->sql();
    }

    public function testOptions() : void
    {
        $this->select->options($this->select::OPT_ALL);
        self::assertSame(
            "SELECT\nALL\n",
            $this->select->sql()
        );
        $this->select->options($this->select::OPT_HIGH_PRIORITY);
        self::assertSame(
            "SELECT\nHIGH_PRIORITY\n",
            $this->select->sql()
        );
        $this->select->options($this->select::OPT_ALL, $this->select::OPT_HIGH_PRIORITY);
        self::assertSame(
            "SELECT\nALL HIGH_PRIORITY\n",
            $this->select->sql()
        );
    }

    public function testInvalidOption() : void
    {
        $this->select->options('al');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid option: al');
        $this->select->sql();
    }

    public function testIOptionsConflictAllAndDistinct() : void
    {
        $this->select->options($this->select::OPT_ALL, $this->select::OPT_DISTINCT);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Options ALL and DISTINCT can not be used together');
        $this->select->sql();
    }

    public function testIOptionsConflictSqlCacheAndSqlNoCache() : void
    {
        $this->select->options($this->select::OPT_SQL_CACHE, $this->select::OPT_SQL_NO_CACHE);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Options SQL_CACHE and SQL_NO_CACHE can not be used together');
        $this->select->sql();
    }

    public function testExpressions() : void
    {
        $this->select->expressions('1');
        self::assertSame("SELECT\n `1`\n", $this->select->sql());
        $this->select->expressions(static function () {
            return 'now()';
        });
        self::assertSame("SELECT\n `1`, (now())\n", $this->select->sql());
    }

    public function testColumns() : void
    {
        $this->select->columns('1');
        self::assertSame("SELECT\n `1`\n", $this->select->sql());
        $this->select->columns(static function () {
            return 'now()';
        });
        self::assertSame("SELECT\n `1`, (now())\n", $this->select->sql());
    }

    public function testEmptyExpressionsWithFrom() : void
    {
        $this->select->from('Users');
        self::assertSame("SELECT\n *\n FROM `Users`\n", $this->select->sql());
        $this->select->columns('id', 'name');
        self::assertSame("SELECT\n `id`, `name`\n FROM `Users`\n", $this->select->sql());
    }

    public function testLimit() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " LIMIT 10\n",
            $this->select->limit(10)->sql()
        );
        self::assertSame(
            $part . " LIMIT 10 OFFSET 20\n",
            $this->select->limit(10, 20)->sql()
        );
    }

    public function testProcedure() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " PROCEDURE count_foo()\n",
            $this->select->procedure('count_foo')->sql()
        );
        self::assertSame(
            $part . " PROCEDURE count_bar('a', 1)\n",
            $this->select->procedure('count_bar', 'a', 1)->sql()
        );
    }

    public function testIntoOutfile() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " INTO OUTFILE '/tmp/foo-bar'\n",
            $this->select->intoOutfile('/tmp/foo-bar')->sql()
        );
        self::assertSame(
            $part . " INTO OUTFILE '/tmp/foo-bar' CHARACTER SET 'utf8'\n",
            $this->select->intoOutfile('/tmp/foo-bar', 'utf8')->sql()
        );
        self::assertSame(
            $part . " INTO OUTFILE '/tmp/foo-bar' CHARACTER SET 'utf8' FIELDS ENCLOSED BY '\\''\n",
            $this->select->intoOutfile(
                '/tmp/foo-bar',
                'utf8',
                [
                    $this->select::EXP_FIELDS_ENCLOSED_BY => "'",
                ]
            )->sql()
        );
        self::assertSame(
            $part . " INTO OUTFILE '/tmp/foo-bar' LINES TERMINATED BY ' '\n",
            $this->select->intoOutfile(
                '/tmp/foo-bar',
                null,
                [],
                [
                    $this->select::EXP_LINES_TERMINATED_BY => ' ',
                ]
            )->sql()
        );
    }

    public function testIntoOutfileFileExists() : void
    {
        $this->selectAllFrom('t1');
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('INTO OUTFILE filename must not exist: ' . __FILE__);
        $this->select->intoOutfile(__FILE__)->sql();
    }

    public function testInvalidIntoOutfileFieldOption() : void
    {
        $this->selectAllFrom('t1');
        $this->select->intoOutfile('/tmp/foo-bar', null, ['foo' => 'bar']);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid INTO OUTFILE fields option: foo');
        $this->select->sql();
    }

    public function testInvalidIntoOutfileLineOption() : void
    {
        $this->selectAllFrom('t1');
        $this->select->intoOutfile('/tmp/foo-bar', null, [], ['foo' => 'bar']);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid INTO OUTFILE lines option: foo');
        $this->select->sql();
    }

    public function testIntoDumpfile() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " INTO DUMPFILE '/tmp/foo-bar'\n",
            $this->select->intoDumpfile('/tmp/foo-bar')->sql()
        );
        self::assertSame(
            $part . " INTO DUMPFILE '/tmp/foo-bar' INTO @var1, @Var2\n",
            $this->select->intoDumpfile('/tmp/foo-bar', 'var1', 'Var2')->sql()
        );
    }

    public function testIntoDumpfileFileExists() : void
    {
        $this->selectAllFrom('t1');
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('INTO DUMPFILE filepath must not exist: ' . __FILE__);
        $this->select->intoDumpfile(__FILE__)->sql();
    }

    public function testLockForUpdate() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " FOR UPDATE\n",
            $this->select->lockForUpdate()->sql()
        );
        self::assertSame(
            $part . " FOR UPDATE WAIT 120\n",
            $this->select->lockForUpdate(120)->sql()
        );
    }

    public function testInvalidLockForUpdateWait() : void
    {
        $this->selectAllFrom('t1');
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid FOR UPDATE WAIT value: -1');
        $this->select->lockForUpdate(-1)->sql();
    }

    public function testLockInShareMode() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " LOCK IN SHARE MODE\n",
            $this->select->lockInShareMode()->sql()
        );
        self::assertSame(
            $part . " LOCK IN SHARE MODE WAIT 1\n",
            $this->select->lockInShareMode(1)->sql()
        );
    }

    public function testInvalidLockInShareModeWait() : void
    {
        $this->selectAllFrom('t1');
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid LOCK IN SHARE MODE WAIT value: -10');
        $this->select->lockInShareMode(-10)->sql();
    }

    public function testJoin() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " JOIN `t2` USING (`user_id`)\n",
            $this->select->joinUsing('t2', 'user_id')->sql()
        );
    }

    public function testWhere() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " WHERE `id` = 10\n",
            $this->select->whereEqual('id', 10)->sql()
        );
    }

    public function testHaving() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " HAVING `id` = 10\n",
            $this->select->havingEqual('id', 10)->sql()
        );
    }

    public function testOrderBy() : void
    {
        $part = $this->selectAllFrom('t1');
        self::assertSame(
            $part . " ORDER BY `name` ASC, `id`\n",
            $this->select->orderByAsc('name')->orderBy('id')->sql()
        );
    }

    public function testRun() : void
    {
        $this->createDummyData();
        $this->selectAllFrom('t1');
        $this->select->limit(1);
        $result = $this->select->run();
        self::assertInstanceOf(Result::class, $result);
        self::assertTrue($result->moveCursor(0));
    }

    public function testRunUnbuffered() : void
    {
        $this->createDummyData();
        $this->selectAllFrom('t1');
        $this->select->limit(1);
        $result = $this->select->runUnbuffered();
        self::assertInstanceOf(Result::class, $result);
        $this->expectException(\LogicException::class);
        $result->moveCursor(0);
    }
}
