<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Indexes;

use Framework\Database\Database;
use Framework\Database\Definition\Table\DefinitionPart;
use LogicException;

/**
 * Class Index.
 *
 * @see https://mariadb.com/kb/en/getting-started-with-indexes/
 *
 * @package database
 */
abstract class Index extends DefinitionPart
{
    protected Database $database;
    /**
     * @var array<string>
     */
    protected array $columns;
    protected string $type = '';
    protected ?string $name;

    public function __construct(Database $database, ?string $name, string $column, string ...$columns)
    {
        $this->database = $database;
        $this->name = $name;
        $this->columns = $columns ? \array_merge([$column], $columns) : [$column];
    }

    protected function renderType() : string
    {
        if (empty($this->type)) {
            throw new LogicException('Key type is empty');
        }
        return " {$this->type}";
    }

    protected function renderName() : ?string
    {
        if ($this->name === null) {
            return null;
        }
        return ' ' . $this->database->protectIdentifier($this->name);
    }

    protected function renderColumns() : string
    {
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $this->database->protectIdentifier($column);
        }
        $columns = \implode(', ', $columns);
        return " ({$columns})";
    }

    protected function renderTypeAttributes() : ?string
    {
        return null;
    }

    protected function sql() : string
    {
        $sql = $this->renderType();
        $sql .= $this->renderName();
        $sql .= $this->renderColumns();
        $sql .= $this->renderTypeAttributes();
        return $sql;
    }
}
