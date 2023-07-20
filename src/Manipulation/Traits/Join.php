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
use LogicException;

/**
 * Trait Join.
 *
 * @see  https://mariadb.com/kb/en/joins/
 *
 * @package database
 *
 * @todo STRAIGHT_JOIN - https://mariadb.com/kb/en/index-hints-how-to-force-query-plans/
 */
trait Join
{
    /**
     * Sets the FROM clause.
     *
     * @param array<string,Closure|string>|Closure|string $reference Table reference
     * @param array<string,Closure|string>|Closure|string ...$references Table references
     *
     * @see https://mariadb.com/kb/en/join-syntax/
     *
     * @return static
     */
    public function from(
        array | Closure | string $reference,
        array | Closure | string ...$references
    ) : static {
        $this->sql['from'] = [];
        foreach ([$reference, ...$references] as $reference) {
            $this->sql['from'][] = $reference;
        }
        return $this;
    }

    /**
     * Renders the FROM clause.
     *
     * @return string|null The FROM clause or null if it was not set
     */
    protected function renderFrom() : ?string
    {
        if ( ! isset($this->sql['from'])) {
            return null;
        }
        $tables = [];
        foreach ($this->sql['from'] as $table) {
            $tables[] = $this->renderAliasedIdentifier($table);
        }
        return ' FROM ' . \implode(', ', $tables);
    }

    /**
     * Tells if the FROM clause was set.
     *
     * @param string|null $clause A clause where FROM is required
     *
     * @throws LogicException if FROM is not set, but is required for some other clause
     *
     * @return bool True if it has FROM, otherwise false
     */
    protected function hasFrom(string $clause = null) : bool
    {
        if (isset($this->sql['from'])) {
            return true;
        }
        if ($clause === null) {
            return false;
        }
        throw new LogicException("Clause {$clause} only works with FROM");
    }

    /**
     * Adds a JOIN clause with "$type JOIN $table $clause $conditional".
     *
     * @param Closure|string $table Table factor
     * @param string $type JOIN type. One of: `CROSS`, `INNER`, `LEFT`, `LEFT OUTER`,
     * `RIGHT`, `RIGHT OUTER`, `NATURAL`, `NATURAL LEFT`, `NATURAL LEFT OUTER`,
     * `NATURAL RIGHT`, `NATURAL RIGHT OUTER` or empty (same as `INNER`)
     * @param string|null $clause Condition clause. Null if it has a NATURAL type,
     * otherwise `ON` or `USING`
     * @param array<int,Closure|string>|Closure|null $conditional A conditional
     * expression as Closure or the columns list as array
     *
     * @return static
     */
    public function join(
        Closure | string $table,
        string $type = '',
        string $clause = null,
        array | Closure $conditional = null
    ) : static {
        return $this->setJoin($table, $type, $clause, $conditional);
    }

    /**
     * Adds a JOIN clause with "JOIN $table ON $conditional".
     *
     * @param Closure|string $table Table factor
     * @param Closure $conditional Conditional expression
     *
     * @return static
     */
    public function joinOn(Closure | string $table, Closure $conditional) : static
    {
        return $this->setJoin($table, '', 'ON', $conditional);
    }

    /**
     * Adds a JOIN clause with "JOIN $table USING ...$columns".
     *
     * @param Closure|string $table Table factor
     * @param Closure|string ...$columns Columns list
     *
     * @return static
     */
    public function joinUsing(Closure | string $table, Closure | string ...$columns) : static
    {
        return $this->setJoin($table, '', 'USING', $columns);
    }

    /**
     * Adds a JOIN clause with "INNER JOIN $table ON $conditional".
     *
     * @param Closure|string $table Table factor
     * @param Closure $conditional Conditional expression
     *
     * @return static
     */
    public function innerJoinOn(Closure | string $table, Closure $conditional) : static
    {
        return $this->setJoin($table, 'INNER', 'ON', $conditional);
    }

    /**
     * Adds a JOIN clause with "INNER JOIN $table USING ...$columns".
     *
     * @param Closure|string $table Table factor
     * @param Closure|string ...$columns Columns list
     *
     * @return static
     */
    public function innerJoinUsing(Closure | string $table, Closure | string ...$columns) : static
    {
        return $this->setJoin($table, 'INNER', 'USING', $columns);
    }

