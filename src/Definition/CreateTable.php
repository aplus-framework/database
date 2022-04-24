<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition;

use Framework\Database\Definition\Table\TableDefinition;
use Framework\Database\Definition\Table\TableStatement;
use LogicException;

/**
 * Class CreateTable.
 *
 * @see https://mariadb.com/kb/en/create-table/
 *
 * @package database
 */
class CreateTable extends TableStatement
{
    /**
     * Adds a OR REPLACE part.
     *
     * WARNING: This feature is MariaDB only. It is not compatible with MySQL.
     *
     * @return static
     */
    public function orReplace() : static
    {
        $this->sql['or_replace'] = true;
        return $this;
    }

    protected function renderOrReplace() : ?string
    {
        if ( ! isset($this->sql['or_replace'])) {
            return null;
        }
        return ' OR REPLACE';
    }

    /**
     * @return static
     */
    public function temporary() : static
    {
        $this->sql['temporary'] = true;
        return $this;
    }

    protected function renderTemporary() : ?string
    {
        if ( ! isset($this->sql['temporary'])) {
            return null;
        }
        return ' TEMPORARY';
    }

    /**
     * @return static
     */
    public function ifNotExists() : static
    {
        $this->sql['if_not_exists'] = true;
        return $this;
    }

    protected function renderIfNotExists() : ?string
    {
        if ( ! isset($this->sql['if_not_exists'])) {
            return null;
        }
        if (isset($this->sql['or_replace'])) {
            throw new LogicException(
                'Clauses OR REPLACE and IF NOT EXISTS can not be used together'
            );
        }
        return ' IF NOT EXISTS';
    }

    /**
     * @param string $tableName
     *
     * @return static
     */
    public function table(string $tableName) : static
    {
        $this->sql['table'] = $tableName;
        return $this;
    }

    protected function renderTable() : string
    {
        if (isset($this->sql['table'])) {
            return ' ' . $this->database->protectIdentifier($this->sql['table']);
        }
        throw new LogicException('TABLE name must be set');
    }

    /**
     * @param callable $definition
     *
     * @return static
     */
    public function definition(callable $definition) : static
    {
        $this->sql['definition'] = $definition;
        return $this;
    }

    protected function renderDefinition() : string
    {
        if ( ! isset($this->sql['definition'])) {
            throw new LogicException('Table definition must be set');
        }
        $definition = new TableDefinition($this->database);
        $this->sql['definition']($definition);
        return $definition->sql();
    }

    public function sql() : string
    {
        $sql = 'CREATE' . $this->renderOrReplace() . $this->renderTemporary();
        $sql .= ' TABLE' . $this->renderIfNotExists();
        $sql .= $this->renderTable() . ' (' . \PHP_EOL;
        $sql .= $this->renderDefinition() . \PHP_EOL;
        $sql .= ')' . $this->renderOptions();
        return $sql;
    }

    /**
     * Runs the CREATE TABLE statement.
     *
     * @return int|string The number of affected rows
     */
    public function run() : int|string
    {
        return $this->database->exec($this->sql());
    }
}
