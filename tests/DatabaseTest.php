<?php namespace Tests\Database;

use Framework\Database\Definition\CreateSchema;
use Framework\Database\Definition\DropSchema;
use Framework\Database\Driver\PreparedStatement;
use Framework\Database\Driver\Result;
use Framework\Database\Manipulation\Insert;
use Framework\Database\Manipulation\LoadData;
use Framework\Database\Manipulation\Select;
use Framework\Database\Manipulation\Update;
use Framework\Database\Manipulation\With;

class DatabaseTest extends TestCase
{
	public function testProtectIdentifier()
	{
		$this->assertEquals('`foo`', $this->database->protectIdentifier('foo'));
		$this->assertEquals('```foo```', $this->database->protectIdentifier('`foo`'));
		$this->assertEquals('`foo ``bar`', $this->database->protectIdentifier('foo `bar'));
		$this->assertEquals('`foo`.`bar`', $this->database->protectIdentifier('foo.bar'));
		$this->assertEquals('`foo`.*', $this->database->protectIdentifier('foo.*'));
		$this->assertEquals('```foo```.*', $this->database->protectIdentifier('`foo`.*'));
		$this->assertEquals('`db`.`table`.*', $this->database->protectIdentifier('db.table.*'));
	}

	public function testQuote()
	{
		$this->assertEquals(0, $this->database->quote(0));
		$this->assertEquals(1, $this->database->quote(1));
		$this->assertEquals(-1, $this->database->quote(-1));
		$this->assertEquals(.0, $this->database->quote(.0));
		$this->assertEquals(1.1, $this->database->quote(1.1));
		$this->assertEquals(-1.1, $this->database->quote(-1.1));
		$this->assertEquals("'0'", $this->database->quote('0'));
		$this->assertEquals("'-1'", $this->database->quote('-1'));
		$this->assertEquals("'abc'", $this->database->quote('abc'));
		$this->assertEquals("'ab\\'c'", $this->database->quote("ab'c"));
		$this->assertEquals("'ab\\'cd\\'\\''", $this->database->quote("ab'cd''"));
		$this->assertEquals('\'ab\"cd\"\"\'', $this->database->quote('ab"cd""'));
		$this->assertEquals('NULL', $this->database->quote(null));
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid value type: array');
		$this->database->quote([]);
	}

	public function testDefinitionInstances()
	{
		$this->assertInstanceOf(CreateSchema::class, $this->database->createSchema());
		$this->assertInstanceOf(DropSchema::class, $this->database->dropSchema());
	}

	public function testManipulationInstances()
	{
		$this->assertInstanceOf(Insert::class, $this->database->insert());
		$this->assertInstanceOf(LoadData::class, $this->database->loadData());
		$this->assertInstanceOf(Select::class, $this->database->select());
		$this->assertInstanceOf(Update::class, $this->database->update());
		$this->assertInstanceOf(With::class, $this->database->with());
	}

	public function testExec()
	{
		$this->createDummyData();
		$this->assertEquals(1, $this->database->exec(
			'INSERT INTO `t1` SET `c2` = "a"'
		));
		$this->assertEquals(3, $this->database->exec(
			'INSERT INTO `t1` (`c2`) VALUES ("a"),("a"),("a")'
		));
		$this->assertEquals(9, $this->database->exec('SELECT * FROM `t1`'));
	}

	public function testQuery()
	{
		$this->createDummyData();
		$this->assertInstanceOf(Result::class, $this->database->query('SELECT * FROM `t1`'));
	}

	public function testQueryNoResult()
	{
		$this->createDummyData();
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage(
			'Statement does not return result: INSERT INTO `t1` SET `c2` = "a"'
		);
		$this->database->query('INSERT INTO `t1` SET `c2` = "a"');
	}

	public function testPrepare()
	{
		$this->assertInstanceOf(
			PreparedStatement::class,
			$this->database->prepare('SELECT * FROM `t1` WHERE `c1` = ?')
		);
	}

	public function testInsertId()
	{
		$this->createDummyData();
		$this->assertEquals(1, $this->database->insertId());
		$this->database->exec(
			'INSERT INTO `t1` SET `c2` = "a"'
		);
		$this->assertEquals(6, $this->database->insertId());
		$this->database->exec(
			'INSERT INTO `t1` (`c2`) VALUES ("a"),("a"),("a")'
		);
		$this->assertEquals(7, $this->database->insertId());
		$this->database->exec(
			'INSERT INTO `t1` SET `c2` = "a"'
		);
		$this->assertEquals(10, $this->database->insertId());
	}
}
