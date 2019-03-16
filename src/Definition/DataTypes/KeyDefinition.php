<?php namespace Framework\Database\Definition\DataTypes;

use Framework\Database\Database;
use Framework\Database\Definition\DataTypes\Keys\ForeignKey;
use Framework\Database\Definition\DataTypes\Keys\FulltextKey;
use Framework\Database\Definition\DataTypes\Keys\IndexKey;
use Framework\Database\Definition\DataTypes\Keys\PrimaryKey;
use Framework\Database\Definition\DataTypes\Keys\SpatialKey;
use Framework\Database\Definition\DataTypes\Keys\UniqueKey;

/**
 * Class KeyDefinition.
 *
 * @see https://mariadb.com/kb/en/library/create-table/#indexes
 */
class KeyDefinition
{
	protected $database;
	protected $keys = [];

	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	public function __toString()
	{
		return $this->sql();
	}

	public function index(string $column, ...$columns) : IndexKey
	{
		return $this->keys[] = new IndexKey($this->database, $column, ...$columns);
	}

	public function primary(string $column, ...$columns) : PrimaryKey
	{
		return $this->keys[] = new PrimaryKey($this->database, $column, ...$columns);
	}

	public function unique(string $column, ...$columns) : UniqueKey
	{
		return $this->keys[] = new UniqueKey($this->database, $column, ...$columns);
	}

	public function fulltext(string $column, ...$columns) : FulltextKey
	{
		return $this->keys[] = new FulltextKey($this->database, $column, ...$columns);
	}

	public function foreign(string $column, ...$columns) : ForeignKey
	{
		return $this->keys[] = new ForeignKey($this->database, $column, ...$columns);
	}

	public function spatial(string $column, ...$columns) : SpatialKey
	{
		return $this->keys[] = new SpatialKey($this->database, $column, ...$columns);
	}

	protected function sql() : string
	{
		$sql = [];
		foreach ($this->keys as $key) {
			$sql[] = ' ' . $key;
		}
		return \implode(',' . \PHP_EOL, $sql);
	}
}
