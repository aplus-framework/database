<?php namespace Tests\Database;

use Framework\Database\Result;

final class PreparedStatementTest extends TestCase
{
	public function testExec() : void
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('INSERT INTO `t1` SET `c2` = "f"');
		$this->assertIsInt($prepared->exec());
		$this->assertSame(1, $prepared->exec());
		$prepared = static::$database->prepare('INSERT INTO `t1` (`c2`) VALUES ("h"), ("i")');
		$this->assertIsInt($prepared->exec());
		$this->assertSame(2, $prepared->exec());
	}

	public function testExecWithBinds() : void
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('INSERT INTO `t1` SET `c2` = ?');
		$this->assertIsInt($prepared->exec('f'));
		$this->assertSame(1, $prepared->exec('g'));
		$prepared = static::$database->prepare('INSERT INTO `t1` (`c2`) VALUES (?), (?), ("l")');
		$this->assertIsInt($prepared->exec('h', 'i'));
		$this->assertSame(3, $prepared->exec('j', 'k'));
		$prepared = static::$database->prepare(
			'INSERT INTO `t1` (`c2`) VALUES (?), (?), (?), (?), (?)'
		);
		$this->assertSame(5, $prepared->exec('a', 1, false, true, null));
	}

	public function testExecResult() : void
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('SELECT * FROM `t1`');
		$this->assertIsInt($prepared->exec());
		$this->assertSame(-1, $prepared->exec());
		$prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c1` < ?');
		$this->assertIsInt($prepared->exec(4));
		$this->assertSame(-1, $prepared->exec(4));
	}

	public function testQuery() : void
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('SELECT * FROM `t1`');
		$this->assertInstanceOf(Result::class, $prepared->query());
		$this->assertSame('a', $prepared->query()->fetch()->c2);
		$this->assertSame('a', $prepared->query()->fetch()->c2);
		$result = $prepared->query();
		$this->assertSame('a', $result->fetch()->c2);
		$this->assertSame('b', $result->fetch()->c2);
	}

	public function testQueryResultMoveCursor() : void
	{
		$this->createDummyData();
		$result = static::$database->prepare('SELECT * FROM `t1`')->query();
		$this->assertSame('a', $result->fetch()->c2);
		$this->assertSame('b', $result->fetch()->c2);
		$this->assertTrue($result->moveCursor(0));
		$this->assertSame('a', $result->fetch()->c2);
	}

	public function todo_testQueryResultMoveCursorUnbuffered() : void
	{
		$this->createDummyData();
		$result = static::$database->prepare('SELECT * FROM `t1`')->query();
		$this->assertSame('a', $result->fetch()->c2);
		$this->assertSame('b', $result->fetch()->c2);
		$this->expectException(\mysqli_sql_exception::class);
		$this->expectExceptionMessage("Commands out of sync; you can't run this command now");
		$this->assertTrue($result->moveCursor(0));
	}

	public function testQueryWithBinds() : void
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c1` = ?');
		$this->assertInstanceOf(Result::class, $prepared->query(1));
		$this->assertSame('b', $prepared->query(2)->fetch()->c2);
		$this->assertSame('e', $prepared->query(5)->fetch()->c2);
		$prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c2` = ?');
		$this->assertInstanceOf(Result::class, $prepared->query('a'));
		$this->assertSame(2, $prepared->query('b')->fetch()->c1);
		$this->assertSame(5, $prepared->query('e')->fetch()->c1);
	}

	public function testBindParams() : void
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c1` = ?');
		$this->assertInstanceOf(Result::class, $prepared->query(1));
		$this->assertInstanceOf(Result::class, $prepared->query(1.1));
		$this->assertInstanceOf(Result::class, $prepared->query('a'));
		$this->assertInstanceOf(Result::class, $prepared->query(true));
		$this->assertInstanceOf(Result::class, $prepared->query(null));
		$this->expectException(\TypeError::class);
		$prepared->query([]); // @phpstan-ignore-line
	}

	public function testSendBlob() : void
	{
		$this->createDummyData();
		$this->assertSame(
			'c',
			static::$database->query('SELECT `c2` FROM `t1` WHERE `c1` = 3')->fetch()->c2
		);
		$prepared = static::$database->prepare('UPDATE `t1` SET `c2` = ? WHERE `c1` = 3');
		$prepared->sendBlob('chunk1');
		$prepared->sendBlob('chunk2');
		$this->assertSame(1, $prepared->exec());
		$this->assertSame(
			'chunk1chunk2',
			static::$database->query('SELECT `c2` FROM `t1` WHERE `c1` = 3')->fetch()->c2
		);
	}
}
