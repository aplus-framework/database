<?php namespace Framework\Database\Manipulation;

use Framework\Database\Database;
use Framework\Database\Manipulation\Statements\Insert;
use Framework\Database\Manipulation\Statements\Select;
use Framework\Database\Manipulation\Statements\Update;
use Framework\Database\Manipulation\Statements\With;

/**
 * Class Manipulation.
 *
 * @see https://mariadb.com/kb/en/library/data-manipulation/
 */
class Manipulation
{
	/**
	 * @var Database
	 */
	protected $database;

	/**
	 * Manipulation constructor.
	 *
	 * @param Database $database
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	public function insert() : Insert
	{
		return new Insert($this->database);
	}

	public function select() : Select
	{
		return new Select($this->database);
	}

	public function update() : Update
	{
		return new Update($this->database);
	}

	public function with() : With
	{
		return new With($this->database);
	}
}
