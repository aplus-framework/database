<?php namespace Framework\Database\Manipulation\Statements\Traits;

/**
 * Trait Having.
 */
trait Having
{
	use Where;

	/**
	 * Appends a "AND $column $operator ...$values" condition in the HAVING clause.
	 *
	 * @param \Closure|string $column   \Closure for a subquery or a string with the column name
	 * @param string          $operator
	 * @param mixed           $values   Each value must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function having($column, string $operator, ...$values)
	{
		return $this->addHaving('AND', $column, $operator, $values);
	}

	/**
	 * Appends a "OR $column $operator ...$values" condition in the HAVING clause.
	 *
	 * @param \Closure|string $column   \Closure for a subquery or a string with the column name
	 * @param string          $operator
	 * @param mixed           $values   Each value must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function orHaving($column, string $operator, ...$values)
	{
		return $this->addHaving('OR', $column, $operator, $values);
	}

	/**
	 * Appends a "AND $column = $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/equal/
	 *
	 * @return $this
	 */
	public function havingEqual($column, $value)
	{
		return $this->having($column, '=', $value);
	}

	/**
	 * Appends a "OR $column = $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/equal/
	 *
	 * @return $this
	 */
	public function orHavingEqual($column, $value)
	{
		return $this->orHaving($column, '=', $value);
	}

	/**
	 * Appends a "AND $column != $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/not-equal/
	 *
	 * @return $this
	 */
	public function havingNotEqual($column, $value)
	{
		return $this->having($column, '!=', $value);
	}

	/**
	 * Appends a "OR $column != $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/not-equal/
	 *
	 * @return $this
	 */
	public function orHavingNotEqual($column, $value)
	{
		return $this->orHaving($column, '!=', $value);
	}

	/**
	 * Appends a "AND $column <=> $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/null-safe-equal/
	 *
	 * @return $this
	 */
	public function havingNullSafeEqual($column, $value)
	{
		return $this->having($column, '<=>', $value);
	}

	/**
	 * Appends a "OR $column <=> $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/null-safe-equal/
	 *
	 * @return $this
	 */
	public function orHavingNullSafeEqual($column, $value)
	{
		return $this->orHaving($column, '<=>', $value);
	}

	/**
	 * Appends a "AND $column < $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/less-than/
	 *
	 * @return $this
	 */
	public function havingLessThan($column, $value)
	{
		return $this->having($column, '<', $value);
	}

	/**
	 * Appends a "OR $column < $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/less-than/
	 *
	 * @return $this
	 */
	public function orHavingLessThan($column, $value)
	{
		return $this->orHaving($column, '<', $value);
	}

	/**
	 * Appends a "AND $column <= $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/less-than-or-equal/
	 *
	 * @return $this
	 */
	public function havingLessThanOrEqual($column, $value)
	{
		return $this->having($column, '<=', $value);
	}

	/**
	 * Appends a "OR $column <= $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/less-than-or-equal/
	 *
	 * @return $this
	 */
	public function orHavingLessThanOrEqual($column, $value)
	{
		return $this->orHaving($column, '<=', $value);
	}

	/**
	 * Appends a "AND $column > $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/greater-than/
	 *
	 * @return $this
	 */
	public function havingGreaterThan($column, $value)
	{
		return $this->having($column, '>', $value);
	}

	/**
	 * Appends a "OR $column > $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/greater-than/
	 *
	 * @return $this
	 */
	public function orHavingGreaterThan($column, $value)
	{
		return $this->orHaving($column, '>', $value);
	}

	/**
	 * Appends a "AND $column >= $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/greater-than-or-equal/
	 *
	 * @return $this
	 */
	public function havingGreaterThanOrEqual($column, $value)
	{
		return $this->having($column, '>=', $value);
	}

	/**
	 * Appends a "OR $column >= $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/greater-than-or-equal/
	 *
	 * @return $this
	 */
	public function orHavingGreaterThanOrEqual($column, $value)
	{
		return $this->orHaving($column, '>=', $value);
	}

	/**
	 * Appends a "AND $column LIKE $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/like/
	 *
	 * @return $this
	 */
	public function havingLike($column, $value)
	{
		return $this->having($column, 'LIKE', $value);
	}

