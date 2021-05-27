<?php namespace Tests\Database;

use Framework\Database\Result;

class PreparedStatementTest extends TestCase
{
	public function testExec()
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('INSERT INTO `t1` SET `c2` = "f"');
		$this->assertIsInt($prepared->exec());
		$this->assertEquals(1, $prepared->exec());
		$prepared = static::$database->prepare('INSERT INTO `t1` (`c2`) VALUES ("h"), ("i")');
		$this->assertIsInt($prepared->exec());
		$this->assertEquals(2, $prepared->exec());
	}

	public function testExecWithBinds()
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('INSERT INTO `t1` SET `c2` = ?');
		$this->assertIsInt($prepared->exec('f'));
		$this->assertEquals(1, $prepared->exec('g'));
		$prepared = static::$database->prepare('INSERT INTO `t1` (`c2`) VALUES (?), (?), ("l")');
		$this->assertIsInt($prepared->exec('h', 'i'));
		$this->assertEquals(3, $prepared->exec('j', 'k'));
		$prepared = static::$database->prepare(
			'INSERT INTO `t1` (`c2`) VALUES (?), (?), (?), (?), (?)'
		);
		$this->assertEquals(5, $prepared->exec('a', 1, false, true, null));
	}

	public function testExecResult()
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('SELECT * FROM `t1`');
		$this->assertIsInt($prepared->exec());
		$this->assertEquals(-1, $prepared->exec());
		$prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c1` < ?');
		$this->assertIsInt($prepared->exec(4));
		$this->assertEquals(-1, $prepared->exec(4));
	}

	public function testQuery()
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('SELECT * FROM `t1`');
		$this->assertInstanceOf(Result::class, $prepared->query());
		$this->assertEquals('a', $prepared->query()->fetch()->c2);
		$this->assertEquals('a', $prepared->query()->fetch()->c2);
		$result = $prepared->query();
		$this->assertEquals('a', $result->fetch()->c2);
		$this->assertEquals('b', $result->fetch()->c2);
	}

	public function testQueryWithBinds()
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c1` = ?');
		$this->assertInstanceOf(Result::class, $prepared->query(1));
		$this->assertEquals('b', $prepared->query(2)->fetch()->c2);
		$this->assertEquals('e', $prepared->query(5)->fetch()->c2);
		$prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c2` = ?');
		$this->assertInstanceOf(Result::class, $prepared->query('a'));
		$this->assertEquals(2, $prepared->query('b')->fetch()->c1);
		$this->assertEquals(5, $prepared->query('e')->fetch()->c1);
	}

	public function testBindParams()
	{
		$this->createDummyData();
		$prepared = static::$database->prepare('SELECT * FROM `t1` WHERE `c1` = ?');
		$this->assertInstanceOf(Result::class, $prepared->query(1));
		$this->assertInstanceOf(Result::class, $prepared->query(1.1));
		$this->assertInstanceOf(Result::class, $prepared->query('a'));
		$this->assertInstanceOf(Result::class, $prepared->query(true));
		$this->assertInstanceOf(Result::class, $prepared->query(null));
		$this->expectException(\TypeError::class);
		$prepared->query([]);
	}

	public function testSendBlob()
	{
		$this->createDummyData();
		$this->assertEquals(
			'c',
			static::$database->query('SELECT `c2` FROM `t1` WHERE `c1` = 3')->fetch()->c2
		);
		$prepared = static::$database->prepare('UPDATE `t1` SET `c2` = ? WHERE `c1` = 3');
		$prepared->sendBlob('chunk1');
		$prepared->sendBlob('chunk2');
		$this->assertEquals(1, $prepared->exec());
		$this->assertEquals(
			'chunk1chunk2',
			static::$database->query('SELECT `c2` FROM `t1` WHERE `c1` = 3')->fetch()->c2
		);
	}
}
