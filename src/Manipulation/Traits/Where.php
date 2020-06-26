<?php namespace Framework\Database\Manipulation\Traits;

use Closure;
use InvalidArgumentException;

/**
 * Trait Where.
 */
trait Where
{
	/**
	 * Appends a "AND $column $operator ...$values" condition in the WHERE clause.
	 *
	 * @param array|Closure|string $column   \Closure for a subquery, a string with the column name
	 *                                       or and array with column names on WHERE MATCH clause
	 * @param string               $operator
	 * @param mixed                $values   Each value must be of type: string or \Closure
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
	 * @param array|Closure|string $column   \Closure for a subquery, a string with the column name
	 *                                       or and array with column names on WHERE MATCH clause
	 * @param string               $operator
	 * @param mixed                $values   Each value must be of type: string or \Closure
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
	 * @param mixed                         $values
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
	 * @param mixed                         $values
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
	 * @param mixed                         $values
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $value
	 * @param mixed                         $values
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $min
	 * @param Closure|float|int|string|null $max
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $min
	 * @param Closure|float|int|string|null $max
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $min
	 * @param Closure|float|int|string|null $max
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
	 * @param Closure|string                $column \Closure for a subquery or a string with the
	 *                                              column name
	 * @param Closure|float|int|string|null $min
	 * @param Closure|float|int|string|null $max
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
	 * @param Closure|string $column \Closure for a subquery or a string with the column name
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
	 * @param Closure|string $column \Closure for a subquery or a string with the column name
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
	 * @param Closure|string $column \Closure for a subquery or a string with the column name
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
	 * @param Closure|string $column \Closure for a subquery or a string with the column name
	 *
	 * @see https://mariadb.com/kb/en/library/is-not-null/
	 *
	 * @return $this
	 */
	public function orWhereIsNotNull($column)
	{
		return $this->orWhere($column, 'IS NOT NULL');
	}

	/* TODO: https://mariadb.com/kb/en/subqueries-and-exists/
	 public function whereExists(\Closure $subquery)
	{
		$this->subquery($subquery);
	}

	public function whereNotExists(\Closure $subquery)
	{
		$this->subquery($subquery);
	}*/

	/**
	 * Appends a "AND MATCH (...$columns) AGAINST ($against IN NATURAL LANGUAGE MODE)" fulltext
	 * searching in the WHERE clause.
	 *
	 * @param array        $columns Columns to MATCH
	 * @param array|string $against AGAINST expression
	 *
	 * @see https://mariadb.com/kb/en/library/full-text-index-overview/
	 * @see https://mariadb.com/kb/en/library/match-against/
	 *
	 * @return $this
	 */
	public function whereMatch(array $columns, $against)
	{
		return $this->where($columns, 'MATCH', $against);
	}

	/**
	 * Appends a "OR MATCH (...$columns) AGAINST ($against IN NATURAL LANGUAGE MODE)" fulltext
	 * searching in the WHERE clause.
	 *
	 * @param array        $columns Columns to MATCH
	 * @param array|string $against AGAINST expression
	 *
	 * @see https://mariadb.com/kb/en/library/full-text-index-overview/
	 * @see https://mariadb.com/kb/en/library/match-against/
	 *
	 * @return $this
	 */
	public function orWhereMatch(array $columns, $against)
	{
		return $this->orWhere($columns, 'MATCH', $against);
	}

	/**
	 * Appends a "AND MATCH (...$columns) AGAINST ($against WITH QUERY EXPANSION)" fulltext
	 * searching in the WHERE clause.
	 *
	 * @param array        $columns Columns to MATCH
	 * @param array|string $against AGAINST expression
	 *
	 * @see https://mariadb.com/kb/en/library/full-text-index-overview/
	 * @see https://mariadb.com/kb/en/library/match-against/
	 *
	 * @return $this
	 */
	public function whereMatchWithQueryExpansion(array $columns, $against)
	{
		return $this->where($columns, 'MATCH', $against, 'WITH QUERY EXPANSION');
	}

	/**
	 * Appends a "OR MATCH (...$columns) AGAINST ($against WITH QUERY EXPANSION)" fulltext
	 * searching in the WHERE clause.
	 *
	 * @param array        $columns Columns to MATCH
	 * @param array|string $against AGAINST expression
	 *
	 * @see https://mariadb.com/kb/en/library/full-text-index-overview/
	 * @see https://mariadb.com/kb/en/library/match-against/
	 *
	 * @return $this
	 */
	public function orWhereMatchWithQueryExpansion(array $columns, $against)
	{
		return $this->orWhere($columns, 'MATCH', $against, 'WITH QUERY EXPANSION');
	}

