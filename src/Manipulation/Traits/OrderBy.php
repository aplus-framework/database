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
 * Trait OrderBy.
 *
 * @see https://mariadb.com/kb/en/order-by/
 *
 * @package database
 */
trait OrderBy
{
    /**
     * Appends columns to the ORDER BY clause.
     *
     * @param Closure|string $column The column name or a subquery
     * @param Closure|string ...$columns Extra column names and/or subqueries
     *
     * @return static
     */
    public function orderBy(Closure | string $column, Closure | string ...$columns) : static
    {
        return $this->addOrderBy($column, $columns, null);
    }

    /**
     * Appends columns with the ASC direction to the ORDER BY clause.
     *
     * @param Closure|string $column The column name or a subquery
     * @param Closure|string ...$columns Extra column names and/or subqueries
     *
     * @return static
     */
    public function orderByAsc(Closure | string $column, Closure | string ...$columns) : static
    {
        return $this->addOrderBy($column, $columns, 'ASC');
    }

    /**
     * Appends columns with the DESC direction to the ORDER BY clause.
     *
     * @param Closure|string $column The column name or a subquery
     * @param Closure|string ...$columns Extra column names and/or subqueries
     *
     * @return static
     */
    public function orderByDesc(Closure | string $column, Closure | string ...$columns) : static
    {
        return $this->addOrderBy($column, $columns, 'DESC');
    }

    /**
     * Adds a ORDER BY expression.
     *
     * @param Closure|string $column The column name or a subquery
     * @param array<Closure|string> $columns Extra column names and/or subqueries
     * @param string|null $direction `ASC`, `DESC` or null for none
     *
     * @return static
     */
    private function addOrderBy(Closure | string $column, array $columns, ?string $direction) : static
    {
        foreach ([$column, ...$columns] as $column) {
            $this->sql['order_by'][] = [
                'column' => $column,
                'direction' => $direction,
            ];
        }
        return $this;
    }

    /**
     * Renders the ORDER BY clause.
     *
     * @return string|null The ORDER BY clause or null if it was not set
     */
    protected function renderOrderBy() : ?string
    {
        if ( ! isset($this->sql['order_by'])) {
            return null;
        }
        $expressions = [];
        foreach ($this->sql['order_by'] as $part) {
            $expression = $this->renderIdentifier($part['column']);
            if ($part['direction']) {
                $expression .= " {$part['direction']}";
            }
            $expressions[] = $expression;
        }
        $expressions = \implode(', ', $expressions);
        return " ORDER BY {$expressions}";
    }
}
