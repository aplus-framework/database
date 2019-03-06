<?php namespace Framework\Database\Manipulation;

use Framework\Database\Database;
use Framework\Database\Manipulation\Statements\Select;

/**
 * Class Manipulation.
 *
 * @see https://mariadb.com/kb/en/library/data-manipulation/
 */
class Manipulation
{
	protected $database;

	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	public function __get($property)
	{
		if ($property === 'database') {
			return $this->database;
		}
	}

	public function select() : Select
	{
		return new Select($this);
	}
}