    /**
     * Adds a JOIN clause with "CROSS JOIN $table".
     *
     * @param Closure|string $table Table factor
     *
     * @return static
     */
    public function crossJoin(Closure | string $table) : static
    {
        return $this->setJoin($table, 'CROSS');
    }

    /**
     * Adds a JOIN clause with "CROSS JOIN $table ON $conditional".
     *
     * @param Closure|string $table Table factor
     * @param Closure $conditional Conditional expression
     *
     * @return static
     */
    public function crossJoinOn(Closure | string $table, Closure $conditional) : static
    {
        return $this->setJoin($table, 'CROSS', 'ON', $conditional);
    }

    /**
     * Adds a JOIN clause with "CROSS JOIN $table USING ...$columns".
     *
     * @param Closure|string $table Table factor
     * @param Closure|string ...$columns Columns list
     *
     * @return static
     */
    public function crossJoinUsing(Closure | string $table, Closure | string ...$columns) : static
    {
        return $this->setJoin($table, 'CROSS', 'USING', $columns);
    }

    /**
     * Adds a JOIN clause with "LEFT JOIN $table ON $conditional".
     *
     * @param Closure|string $table Table factor
     * @param Closure $conditional Conditional expression
     *
     * @return static
     */
    public function leftJoinOn(Closure | string $table, Closure $conditional) : static
    {
        return $this->setJoin($table, 'LEFT', 'ON', $conditional);
    }

    /**
     * Adds a JOIN clause with "LEFT JOIN $table USING ...$columns".
     *
     * @param Closure|string $table Table factor
     * @param Closure|string ...$columns Columns list
     *
     * @return static
     */
    public function leftJoinUsing(Closure | string $table, Closure | string ...$columns) : static
    {
        return $this->setJoin($table, 'LEFT', 'USING', $columns);
    }

    /**
     * Adds a JOIN clause with "LEFT OUTER JOIN $table ON $conditional".
     *
     * @param Closure|string $table Table factor
     * @param Closure $conditional Conditional expression
     *
     * @return static
     */
    public function leftOuterJoinOn(Closure | string $table, Closure $conditional) : static
    {
        return $this->setJoin($table, 'LEFT OUTER', 'ON', $conditional);
    }

    /**
     * Adds a JOIN clause with "LEFT OUTER JOIN $table USING ...$columns".
     *
     * @param Closure|string $table Table factor
     * @param Closure|string ...$columns Columns list
     *
     * @return static
     */
    public function leftOuterJoinUsing(Closure | string $table, Closure | string ...$columns) : static
    {
        return $this->setJoin($table, 'LEFT OUTER', 'USING', $columns);
    }

    /**
     * Adds a JOIN clause with "RIGHT JOIN $table ON $conditional".
     *
     * @param Closure|string $table Table factor
     * @param Closure $conditional Conditional expression
     *
     * @return static
     */
    public function rightJoinOn(Closure | string $table, Closure $conditional) : static
    {
        return $this->setJoin($table, 'RIGHT', 'ON', $conditional);
    }

    /**
     * Adds a JOIN clause with "RIGHT JOIN $table USING ...$columns".
     *
     * @param Closure|string $table Table factor
     * @param Closure|string ...$columns Columns list
     *
     * @return static
     */
    public function rightJoinUsing(Closure | string $table, Closure | string ...$columns) : static
    {
        return $this->setJoin($table, 'RIGHT', 'USING', $columns);
    }

    /**
     * Adds a JOIN clause with "RIGHT OUTER JOIN $table ON $conditional".
     *
     * @param Closure|string $table Table factor
     * @param Closure $conditional Conditional expression
     *
     * @return static
     */
    public function rightOuterJoinOn(Closure | string $table, Closure $conditional) : static
    {
        return $this->setJoin($table, 'RIGHT OUTER', 'ON', $conditional);
    }

