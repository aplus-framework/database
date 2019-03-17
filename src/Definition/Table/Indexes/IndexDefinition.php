<?php namespace Framework\Database\Definition\Table\Indexes;

use Framework\Database\Database;
use Framework\Database\Definition\Table\DefinitionPart;
use Framework\Database\Definition\Table\Indexes\Keys\ForeignKey;
use Framework\Database\Definition\Table\Indexes\Keys\FulltextKey;
use Framework\Database\Definition\Table\Indexes\Keys\Key;
use Framework\Database\Definition\Table\Indexes\Keys\PrimaryKey;
use Framework\Database\Definition\Table\Indexes\Keys\SpatialKey;
use Framework\Database\Definition\Table\Indexes\Keys\UniqueKey;

/**
 * Class IndexDefinition.
 *
 * @see https://mariadb.com/kb/en/library/create-table/#indexes
 * @see https://mariadb.com/kb/en/library/optimization-and-indexes/
 */
class IndexDefinition extends DefinitionPart
{
	protected $database;
	protected $index;

	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	public function key(string $column, ...$columns) : Key
	{
		return $this->index = new Key($this->database, $column, ...$columns);
	}

	public function primaryKey(string $column, ...$columns) : PrimaryKey
	{
		return $this->index = new PrimaryKey($this->database, $column, ...$columns);
	}

	public function uniqueKey(string $column, ...$columns) : UniqueKey
	{
		return $this->index = new UniqueKey($this->database, $column, ...$columns);
	}

	public function fulltextKey(string $column, ...$columns) : FulltextKey
	{
		return $this->index = new FulltextKey($this->database, $column, ...$columns);
	}

	public function foreignKey(string $column, ...$columns) : ForeignKey
	{
		return $this->index = new ForeignKey($this->database, $column, ...$columns);
	}

	public function spatialKey(string $column, ...$columns) : SpatialKey
	{
		return $this->index = new SpatialKey($this->database, $column, ...$columns);
	}

	protected function sql() : string
	{
		return $this->index->sql();
	}
}
