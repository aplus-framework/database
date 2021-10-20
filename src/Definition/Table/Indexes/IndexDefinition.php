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
use Framework\Database\Definition\Table\Indexes\Keys\ForeignKey;
use Framework\Database\Definition\Table\Indexes\Keys\FulltextKey;
use Framework\Database\Definition\Table\Indexes\Keys\Key;
use Framework\Database\Definition\Table\Indexes\Keys\PrimaryKey;
use Framework\Database\Definition\Table\Indexes\Keys\SpatialKey;
use Framework\Database\Definition\Table\Indexes\Keys\UniqueKey;
use RuntimeException;

/**
 * Class IndexDefinition.
 *
 * @see https://mariadb.com/kb/en/create-table/#index-definitions
 * @see https://mariadb.com/kb/en/optimization-and-indexes/
 *
 * @package database
 */
class IndexDefinition extends DefinitionPart
{
    protected Database $database;
    protected ?string $name;
    protected ?Index $index = null;

    public function __construct(Database $database, string $name = null)
    {
        $this->database = $database;
        $this->name = $name;
    }

    public function key(string $column, string ...$columns) : Key
    {
        return $this->index = new Key($this->database, $this->name, $column, ...$columns);
    }

    public function primaryKey(string $column, string ...$columns) : PrimaryKey
    {
        return $this->index = new PrimaryKey($this->database, $this->name, $column, ...$columns);
    }

    public function uniqueKey(string $column, string ...$columns) : UniqueKey
    {
        return $this->index = new UniqueKey($this->database, $this->name, $column, ...$columns);
    }

    public function fulltextKey(string $column, string ...$columns) : FulltextKey
    {
        return $this->index = new FulltextKey($this->database, $this->name, $column, ...$columns);
    }

    public function foreignKey(string $column, string ...$columns) : ForeignKey
    {
        return $this->index = new ForeignKey($this->database, $this->name, $column, ...$columns);
    }

    public function spatialKey(string $column, string ...$columns) : SpatialKey
    {
        return $this->index = new SpatialKey($this->database, $this->name, $column, ...$columns);
    }

    protected function sql() : string
    {
        if ( ! $this->index) {
            throw new RuntimeException("Key type not set in index {$this->name}");
        }
        return $this->index->sql();
    }
}
