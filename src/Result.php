<?php namespace Framework\Database;

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

	public function fetchFields() : array | false
	{
		$this->checkIsFree();
		$fields = $this->result->fetch_fields();
		if ($fields === false) {
			return false;
		}
		foreach ($fields as $field) {
			$field->type_name = match ($field->type) {
				\MYSQLI_TYPE_BIT => 'BIT',
				\MYSQLI_TYPE_BLOB => 'BLOB',
				\MYSQLI_TYPE_CHAR => 'CHAR',
				\MYSQLI_TYPE_DATE => 'DATE',
				\MYSQLI_TYPE_DATETIME => 'DATETIME',
				\MYSQLI_TYPE_DECIMAL => 'DECIMAL',
				\MYSQLI_TYPE_DOUBLE => 'DOUBLE',
				\MYSQLI_TYPE_ENUM => 'ENUM',
				\MYSQLI_TYPE_FLOAT => 'FLOAT',
				\MYSQLI_TYPE_GEOMETRY => 'GEOMETRY',
				\MYSQLI_TYPE_INT24 => 'INT24',
				\MYSQLI_TYPE_INTERVAL => 'INTERVAL',
				\MYSQLI_TYPE_JSON => 'JSON',
				\MYSQLI_TYPE_LONG => 'LONG',
				\MYSQLI_TYPE_LONG_BLOB => 'LONG_BLOB',
				\MYSQLI_TYPE_LONGLONG => 'LONGLONG',
				\MYSQLI_TYPE_MEDIUM_BLOB => 'MEDIUM_BLOB',
				\MYSQLI_TYPE_NEWDATE => 'NEWDATE',
				\MYSQLI_TYPE_NEWDECIMAL => 'NEWDECIMAL',
				\MYSQLI_TYPE_NULL => 'NULL',
				\MYSQLI_TYPE_SET => 'SET',
				\MYSQLI_TYPE_SHORT => 'SHORT',
				\MYSQLI_TYPE_STRING => 'STRING',
				\MYSQLI_TYPE_TIME => 'TIME',
				\MYSQLI_TYPE_TIMESTAMP => 'TIMESTAMP',
				\MYSQLI_TYPE_TINY => 'TINY',
				\MYSQLI_TYPE_TINY_BLOB => 'TINY_BLOB',
				\MYSQLI_TYPE_VAR_STRING => 'VAR_STRING',
				\MYSQLI_TYPE_YEAR => 'YEAR',
				default => null
			};
			$field->binary_flag = false;
			if ($field->flags & \MYSQLI_BINARY_FLAG) {
				$field->binary_flag = true;
			}
			$field->blob_flag = false;
			if ($field->flags & \MYSQLI_BLOB_FLAG) {
				$field->blob_flag = true;
			}
			$field->enum_flag = false;
			if ($field->flags & \MYSQLI_ENUM_FLAG) {
				$field->enum_flag = true;
			}
			$field->group_flag = false;
			if ($field->flags & \MYSQLI_GROUP_FLAG) {
				$field->group_flag = true;
			}
			$field->num_flag = false;
			if ($field->flags & \MYSQLI_NUM_FLAG) {
				$field->num_flag = true;
			}
			$field->set_flag = false;
			if ($field->flags & \MYSQLI_SET_FLAG) {
				$field->set_flag = true;
			}
			$field->timestamp_flag = false;
			if ($field->flags & \MYSQLI_TIMESTAMP_FLAG) {
				$field->timestamp_flag = true;
			}
			$field->unsigned_flag = false;
			if ($field->flags & \MYSQLI_UNSIGNED_FLAG) {
				$field->unsigned_flag = true;
			}
			$field->zerofill_flag = false;
			if ($field->flags & \MYSQLI_ZEROFILL_FLAG) {
				$field->zerofill_flag = true;
			}
			$field->auto_increment_flag = false;
			if ($field->flags & \MYSQLI_AUTO_INCREMENT_FLAG) {
				$field->auto_increment_flag = true;
			}
			$field->multiple_key_flag = false;
			if ($field->flags & \MYSQLI_MULTIPLE_KEY_FLAG) {
				$field->multiple_key_flag = true;
			}
			$field->not_null_flag = false;
			if ($field->flags & \MYSQLI_NOT_NULL_FLAG) {
				$field->not_null_flag = true;
			}
			$field->part_key_flag = false;
			if ($field->flags & \MYSQLI_PART_KEY_FLAG) {
				$field->part_key_flag = true;
			}
			$field->pri_key_flag = false;
			if ($field->flags & \MYSQLI_PRI_KEY_FLAG) {
				$field->pri_key_flag = true;
			}
			$field->unique_key_flag = false;
			if ($field->flags & \MYSQLI_UNIQUE_KEY_FLAG) {
				$field->unique_key_flag = true;
			}
			$field->no_default_value_flag = false;
			if ($field->flags & \MYSQLI_NO_DEFAULT_VALUE_FLAG) {
				$field->no_default_value_flag = true;
			}
			$field->on_update_now_flag = false;
			if ($field->flags & \MYSQLI_ON_UPDATE_NOW_FLAG) {
				$field->on_update_now_flag = true;
			}
		}
		return $fields;
	}
}
