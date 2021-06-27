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

final class ResultTest extends TestCase
{
	protected function setUp() : void
	{
		parent::setUp();
		$this->createDummyData();
	}

	protected function expectFreeResult() : Result
	{
		$result = static::$database->query('SELECT * FROM `t1`', false);
		$result->free();
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Result is already free');
		return $result;
	}

	public function testNumRows() : void
	{
		self::assertSame(
			5,
			static::$database->query('SELECT * FROM `t1`')->numRows()
		);
		self::assertSame(
			2,
			static::$database->query('SELECT * FROM `t1` WHERE `c1` < 3')->numRows()
		);
		self::assertSame(
			0,
			static::$database->query('SELECT * FROM `t1` WHERE `c1` > 100')->numRows()
		);
	}

	public function testNumRowsFree() : void
	{
		$this->expectFreeResult()->numRows();
	}

	public function testMoveCursor() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		self::assertSame(1, $result->fetch()->c1);
		self::assertSame(2, $result->fetch()->c1);
		$result->moveCursor(1);
		self::assertSame(2, $result->fetch()->c1);
		$result->moveCursor(4);
		self::assertSame(5, $result->fetch()->c1);
		$this->expectException(\OutOfRangeException::class);
		$this->expectExceptionMessage('Invalid cursor offset: 5');
		$result->moveCursor(5);
	}

	public function testMoveCursorFree() : void
	{
		$this->expectFreeResult()->moveCursor(0);
	}

	public function testMoveCursorLessThanZero() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$this->expectException(\OutOfRangeException::class);
		$this->expectExceptionMessage('Invalid cursor offset: -1');
		$result->moveCursor(-1);
	}

	public function testUnbufferMoveCursor() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`', false);
		$result->fetch();
		self::assertCount(4, $result->fetchAll());
		self::assertCount(0, $result->fetchAll());
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'Cursor cannot be moved on unbuffered results'
		);
		$result->moveCursor(0);
	}

	public function testFetchRow() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		self::assertSame(1, $result->fetchRow(0)->c1);
		self::assertSame(4, $result->fetchRow(3)->c1);
	}

	public function testFetchRowFree() : void
	{
		$this->expectFreeResult()->fetchRow(0);
	}

	public function testFetchArrayRow() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		self::assertSame(1, $result->fetchArrayRow(0)['c1']);
		self::assertSame(4, $result->fetchArrayRow(3)['c1']);
	}

	public function testFetchArrayRowFree() : void
	{
		$this->expectFreeResult()->fetchArrayRow(0);
	}

	public function testFetchClass() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$result->setFetchClass(ResultEntity::class, 'a', 'b');
		$row = $result->fetch();
		self::assertInstanceOf(ResultEntity::class, $row);
		self::assertSame('a', $row->p1);
		self::assertSame('b', $row->p2);
		$rows = $result->fetchAll();
		self::assertInstanceOf(ResultEntity::class, $rows[0]);
		self::assertInstanceOf(ResultEntity::class, $rows[1]);
	}

	public function testFetch() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		self::assertSame(1, $result->fetch()->c1);
		self::assertSame(2, $result->fetch()->c1);
		self::assertSame('c', $result->fetch()->c2);
		self::assertSame('d', $result->fetch()->c2);
		self::assertSame('e', $result->fetch()->c2);
		self::assertNull($result->fetch());
	}

	public function testFetchFree() : void
	{
		$this->expectFreeResult()->fetch();
	}

	public function testFetchAll() : void
	{
		$all = static::$database->query('SELECT * FROM `t1`')->fetchAll();
		self::assertCount(5, $all);
		self::assertSame(1, $all[0]->c1);
		self::assertSame(2, $all[1]->c1);
		self::assertSame('c', $all[2]->c2);
	}

	public function testFetchAllFree() : void
	{
		$this->expectFreeResult()->fetchAll();
	}

	public function testFetchAllRest() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$result->fetch();
		$result->fetch();
		$all = $result->fetchAll();
		self::assertCount(3, $all);
		self::assertSame(3, $all[0]->c1);
		self::assertSame(4, $all[1]->c1);
		self::assertSame('e', $all[2]->c2);
	}

	public function testFetchArray() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		self::assertSame(1, $result->fetchArray()['c1']);
		self::assertSame(2, $result->fetchArray()['c1']);
		self::assertSame('c', $result->fetchArray()['c2']);
		self::assertSame('d', $result->fetchArray()['c2']);
		self::assertSame('e', $result->fetchArray()['c2']);
		self::assertNull($result->fetchArray());
	}

	public function testFetchArrayFree() : void
	{
		$this->expectFreeResult()->fetchArray();
	}

	public function testFetchArrayAll() : void
	{
		$all = static::$database->query('SELECT * FROM `t1`')->fetchArrayAll();
		self::assertCount(5, $all);
		self::assertSame(1, $all[0]['c1']);
		self::assertSame(2, $all[1]['c1']);
		self::assertSame('c', $all[2]['c2']);
	}

	public function testFetchArrayAllFree() : void
	{
		$this->expectFreeResult()->fetchArrayAll();
	}

	public function testFetchArrayAllRest() : void
	{
		$result = static::$database->query('SELECT * FROM `t1`');
		$result->fetchArray();
		$result->fetchArray();
		$rest = $result->fetchArrayAll();
		self::assertCount(3, $rest);
		self::assertSame(3, $rest[0]['c1']);
		self::assertSame(4, $rest[1]['c1']);
		self::assertSame('e', $rest[2]['c2']);
	}

	public function testFetchFields() : void
	{
		$fields = static::$database->query('SELECT * FROM `t1`')->fetchFields();
		self::assertSame('c1', $fields[0]->name);
		self::assertSame('long', $fields[0]->typeName);
		self::assertSame(0, $fields[0]->maxLength);
		self::assertTrue($fields[0]->flagPriKey);
		self::assertTrue($fields[0]->flagAutoIncrement);
		self::assertSame('c2', $fields[1]->name);
		self::assertSame('var_string', $fields[1]->typeName);
		self::assertSame(0, $fields[0]->maxLength);
		self::assertFalse($fields[1]->flagPriKey);
		self::assertFalse($fields[1]->flagAutoIncrement);
		$this->expectException(\Error::class);
		$this->expectExceptionMessage(
			'Undefined property: Framework\Database\Result\Field::$unknown'
		);
		$fields[1]->unknown; // @phpstan-ignore-line
	}

	public function testFetchFieldsFree() : void
	{
		$this->expectFreeResult()->fetchFields();
	}

	public function testBuffer() : void
	{
		self::assertTrue(
			static::$database->query('SELECT * FROM `t1`')->isBuffered()
		);
		self::assertFalse(
			static::$database->query('SELECT * FROM `t1`', false)->isBuffered()
		);
	}
}
