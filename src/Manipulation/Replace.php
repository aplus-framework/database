<?php namespace Framework\Database\Manipulation;

use Closure;
use InvalidArgumentException;
use LogicException;

/**
 * Class Replace.
 *
 * @see https://mariadb.com/kb/en/library/replace/
 */
class Replace extends Statement
{
	use Traits\Set;

	/**
	 * @see https://mariadb.com/kb/en/library/insert-delayed/
	 */
	public const OPT_DELAYED = 'DELAYED';
	/**
	 * @see https://mariadb.com/kb/en/library/high_priority-and-low_priority/
	 */
	public const OPT_LOW_PRIORITY = 'LOW_PRIORITY';

	/**
	 * @param string $table
	 *
	 * @return $this
	 */
	public function into(string $table)
	{
		$this->sql['into'] = $table;
		return $this;
	}

	protected function renderInto() : string
	{
		if ( ! isset($this->sql['into'])) {
			throw new LogicException('INTO table must be set');
		}
		return ' INTO ' . $this->renderIdentifier($this->sql['into']);
	}

	/**
	 * @param string $column
	 * @param mixed  $columns
	 *
	 * @return $this
	 */
	public function columns(string $column, ...$columns)
	{
		$this->sql['columns'] = $this->mergeExpressions($column, $columns);
		return $this;
	}

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
				static::OPT_LOW_PRIORITY,
			], true)) {
				throw new InvalidArgumentException("Invalid option: {$input}");
			}
		}
		unset($option);
		$intersection = \array_intersect(
			$options,
			[static::OPT_DELAYED, static::OPT_LOW_PRIORITY]
		);
		if (\count($intersection) > 1) {
			throw new LogicException(
				'Options LOW_PRIORITY and DELAYED can not be used together'
			);
		}
		$options = \implode(' ', $options);
		return " {$options}";
	}

	/**
	 * @param mixed $value
	 * @param mixed $values
	 *
	 * @return $this
	 */
	public function values($value, ...$values)
	{
		$this->sql['values'][] = $this->mergeExpressions($value, $values);
		return $this;
	}

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

	protected function checkRowStatementsConflict() : void
	{
		if ( ! isset($this->sql['values'])
			&& ! isset($this->sql['select'])
			&& ! $this->hasSet()
		) {
			throw new LogicException(
				'The REPLACE INTO must be followed by VALUES, SET or SELECT statement'
			);
		}
	}

	/**
	 * Sets the SELECT statement part.
	 *
	 * @param Closure $select
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

	protected function renderSetPart() : ?string
	{
		$part = $this->renderSet();
		if ($part) {
			if (isset($this->sql['columns'])) {
				throw new LogicException('SET statement is not allowed when columns are set');
			}
			if (isset($this->sql['values'])) {
				throw new LogicException('SET statement is not allowed when VALUES is set');
			}
		}
		return $part;
	}

	public function sql() : string
	{
		$sql = 'REPLACE' . \PHP_EOL;
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
		return $sql;
	}

	/**
	 * Runs the REPLACE statement.
	 *
	 * @return int The number of affected rows
	 */
	public function run() : int
	{
		return $this->database->exec($this->sql());
	}
}
