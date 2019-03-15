<?php namespace Framework\Database\Definition;

use Framework\Database\Definition\DataTypes\ColumnDefinition;
use Framework\Database\Statement;

/**
 * Class CreateTable.
 *
 * @see https://mariadb.com/kb/en/library/create-table/
 */
class CreateTable extends Statement
{
	public function orReplace()
	{
		$this->sql['or_replace'] = true;
		return $this;
	}

	protected function renderOrReplace() : ?string
	{
		if ( ! isset($this->sql['or_replace'])) {
			return null;
		}
		return ' OR REPLACE';
	}

	public function temporary()
	{
		$this->sql['temporary'] = true;
		return $this;
	}

	protected function renderTemporary() : ?string
	{
		if ( ! isset($this->sql['temporary'])) {
			return null;
		}
		return ' TEMPORARY';
	}

	public function ifNotExists()
	{
		$this->sql['if_not_exists'] = true;
		return $this;
	}

	protected function renderIfNotExists() : ?string
	{
		if ( ! isset($this->sql['if_not_exists'])) {
			return null;
		}
		if (isset($this->sql['or_replace'])) {
			throw new \LogicException(
				'Clauses OR REPLACE and IF NOT EXISTS can not be used together'
			);
		}
		return ' IF NOT EXISTS';
	}

	public function table(string $schema_name)
	{
		$this->sql['table'] = $schema_name;
		return $this;
	}

	protected function renderTable() : string
	{
		if (isset($this->sql['table'])) {
			return ' ' . $this->database->protectIdentifier($this->sql['table']);
		}
		throw new \LogicException('TABLE name must be set');
	}

	public function columns(callable $definition)
	{
		$this->sql['columns'] = $definition;
	}

	protected function renderColumns() : string
	{
		if ( ! isset($this->sql['columns'])) {
			throw new \LogicException('Columns must be defined');
		}
		$definition = new ColumnDefinition($this->database);
		$this->sql['columns']($definition);
		return $definition;
	}

	public function sql() : string
	{
		$sql = 'CREATE' . $this->renderOrReplace() . $this->renderTemporary();
		$sql .= ' TABLE' . $this->renderIfNotExists();
		$sql .= $this->renderTable() . ' (' . \PHP_EOL;
		$sql .= $this->renderColumns() . ')';
		return $sql;
	}

	/**
	 * Runs the CREATE TABLE statement.
	 *
	 * @return int The number of affected rows
	 */
	public function run() : int
	{
		return $this->database->exec($this->sql());
	}
}
