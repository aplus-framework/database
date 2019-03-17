<?php namespace Framework\Database\Definition;

use Framework\Database\Definition\Columns\ColumnDefinition;
use Framework\Database\Definition\Indexes\IndexDefinition;
use Framework\Database\Statement;

/**
 * Class AlterTable.
 *
 * @see https://mariadb.com/kb/en/library/alter-table/
 */
class AlterTable extends Statement
{
	public function online()
	{
		$this->sql['online'] = true;
		return $this;
	}

	protected function renderOnline() : ?string
	{
		if ( ! isset($this->sql['online'])) {
			return null;
		}
		return ' ONLINE';
	}

	public function ignore()
	{
		$this->sql['ignore'] = true;
		return $this;
	}

	protected function renderIgnore() : ?string
	{
		if ( ! isset($this->sql['ignore'])) {
			return null;
		}
		return ' IGNORE';
	}

	public function table(string $table_name)
	{
		$this->sql['table'] = $table_name;
		return $this;
	}

	protected function renderTable() : string
	{
		if (isset($this->sql['table'])) {
			return ' ' . $this->database->protectIdentifier($this->sql['table']);
		}
		throw new \LogicException('TABLE name must be set');
	}

	public function wait(int $seconds)
	{
		$this->sql['wait'] = $seconds;
		return $this;
	}

	public function renderWait() : ?string
	{
		if ( ! isset($this->sql['wait'])) {
			return null;
		}
		if ($this->sql['wait'] < 0) {
			throw new \InvalidArgumentException(
				"Invalid WAIT value: {$this->sql['wait']}"
			);
		}
		return " WAIT {$this->sql['wait']}";
	}

	public function addColumns(callable $definition)
	{
		$this->sql['add_columns'] = $definition;
		return $this;
	}

	protected function renderAddColumns() : ?string
	{
		if ( ! isset($this->sql['add_columns'])) {
			return null;
		}
		$definition = new ColumnDefinition($this->database);
		$this->sql['add_columns']($definition);
		return $definition->sql('ADD COLUMN');
	}

	public function changeColumns(callable $definition)
	{
		$this->sql['change_columns'] = $definition;
		return $this;
	}

	protected function renderChangeColumns() : ?string
	{
		if ( ! isset($this->sql['change_columns'])) {
			return null;
		}
		$definition = new ColumnDefinition($this->database);
		$this->sql['change_columns']($definition);
		return $definition->sql('CHANGE COLUMN');
	}

	public function dropColumns(callable $definition)
	{
		$this->sql['drop_columns'] = $definition;
		return $this;
	}

	public function addIndexes(callable $definition)
	{
		$this->sql['add_indexes'] = $definition;
		return $this;
	}

	protected function renderAddIndexes() : ?string
	{
		if ( ! isset($this->sql['add_indexes'])) {
			return null;
		}
		$definition = new IndexDefinition($this->database);
		$this->sql['add_indexes']($definition);
		return $definition->sql('ADD');
	}

	public function dropIndexes(callable $definition)
	{
		$this->sql['drop_indexes'] = $definition;
		return $this;
	}

	public function sql() : string
	{
		$sql = 'ALTER' . $this->renderOnline() . $this->renderIgnore();
		$sql .= ' TABLE';
		$sql .= $this->renderTable() . \PHP_EOL;
		if ($part = $this->renderWait()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderAddColumns()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderChangeColumns()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderAddIndexes()) {
			$sql .= $part . \PHP_EOL;
		}
		return $sql;
	}

	public function run()
	{
		// TODO: Implement run() method.
	}
}
