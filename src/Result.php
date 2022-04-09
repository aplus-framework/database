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

use Framework\Database\Result\Field;
use LogicException;
use mysqli_result;
use OutOfBoundsException;
use OutOfRangeException;

/**
 * Class Result.
 *
 * @package database
 */
class Result
{
    /**
     * @var mysqli_result<int,array|false|null>
     */
    protected mysqli_result $result;
    protected bool $buffered;
    protected bool $free = false;
    protected string $fetchClass = \stdClass::class;
    /**
     * @var array<mixed>
     */
    protected array $fetchConstructor = [];

    /**
     * Result constructor.
     *
     * @param mysqli_result<int,array|false|null> $result
     * @param bool $buffered
     */
    public function __construct(mysqli_result $result, bool $buffered)
    {
        $this->result = $result;
        $this->buffered = $buffered;
    }

    public function __destruct()
    {
        if ( ! $this->isFree()) {
            $this->free();
        }
    }

    /**
     * Frees the memory associated with a result.
     */
    public function free() : void
    {
        $this->checkIsFree();
        $this->free = true;
        $this->result->free();
    }

    public function isFree() : bool
    {
        return $this->free;
    }

    protected function checkIsFree() : void
    {
        if ($this->isFree()) {
            throw new LogicException('Result is already free');
        }
    }

    public function isBuffered() : bool
    {
        return $this->buffered;
    }

    /**
     * Adjusts the result pointer to an arbitrary row in the result.
     *
     * @param int $offset The field offset. Must be between zero and the total
     * number of rows minus one
     *
     * @throws LogicException if is an unbuffered result
     * @throws OutOfBoundsException for invalid cursor offset
     *
     * @return bool
     */
    public function moveCursor(int $offset) : bool
    {
        $this->checkIsFree();
        if ( ! $this->isBuffered()) {
            throw new LogicException('Cursor cannot be moved on unbuffered results');
        }
        if ($offset < 0 || ($offset !== 0 && $offset >= $this->result->num_rows)) {
            throw new OutOfRangeException(
                "Invalid cursor offset: {$offset}"
            );
        }
        return $this->result->data_seek($offset);
    }

    /**
     * @param string $class
     * @param mixed ...$constructor
     *
     * @return static
     */
    public function setFetchClass(string $class, mixed ...$constructor) : static
    {
        $this->fetchClass = $class;
        $this->fetchConstructor = $constructor;
        return $this;
    }

    /**
     * Fetches the current row as object and move the cursor to the next.
     *
     * @param string|null $class
     * @param mixed ...$constructor
     *
     * @return object|null
     */
    public function fetch(string $class = null, mixed ...$constructor) : object | null
    {
        $this->checkIsFree();
        $class ??= $this->fetchClass;
        $constructor = $constructor ?: $this->fetchConstructor;
        if ($constructor) {
            return $this->result->fetch_object($class, $constructor);
        }
        return $this->result->fetch_object($class);
    }

    /**
     * Fetches all rows as objects.
     *
     * @param string|null $class
     * @param mixed ...$constructor
     *
     * @return array<int,object>
     */
    public function fetchAll(string $class = null, mixed ...$constructor) : array
    {
        $this->checkIsFree();
        $all = [];
        while ($row = $this->fetch($class, ...$constructor)) {
            $all[] = $row;
        }
        return $all;
    }

    /**
     * Fetches a specific row as object and move the cursor to the next.
     *
     * @param int $offset
     * @param string|null $class
     * @param mixed ...$constructor
     *
     * @return object|null
     */
    public function fetchRow(int $offset, string $class = null, mixed ...$constructor) : object | null
    {
        $this->checkIsFree();
        $this->moveCursor($offset);
        return $this->fetch($class, ...$constructor);
    }

    /**
     * Fetches the current row as array and move the cursor to the next.
     *
     * @return array<string,int|string|null>|null
     */
    public function fetchArray() : ?array
    {
        $this->checkIsFree();
        return $this->result->fetch_assoc();
    }

    /**
     * Fetches all rows as arrays.
     *
     * @return array<int,array<mixed>>
     */
    public function fetchArrayAll() : array
    {
        $this->checkIsFree();
        return $this->result->fetch_all(\MYSQLI_ASSOC);
    }

    /**
     * Fetches a specific row as array and move the cursor to the next.
     *
     * @param int $offset
     *
     * @return array<string,int|string|null>
     */
    public function fetchArrayRow(int $offset) : array
    {
        $this->checkIsFree();
        $this->moveCursor($offset);
        return $this->result->fetch_assoc();
    }

    /**
     * Gets the number of rows in the result set.
     *
     * @return int|string
     */
    public function numRows() : int | string
    {
        $this->checkIsFree();
        return $this->result->num_rows;
    }

    /**
     * Returns an array of objects representing the fields in a result set.
     *
     * @return array<int,Field>|false an array of objects which contains field
     * definition information or false if no field information is available
     */
    public function fetchFields() : array | false
    {
        $this->checkIsFree();
        $fields = $this->result->fetch_fields();
        if ($fields === false) { // @phpstan-ignore-line
            return false;
        }
        foreach ($fields as &$field) {
            $field = new Field($field);
        }
        return $fields;
    }
}
