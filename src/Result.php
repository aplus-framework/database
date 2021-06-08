<?php namespace Framework\Database;

use Framework\Database\Result\Field;
use OutOfRangeException;

/**
 * Class Result.
 */
class Result
{
	protected \mysqli_result $result;
	protected bool $buffered;
	protected bool $free = false;
	protected string $fetchClass = \stdClass::class;
	protected array $fetchConstructor = [];

	public function __construct(\mysqli_result $result, bool $buffered)
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
			throw new \LogicException('Result is already free');
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
	 *                    number of rows minus one
	 *
	 * @throws \LogicException       if is an unbuffered result
	 * @throws \OutOfBoundsException for invalid cursor offset
	 *
	 * @return bool
	 */
	public function moveCursor(int $offset) : bool
	{
		$this->checkIsFree();
		if ( ! $this->isBuffered()) {
			throw new \LogicException('Cursor cannot be moved on unbuffered results');
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
	 * @param mixed  ...$constructor
	 *
	 * @return $this
	 */
	public function setFetchClass(string $class, mixed ...$constructor)
	{
		$this->fetchClass = $class;
		$this->fetchConstructor = $constructor;
		return $this;
	}

	/**
	 * Fetches the current row as object and move the cursor to the next.
	 *
	 * @param string|null $class
	 * @param mixed       ...$constructor
	 *
	 * @return mixed|null
	 */
	public function fetch(string $class = null, mixed ...$constructor)
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
	 * @param mixed       ...$constructor
	 *
	 * @return array|mixed[]
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
	 * @param int         $offset
	 * @param string|null $class
	 * @param mixed       ...$constructor
	 *
	 * @return mixed|null
	 */
	public function fetchRow(int $offset, string $class = null, mixed ...$constructor)
	{
		$this->checkIsFree();
		$this->moveCursor($offset);
		return $this->fetch($class, ...$constructor);
	}

	/**
	 * Fetches the current row as array and move the cursor to the next.
	 *
	 * @return array|mixed[]|null
	 */
	public function fetchArray() : ?array
	{
		$this->checkIsFree();
		return $this->result->fetch_assoc();
	}

	/**
	 * Fetches all rows as arrays.
	 *
	 * @return array
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
	 * @return array
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
	 * @return int
	 */
	public function numRows() : int
	{
		$this->checkIsFree();
		return $this->result->num_rows;
	}

	/**
	 * Returns an array of objects representing the fields in a result set.
	 *
	 * @return array|false|Field[] an array of objects which contains field
	 *                             definition information or false if no field
	 *                             information is available
	 */
	public function fetchFields() : array | false
	{
		$this->checkIsFree();
		$fields = $this->result->fetch_fields();
		if ($fields === false) {
			return false;
		}
		foreach ($fields as &$field) {
			$field = new Field($field);
		}
		return $fields;
	}
}
