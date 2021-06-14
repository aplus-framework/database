<?php namespace Framework\Database\Manipulation;

use Closure;
use InvalidArgumentException;
use LogicException;

/**
 * Class Insert.
 *
 * @see https://mariadb.com/kb/en/library/insert/
 */
class Insert extends Statement
{
	use Traits\Set;

	/**
	 * @see https://mariadb.com/kb/en/library/insert-delayed/
	 */
	public const OPT_DELAYED = 'DELAYED';
	/**
	 * Convert errors to warnings, which will not stop inserts of additional rows.
	 *
	 * @see https://mariadb.com/kb/en/library/insert-ignore/
	 */
	public const OPT_IGNORE = 'IGNORE';
	/**
	 * @see https://mariadb.com/kb/en/library/high_priority-and-low_priority/
	 */
	public const OPT_HIGH_PRIORITY = 'HIGH_PRIORITY';
	/**
	 * @see https://mariadb.com/kb/en/library/high_priority-and-low_priority/
	 */
	public const OPT_LOW_PRIORITY = 'LOW_PRIORITY';

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
				static::OPT_DELAYED,
				static::OPT_IGNORE,
				static::OPT_LOW_PRIORITY,
				static::OPT_HIGH_PRIORITY,
			], true)) {
				throw new InvalidArgumentException("Invalid option: {$input}");
			}
		}
		unset($option);
		$intersection = \array_intersect(
			$options,
			[static::OPT_DELAYED, static::OPT_HIGH_PRIORITY, static::OPT_LOW_PRIORITY]
		);
		if (\count($intersection) > 1) {
			throw new LogicException(
				'Options LOW_PRIORITY, DELAYED or HIGH_PRIORITY can not be used together'
			);
		}
		$options = \implode(' ', $options);
		return " {$options}";
	}

	/**
	 * Sets the INTO table.
	 *
	 * @param string $table Table name
	 *
	 * @return $this
	 */
	public function into(string $table)
	{
		$this->sql['into'] = $table;
		return $this;
	}

	/**
	 * Renders the "INTO $table" clause.
	 *
	 * @throws LogicException if INTO was not set
	 *
	 * @return string
	 */
	protected function renderInto() : string
	{
		if ( ! isset($this->sql['into'])) {
			throw new LogicException('INTO table must be set');
		}
		return ' INTO ' . $this->renderIdentifier($this->sql['into']);
	}

	/**
	 * Sets the INTO columns.
	 *
	 * @param string $column Column name
	 * @param string ...$columns Extra column names
	 *
	 * @return $this
	 */
	public function columns(string $column, string ...$columns)
	{
		$this->sql['columns'] = $this->mergeExpressions($column, $columns);
		return $this;
	}

	/**
	 * Renders the INTO $table "(...$columns)" part.
	 *
	 * @return string|null The imploded columns or null if none was set
	 */
	protected function renderColumns() : ?string
	{
		if ( ! isset($this->sql['columns'])) {
			return null;
		}
		$columns = [];
		foreach ($this->sql['columns'] as $column) {
			$columns[] = $this->renderIdentifier($column);
		}
		$columns = \implode(', ', $columns);
		return " ({$columns})";
	}

	/**
	 * Adds a row of values to the VALUES clause.
	 *
	 * @param Closure|float|int|string|null $value
	 * @param Closure|float|int|string|null ...$values
	 *
	 * @return $this
	 */
	public function values(
		Closure | float | int | string | null $value,
		Closure | float | int | string | null ...$values
	) {
		$this->sql['values'][] = $this->mergeExpressions($value, $values);
		return $this;
	}

	/**
	 * Renders the VALUES clause.
	 *
	 * @return string|null The VALUES part or null if none was set
	 */
	protected function renderValues() : ?string
	{
		if ( ! isset($this->sql['values'])) {
			return null;
		}
		$values = [];
		foreach ($this->sql['values'] as $value) {
			foreach ($value as &$item) {
				$item = $this->renderValue($item);
			}
			unset($item);
			$values[] = ' (' . \implode(', ', $value) . ')';
		}
		$values = \implode(',' . \PHP_EOL, $values);
		return " VALUES{$values}";
	}

	/**
	 * Sets the SELECT statement part.
	 *
	 * @param Closure $select A Closure for a subquery with a
	 * {@see \Framework\Database\Manipulation\Select} instance as param
	 *
	 * @see https://mariadb.com/kb/en/library/insert-select/
	 *
	 * @return $this
	 */
	public function select(Closure $select)
	{
		$this->sql['select'] = $select(new Select($this->database));
		return $this;
	}

	/**
	 * Renders the SELECT statement.
	 *
	 * @throws LogicException if SELECT was set with the VALUES or SET clauses
	 *
	 * @return string|null The SELECT statement or null if it was not set
	 */
	protected function renderSelect() : ?string
	{
		if ( ! isset($this->sql['select'])) {
			return null;
		}
		if (isset($this->sql['values'])) {
			throw new LogicException('SELECT statement is not allowed when VALUES is set');
		}
		if (isset($this->sql['set'])) {
			throw new LogicException('SELECT statement is not allowed when SET is set');
		}
		return " {$this->sql['select']}";
	}

	/**
	 * Sets the ON DUPLICATE KEY UPDATE part.
	 *
	 * @param array<string,Closure|float|int|string|null> $columns Column name
	 * as key, column value/expression as array value
	 *
	 * @see https://mariadb.com/kb/en/library/insert-on-duplicate-key-update/
	 *
	 * @return $this
	 */
	public function onDuplicateKeyUpdate(array $columns)
	{
		$this->sql['on_duplicate'] = $columns;
		return $this;
	}

	/**
	 * Renders the ON DUPLICATE KEY UPDATE part.
	 *
	 * @return string|null The part or null if it was not set
	 */
	protected function renderOnDuplicateKeyUpdate() : ?string
	{
		if ( ! isset($this->sql['on_duplicate'])) {
			return null;
		}
		$on_duplicate = [];
		foreach ($this->sql['on_duplicate'] as $column => $value) {
			$on_duplicate[] = $this->renderAssignment($column, $value);
		}
		$on_duplicate = \implode(', ', $on_duplicate);
		return " ON DUPLICATE KEY UPDATE {$on_duplicate}";
	}

	/**
	 * Renders the SET clause.
	 *
	 * @throws LogicException if SET was set with columns or with the VALUES clause
	 *
	 * @return string|null The SET part or null if it was not set
	 */
	protected function renderSetPart() : ?string
	{
		$part = $this->renderSet();
		if ($part === null) {
			return null;
		}
		if (isset($this->sql['columns'])) {
			throw new LogicException('SET clause is not allowed when columns are set');
		}
		if (isset($this->sql['values'])) {
			throw new LogicException('SET clause is not allowed when VALUES is set');
		}
		return $part;
	}

	/**
	 * Check for conflicts in the INSERT statement.
	 *
	 * @throws LogicException if has conflicts
	 */
	protected function checkRowStatementsConflict() : void
	{
		if ( ! isset($this->sql['values'])
			&& ! isset($this->sql['select'])
			&& ! $this->hasSet()
		) {
			throw new LogicException(
				'The INSERT INTO must be followed by VALUES, SET or SELECT statement'
			);
		}
	}

	/**
	 * Renders the INSERT statement.
	 *
	 * @return string
	 */
	public function sql() : string
	{
		$sql = 'INSERT' . \PHP_EOL;
		$part = $this->renderOptions();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$sql .= $this->renderInto() . \PHP_EOL;
		$part = $this->renderColumns();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$this->checkRowStatementsConflict();
		$part = $this->renderValues();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$part = $this->renderSetPart();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$part = $this->renderSelect();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		$part = $this->renderOnDuplicateKeyUpdate();
		if ($part) {
			$sql .= $part . \PHP_EOL;
		}
		return $sql;
	}

	/**
	 * Runs the INSERT statement.
	 *
	 * @return int The number of affected rows
	 */
	public function run() : int
	{
		return $this->database->exec($this->sql());
	}
}
