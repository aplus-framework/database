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

use Framework\Database\Database;
use Framework\Database\Definition\Table\Columns\ColumnDefinition;
use Framework\Database\Definition\Table\Indexes\IndexDefinition;

class TableDefinition extends DefinitionPart
{
    protected Database $database;
    /**
     * @var array<int,array>
     */
    protected array $columns = [];
    /**
     * @var array<int,array>
     */
    protected array $indexes = [];

    /**
     * TableDefinition constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
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

    protected function renderColumns(string $prefix = null) : string
    {
        if ($prefix) {
            $prefix .= ' COLUMN';
        }
        $sql = [];
        foreach ($this->columns as $column) {
            $name = $this->database->protectIdentifier($column['name']);
            $change_name = $column['change_name']
                ? ' ' . $this->database->protectIdentifier($column['change_name'])
                : null;
            $definition = $column['definition']->sql();
            $sql[] = " {$prefix} {$name}{$change_name}{$definition}";
        }
        return \implode(',' . \PHP_EOL, $sql);
    }

    protected function renderIndexes(string $prefix = null) : string
    {
        $sql = [];
        foreach ($this->indexes as $index) {
            $definition = $index['definition']->sql();
            $sql[] = " {$prefix}{$definition}";
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
        return $sql;
    }
}
