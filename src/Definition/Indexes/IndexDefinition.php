<?php namespace Framework\Database\Definition\Indexes;

use Framework\Database\Database;
use Framework\Database\Definition\Indexes\Keys\ForeignKey;
use Framework\Database\Definition\Indexes\Keys\FulltextKey;
use Framework\Database\Definition\Indexes\Keys\Key;
use Framework\Database\Definition\Indexes\Keys\PrimaryKey;
use Framework\Database\Definition\Indexes\Keys\SpatialKey;
use Framework\Database\Definition\Indexes\Keys\UniqueKey;

/**
 * Class IndexDefinition.
 *
 * @see https://mariadb.com/kb/en/library/create-table/#indexes
 * @see https://mariadb.com/kb/en/library/optimization-and-indexes/
 */
class IndexDefinition
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

	public function __call($method, $arguments)
	{
		if ($method === 'sql') {
			return $this->sql();
		}
		throw new \BadMethodCallException("Method not found: {$method}");
	}

	public function key(string $column, ...$columns) : Key
	{
		return $this->keys[] = new Key($this->database, $column, ...$columns);
	}

	public function primaryKey(string $column, ...$columns) : PrimaryKey
	{
		return $this->keys[] = new PrimaryKey($this->database, $column, ...$columns);
	}

	public function uniqueKey(string $column, ...$columns) : UniqueKey
	{
		return $this->keys[] = new UniqueKey($this->database, $column, ...$columns);
	}

	public function fulltextKey(string $column, ...$columns) : FulltextKey
	{
		return $this->keys[] = new FulltextKey($this->database, $column, ...$columns);
	}

	public function foreignKey(string $column, ...$columns) : ForeignKey
	{
		return $this->keys[] = new ForeignKey($this->database, $column, ...$columns);
	}

	public function spatialKey(string $column, ...$columns) : SpatialKey
	{
		return $this->keys[] = new SpatialKey($this->database, $column, ...$columns);
	}

	protected function sql() : string
	{
		$sql = [];
		foreach ($this->keys as $key) {
			$sql[] = ' ' . $key->sql();
		}
		return \implode(',' . \PHP_EOL, $sql);
	}
}
