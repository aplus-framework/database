<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Manipulation;

use Closure;
use InvalidArgumentException;

/**
 * Class Statement.
 *
 * @see https://mariadb.com/kb/en/data-manipulation/
 *
 * @package database
 */
abstract class Statement extends \Framework\Database\Statement
{
    /**
     * Sets the statement options.
     *
     * @param string $option One of the OPT_* constants
     * @param string ...$options Each option value must be one of the OPT_* constants
     *
     * @return static
     */
    public function options(string $option, string ...$options) : static
    {
        $this->sql['options'] = [];
        foreach ([$option, ...$options] as $option) {
            $this->sql['options'][] = $option;
        }
        return $this;
    }

    /**
     * Tells if the statement has options set.
     *
     * @return bool
     */
    protected function hasOptions() : bool
    {
        return isset($this->sql['options']);
    }

    abstract protected function renderOptions() : ?string;

    /**
     * Returns an SQL part between parentheses.
     *
     * @param Closure $subquery A {@see Closure} having the current Manipulation
     * instance as first argument. The returned value must be scalar
     *
     * @see https://mariadb.com/kb/en/subqueries/
     * @see https://mariadb.com/kb/en/built-in-functions/
     *
     * @return string
     */
    protected function subquery(Closure $subquery) : string
    {
        return '(' . $subquery($this->database) . ')';
    }

    /**
     * Sets the LIMIT clause.
     *
     * @param int $limit
     * @param int|null $offset
     *
     * @see https://mariadb.com/kb/en/limit/
     *
     * @return static
     */
    protected function setLimit(int $limit, int $offset = null) : static
    {
        $this->sql['limit'] = [
            'limit' => $limit,
            'offset' => $offset,
        ];
        return $this;
    }

    /**
     * Renders the LIMIT clause.
     *
     * @return string|null
     */
    protected function renderLimit() : ?string
    {
        if ( ! isset($this->sql['limit'])) {
            return null;
        }
        if ($this->sql['limit']['limit'] < 1) {
            throw new InvalidArgumentException('LIMIT must be greater than 0');
        }
        $offset = $this->sql['limit']['offset'];
        if ($offset !== null) {
            if ($offset < 1) {
                throw new InvalidArgumentException('LIMIT OFFSET must be greater than 0');
            }
            $offset = " OFFSET {$this->sql['limit']['offset']}";
        }
        return " LIMIT {$this->sql['limit']['limit']}{$offset}";
    }

    /**
     * Renders a column part.
     *
     * @param Closure|string $column The column name or a subquery
     *
     * @return string
     */
    protected function renderIdentifier(Closure | string $column) : string
    {
        return $column instanceof Closure
            ? $this->subquery($column)
            : $this->database->protectIdentifier($column);
    }

    /**
     * Renders a column part with an optional alias name, AS clause.
     *
     * @param array<string,Closure|string>|Closure|string $column The column name,
     * a subquery or an array where the index is the alias and the value is the column/subquery
     *
     * @return string
     */
    protected function renderAliasedIdentifier(array | Closure | string $column) : string
    {
        if (\is_array($column)) {
            if (\count($column) !== 1) {
                throw new InvalidArgumentException('Aliased column must have only 1 key');
            }
            $alias = (string) \array_key_first($column);
            return $this->renderIdentifier($column[$alias]) . ' AS '
                . $this->database->protectIdentifier($alias);
        }
        return $this->renderIdentifier($column);
    }

    /**
     * Renders a subquery or quote a value.
     *
     * @param Closure|float|int|string|null $value A {@see Closure} for
     * subquery, other types to quote
     *
     * @return float|int|string
     */
    protected function renderValue(Closure | float | int | string | null $value) : float | int | string
    {
        return $value instanceof Closure
            ? $this->subquery($value)
            : $this->database->quote($value);
    }

    /**
     * Renders an assignment part.
     *
     * @param string $identifier Identifier/column name
     * @param Closure|float|int|string|null $expression Expression/value
     *
     * @see Statement::renderValue()
     * @see https://mariadb.com/kb/en/assignment-operators-assignment-operator/
     *
     * @return string
     */
    protected function renderAssignment(
        string $identifier,
        Closure | float | int | string | null $expression
    ) : string {
        return $this->database->protectIdentifier($identifier)
            . ' = ' . $this->renderValue($expression);
    }
}
