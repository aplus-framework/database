<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Manipulation\Traits;

use Closure;

/**
 * Trait Having.
 *
 * @package database
 */
trait Having
{
    use Where;

    /**
     * Appends an "AND $column $operator ...$values" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param string $operator
     * @param Closure|float|int|string|null ...$values
     *
     * @return static
     */
    public function having(
        Closure | string $column,
        string $operator,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->addHaving('AND', $column, $operator, $values);
    }

    /**
     * Appends a "OR $column $operator ...$values" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param string $operator
     * @param Closure|float|int|string|null ...$values
     *
     * @return static
     */
    public function orHaving(
        Closure | string $column,
        string $operator,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->addHaving('OR', $column, $operator, $values);
    }

    /**
     * Appends an "AND $column = $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/equal/
     *
     * @return static
     */
    public function havingEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->having($column, '=', $value);
    }

    /**
     * Appends a "OR $column = $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/equal/
     *
     * @return static
     */
    public function orHavingEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orHaving($column, '=', $value);
    }

    /**
     * Appends an "AND $column != $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/not-equal/
     *
     * @return static
     */
    public function havingNotEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->having($column, '!=', $value);
    }

    /**
     * Appends a "OR $column != $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/not-equal/
     *
     * @return static
     */
    public function orHavingNotEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orHaving($column, '!=', $value);
    }

    /**
     * Appends an "AND $column <=> $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/null-safe-equal/
     *
     * @return static
     */
    public function havingNullSafeEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->having($column, '<=>', $value);
    }

    /**
     * Appends a "OR $column <=> $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/null-safe-equal/
     *
     * @return static
     */
    public function orHavingNullSafeEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orHaving($column, '<=>', $value);
    }

    /**
     * Appends an "AND $column < $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/less-than/
     *
     * @return static
     */
    public function havingLessThan(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->having($column, '<', $value);
    }

    /**
     * Appends a "OR $column < $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/less-than/
     *
     * @return static
     */
    public function orHavingLessThan(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orHaving($column, '<', $value);
    }

    /**
     * Appends an "AND $column <= $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/less-than-or-equal/
     *
     * @return static
     */
    public function havingLessThanOrEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->having($column, '<=', $value);
    }

    /**
     * Appends a "OR $column <= $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/less-than-or-equal/
     *
     * @return static
     */
    public function orHavingLessThanOrEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orHaving($column, '<=', $value);
    }

    /**
     * Appends an "AND $column > $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/greater-than/
     *
     * @return static
     */
    public function havingGreaterThan(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->having($column, '>', $value);
    }

    /**
     * Appends a "OR $column > $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/greater-than/
     *
     * @return static
     */
    public function orHavingGreaterThan(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orHaving($column, '>', $value);
    }

    /**
     * Appends an "AND $column >= $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/greater-than-or-equal/
     *
     * @return static
     */
    public function havingGreaterThanOrEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->having($column, '>=', $value);
    }

    /**
     * Appends a "OR $column >= $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/greater-than-or-equal/
     *
     * @return static
     */
    public function orHavingGreaterThanOrEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orHaving($column, '>=', $value);
    }

    /**
     * Appends an "AND $column LIKE $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/like/
     *
     * @return static
     */
    public function havingLike(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->having($column, 'LIKE', $value);
    }

    /**
     * Appends a "OR $column LIKE $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/like/
     *
     * @return static
     */
    public function orHavingLike(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orHaving($column, 'LIKE', $value);
    }

    /**
     * Appends an "AND $column NOT LIKE" $value condition.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/not-like/
     *
     * @return static
     */
    public function havingNotLike(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->having($column, 'NOT LIKE', $value);
    }

    /**
     * Appends a "OR $column NOT LIKE $value" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/not-like/
     *
     * @return static
     */
    public function orHavingNotLike(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orHaving($column, 'NOT LIKE', $value);
    }

    /**
     * Appends an "AND $column IN (...$values)" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     * @param Closure|float|int|string|null ...$values
     *
     * @see https://mariadb.com/kb/en/in/
     *
     * @return static
     */
    public function havingIn(
        Closure | string $column,
        Closure | float | int | string | null $value,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->having($column, 'IN', ...[$value, ...$values]);
    }

    /**
     * Appends a "OR $column IN (...$values)" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     * @param Closure|float|int|string|null ...$values
     *
     * @see https://mariadb.com/kb/en/in/
     *
     * @return static
     */
    public function orHavingIn(
        Closure | string $column,
        Closure | float | int | string | null $value,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->orHaving($column, 'IN', ...[$value, ...$values]);
    }

    /**
     * Appends an "AND $column NOT IN (...$values)" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     * @param Closure|float|int|string|null ...$values
     *
     * @see https://mariadb.com/kb/en/not-in/
     *
     * @return static
     */
    public function havingNotIn(
        Closure | string $column,
        Closure | float | int | string | null $value,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->having($column, 'NOT IN', ...[$value, ...$values]);
    }

    /**
     * Appends a "OR $column NOT IN (...$values)" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     * @param Closure|float|int|string|null ...$values
     *
     * @see https://mariadb.com/kb/en/not-in/
     *
     * @return static
     */
    public function orHavingNotIn(
        Closure | string $column,
        Closure | float | int | string | null $value,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->orHaving($column, 'NOT IN', ...[$value, ...$values]);
    }

    /**
     * Appends an "AND $column BETWEEN $min AND $max" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $min
     * @param Closure|float|int|string|null $max
     *
     * @see https://mariadb.com/kb/en/between-and/
     *
     * @return static
     */
    public function havingBetween(
        Closure | string $column,
        Closure | float | int | string | null $min,
        Closure | float | int | string | null $max
    ) : static {
        return $this->having($column, 'BETWEEN', $min, $max);
    }

    /**
     * Appends a "OR $column BETWEEN $min AND $max" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $min
     * @param Closure|float|int|string|null $max
     *
     * @see https://mariadb.com/kb/en/between-and/
     *
     * @return static
     */
    public function orHavingBetween(
        Closure | string $column,
        Closure | float | int | string | null $min,
        Closure | float | int | string | null $max
    ) : static {
        return $this->orHaving($column, 'BETWEEN', $min, $max);
    }

    /**
     * Appends an "AND $column NOT BETWEEN $min AND $max" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $min
     * @param Closure|float|int|string|null $max
     *
     * @see https://mariadb.com/kb/en/not-between/
     *
     * @return static
     */
    public function havingNotBetween(
        Closure | string $column,
        Closure | float | int | string | null $min,
        Closure | float | int | string | null $max
    ) : static {
        return $this->having($column, 'NOT BETWEEN', $min, $max);
    }

    /**
     * Appends a "OR $column NOT BETWEEN $min AND $max" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $min
     * @param Closure|float|int|string|null $max
     *
     * @see https://mariadb.com/kb/en/not-between/
     *
     * @return static
     */
    public function orHavingNotBetween(
        Closure | string $column,
        Closure | float | int | string | null $min,
        Closure | float | int | string | null $max
    ) : static {
        return $this->orHaving($column, 'NOT BETWEEN', $min, $max);
    }

    /**
     * Appends an "AND $column IS NULL" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     *
     * @see https://mariadb.com/kb/en/is-null/
     *
     * @return static
     */
    public function havingIsNull(Closure | string $column) : static
    {
        return $this->having($column, 'IS NULL');
    }

    /**
     * Appends a "OR $column IS NULL" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     *
     * @see https://mariadb.com/kb/en/is-null/
     *
     * @return static
     */
    public function orHavingIsNull(Closure | string $column) : static
    {
        return $this->orHaving($column, 'IS NULL');
    }

    /**
     * Appends an "AND $column IS NOT NULL" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     *
     * @see https://mariadb.com/kb/en/is-not-null/
     *
     * @return static
     */
    public function havingIsNotNull(Closure | string $column) : static
    {
        return $this->having($column, 'IS NOT NULL');
    }

    /**
     * Appends a "OR $column IS NOT NULL" condition in the HAVING clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     *
     * @see https://mariadb.com/kb/en/is-not-null/
     *
     * @return static
     */
    public function orHavingIsNotNull(Closure | string $column) : static
    {
        return $this->orHaving($column, 'IS NOT NULL');
    }

    /**
     * Adds a HAVING part.
     *
     * @param string $glue `AND` or `OR`
     * @param array<array<mixed>|Closure|string>|Closure|string $column
     * @param string $operator `=`, `<=>`, `!=`, `<>`, `>`, `>=`, `<`, `<=`,
     * `LIKE`, `NOT LIKE`, `IN`, `NOT IN`, `BETWEEN`, `NOT BETWEEN`, `IS NULL`,
     * `IS NOT NULL` or `MATCH`
     * @param array<Closure|float|int|string|null> $values Values used by the operator
     *
     * @return static
     */
    private function addHaving(
        string $glue,
        array | Closure | string $column,
        string $operator,
        array $values
    ) : static {
        return $this->addWhere($glue, $column, $operator, $values, 'having');
    }

    /**
     * Renders the full HAVING clause.
     *
     * @return string|null The full clause or null if has not a clause
     */
    protected function renderHaving() : ?string
    {
        return $this->renderWhere('having');
    }
}