	/**
	 * Appends a "AND MATCH (...$columns) AGAINST ($against IN BOOLEAN MODE)" fulltext searching in
	 * the WHERE clause.
	 *
	 * @param array        $columns Columns to MATCH
	 * @param array|string $against AGAINST expression
	 *
	 * @see https://mariadb.com/kb/en/library/full-text-index-overview/
	 * @see https://mariadb.com/kb/en/library/match-against/
	 *
	 * @return $this
	 */
	public function whereMatchInBooleanMode(array $columns, $against)
	{
		return $this->where($columns, 'MATCH', $against, 'IN BOOLEAN MODE');
	}

	/**
	 * Appends a "OR MATCH (...$columns) AGAINST ($against IN BOOLEAN MODE)" fulltext searching in
	 * the WHERE clause.
	 *
	 * @param array        $columns Columns to MATCH
	 * @param array|string $against AGAINST expression
	 *
	 * @see https://mariadb.com/kb/en/library/full-text-index-overview/
	 * @see https://mariadb.com/kb/en/library/match-against/
	 *
	 * @return $this
	 */
	public function orWhereMatchInBooleanMode(array $columns, $against)
	{
		return $this->orWhere($columns, 'MATCH', $against, 'IN BOOLEAN MODE');
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

	private function renderMatch(array $columns, $expression, string $modifier = '')
	{
		foreach ($columns as &$column) {
			$column = $this->renderIdentifier($column);
		}
		unset($column);
		$columns = \implode(', ', $columns);
		if (\is_array($expression)) {
			$expression = \implode(', ', $expression);
		}
		$expression = $this->database->quote($expression);
		if ($modifier) {
			$modifier = ' ' . $modifier;
		}
		return "MATCH ({$columns}) AGAINST ({$expression}{$modifier})";
	}

	protected function renderWhere(string $clause = 'where') : ?string
	{
		if ( ! isset($this->sql[$clause])) {
			return null;
		}
		$parts = $this->sql[$clause];
		$condition = $this->renderWherePart($parts[0], true);
		unset($parts[0]);
		foreach ($parts as $part) {
			$condition .= $this->renderWherePart($part);
		}
		$clause = \strtoupper($clause);
		return " {$clause} {$condition}";
	}

	private function renderWherePart(array $part, bool $first = false) : string
	{
		$condition = '';
		if ($first === false) {
			$condition .= " {$part['glue']} ";
		}
		if ($part['operator'] === 'MATCH') {
			return $condition . $this->renderMatch($part['column'], ...$part['values']);
		}
		$part['column'] = $this->renderIdentifier($part['column']);
		$part['operator'] = $this->renderWhereOperator($part['operator']);
		$part['values'] = $this->renderWhereValues($part['operator'], $part['values']);
		$condition .= "{$part['column']} {$part['operator']}";
		$condition .= $part['values'] === null ? '' : " {$part['values']}";
		return $condition;
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
		throw new InvalidArgumentException("Invalid comparison operator: {$operator}");
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
		throw new InvalidArgumentException("Invalid comparison operator: {$operator}");
		// @codeCoverageIgnoreEnd
	}

	private function prepareWhereValues(array $values) : array
	{
		foreach ($values as &$value) {
			$value = $value instanceof Closure
				? $this->subquery($value)
				: $this->database->quote($value);
		}
		return $values;
	}

	private function renderWhereValuesPartComparator(string $operator, array $values) : string
	{
		if (isset($values[1]) || ! isset($values[0])) {
			throw new InvalidArgumentException(
				"Operator {$operator} must receive exactly 1 parameter"
			);
		}
		return $values[0];
	}

	private function renderWhereValuesPartIn(string $operator, array $values) : string
	{
		if (empty($values)) {
			throw new InvalidArgumentException(
				"Operator {$operator} must receive at least 1 parameter"
			);
		}
		return '(' . \implode(', ', $values) . ')';
	}

	private function renderWhereValuesPartBetween(string $operator, array $values) : string
	{
		if (isset($values[2]) || ! isset($values[0], $values[1])) {
			throw new InvalidArgumentException(
				"Operator {$operator} must receive exactly 2 parameters"
			);
		}
		return "{$values[0]} AND {$values[1]}";
	}

	private function renderWhereValuesPartIsNull(string $operator, array $values)
	{
		if ( ! empty($values)) {
			throw new InvalidArgumentException(
				"Operator {$operator} must not receive parameters"
			);
		}
		return null;
	}
}
