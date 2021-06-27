<?php declare(strict_types=1);
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition;

use Framework\Database\Statement;
use LogicException;

/**
 * Class CreateSchema.
 *
 * @see https://mariadb.com/kb/en/library/create-database/
 */
class CreateSchema extends Statement
{
	/**
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
		return ' OR REPLACE';
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
	 * @param string $schemaName
	 *
	 * @return $this
	 */
	public function schema(string $schemaName)
	{
		$this->sql['schema'] = $schemaName;
		return $this;
	}

	protected function renderSchema() : string
	{
		if (isset($this->sql['schema'])) {
			return ' ' . $this->database->protectIdentifier($this->sql['schema']);
		}
		throw new LogicException('SCHEMA name must be set');
	}

	/**
	 * @param string $charset
	 *
	 * @return $this
	 */
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

	/**
	 * @param string $collation
	 *
	 * @return $this
	 */
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
	 * @return int The number of affected rows
	 */
	public function run() : int
	{
		return $this->database->exec($this->sql());
	}
}
