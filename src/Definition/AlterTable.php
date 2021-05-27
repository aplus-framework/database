<?php namespace Framework\Database\Definition;

use Framework\Database\Definition\Table\TableDefinition;
use Framework\Database\Statement;
use InvalidArgumentException;
use LogicException;

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
		throw new LogicException('TABLE name must be set');
	}

	public function wait(int $seconds)
	{
		$this->sql['wait'] = $seconds;
		return $this;
	}

	protected function renderWait() : ?string
	{
		if ( ! isset($this->sql['wait'])) {
			return null;
		}
		if ($this->sql['wait'] < 0) {
			throw new InvalidArgumentException(
				"Invalid WAIT value: {$this->sql['wait']}"
			);
		}
		return " WAIT {$this->sql['wait']}";
	}

	public function add(callable $definition)
	{
		$this->sql['add'] = $definition;
		return $this;
	}

	protected function renderAdd() : ?string
	{
		if ( ! isset($this->sql['add'])) {
			return null;
		}
		$definition = new TableDefinition($this->database);
		$this->sql['add']($definition);
		return $definition->sql('ADD');
	}

	public function change(callable $definition)
	{
		$this->sql['change'] = $definition;
		return $this;
	}

	protected function renderChange() : ?string
	{
		if ( ! isset($this->sql['change'])) {
			return null;
		}
		$definition = new TableDefinition($this->database);
		$this->sql['change']($definition);
		return $definition->sql('CHANGE');
	}

	public function modify(callable $definition)
	{
		$this->sql['modify'] = $definition;
		return $this;
	}

	protected function renderModify() : ?string
	{
		if ( ! isset($this->sql['modify'])) {
			return null;
		}
		$definition = new TableDefinition($this->database);
		$this->sql['modify']($definition);
		return $definition->sql('MODIFY');
	}

	public function dropColumns(callable $definition)
	{
		$this->sql['drop_columns'] = $definition;
		return $this;
	}

	public function drop(callable $definition)
	{
		$this->sql['drop'] = $definition;
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
		$sql .= $this->renderAdd();
		$sql .= $this->renderChange();
		$sql .= $this->renderModify();
		return $sql;
	}

	/**
	 * Runs the ALTER TABLE statement.
	 *
	 * @return int The number of affected rows
	 */
	public function run() : int
	{
		return $this->database->exec($this->sql());
	}
}
