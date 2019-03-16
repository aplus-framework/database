<?php namespace Framework\Database\Definition\Indexes;

use Framework\Database\Database;

/**
 * Class Index.
 *
 * @see https://mariadb.com/kb/en/library/getting-started-with-indexes/
 */
abstract class Index
{
	/**
	 * @var Database
	 */
	protected $database;
	/**
	 * @var array
	 */
	protected $columns;
	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var string|null
	 */
	protected $name;

	public function __construct(Database $database, string $column, ...$columns)
	{
		$this->database = $database;
		$this->columns = $columns ? \array_merge([$column], $columns) : [$column];
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

	protected function renderType() : string
	{
		if ( ! isset($this->type)) {
			throw new \LogicException('Key type is empty');
		}
		return ' ' . $this->type;
	}

	public function name(string $name)
	{
		$this->name = $name;
		return $this;
	}

	protected function renderName() : ?string
	{
		if ($this->name === null) {
			return null;
		}
		return ' ' . $this->database->protectIdentifier($this->name);
	}

	protected function renderColumns() : string
	{
		$columns = [];
		foreach ($this->columns as $column) {
			$columns[] = $this->database->protectIdentifier($column);
		}
		$columns = \implode(', ', $columns);
		return " ({$columns})";
	}

	protected function renderTypeAttributes() : ?string
	{
		return null;
	}

	protected function sql() : string
	{
		$sql = $this->renderType();
		$sql .= $this->renderName();
		$sql .= $this->renderColumns();
		$sql .= $this->renderTypeAttributes();
		return $sql;
	}
}
