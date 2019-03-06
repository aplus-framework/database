<?php namespace Tests\Database;

use Framework\Database\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
	/**
	 * @var Database
	 */
	protected $database;

	public function setup()
	{
		$this->database = new Database();
	}

	public function testSample()
	{
		$this->assertEquals(
			'Framework\Database\Database::test',
			$this->database->test()
		);
	}
}
