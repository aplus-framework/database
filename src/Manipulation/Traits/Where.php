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
use InvalidArgumentException;

/**
 * Trait Where.
 *
 * @package database
 */
trait Where
{
    /**
     * Appends an "AND $column $operator ...$values" condition in the WHERE clause.
     *
     * @param array<array<mixed>|Closure|string>|Closure|string $column Closure for a subquery,
     * a string with the column name or an array with column names on WHERE MATCH clause
     * @param string $operator
     * @param array<array<mixed>|Closure|float|int|string|null>|Closure|float|int|string|null ...$values
     *
     * @return static
     */
    public function where(
        array | Closure | string $column,
        string $operator,
        array | Closure | float | int | string | null ...$values
    ) : static {
        // @phpstan-ignore-next-line
        return $this->addWhere('AND', $column, $operator, $values);
    }

    /**
     * Appends a "OR $column $operator ...$values" condition in the WHERE clause.
     *
     * @param array<array<mixed>|Closure|string>|Closure|string $column Closure for a subquery,
     * a string with the column name or an array with column names on WHERE MATCH clause
     * @param string $operator
     * @param array<array<mixed>|Closure|float|int|string|null>|Closure|float|int|string|null ...$values
     *
     * @return static
     */
    public function orWhere(
        array | Closure | string $column,
        string $operator,
        array | Closure | float | int | string | null ...$values
    ) : static {
        // @phpstan-ignore-next-line
        return $this->addWhere('OR', $column, $operator, $values);
    }

