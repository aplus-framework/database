<?php namespace Framework\Database\Definition;

use Framework\Database\Statement;

/**
 * Class AlterSchema.
 *
 * @see https://mariadb.com/kb/en/library/alter-database/
 */
class AlterSchema extends Statement
{
	public function schema(string $schema_name)
	{
		$this->sql['schema'] = $schema_name;
		return $this;
	}

	protected function renderSchema() : ?string
	{
		if ( ! isset($this->sql['schema'])) {
			return null;
		}
		$schema = $this->sql['schema'];
		if (isset($this->sql['upgrade'])) {
			$schema = "#mysql50#{$schema}";
		}
		return ' ' . $this->database->protectIdentifier($schema);
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

	public function upgrade()
	{
		$this->sql['upgrade'] = true;
		return $this;
	}

	protected function renderUpgrade() : ?string
	{
		if ( ! isset($this->sql['upgrade'])) {
			return null;
		}
		if (isset($this->sql['charset']) || isset($this->sql['collation'])) {
			throw new \LogicException(
				'UPGRADE DATA DIRECTORY NAME can not be used with CHARACTER SET or COLLATE'
			);
		}
		return ' UPGRADE DATA DIRECTORY NAME';
	}

	protected function checkSpecifications() : void
	{
		if ( ! isset($this->sql['charset'])
			&& ! isset($this->sql['collation'])
			&& ! isset($this->sql['upgrade'])
		) {
			throw new \LogicException(
				'ALTER SCHEMA must have a specification'
			);
		}
	}

	public function sql() : string
	{
		$sql = 'ALTER SCHEMA';
		$sql .= $this->renderSchema() . \PHP_EOL;
		if ($part = $this->renderCharset()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderCollate()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderUpgrade()) {
			$sql .= $part . \PHP_EOL;
		}
		$this->checkSpecifications();
		return $sql;
	}

	/**
	 * Runs the ALTER SCHEMA statement.
	 *
	 * @return int The number of affected rows
	 */
	public function run() : int
	{
		return $this->database->exec($this->sql());
	}
}
