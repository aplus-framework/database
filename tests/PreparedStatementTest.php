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

use Framework\Database\Result;

final class PreparedStatementTest extends TestCase
{
    public function testExec() : void
    {
        $this->createDummyData();
        $prepared = static::$database->prepare('INSERT INTO `t1` SET `c2` = "f"');
        self::assertIsInt($prepared->exec());
        self::assertSame(1, $prepared->exec());
        $prepared = static::$database->prepare('INSERT INTO `t1` (`c2`) VALUES ("h"), ("i")');
        self::assertIsInt($prepared->exec());
        self::assertSame(2, $prepared->exec());
    }

    public function testExecWithBinds() : void
    {
        $this->createDummyData();
        $prepared = static::$database->prepare('INSERT INTO `t1` SET `c2` = ?');
        self::assertIsInt($prepared->exec('f'));
        self::assertSame(1, $prepared->exec('g'));
        $prepared = static::$database->prepare('INSERT INTO `t1` (`c2`) VALUES (?), (?), ("l")');
        self::assertIsInt($prepared->exec('h', 'i'));
        self::assertSame(3, $prepared->exec('j', 'k'));
        $prepared = static::$database->prepare(
            'INSERT INTO `t1` (`c2`) VALUES (?), (?), (?), (?), (?)'
        );
        self::assertSame(5, $prepared->exec('a', 1, false, true, null));
    }

    public function testExecResult() : void
    {
        $this->createDummyData();
        $prepared = static::$database->prepare('SELECT * FROM `t1`');
        self::assertIsInt($prepared->exec());
        self::assertSame(-1, $prepared->exec());
        $prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c1` < ?');
        self::assertIsInt($prepared->exec(4));
        self::assertSame(-1, $prepared->exec(4));
    }

    public function testQuery() : void
    {
        $this->createDummyData();
        $prepared = static::$database->prepare('SELECT * FROM `t1`');
        self::assertInstanceOf(Result::class, $prepared->query());
        self::assertSame('a', $prepared->query()->fetch()->c2);
        self::assertSame('a', $prepared->query()->fetch()->c2);
        $result = $prepared->query();
        self::assertSame('a', $result->fetch()->c2);
        self::assertSame('b', $result->fetch()->c2);
    }

    public function testQueryResultMoveCursor() : void
    {
        $this->createDummyData();
        $result = static::$database->prepare('SELECT * FROM `t1`')->query();
        self::assertSame('a', $result->fetch()->c2);
        self::assertSame('b', $result->fetch()->c2);
        self::assertTrue($result->moveCursor(0));
        self::assertSame('a', $result->fetch()->c2);
    }

    public function todo_testQueryResultMoveCursorUnbuffered() : void
    {
        $this->createDummyData();
        $result = static::$database->prepare('SELECT * FROM `t1`')->query();
        self::assertSame('a', $result->fetch()->c2);
        self::assertSame('b', $result->fetch()->c2);
        $this->expectException(\mysqli_sql_exception::class);
        $this->expectExceptionMessage("Commands out of sync; you can't run this command now");
        self::assertTrue($result->moveCursor(0));
    }

    public function testQueryWithBinds() : void
    {
        $this->createDummyData();
        $prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c1` = ?');
        self::assertInstanceOf(Result::class, $prepared->query(1));
        self::assertSame('b', $prepared->query(2)->fetch()->c2);
        self::assertSame('e', $prepared->query(5)->fetch()->c2);
        $prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c2` = ?');
        self::assertInstanceOf(Result::class, $prepared->query('a'));
        self::assertSame(2, $prepared->query('b')->fetch()->c1);
        self::assertSame(5, $prepared->query('e')->fetch()->c1);
    }

    public function testBindParams() : void
    {
        $this->createDummyData();
        $prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c1` = ?');
        self::assertInstanceOf(Result::class, $prepared->query(1));
        self::assertInstanceOf(Result::class, $prepared->query(1.1));
        self::assertInstanceOf(Result::class, $prepared->query('a'));
        self::assertInstanceOf(Result::class, $prepared->query(true));
        self::assertInstanceOf(Result::class, $prepared->query(null));
        $this->expectException(\TypeError::class);
        $prepared->query([]); // @phpstan-ignore-line
    }

    public function testSendBlob() : void
    {
        $this->createDummyData();
        self::assertSame(
            'c',
            static::$database->query('SELECT `c2` FROM `t1` WHERE `c1` = 3')->fetch()->c2
        );
        $prepared = static::$database->prepare('UPDATE `t1` SET `c2` = ? WHERE `c1` = 3');
        $prepared->sendBlob('chunk1');
        $prepared->sendBlob('chunk2');
        self::assertSame(1, $prepared->exec());
        self::assertSame(
            'chunk1chunk2',
            static::$database->query('SELECT `c2` FROM `t1` WHERE `c1` = 3')->fetch()->c2
        );
    }
}
