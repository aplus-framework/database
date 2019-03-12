<?php namespace Framework\Database\Definition\Statements;

/**
 * Class CreateSchema.
 *
 * @see https://mariadb.com/kb/en/library/create-database/
 */
class CreateSchema extends Statement
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

	public function schema(string $schema_name)
	{
		$this->sql['schema'] = $schema_name;
		return $this;
	}

	protected function renderSchema() : string
	{
		if (isset($this->sql['schema'])) {
			return ' ' . $this->database->protectIdentifier($this->sql['schema']);
		}
		throw new \LogicException('SCHEMA name must be set');
	}

	public function charset(string $charset)
	{
		$this->sql['charset'] = $charset;
		return $this;
	}

	protected function renderCharset() : ?string
	{
		if ( ! isset($this->sql['charset'])) {
			return null;
		}
		$charset = $this->database->quote($this->sql['charset']);
		return " CHARACTER SET = {$charset}";
	}

	public function collate(string $collation)
	{
		$this->sql['collation'] = $collation;
		return $this;
	}

	protected function renderCollate() : ?string
	{
		if ( ! isset($this->sql['collation'])) {
			return null;
		}
		$collation = $this->database->quote($this->sql['collation']);
		return " COLLATE = {$collation}";
	}

	public function sql() : string
	{
		$sql = 'CREATE' . $this->renderOrReplace();
		$sql .= ' SCHEMA' . $this->renderIfNotExists();
		$sql .= $this->renderSchema() . \PHP_EOL;
		if ($part = $this->renderCharset()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderCollate()) {
			$sql .= $part . \PHP_EOL;
		}
		return $sql;
	}

	/**
	 * Runs the CREATE SCHEMA statement.
	 *
	 * @return false|int The number of affected rows or false if an error occurs
	 */
	public function run()
	{
		return $this->database->pdo->exec($this->sql());
	}
}
