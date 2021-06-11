<?php namespace Framework\Database\Definition;

use Framework\Database\Definition\Table\TableDefinition;
use Framework\Database\Statement;
use LogicException;

/**
 * Class CreateTable.
 *
 * @see https://mariadb.com/kb/en/library/create-table/
 */
class CreateTable extends Statement
{
	/**
	 * Adds a OR REPLACE part.
	 *
	 * WARNING: This feature is MariaDB only. It is not compatible with MySQL.
	 *
	 * @return $this
	 */
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
		if (isset($this->sql['if_not_exists'])) {
			throw new LogicException(
				'Clauses OR REPLACE and IF NOT EXISTS can not be used together'
			);
		}
		return ' OR REPLACE';
	}

	/**
	 * @return $this
	 */
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

	/**
	 * @return $this
	 */
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
			throw new LogicException(
				'Clauses OR REPLACE and IF NOT EXISTS can not be used together'
			);
		}
		return ' IF NOT EXISTS';
	}

	/**
	 * @param string $tableName
	 *
	 * @return $this
	 */
	public function table(string $tableName)
	{
		$this->sql['table'] = $tableName;
		return $this;
	}

	protected function renderTable() : string
	{
		if (isset($this->sql['table'])) {
			return ' ' . $this->database->protectIdentifier($this->sql['table']);
		}
		throw new LogicException('TABLE name must be set');
	}

	/**
	 * @param callable $definition
	 *
	 * @return $this
	 */
	public function definition(callable $definition)
	{
		$this->sql['definition'] = $definition;
		return $this;
	}

	protected function renderDefinition() : string
	{
		if ( ! isset($this->sql['definition'])) {
			throw new LogicException('Table definition must be set');
		}
		$definition = new TableDefinition($this->database);
		$this->sql['definition']($definition);
		return $definition->sql();
	}

	public function sql() : string
	{
		$sql = 'CREATE' . $this->renderOrReplace() . $this->renderTemporary();
		$sql .= ' TABLE' . $this->renderIfNotExists();
		$sql .= $this->renderTable() . ' (' . \PHP_EOL;
		$sql .= $this->renderDefinition() . \PHP_EOL;
		$sql .= ')';
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