    /**
     * Adds a JOIN clause with "RIGHT OUTER JOIN $table USING ...$columns".
     *
     * @param Closure|string $table Table factor
     * @param Closure|string ...$columns Columns list
     *
     * @return static
     */
    public function rightOuterJoinUsing(Closure | string $table, Closure | string ...$columns) : static
    {
        return $this->setJoin($table, 'RIGHT OUTER', 'USING', $columns);
    }

    /**
     * Adds a JOIN clause with "NATURAL JOIN $table".
     *
     * @param Closure|string $table Table factor
     *
     * @return static
     */
    public function naturalJoin(Closure | string $table) : static
    {
        return $this->setJoin($table, 'NATURAL');
    }

    /**
     * Adds a JOIN clause with "NATURAL LEFT JOIN $table".
     *
     * @param Closure|string $table Table factor
     *
     * @return static
     */
    public function naturalLeftJoin(Closure | string $table) : static
    {
        return $this->setJoin($table, 'NATURAL LEFT');
    }

    /**
     * Adds a JOIN clause with "NATURAL LEFT OUTER JOIN $table".
     *
     * @param Closure|string $table Table factor
     *
     * @return static
     */
    public function naturalLeftOuterJoin(Closure | string $table) : static
    {
        return $this->setJoin($table, 'NATURAL LEFT OUTER');
    }

    /**
     * Adds a JOIN clause with "NATURAL RIGHT JOIN $table".
     *
     * @param Closure|string $table Table factor
     *
     * @return static
     */
    public function naturalRightJoin(Closure | string $table) : static
    {
        return $this->setJoin($table, 'NATURAL RIGHT');
    }

    /**
     * Adds a JOIN clause with "NATURAL RIGHT OUTER JOIN $table".
     *
     * @param Closure|string $table Table factor
     *
     * @return static
     */
    public function naturalRightOuterJoin(Closure | string $table) : static
    {
        return $this->setJoin($table, 'NATURAL RIGHT OUTER');
    }

    /**
     * Sets the JOIN clause.
     *
     * @param Closure|string $table The table factor
     * @param string $type ``, `CROSS`, `INNER`, `LEFT`, `LEFT OUTER`, `RIGHT`,
     * `RIGHT OUTER`, `NATURAL`, `NATURAL LEFT`, `NATURAL LEFT OUTER`, `NATURAL RIGHT`
     * or `NATURAL RIGHT OUTER`
     * @param string|null $clause `ON`, `USING` or null for none
     * @param array<Closure|string>|Closure|null $expression Column(s) or subquery(ies)
     *
     * @return static
     */
    private function setJoin(
        Closure | string $table,
        string $type,
        string $clause = null,
        Closure | array $expression = null
    ) : static {
        $this->sql['join'][] = [
            'type' => $type,
            'table' => $table,
            'clause' => $clause,
            'expression' => $expression,
        ];
        return $this;
    }

    /**
     * Renders the JOIN clause.
     *
     * @return string|null The JOIN clause or null if it was not set
     */
    protected function renderJoin() : ?string
    {
        if ( ! isset($this->sql['join'])) {
            return null;
        }
        $result = '';
        foreach ($this->sql['join'] as $index => $join) {
            $type = $this->renderJoinType($join['type']);
            $conditional = $this->renderJoinConditional(
                $type,
                $join['table'],
                $join['clause'],
                $join['expression']
            );
            if ($type) {
                $type .= ' ';
            }
            if ($index > 0) {
                $result .= \PHP_EOL;
            }
            $result .= " {$type}JOIN {$conditional}";
        }
        return $result;
    }

    /**
     * Renders the JOIN conditional part.
     *
     * @param string $type ``, `CROSS`,`INNER`, `LEFT`, `LEFT OUTER`, `RIGHT`,
     * `RIGHT OUTER`, `NATURAL`, `NATURAL LEFT`, `NATURAL LEFT OUTER`, `NATURAL RIGHT`
     * or `NATURAL RIGHT OUTER`
     * @param string $table The table name
     * @param string|null $clause `ON`, `USING` or null for none
     * @param array<Closure|string>|Closure|null $expression Column(s) or subquery(ies)
     *
     * @return string The JOIN conditional part
     */
    private function renderJoinConditional(
        string $type,
        string $table,
        ?string $clause,
        Closure | array | null $expression
    ) : string {
        $table = $this->renderAliasedIdentifier($table);
        $isNatural = $this->checkNaturalJoinType($type, $clause, $expression);
        if ($isNatural) {
            return $table;
        }
        $conditional = '';
        $clause = $this->renderJoinConditionClause($clause);
        if ($clause) {
            $conditional .= ' ' . $clause;
        }
        $expression = $this->renderJoinConditionExpression($clause, $expression);
        if ($expression) {
            $conditional .= ' ' . $expression;
        }
        return $table . $conditional;
    }