	/**
	 * Appends a "OR $column LIKE $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/like/
	 *
	 * @return $this
	 */
	public function orHavingLike($column, $value)
	{
		return $this->orHaving($column, 'LIKE', $value);
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
	public function havingNotLike($column, $value)
	{
		return $this->having($column, 'NOT LIKE', $value);
	}

	/**
	 * Appends a "OR $column NOT LIKE $value" condition in the HAVING clause.
	 *
	 * @param \Closure|string                $column \Closure for a subquery or a string with the
	 *                                               column name
	 * @param \Closure|float|int|string|null $value
	 *
	 * @see https://mariadb.com/kb/en/library/not-like/
	 *
	 * @return $this
	 */
	public function orHavingNotLike($column, $value)
	{
		return $this->orHaving($column, 'NOT LIKE', $value);
	}

	/**
	 * Appends a "AND $column IN (...$values)" condition in the HAVING clause.
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
	public function havingIn($column, $value, ...$values)
	{
		$values = $this->mergeExpressions($value, $values);
		return $this->having($column, 'IN', ...$values);
	}

	/**
	 * Appends a "OR $column IN (...$values)" condition in the HAVING clause.
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
	public function orHavingIn($column, $value, ...$values)
	{
		$values = $this->mergeExpressions($value, $values);
		return $this->orHaving($column, 'IN', ...$values);
	}

	/**
	 * Appends a "AND $column NOT IN (...$values)" condition in the HAVING clause.
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
	public function havingNotIn($column, $value, ...$values)
	{
		$values = $this->mergeExpressions($value, $values);
		return $this->having($column, 'NOT IN', ...$values);
	}

	/**
	 * Appends a "OR $column NOT IN (...$values)" condition in the HAVING clause.
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
	public function orHavingNotIn($column, $value, ...$values)
	{
		$values = $this->mergeExpressions($value, $values);
		return $this->orHaving($column, 'NOT IN', ...$values);
	}

	/**
	 * Appends a "AND $column BETWEEN $min AND $max" condition in the HAVING clause.
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
	public function havingBetween($column, $min, $max)
	{
		return $this->having($column, 'BETWEEN', $min, $max);
	}

	/**
	 * Appends a "OR $column BETWEEN $min AND $max" condition in the HAVING clause.
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
	public function orHavingBetween($column, $min, $max)
	{
		return $this->orHaving($column, 'BETWEEN', $min, $max);
	}

	/**
	 * Appends a "AND $column NOT BETWEEN $min AND $max" condition in the HAVING clause.
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
	public function havingNotBetween($column, $min, $max)
	{
		return $this->having($column, 'NOT BETWEEN', $min, $max);
	}

	/**
	 * Appends a "OR $column NOT BETWEEN $min AND $max" condition in the HAVING clause.
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
	public function orHavingNotBetween($column, $min, $max)
	{
		return $this->orHaving($column, 'NOT BETWEEN', $min, $max);
	}

	/**
	 * Appends a "AND $column IS NULL" condition in the HAVING clause.
	 *
	 * @param \Closure|string $column \Closure for a subquery or a string with the column name
	 *
	 * @see https://mariadb.com/kb/en/library/is-null/
	 *
	 * @return $this
	 */
	public function havingIsNull($column)
	{
		return $this->having($column, 'IS NULL');
	}

	/**
	 * Appends a "OR $column IS NULL" condition in the HAVING clause.
	 *
	 * @param \Closure|string $column \Closure for a subquery or a string with the column name
	 *
	 * @see https://mariadb.com/kb/en/library/is-null/
	 *
	 * @return $this
	 */
	public function orHavingIsNull($column)
	{
		return $this->orHaving($column, 'IS NULL');
	}

	/**
	 * Appends a "AND $column IS NOT NULL" condition in the HAVING clause.
	 *
	 * @param \Closure|string $column \Closure for a subquery or a string with the column name
	 *
	 * @see https://mariadb.com/kb/en/library/is-not-null/
	 *
	 * @return $this
	 */
	public function havingIsNotNull($column)
	{
		return $this->having($column, 'IS NOT NULL');
	}

	/**
	 * Appends a "OR $column IS NOT NULL" condition in the HAVING clause.
	 *
	 * @param \Closure|string $column \Closure for a subquery or a string with the column name
	 *
	 * @see https://mariadb.com/kb/en/library/is-not-null/
	 *
	 * @return $this
	 */
	public function orHavingIsNotNull($column)
	{
		return $this->orHaving($column, 'IS NOT NULL');
	}

	private function addHaving(
		string $glue,
		$column,
		string $operator,
		array $values
	) {
		return $this->addWhere($glue, $column, $operator, $values, 'having');
	}

	protected function renderHaving() : ?string
	{
		return $this->renderWhere('having');
	}
}
