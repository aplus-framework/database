<?php namespace Framework\Database;

/**
 * Class Result.
 */
class Result
{
	protected \mysqli_result $result;
	protected string $fetchClass = \stdClass::class;
	protected array $fetchConstructor = [];

	public function __construct(\mysqli_result $result)
	{
		$this->result = $result;
	}

	public function __destruct()
	{
		$this->result->free();
	}

	/* TODO: meta infos
	 public function meta()
	{
		foreach ($this->result->fetch_fields() as $field) {
			if ($field->flags & \MYSQLI_NOT_NULL_FLAG) {
				echo 'Field has NOT NULL';
			}
			if ($field->type === \MYSQLI_TYPE_VAR_STRING) {
				echo 'Field type VARCHAR';
			}
		}
	}*/

	public function moveCursor(int $offset) : bool
	{
		if ($offset < 0 || ($offset !== 0 && $offset >= $this->result->num_rows)) {
			throw new \OutOfRangeException(
				"Invalid cursor offset: {$offset}"
			);
		}
		return $this->result->data_seek($offset);
	}

	public function setFetchClass(string $class, ...$constructor)
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
	public function fetch(string $class = null, ...$constructor)
	{
		$class = $class ?? $this->fetchClass;
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
	public function fetchAll(string $class = null, ...$constructor) : array
	{
		$this->moveCursor(0);
		$all = [];
		for ($i = $this->result->num_rows; $i > 0; $i--) {
			$all[] = $this->fetch($class, ...$constructor);
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
	public function fetchRow(int $offset, string $class = null, ...$constructor)
	{
		$this->moveCursor($offset);
		return $this->fetch($class, ...$constructor);
	}

	/**
	 * Fetches the current row as array and move the cursor to the next.
	 *
	 * @return array|null
	 */
	public function fetchArray() : ?array
	{
		return $this->result->fetch_assoc();
	}

	/**
	 * Fetches all rows as arrays.
	 *
	 * @return array
	 */
	public function fetchArrayAll() : array
	{
		$this->moveCursor(0);
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
		return $this->result->num_rows;
	}
}
