<?php namespace Framework\Database\Manipulation\Traits;

/**
 * Trait Where.
 */
trait Where
{
	/**
	 * Appends a "AND $column $operator ...$values" condition in the WHERE clause.
	 *
	 * @param \Closure|string $column   \Closure for a subquery or a string with the column name
	 * @param string          $operator
	 * @param mixed           $values   Each value must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function where($column, string $operator, ...$values)
	{
		return $this->addWhere('AND', $column, $operator, $values);
	}

	/**
	 * Appends a "OR $column $operator ...$values" condition in the WHERE clause.
	 *
	 * @param \Closure|string $column   \Closure for a subquery or a string with the column name
	 * @param string          $operator
	 * @param mixed           $values   Each value must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function orWhere($column, string $operator, ...$values)
	{
		return $this->addWhere('OR', $column, $operator, $values);
	}

	/**
	 * Appends a "AND $column = $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/equal/
	 *
	 * @return $this
	 */
	public function whereEqual($column, $value)
	{
		return $this->where($column, '=', $value);
	}

	/**
	 * Appends a "OR $column = $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/equal/
	 *
	 * @return $this
	 */
	public function orWhereEqual($column, $value)
	{
		return $this->orWhere($column, '=', $value);
	}

	/**
	 * Appends a "AND $column != $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/not-equal/
	 *
	 * @return $this
	 */
	public function whereNotEqual($column, $value)
	{
		return $this->where($column, '!=', $value);
	}

	/**
	 * Appends a "OR $column != $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/not-equal/
	 *
	 * @return $this
	 */
	public function orWhereNotEqual($column, $value)
	{
		return $this->orWhere($column, '!=', $value);
	}

	/**
	 * Appends a "AND $column <=> $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/null-safe-equal/
	 *
	 * @return $this
	 */
	public function whereNullSafeEqual($column, $value)
	{
		return $this->where($column, '<=>', $value);
	}

	/**
	 * Appends a "OR $column <=> $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/null-safe-equal/
	 *
	 * @return $this
	 */
	public function orWhereNullSafeEqual($column, $value)
	{
		return $this->orWhere($column, '<=>', $value);
	}

	/**
	 * Appends a "AND $column < $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/less-than/
	 *
	 * @return $this
	 */
	public function whereLessThan($column, $value)
	{
		return $this->where($column, '<', $value);
	}

	/**
	 * Appends a "OR $column < $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/less-than/
	 *
	 * @return $this
	 */
	public function orWhereLessThan($column, $value)
	{
		return $this->orWhere($column, '<', $value);
	}

	/**
	 * Appends a "AND $column <= $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/less-than-or-equal/
	 *
	 * @return $this
	 */
	public function whereLessThanOrEqual($column, $value)
	{
		return $this->where($column, '<=', $value);
	}

	/**
	 * Appends a "OR $column <= $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/less-than-or-equal/
	 *
	 * @return $this
	 */
	public function orWhereLessThanOrEqual($column, $value)
	{
		return $this->orWhere($column, '<=', $value);
	}

	/**
	 * Appends a "AND $column > $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/greater-than/
	 *
	 * @return $this
	 */
	public function whereGreaterThan($column, $value)
	{
		return $this->where($column, '>', $value);
	}

	/**
	 * Appends a "OR $column > $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/greater-than/
	 *
	 * @return $this
	 */
	public function orWhereGreaterThan($column, $value)
	{
		return $this->orWhere($column, '>', $value);
	}

	/**
	 * Appends a "AND $column >= $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/greater-than-or-equal/
	 *
	 * @return $this
	 */
	public function whereGreaterThanOrEqual($column, $value)
	{
		return $this->where($column, '>=', $value);
	}

	/**
	 * Appends a "OR $column >= $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/greater-than-or-equal/
	 *
	 * @return $this
	 */
	public function orWhereGreaterThanOrEqual($column, $value)
	{
		return $this->orWhere($column, '>=', $value);
	}

	/**
	 * Appends a "AND $column LIKE $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/like/
	 *
	 * @return $this
	 */
	public function whereLike($column, $value)
	{
		return $this->where($column, 'LIKE', $value);
	}

	/**
	 * Appends a "OR $column LIKE $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/like/
	 *
	 * @return $this
	 */
	public function orWhereLike($column, $value)
	{
		return $this->orWhere($column, 'LIKE', $value);
	}

	/**
	 * Appends a "AND $column NOT LIKE" $value condition.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/not-like/
	 *
	 * @return $this
	 */
	public function whereNotLike($column, $value)
	{
		return $this->where($column, 'NOT LIKE', $value);
	}

