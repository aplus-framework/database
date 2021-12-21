<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table;

use Closure;
use Framework\Database\Database;

/**
 * Class Check.
 *
 * @see https://mariadb.com/kb/en/constraint/#check-constraints
 *
 * @package database
 */
class Check extends DefinitionPart
{
    use Constraint;

    protected Database $database;
    protected Closure $check;

    public function __construct(Database $database, Closure $check)
    {
        $this->database = $database;
        $this->check = $check;
    }

    protected function renderCheck() : ?string
    {
        return ' CHECK (' . ($this->check)($this->database) . ')';
    }

    protected function sql() : string
    {
        $sql = $this->renderConstraint();
        $sql .= $this->renderCheck();
        return $sql;
    }
}
