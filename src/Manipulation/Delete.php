<?php namespace Framework\Database\Manipulation;

use Closure;
use InvalidArgumentException;

/**
 * Class Delete.
 *
 * @see https://mariadb.com/kb/en/library/delete/
 */
class Delete extends Statement
{
	use Traits\Join;
	use Traits\OrderBy;
	use Traits\Where;

	public const OPT_LOW_PRIORITY = 'LOW_PRIORITY';
	public const OPT_QUICK = 'QUICK';
	public const OPT_IGNORE = 'IGNORE';

	protected function renderOptions() : ?string
	{
		if ( ! $this->hasOptions()) {
			return null;
		}
		$options = $this->sql['options'];
		foreach ($options as &$option) {
			$input = $option;
			$option = \strtoupper($option);
			if ( ! \in_array($option, [
				static::OPT_LOW_PRIORITY,
				static::OPT_QUICK,
				static::OPT_IGNORE,
			], true)) {
				throw new InvalidArgumentException("Invalid option: {$input}");
			}
		}
		unset($option);
		$options = \implode(' ', $options);
		return " {$options}";
	}

	/**
	 * Sets the table references.
	 *
	 * @param array|Closure|string $reference
	 * @param mixed                ...$references
	 *
	 * @return $this
	 */
	public function table($reference, ...$references)
	{
		$this->sql['table'] = [];
		$references = $this->mergeExpressions($reference, $references);
		foreach ($references as $reference) {
			$this->sql['table'][] = $reference;
		}
		return $this;
	}

	protected function renderTable() : ?string
	{
		if ( ! isset($this->sql['table'])) {
			return null;
		}
		$tables = [];
		foreach ($this->sql['table'] as $table) {
			$tables[] = $this->renderAliasedIdentifier($table);
		}
		return ' ' . \implode(', ', $tables);
	}

	/**
	 * Sets the LIMIT clause.
	 *
	 * @param int $limit
	 *
	 * @see https://mariadb.com/kb/en/library/limit/
	 *
	 * @return $this
	 */
	public function limit(int $limit)
	{
		return $this->setLimit($limit);
	}

	public function sql() : string
	{
		$sql = 'DELETE' . \PHP_EOL;
		$part = $this->renderOptions();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$part = $this->renderTable();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$part = $this->renderFrom();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$part = $this->renderJoin();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$part = $this->renderWhere();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$part = $this->renderOrderBy();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$part = $this->renderLimit();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		return $sql;
	}

	/**
	 * Runs the DELETE statement.
	 *
	 * @return int The number of affected rows
	 */
	public function run() : int
	{
		return $this->database->exec($this->sql());
	}
}