    /**
     * Validates and renders the JOIN type.
     *
     * @param string $type ``, `CROSS`,`INNER`, `LEFT`, `LEFT OUTER`, `RIGHT`,
     * `RIGHT OUTER`, `NATURAL`, `NATURAL LEFT`, `NATURAL LEFT OUTER`, `NATURAL RIGHT`
     * or `NATURAL RIGHT OUTER`
     *
     * @throws InvalidArgumentException for invalid type
     *
     * @return string The input ype
     */
    private function renderJoinType(string $type) : string
    {
        $result = \strtoupper($type);
        if (\in_array($result, [
            '',
            'CROSS',
            'INNER',
            'LEFT',
            'LEFT OUTER',
            'RIGHT',
            'RIGHT OUTER',
            'NATURAL',
            'NATURAL LEFT',
            'NATURAL LEFT OUTER',
            'NATURAL RIGHT',
            'NATURAL RIGHT OUTER',
        ], true)) {
            return $result;
        }
        throw new InvalidArgumentException("Invalid JOIN type: {$type}");
    }

    /**
     * Check if a JOIN type belongs to the NATURAL group.
     *
     * @param string $type `NATURAL`, `NATURAL LEFT`, `NATURAL LEFT OUTER`,
     * `NATURAL RIGHT`, `NATURAL RIGHT OUTER` or any other non-natural
     * @param string|null $clause Must be null if type is natural
     * @param array<Closure|string>|Closure|null $expression Must be null if type is natural
     *
     * @throws InvalidArgumentException if $type is natural and has clause or expression
     *
     * @return bool True if the type is natural, otherwise false
     */
    private function checkNaturalJoinType(
        string $type,
        ?string $clause,
        Closure | array | null $expression
    ) : bool {
        if (\in_array($type, [
            'NATURAL',
            'NATURAL LEFT',
            'NATURAL LEFT OUTER',
            'NATURAL RIGHT',
            'NATURAL RIGHT OUTER',
        ], true)) {
            if ($clause !== null || $expression !== null) {
                throw new InvalidArgumentException(
                    "{$type} JOIN has not condition"
                );
            }
            return true;
        }
        return false;
    }

    /**
     * Validates and renders the JOIN condition clause.
     *
     * @param string|null $clause `ON`, `USING` or null for none
     *
     * @throws InvalidArgumentException for invalid condition clause
     *
     * @return string|null The condition clause or none
     */
    private function renderJoinConditionClause(?string $clause) : ?string
    {
        if ($clause === null) {
            return null;
        }
        $result = \strtoupper($clause);
        if (\in_array($result, [
            'ON',
            'USING',
        ], true)) {
            return $result;
        }
        throw new InvalidArgumentException("Invalid JOIN condition clause: {$clause}");
    }

    /**
     * Renders the JOIN condition expression.
     *
     * @param string|null $clause `ON`or null
     * @param array<Closure|string>|Closure|null $expression Column(s) or subquery(ies)
     *
     * @return string|null The condition or null if $clause is null
     */
    private function renderJoinConditionExpression(
        ?string $clause,
        Closure | array | null $expression
    ) : ?string {
        if ($clause === null) {
            return null;
        }
        if ($clause === 'ON') {
            // @phpstan-ignore-next-line
            return $this->subquery($expression);
        }
        // @phpstan-ignore-next-line
        foreach ($expression as &$column) {
            $column = $this->renderIdentifier($column);
        }
        // @phpstan-ignore-next-line
        return '(' . \implode(', ', $expression) . ')';
    }
}
