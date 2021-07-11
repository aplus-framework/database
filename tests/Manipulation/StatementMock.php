<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation;

use Closure;
use Framework\Database\Manipulation\Statement;

class StatementMock extends Statement
{
    public function subquery(Closure $subquery) : string
    {
        return parent::subquery($subquery);
    }

    /**
     * @param int $limit
     * @param int|null $offset
     *
     * @return static
     */
    public function limit(int $limit, int $offset = null) : static
    {
        return $this->setLimit($limit, $offset);
    }

    public function renderLimit() : ?string
    {
        return parent::renderLimit();
    }

    public function renderIdentifier(Closure | string $column) : string
    {
        return parent::renderIdentifier($column);
    }

    public function renderAliasedIdentifier(array | Closure | string $column) : string
    {
        return parent::renderAliasedIdentifier($column);
    }

    public function renderAssignment(string $identifier, $expression) : string
    {
        return parent::renderAssignment($identifier, $expression);
    }

    public function mergeExpressions($expression, array $expressions) : array
    {
        return parent::mergeExpressions($expression, $expressions);
    }

    public function renderOptions() : ?string
    {
        if ( ! $this->hasOptions()) {
            return null;
        }
        return \implode(' ', $this->sql['options']);
    }

    public function sql() : string
    {
        return 'SQL';
    }

    public function run() : mixed
    {
    }
}
