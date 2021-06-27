<?php
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
use InvalidArgumentException;
use LogicException;

/**
 * Class DropTable.
 *
 * @see https://mariadb.com/kb/en/library/drop-table/
 */
class DropTable extends Statement
{
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
	public function ifExists()
	{
		$this->sql['if_exists'] = true;
		return $this;
	}

	protected function renderIfExists() : ?string
	{
		if ( ! isset($this->sql['if_exists'])) {
			return null;
		}
		return ' IF EXISTS';
	}

	/**
	 * @param string $comment
	 *
	 * @return $this
	 */
	public function commentToSave(string $comment)
	{
		$this->sql['comment'] = $comment;
		return $this;
	}

	protected function renderCommentToSave() : ?string
	{
		if ( ! isset($this->sql['comment'])) {
			return null;
		}
		$comment = \strtr($this->sql['comment'], ['*/' => '* /']);
		return " /* {$comment} */";
	}

	/**
	 * @param string $table
	 * @param string ...$tables
	 *
	 * @return $this
	 */
	public function table(string $table, string ...$tables)
	{
		$this->sql['tables'] = $tables ? \array_merge([$table], $tables) : [$table];
		return $this;
	}

	protected function renderTables() : string
	{
		if ( ! isset($this->sql['tables'])) {
			throw new LogicException('Table names can not be empty');
		}
		$tables = $this->sql['tables'];
		foreach ($tables as &$table) {
			$table = $this->database->protectIdentifier($table);
		}
		unset($table);
		$tables = \implode(', ', $tables);
		return " {$tables}";
	}

	/**
	 * @param int $seconds
	 *
	 * @return $this
	 */
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
			throw new InvalidArgumentException(
				"Invalid WAIT value: {$this->sql['wait']}"
			);
		}
		return " WAIT {$this->sql['wait']}";
	}

	public function sql() : string
	{
		$sql = 'DROP' . $this->renderTemporary();
		$sql .= ' TABLE' . $this->renderIfExists();
		$sql .= $this->renderCommentToSave();
		$sql .= $this->renderTables() . \PHP_EOL;
		if ($part = $this->renderWait()) {
			$sql .= $part . \PHP_EOL;
		}
		return $sql;
	}

	/**
	 * Runs the DROP TABLE statement.
	 *
	 * @return int The number of affected rows
	 */
	public function run() : int
	{
		return $this->database->exec($this->sql());
	}
}
