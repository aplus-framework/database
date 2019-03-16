<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\CreateTable;
use Tests\Database\TestCase;

class CreateTableTest extends TestCase
{
	/**
	 * @var CreateTable
	 */
	protected $createTable;

	protected function setUp()
	{
		$this->createTable = new CreateTable($this->database);
	}

	public function testEmptyTable()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('TABLE name must be set');
		$this->createTable->sql();
	}
}