    /**
     * Appends an "AND $column = $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/equal/
     *
     * @return static
     */
    public function whereEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->where($column, '=', $value);
    }

    /**
     * Appends a "OR $column = $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/equal/
     *
     * @return static
     */
    public function orWhereEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orWhere($column, '=', $value);
    }

    /**
     * Appends an "AND $column != $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/not-equal/
     *
     * @return static
     */
    public function whereNotEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->where($column, '!=', $value);
    }

    /**
     * Appends a "OR $column != $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/not-equal/
     *
     * @return static
     */
    public function orWhereNotEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orWhere($column, '!=', $value);
    }

    /**
     * Appends an "AND $column <=> $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/null-safe-equal/
     *
     * @return static
     */
    public function whereNullSafeEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->where($column, '<=>', $value);
    }

    /**
     * Appends a "OR $column <=> $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/null-safe-equal/
     *
     * @return static
     */
    public function orWhereNullSafeEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orWhere($column, '<=>', $value);
    }

    /**
     * Appends an "AND $column < $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/less-than/
     *
     * @return static
     */
    public function whereLessThan(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->where($column, '<', $value);
    }

    /**
     * Appends a "OR $column < $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/less-than/
     *
     * @return static
     */
    public function orWhereLessThan(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orWhere($column, '<', $value);
    }

    /**
     * Appends an "AND $column <= $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/less-than-or-equal/
     *
     * @return static
     */
    public function whereLessThanOrEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->where($column, '<=', $value);
    }

    /**
     * Appends a "OR $column <= $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/less-than-or-equal/
     *
     * @return static
     */
    public function orWhereLessThanOrEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orWhere($column, '<=', $value);
    }

    /**
     * Appends an "AND $column > $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/greater-than/
     *
     * @return static
     */
    public function whereGreaterThan(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->where($column, '>', $value);
    }

    /**
     * Appends a "OR $column > $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/greater-than/
     *
     * @return static
     */
    public function orWhereGreaterThan(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orWhere($column, '>', $value);
    }

    /**
     * Appends an "AND $column >= $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/greater-than-or-equal/
     *
     * @return static
     */
    public function whereGreaterThanOrEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->where($column, '>=', $value);
    }

    /**
     * Appends a "OR $column >= $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/greater-than-or-equal/
     *
     * @return static
     */
    public function orWhereGreaterThanOrEqual(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orWhere($column, '>=', $value);
    }

    /**
     * Appends an "AND $column LIKE $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/like/
     *
     * @return static
     */
    public function whereLike(Closure | string $column, Closure | float | int | string | null $value) : static
    {
        return $this->where($column, 'LIKE', $value);
    }

    /**
     * Appends a "OR $column LIKE $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/like/
     *
     * @return static
     */
    public function orWhereLike(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orWhere($column, 'LIKE', $value);
    }

    /**
     * Appends an "AND $column NOT LIKE $value" condition.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/not-like/
     *
     * @return static
     */
    public function whereNotLike(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->where($column, 'NOT LIKE', $value);
    }

    /**
     * Appends a "OR $column NOT LIKE $value" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     *
     * @see https://mariadb.com/kb/en/not-like/
     *
     * @return static
     */
    public function orWhereNotLike(
        Closure | string $column,
        Closure | float | int | string | null $value
    ) : static {
        return $this->orWhere($column, 'NOT LIKE', $value);
    }

    /**
     * Appends an "AND $column IN (...$values)" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     * @param Closure|float|int|string|null ...$values
     *
     * @see https://mariadb.com/kb/en/in/
     *
     * @return static
     */
    public function whereIn(
        Closure | string $column,
        Closure | float | int | string | null $value,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->where($column, 'IN', ...[$value, ...$values]);
    }

    /**
     * Appends a "OR $column IN (...$values)" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     * @param Closure|float|int|string|null ...$values
     *
     * @see https://mariadb.com/kb/en/in/
     *
     * @return static
     */
    public function orWhereIn(
        Closure | string $column,
        Closure | float | int | string | null $value,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->orWhere($column, 'IN', ...[$value, ...$values]);
    }

    /**
     * Appends an "AND $column NOT IN (...$values)" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     * @param Closure|float|int|string|null ...$values
     *
     * @see https://mariadb.com/kb/en/not-in/
     *
     * @return static
     */
    public function whereNotIn(
        Closure | string $column,
        Closure | float | int | string | null $value,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->where($column, 'NOT IN', ...[$value, ...$values]);
    }

    /**
     * Appends a "OR $column NOT IN (...$values)" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $value
     * @param Closure|float|int|string|null ...$values
     *
     * @see https://mariadb.com/kb/en/not-in/
     *
     * @return static
     */
    public function orWhereNotIn(
        Closure | string $column,
        Closure | float | int | string | null $value,
        Closure | float | int | string | null ...$values
    ) : static {
        return $this->orWhere($column, 'NOT IN', ...[$value, ...$values]);
    }

    /**
     * Appends an "AND $column BETWEEN $min AND $max" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $min
     * @param Closure|float|int|string|null $max
     *
     * @see https://mariadb.com/kb/en/between-and/
     *
     * @return static
     */
    public function whereBetween(
        Closure | string $column,
        Closure | float | int | string | null $min,
        Closure | float | int | string | null $max
    ) : static {
        return $this->where($column, 'BETWEEN', $min, $max);
    }

    /**
     * Appends a "OR $column BETWEEN $min AND $max" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $min
     * @param Closure|float|int|string|null $max
     *
     * @see https://mariadb.com/kb/en/between-and/
     *
     * @return static
     */
    public function orWhereBetween(
        Closure | string $column,
        Closure | float | int | string | null $min,
        Closure | float | int | string | null $max
    ) : static {
        return $this->orWhere($column, 'BETWEEN', $min, $max);
    }

    /**
     * Appends an "AND $column NOT BETWEEN $min AND $max" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $min
     * @param Closure|float|int|string|null $max
     *
     * @see https://mariadb.com/kb/en/not-between/
     *
     * @return static
     */
    public function whereNotBetween(
        Closure | string $column,
        Closure | float | int | string | null $min,
        Closure | float | int | string | null $max
    ) : static {
        return $this->where($column, 'NOT BETWEEN', $min, $max);
    }

    /**
     * Appends a "OR $column NOT BETWEEN $min AND $max" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     * @param Closure|float|int|string|null $min
     * @param Closure|float|int|string|null $max
     *
     * @see https://mariadb.com/kb/en/not-between/
     *
     * @return static
     */
    public function orWhereNotBetween(
        Closure | string $column,
        Closure | float | int | string | null $min,
        Closure | float | int | string | null $max
    ) : static {
        return $this->orWhere($column, 'NOT BETWEEN', $min, $max);
    }

    /**
     * Appends an "AND $column IS NULL" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     *
     * @see https://mariadb.com/kb/en/is-null/
     *
     * @return static
     */
    public function whereIsNull(Closure | string $column) : static
    {
        return $this->where($column, 'IS NULL');
    }

    /**
     * Appends a "OR $column IS NULL" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     *
     * @see https://mariadb.com/kb/en/is-null/
     *
     * @return static
     */
    public function orWhereIsNull(Closure | string $column) : static
    {
        return $this->orWhere($column, 'IS NULL');
    }

    /**
     * Appends an "AND $column IS NOT NULL" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     *
     * @see https://mariadb.com/kb/en/is-not-null/
     *
     * @return static
     */
    public function whereIsNotNull(Closure | string $column) : static
    {
        return $this->where($column, 'IS NOT NULL');
    }

    /**
     * Appends a "OR $column IS NOT NULL" condition in the WHERE clause.
     *
     * @param Closure|string $column Closure for a subquery or a string with the column name
     *
     * @see https://mariadb.com/kb/en/is-not-null/
     *
     * @return static
     */
    public function orWhereIsNotNull(Closure | string $column) : static
    {
        return $this->orWhere($column, 'IS NOT NULL');
    }

    /* TODO: https://mariadb.com/kb/en/subqueries-and-exists/
     public function whereExists(Closure $subquery)
    {
        $this->subquery($subquery);
    }

    public function whereNotExists(Closure $subquery)
    {
        $this->subquery($subquery);
    }*/

    /**
     * Appends an "AND MATCH (...$columns) AGAINST ($against IN NATURAL LANGUAGE MODE)" fulltext
     * searching in the WHERE clause.
     *
     * @param array<array<mixed>|Closure|string>|Closure|string $columns Columns to MATCH
     * @param array<array<mixed>|Closure|string>|Closure|string $against AGAINST expression
     *
     * @see https://mariadb.com/kb/en/full-text-index-overview/
     * @see https://mariadb.com/kb/en/match-against/
     *
     * @return static
     */
    public function whereMatch(
        array | Closure | string $columns,
        array | Closure | string $against
    ) : static {
        return $this->where($columns, 'MATCH', $against);
    }

    /**
     * Appends a "OR MATCH (...$columns) AGAINST ($against IN NATURAL LANGUAGE MODE)" fulltext
     * searching in the WHERE clause.
     *
     * @param array<array<mixed>|Closure|string>|Closure|string $columns Columns to MATCH
     * @param array<array<mixed>|Closure|string>|Closure|string $against AGAINST expression
     *
     * @see https://mariadb.com/kb/en/full-text-index-overview/
     * @see https://mariadb.com/kb/en/match-against/
     *
     * @return static
     */
    public function orWhereMatch(
        array | Closure | string $columns,
        array | Closure | string $against
    ) : static {
        return $this->orWhere($columns, 'MATCH', $against);
    }

    /**
     * Appends an "AND MATCH (...$columns) AGAINST ($against WITH QUERY EXPANSION)" fulltext
     * searching in the WHERE clause.
     *
     * @param array<array<mixed>|Closure|string>|Closure|string $columns Columns to MATCH
     * @param array<array<mixed>|Closure|string>|Closure|string $against AGAINST expression
     *
     * @see https://mariadb.com/kb/en/full-text-index-overview/
     * @see https://mariadb.com/kb/en/match-against/
     *
     * @return static
     */
    public function whereMatchWithQueryExpansion(
        array | Closure | string $columns,
        array | Closure | string $against
    ) : static {
        return $this->where($columns, 'MATCH', $against, 'WITH QUERY EXPANSION');
    }

    /**
     * Appends a "OR MATCH (...$columns) AGAINST ($against WITH QUERY EXPANSION)" fulltext
     * searching in the WHERE clause.
     *
     * @param array<array<mixed>|Closure|string>|Closure|string $columns Columns to MATCH
     * @param array<array<mixed>|Closure|string>|Closure|string $against AGAINST expression
     *
     * @see https://mariadb.com/kb/en/full-text-index-overview/
     * @see https://mariadb.com/kb/en/match-against/
     *
     * @return static
     */
    public function orWhereMatchWithQueryExpansion(
        array | Closure | string $columns,
        array | Closure | string $against
    ) : static {
        return $this->orWhere($columns, 'MATCH', $against, 'WITH QUERY EXPANSION');
    }

    /**
     * Appends an "AND MATCH (...$columns) AGAINST ($against IN BOOLEAN MODE)" fulltext searching in
     * the WHERE clause.
     *
     * @param array<array<mixed>|Closure|string>|Closure|string $columns Columns to MATCH
     * @param array<array<mixed>|Closure|string>|Closure|string $against AGAINST expression
     *
     * @see https://mariadb.com/kb/en/full-text-index-overview/
     * @see https://mariadb.com/kb/en/match-against/
     *
     * @return static
     */
    public function whereMatchInBooleanMode(
        array | Closure | string $columns,
        array | Closure | string $against
    ) : static {
        return $this->where($columns, 'MATCH', $against, 'IN BOOLEAN MODE');
    }

    /**
     * Appends a "OR MATCH (...$columns) AGAINST ($against IN BOOLEAN MODE)" fulltext searching in
     * the WHERE clause.
     *
     * @param array<array<mixed>|Closure|string>|Closure|string $columns Columns to MATCH
     * @param array<array<mixed>|Closure|string>|Closure|string $against AGAINST expression
     *
     * @see https://mariadb.com/kb/en/full-text-index-overview/
     * @see https://mariadb.com/kb/en/match-against/
     *
     * @return static
     */
    public function orWhereMatchInBooleanMode(
        array | Closure | string $columns,
        array | Closure | string $against
    ) : static {
        return $this->orWhere($columns, 'MATCH', $against, 'IN BOOLEAN MODE');
    }

    /**
     * Adds a WHERE (or HAVING) part.
     *
     * @param string $glue `AND` or `OR`
     * @param array<array<mixed>|Closure|string>|Closure|string $column
     * @param string $operator `=`, `<=>`, `!=`, `<>`, `>`, `>=`, `<`, `<=`, `LIKE`,
     * `NOT LIKE`, `IN`, `NOT IN`, `BETWEEN`, `NOT BETWEEN`, `IS NULL`, `IS NOT NULL` or `MATCH`
     * @param array<Closure|float|int|string|null> $values Values used by the operator
     * @param string $clause `where` or `having`
     *
     * @return static
     */
    private function addWhere(
        string $glue,
        array | Closure | string $column,
        string $operator,
        array $values,
        string $clause = 'where'
    ) : static {
        $this->sql[$clause][] = [
            'glue' => $glue,
            'column' => $column,
            'operator' => $operator,
            'values' => $values,
        ];
        return $this;
    }

    /**
     * Renders a MATCH AGAINST clause.
     *
     * @param array<Closure|string>|Closure|string $columns
     * @param array<string>|Closure|string $expression
     * @param string $modifier
     *
     * @return string
     */
    private function renderMatch(
        array | Closure | string $columns,
        array | Closure | string $expression,
        string $modifier = ''
    ) {
        $columns = $this->renderMatchColumns($columns);
        $expression = $this->renderMatchExpression($expression);
        if ($modifier) {
            $modifier = ' ' . $modifier;
        }
        return "MATCH ({$columns}) AGAINST ({$expression}{$modifier})";
    }

    /**
     * @param array<Closure|string>|Closure|string $columns
     *
     * @return string
     */
    private function renderMatchColumns(array | Closure | string $columns) : string
    {
        if (\is_array($columns)) {
            foreach ($columns as &$column) {
                $column = $this->renderIdentifier($column);
            }
            unset($column);
            return \implode(', ', $columns); // @phpstan-ignore-line
        }
        if (\is_string($columns)) {
            return $this->renderIdentifier($columns);
        }
        return $this->subquery($columns);
    }

    /**
     * @param array<string>|Closure|string $expression
     *
     * @return float|int|string
     */
    private function renderMatchExpression(
        array | Closure | string $expression
    ) : float | int | string {
        if (\is_array($expression)) {
            $expression = \implode(', ', $expression);
            return $this->database->quote($expression);
        }
        if (\is_string($expression)) {
            return $this->database->quote($expression);
        }
        return $this->subquery($expression);
    }

    /**
     * Renders the full WHERE (or HAVING) clause.
     *
     * @param string $clause `where` or `having`
     *
     * @return string|null The full clause or null if has not a clause
     */
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

    /**
     * Renders a WHERE part. Like: `AND column IN('value1', 'value2')`.
     *
     * @param array<string,mixed> $part Keys: `glue`, `operator`, `column` and `values`
     * @param bool $first Is the first part? Prepends the operator (`AND` or `OR`)
     *
     * @return string
     */
    private function renderWherePart(array $part, bool $first = false) : string
    {
        $condition = '';
        if ($first === false) {
            $condition .= " {$part['glue']} ";
        }
        if ($part['operator'] === 'MATCH') {
            return $condition . $this->renderMatch(
                $part['column'],
                $part['values'][0],
                $part['values'][1] ?? ''
            );
        }
        $part['column'] = $this->renderIdentifier($part['column']);
        $part['operator'] = $this->renderWhereOperator($part['operator']);
        $part['values'] = $this->renderWhereValues($part['operator'], $part['values']);
        $condition .= "{$part['column']} {$part['operator']}";
        $condition .= $part['values'] === null ? '' : " {$part['values']}";
        return $condition;
    }

    /**
     * Renders and validates a comparison operator.
     *
     * @param string $operator `=`, `<=>`, `!=`, `<>`, `>`, `>=`, `<`, `<=`, `LIKE`,
     * `NOT LIKE`, `IN`, `NOT IN`, `BETWEEN`, `NOT BETWEEN`, `IS NULL` or `IS NOT NULL`
     *
     * @throws InvalidArgumentException for invalid comparison operator
     *
     * @return string The operator
     */
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

    /**
     * Renders the values used by a comparison operator.
     *
     * @param string $operator `=`, `<=>`, `!=`, `<>`, `>`, `>=`, `<`, `<=`, `LIKE`,
     * `NOT LIKE`, `IN`, `NOT IN`, `BETWEEN`, `NOT BETWEEN`, `IS NULL` or `IS NOT NULL`
     * @param array<Closure|float|int|string|null> $values
     *
     * @throws InvalidArgumentException for invalid comparison operator
     *
     * @return string|null The values used by the operator
     */
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

    /**
     * Quote the input values or transform it in subqueries.
     *
     * @param array<bool|Closure|float|int|string|null> $values
     *
     * @return array<float|int|string> Each input value quoted or transformed in subquery
     */
    private function prepareWhereValues(array $values) : array
    {
        foreach ($values as &$value) {
            $value = $value instanceof Closure
                ? $this->subquery($value)
                : $this->database->quote($value);
        }
        return $values; // @phpstan-ignore-line
    }

    /**
     * Renders the values of operators that receive exactly one value.
     *
     * @param string $operator `=`, `<=>`,    `!=`, `<>`, `>`, `>=`, `<`, `<=`, `LIKE` or `NOT LIKE`
     * @param array<float|int|string> $values Must receive exactly 1 value, index 0
     *
     * @throws InvalidArgumentException if $values has more than one value
     *
     * @return string
     */
    private function renderWhereValuesPartComparator(string $operator, array $values) : string
    {
        if (isset($values[1]) || ! isset($values[0])) {
            throw new InvalidArgumentException(
                "Operator {$operator} must receive exactly 1 parameter"
            );
        }
        return (string) $values[0];
    }

    /**
     * Implode values for `IN` or `NOT IN`.
     *
     * @param string $operator `IN` or `NOT IN`
     * @param array<float|int|string> $values Must receive at least 1 value
     *
     * @throws InvalidArgumentException if $values does not receive any value
     *
     * @return string
     */
    private function renderWhereValuesPartIn(string $operator, array $values) : string
    {
        if (empty($values)) {
            throw new InvalidArgumentException(
                "Operator {$operator} must receive at least 1 parameter"
            );
        }
        return '(' . \implode(', ', $values) . ')';
    }

    /**
     * Renders values for `BETWEEN` or `NOT BETWEEN`.
     *
     * @param string $operator `BETWEEN` or `NOT BETWEEN`
     * @param array<float|int|string> $values Two values, indexes 0 and 1
     *
     * @throws InvalidArgumentException if $values not receive exactly two values
     *
     * @return string
     */
    private function renderWhereValuesPartBetween(string $operator, array $values) : string
    {
        if (isset($values[2]) || ! isset($values[0], $values[1])) {
            throw new InvalidArgumentException(
                "Operator {$operator} must receive exactly 2 parameters"
            );
        }
        return "{$values[0]} AND {$values[1]}";
    }

    /**
     * Renders the lonely operators, `IS NULL` or `IS NOT NULL`.
     *
     * @param string $operator `IS NULL` or `IS NOT NULL`
     * @param array<float|int|string> $values Must be an empty array
     *
     * @throws InvalidArgumentException if $values is not empty
     *
     * @return null
     */
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
