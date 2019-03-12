<?php namespace Framework\Database\Definition;

use Framework\Database\Database;
use Framework\Database\Definition\Statements\CreateSchema;
use Framework\Database\Definition\Statements\DropSchema;

/**
 * Class Definition.
 *
 * @see https://mariadb.com/kb/en/library/data-definition/
 */
class Definition
{
	/**
	 * @var Database
	 */
	protected $database;

	/**
	 * Definition constructor.
	 *
	 * @param Database $database
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	public function createSchema() : CreateSchema
	{
		return new CreateSchema($this->database);
	}

	public function dropSchema() : DropSchema
	{
		return new DropSchema($this->database);
	}
}
