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
 * Trait GroupBy.
 *
 * @see https://mariadb.com/kb/en/group-by/
 *
 * @package database
 */
trait GroupBy
{
    /**
     * Appends columns to the GROUP BY clause.
     *
     * @param Closure|string $column The column name or a subquery
     * @param Closure|string ...$columns Extra column names and/or subqueries
     *
     * @return static
     */
    public function groupBy(Closure | string $column, Closure | string ...$columns) : static
    {
        return $this->addGroupBy($column, $columns, null);
    }

    /**
     * Appends columns with the ASC direction to the GROUP BY clause.
     *
     * @param Closure|string $column The column name or a subquery
     * @param Closure|string ...$columns Extra column names and/or subqueries
     *
     * @return static
     */
    public function groupByAsc(Closure | string $column, Closure | string ...$columns) : static
    {
        return $this->addGroupBy($column, $columns, 'ASC');
    }

    /**
     * Appends columns with the DESC direction to the GROUP BY clause.
     *
     * @param Closure|string $column The column name or a subquery
     * @param Closure|string ...$columns Extra column names and/or subqueries
     *
     * @return static
     */
    public function groupByDesc(Closure | string $column, Closure | string ...$columns) : static
    {
        return $this->addGroupBy($column, $columns, 'DESC');
    }

    /**
     * Adds a GROUP BY expression.
     *
     * @param Closure|string $column The column name or a subquery
     * @param array<Closure|string> $columns Extra column names and/or subqueries
     * @param string|null $direction `ASC`, `DESC` or null for none
     *
     * @return static
     */
    private function addGroupBy(Closure | string $column, array $columns, ?string $direction) : static
    {
        foreach ([$column, ...$columns] as $column) {
            $this->sql['group_by'][] = [
                'column' => $column,
                'direction' => $direction,
            ];
        }
        return $this;
    }

    /**
     * Renders the GROUP BY clause.
     *
     * @return string|null The GROUP BY clause or null if it was not set
     */
    protected function renderGroupBy() : ?string
    {
        if ( ! isset($this->sql['group_by'])) {
            return null;
        }
        $expressions = [];
        foreach ($this->sql['group_by'] as $part) {
            $expression = $this->renderIdentifier($part['column']);
            if ($part['direction']) {
                $expression .= " {$part['direction']}";
            }
            $expressions[] = $expression;
        }
        $expressions = \implode(', ', $expressions);
        return " GROUP BY {$expressions}";
    }
}
