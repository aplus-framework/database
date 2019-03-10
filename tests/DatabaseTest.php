<?php namespace Tests\Database;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
	/**
	 * @var DatabaseMock
	 */
	protected $database;

	public function setup()
	{
		$this->database = new DatabaseMock();
	}

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
}
