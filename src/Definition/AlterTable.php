<?php namespace Framework\Database\Definition;

use Framework\Database\Statement;

/**
 * Class AlterTable.
 *
 * @see https://mariadb.com/kb/en/library/alter-table/
 */
class AlterTable extends Statement
{
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

	public function dropIndexes(callable $definition)
	{
		$this->sql['drop_indexes'] = $definition;
		return $this;
	}

	public function sql() : string
	{
		// TODO: Implement sql() method.
	}

	public function run()
	{
		// TODO: Implement run() method.
	}
}