	/**
	 * Appends a "OR $column NOT LIKE $value" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/not-like/
	 *
	 * @return $this
	 */
	public function orWhereNotLike($column, $value)
	{
		return $this->orWhere($column, 'NOT LIKE', $value);
	}

	/**
	 * Appends a "AND $column IN (...$values)" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 * @param mixed                          $values
	 *
	 * @see https://mariadb.com/kb/en/library/in/
	 *
	 * @return $this
	 */
	public function whereIn($column, $value, ...$values)
	{
		$values = $this->mergeExpressions($value, $values);
		return $this->where($column, 'IN', ...$values);
	}

	/**
	 * Appends a "OR $column IN (...$values)" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 * @param mixed                          $values
	 *
	 * @see https://mariadb.com/kb/en/library/in/
	 *
	 * @return $this
	 */
	public function orWhereIn($column, $value, ...$values)
	{
		$values = $this->mergeExpressions($value, $values);
		return $this->orWhere($column, 'IN', ...$values);
	}

	/**
	 * Appends a "AND $column NOT IN (...$values)" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 * @param mixed                          $values
	 *
	 * @see https://mariadb.com/kb/en/library/not-in/
	 *
	 * @return $this
	 */
	public function whereNotIn($column, $value, ...$values)
	{
		$values = $this->mergeExpressions($value, $values);
		return $this->where($column, 'NOT IN', ...$values);
	}

	/**
	 * Appends a "OR $column NOT IN (...$values)" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 * @param mixed                          $values
	 *
	 * @see https://mariadb.com/kb/en/library/not-in/
	 *
	 * @return $this
	 */
	public function orWhereNotIn($column, $value, ...$values)
	{
		$values = $this->mergeExpressions($value, $values);
		return $this->orWhere($column, 'NOT IN', ...$values);
	}

	/**
	 * Appends a "AND $column BETWEEN $min AND $max" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $min
	 * @param \Closure|float|int|string|null $max
	 *
	 * @see https://mariadb.com/kb/en/library/between-and/
	 *
	 * @return $this
	 */
	public function whereBetween($column, $min, $max)
	{
		return $this->where($column, 'BETWEEN', $min, $max);
	}

	/**
	 * Appends a "OR $column BETWEEN $min AND $max" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $min
	 * @param \Closure|float|int|string|null $max
	 *
	 * @see https://mariadb.com/kb/en/library/between-and/
	 *
	 * @return $this
	 */
	public function orWhereBetween($column, $min, $max)
	{
		return $this->orWhere($column, 'BETWEEN', $min, $max);
	}

	/**
	 * Appends a "AND $column NOT BETWEEN $min AND $max" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $min
	 * @param \Closure|float|int|string|null $max
	 *
	 * @see https://mariadb.com/kb/en/library/not-between/
	 *
	 * @return $this
	 */
	public function whereNotBetween($column, $min, $max)
	{
		return $this->where($column, 'NOT BETWEEN', $min, $max);
	}

	/**
	 * Appends a "OR $column NOT BETWEEN $min AND $max" condition in the WHERE clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $min
	 * @param \Closure|float|int|string|null $max
	 *
	 * @see https://mariadb.com/kb/en/library/not-between/
	 *
	 * @return $this
	 */
	public function orWhereNotBetween($column, $min, $max)
	{
		return $this->orWhere($column, 'NOT BETWEEN', $min, $max);
	}

	/**
	 * Appends a "AND $column IS NULL" condition in the WHERE clause.
	 *
	 * @param \Closure|string $column \Closure for a subquery or a string with the column name
	 *
	 * @see https://mariadb.com/kb/en/library/is-null/
	 *
	 * @return $this
	 */
	public function whereIsNull($column)
	{
		return $this->where($column, 'IS NULL');
	}

	/**
	 * Appends a "OR $column IS NULL" condition in the WHERE clause.
	 *
	 * @param \Closure|string $column \Closure for a subquery or a string with the column name
	 *
	 * @see https://mariadb.com/kb/en/library/is-null/
	 *
	 * @return $this
	 */
	public function orWhereIsNull($column)
	{
		return $this->orWhere($column, 'IS NULL');
	}

	/**
	 * Appends a "AND $column IS NOT NULL" condition in the WHERE clause.
	 *
	 * @param \Closure|string $column \Closure for a subquery or a string with the column name
	 *
	 * @see https://mariadb.com/kb/en/library/is-not-null/
	 *
	 * @return $this
	 */
	public function whereIsNotNull($column)
	{
		return $this->where($column, 'IS NOT NULL');
	}

	/**
	 * Appends a "OR $column IS NOT NULL" condition in the WHERE clause.
	 *
	 * @param \Closure|string $column \Closure for a subquery or a string with the column name
	 *
	 * @see https://mariadb.com/kb/en/library/is-not-null/
	 *
	 * @return $this
	 */
	public function orWhereIsNotNull($column)
	{
		return $this->orWhere($column, 'IS NOT NULL');
	}

