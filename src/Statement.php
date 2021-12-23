<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database;

/**
 * Class Statement.
 *
 * @package database
 */
abstract class Statement implements \Stringable
{
    protected Database $database;
    /**
     * SQL clauses and parts.
     *
     * @var array<string,mixed>
     */
    protected array $sql = [];

    /**
     * Statement constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __toString() : string
    {
        return $this->sql();
    }

    /**
     * Resets SQL clauses and parts.
     *
     * @param string|null $sql A part name or null to reset all
     *
     * @see Statement::$sql
     *
     * @return static
     */
    public function reset(string $sql = null) : static
    {
        if ($sql === null) {
            unset($this->sql);
            return $this;
        }
        unset($this->sql[$sql]);
        return $this;
    }

    /**
     * Renders the SQL statement.
     *
     * @return string
     */
    abstract public function sql() : string;

    /**
     * Runs the SQL statement.
     *
     * @return mixed
     */
    abstract public function run() : mixed;
}
