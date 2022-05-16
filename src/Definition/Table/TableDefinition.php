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
use Framework\Database\Definition\Table\Columns\ColumnDefinition;
use Framework\Database\Definition\Table\Indexes\IndexDefinition;

/**
 * Class TableDefinition.
 *
 * @package database
 */
class TableDefinition extends DefinitionPart
{
    protected Database $database;
    /**
     * @var array<int,array<string,mixed>>
     */
    protected array $columns = [];
    /**
     * @var array<int,array<string,mixed>>
     */
    protected array $indexes = [];
    /**
     * @var array<int,Check>
     */
    protected array $checks = [];
    protected ?string $condition = null;

    /**
     * TableDefinition constructor.
     *
     * @param Database $database
     * @param string|null $condition
     */
    public function __construct(Database $database, string $condition = null)
    {
        $this->database = $database;
        $this->condition = $condition;
    }

    /**
     * Adds a column to the Table Definition list.
     *
     * @param string $name Column name
     * @param string|null $changeName New column name. Used on ALTER TABLE CHANGE
     *
     * @return ColumnDefinition
     */
    public function column(string $name, string $changeName = null) : ColumnDefinition
    {
        $definition = new ColumnDefinition($this->database);
        $this->columns[] = [
            'name' => $name,
            'change_name' => $changeName,
            'definition' => $definition,
        ];
        return $definition;
    }

    /**
     * Adds an index to the Table Definition list.
     *
     * @param string|null $name Index name
     *
     * @return IndexDefinition
     */
    public function index(string $name = null) : IndexDefinition
    {
        $definition = new IndexDefinition($this->database, $name);
        $this->indexes[] = [
            'name' => $name,
            'definition' => $definition,
        ];
        return $definition;
    }

    /**
     * Adds a check constraint to the Table Definition list.
     *
     * @param Closure $expression Must return a string with the check expression.
     * The function receives a Database instance in the first parameter.
     *
     * @return Check
     */
    public function check(Closure $expression) : Check
    {
        return $this->checks[] = new Check($this->database, $expression);
    }

    protected function renderColumns(string $prefix = null) : string
    {
        if ($prefix) {
            $prefix .= ' COLUMN';
        }
        if ($this->condition) {
            $prefix .= ' ' . $this->condition;
        }
        $sql = [];
        foreach ($this->columns as $column) {
            $name = $this->database->protectIdentifier($column['name']);
            $changeName = $column['change_name']
                ? ' ' . $this->database->protectIdentifier($column['change_name'])
                : null;
            $definition = $column['definition']->sql();
            $sql[] = " {$prefix} {$name}{$changeName}{$definition}";
        }
        return \implode(',' . \PHP_EOL, $sql);
    }

    protected function renderIndexes(string $prefix = null) : string
    {
        $sql = [];
        foreach ($this->indexes as $index) {
            $definition = $index['definition']->sql();
            if ($this->condition) {
                $definition = \explode('(', $definition, 2);
                $definition = $definition[0] . $this->condition . ' (' . $definition[1];
            }
            $sql[] = " {$prefix}{$definition}";
        }
        return \implode(',' . \PHP_EOL, $sql);
    }

    protected function renderChecks() : string
    {
        $sql = [];
        foreach ($this->checks as $check) {
            $sql[] = ' ' . $check->sql();
        }
        return \implode(',' . \PHP_EOL, $sql);
    }

    protected function sql(string $prefix = null) : string
    {
        $sql = $this->renderColumns($prefix);
        $part = $this->renderIndexes($prefix);
        if ($part) {
            $sql .= ',' . \PHP_EOL . $part;
        }
        $part = $this->renderChecks();
        if ($part) {
            $sql .= ',' . \PHP_EOL . $part;
        }
        return $sql;
    }
}