	public function whereExists(\Closure $subquery)
	{
		$this->subquery($subquery);
	}

	public function whereNotExists(\Closure $subquery)
	{
		$this->subquery($subquery);
	}

	private function addWhere(
		string $glue,
		$column,
		string $operator,
		array $values,
		string $clause = 'where'
	) {
		$this->sql[$clause][] = [
			'glue' => $glue,
			'column' => $column,
			'operator' => $operator,
			'values' => $values,
		];
		return $this;
	}

	protected function renderWhere(string $clause = 'where') : ?string
	{
		if ( ! isset($this->sql[$clause])) {
			return null;
		}
		$parts = $this->sql[$clause];
		foreach ($parts as &$part) {
			$part['column'] = $this->renderIdentifier($part['column']);
			$part['operator'] = $this->renderWhereOperator($part['operator']);
			$part['values'] = $this->renderWhereValues($part['operator'], $part['values']);
		}
		unset($part);
		$condition = "{$parts[0]['column']} {$parts[0]['operator']}";
		$condition .= $parts[0]['values'] === null ? '' : " {$parts[0]['values']}";
		unset($parts[0]);
		foreach ($parts as $part) {
			$condition .= " {$part['glue']} {$part['column']} {$part['operator']}";
			$condition .= $part['values'] === null ? '' : " {$part['values']}";
		}
		$clause = \strtoupper($clause);
		return " {$clause} {$condition}";
	}

	private function renderWhereOperator(string $operator) : string
	{
		$result = \strtoupper($operator);
		if (\in_array($result, [
			'=',
			'<=>',
			'!=',
			'<>',
			'>',
			'>=',
			'<',
			'<=',
			'LIKE',
			'NOT LIKE',
			'IN',
			'NOT IN',
			'BETWEEN',
			'NOT BETWEEN',
			'IS NULL',
			'IS NOT NULL',
		], true)) {
			return $result;
		}
		throw new \InvalidArgumentException("Invalid comparison operator: {$operator}");
	}

	private function renderWhereValues(string $operator, array $values) : ?string
	{
		$values = \array_values($values);
		$values = $this->prepareWhereValues($values);
		if (\in_array($operator, [
			'=',
			'<=>',
			'!=',
			'<>',
			'>',
			'>=',
			'<',
			'<=',
			'LIKE',
			'NOT LIKE',
		], true)) {
			return $this->renderWhereValuesPartComparator($operator, $values);
		}
		if (\in_array($operator, [
			'IN',
			'NOT IN',
		], true)) {
			return $this->renderWhereValuesPartIn($operator, $values);
		}
		if (\in_array($operator, [
			'BETWEEN',
			'NOT BETWEEN',
		], true)) {
			return $this->renderWhereValuesPartBetween($operator, $values);
		}
		if (\in_array($operator, [
			'IS NULL',
			'IS NOT NULL',
		], true)) {
			return $this->renderWhereValuesPartIsNull($operator, $values);
		}
		// @codeCoverageIgnoreStart
		// Should never throw - renderWhereOperator runs before on renderWhere
		throw new \InvalidArgumentException("Invalid comparison operator: {$operator}");
		// @codeCoverageIgnoreEnd
	}

	private function prepareWhereValues(array $values) : array
	{
		foreach ($values as &$value) {
			$value = $value instanceof \Closure
				? $this->subquery($value)
				: $this->database->quote($value);
		}
		return $values;
	}

	private function renderWhereValuesPartComparator(string $operator, array $values) : string
	{
		if (isset($values[1]) || ! isset($values[0])) {
			throw new \InvalidArgumentException(
				"Operator {$operator} must receive exactly 1 parameter"
			);
		}
		return $values[0];
	}

	private function renderWhereValuesPartIn(string $operator, array $values) : string
	{
		if (empty($values)) {
			throw new \InvalidArgumentException(
				"Operator {$operator} must receive at least 1 parameter"
			);
		}
		return '(' . \implode(', ', $values) . ')';
	}

	private function renderWhereValuesPartBetween(string $operator, array $values) : string
	{
		if (isset($values[2]) || ! isset($values[0], $values[1])) {
			throw new \InvalidArgumentException(
				"Operator {$operator} must receive exactly 2 parameters"
			);
		}
		return "{$values[0]} AND {$values[1]}";
	}

	private function renderWhereValuesPartIsNull(string $operator, array $values)
	{
		if ( ! empty($values)) {
			throw new \InvalidArgumentException(
				"Operator {$operator} must not receive parameters"
			);
		}
		return null;
	}
}
