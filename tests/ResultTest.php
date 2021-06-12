<?php namespace Tests\Database;

final class ResultTest extends TestCase
{
	protected function setUp() : void
	{
		parent::setUp();
		$this->createDummyData();
	}

	public function testNumRows() : void
	{
		$this->assertEquals(
			5,
			static::$database->query('SELECT * FROM `t1`')->numRows()
		);
		$this->assertEquals(
			2,
			static::$database->query('SELECT * FROM `t1` WHERE `c1` < 3')->numRows()
		);
		$this->assertEquals(
			0,
			static::$database->query('SELECT * FROM `t1` WHERE `c1` > 100')->numRows()
		);
	}

	public function testMoveCursor() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$this->assertEquals(1, $result->fetch()->c1);
		$this->assertEquals(2, $result->fetch()->c1);
		$result->moveCursor(1);
		$this->assertEquals(2, $result->fetch()->c1);
		$result->moveCursor(4);
		$this->assertEquals(5, $result->fetch()->c1);
		$this->expectException(\OutOfRangeException::class);
		$this->expectExceptionMessage('Invalid cursor offset: 5');
		$result->moveCursor(5);
	}

	public function testMoveCursorLessThanZero() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$this->expectException(\OutOfRangeException::class);
		$this->expectExceptionMessage('Invalid cursor offset: -1');
		$result->moveCursor(-1);
	}

	public function testFetchRow() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$this->assertEquals(1, $result->fetchRow(0)->c1);
		$this->assertEquals(4, $result->fetchRow(3)->c1);
	}

	public function testFetchArrayRow() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$this->assertEquals(1, $result->fetchArrayRow(0)['c1']);
		$this->assertEquals(4, $result->fetchArrayRow(3)['c1']);
	}

	public function testFetchClass() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$result->setFetchClass(ResultEntity::class, 'a', 'b');
		$row = $result->fetch();
		$this->assertInstanceOf(ResultEntity::class, $row);
		$this->assertEquals('a', $row->p1);
		$this->assertEquals('b', $row->p2);
		$rows = $result->fetchAll();
		$this->assertInstanceOf(ResultEntity::class, $rows[0]);
		$this->assertInstanceOf(ResultEntity::class, $rows[1]);
	}

	public function testFetch() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$this->assertEquals(1, $result->fetch()->c1);
		$this->assertEquals(2, $result->fetch()->c1);
		$this->assertEquals('c', $result->fetch()->c2);
		$this->assertEquals('d', $result->fetch()->c2);
		$this->assertEquals('e', $result->fetch()->c2);
		$this->assertNull($result->fetch());
	}

	public function testFetchAll() : void
	{
		$all = static::$database->query('SELECT * FROM `t1`')->fetchAll();
		$this->assertCount(5, $all);
		$this->assertEquals(1, $all[0]->c1);
		$this->assertEquals(2, $all[1]->c1);
		$this->assertEquals('c', $all[2]->c2);
	}

	public function testFetchArray() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$this->assertEquals(1, $result->fetchArray()['c1']);
		$this->assertEquals(2, $result->fetchArray()['c1']);
		$this->assertEquals('c', $result->fetchArray()['c2']);
		$this->assertEquals('d', $result->fetchArray()['c2']);
		$this->assertEquals('e', $result->fetchArray()['c2']);
		$this->assertNull($result->fetchArray());
	}

	public function testFetchArrayAll() : void
	{
		$all = static::$database->query('SELECT * FROM `t1`')->fetchArrayAll();
		$this->assertCount(5, $all);
		$this->assertEquals(1, $all[0]['c1']);
		$this->assertEquals(2, $all[1]['c1']);
		$this->assertEquals('c', $all[2]['c2']);
	}

	public function testFetchFields() : void
	{
		$fields = static::$database->query('SELECT * FROM `t1`')->fetchFields();
		$this->assertEquals('c1', $fields[0]->name);
		$this->assertEquals('LONG', $fields[0]->type_name);
		$this->assertTrue($fields[0]->pri_key_flag);
		$this->assertTrue($fields[0]->auto_increment_flag);
		$this->assertEquals('c2', $fields[1]->name);
		$this->assertEquals('VAR_STRING', $fields[1]->type_name);
		$this->assertFalse($fields[1]->pri_key_flag);
		$this->assertFalse($fields[1]->auto_increment_flag);
	}
}
