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

use Framework\Database\Statement;
use LogicException;

/**
 * Class DropSchema.
 *
 * @see https://mariadb.com/kb/en/drop-database/
 *
 * @package database
 */
class DropSchema extends Statement
{
    /**
     * @return static
     */
    public function ifExists() : static
    {
        $this->sql['if_exists'] = true;
        return $this;
    }

    protected function renderIfExists() : ?string
    {
        if ( ! isset($this->sql['if_exists'])) {
            return null;
        }
        return ' IF EXISTS';
    }

    /**
     * @param string $schemaName
     *
     * @return static
     */
    public function schema(string $schemaName) : static
    {
        $this->sql['schema'] = $schemaName;
        return $this;
    }

    protected function renderSchema() : string
    {
        if (isset($this->sql['schema'])) {
            return ' ' . $this->database->protectIdentifier($this->sql['schema']);
        }
        throw new LogicException('SCHEMA name must be set');
    }

    public function sql() : string
    {
        $sql = 'DROP SCHEMA' . $this->renderIfExists();
        $sql .= $this->renderSchema() . \PHP_EOL;
        return $sql;
    }

    /**
     * Runs the CREATE SCHEMA statement.
     *
     * @return int|string The number of affected rows
     */
    public function run() : int|string
    {
        return $this->database->exec($this->sql());
    }
}
