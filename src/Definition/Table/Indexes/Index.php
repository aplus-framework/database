<?php namespace Framework\Database\Definition\Table\Indexes;

use Framework\Database\Database;
use Framework\Database\Definition\Table\DefinitionPart;
use LogicException;

/**
 * Class Index.
 *
 * @see https://mariadb.com/kb/en/library/getting-started-with-indexes/
 */
abstract class Index extends DefinitionPart
{
	protected Database $database;
	protected array $columns;
	protected string $type = '';
	protected ?string $name;

	public function __construct(Database $database, ?string $name, string $column, ...$columns)
	{
		$this->database = $database;
		$this->name = $name;
		$this->columns = $columns ? \array_merge([$column], $columns) : [$column];
	}

	protected function renderType() : string
	{
		if (empty($this->type)) {
			throw new LogicException('Key type is empty');
		}
		return " {$this->type}";
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
