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
use Framework\Database\Manipulation\Select as SelectStatement;
use LogicException;

/**
 * Trait Select.
 *
 * @package database
 */
trait Select
{
    /**
     * Sets the SELECT statement part.
     *
     * @param Closure $select
     *
     * @see https://mariadb.com/kb/en/insert-select/
     *
     * @return static
     */
    public function select(Closure $select) : static
    {
        $this->sql['select'] = $select(new SelectStatement($this->database));
        return $this;
    }

    protected function renderSelect() : ?string
    {
        if ( ! isset($this->sql['select'])) {
            return null;
        }
        if (isset($this->sql['values'])) {
            throw new LogicException('SELECT statement is not allowed when VALUES is set');
        }
        if (isset($this->sql['set'])) {
            throw new LogicException('SELECT statement is not allowed when SET is set');
        }
        return " {$this->sql['select']}";
    }
}
