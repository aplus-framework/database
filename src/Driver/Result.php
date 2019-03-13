<?php namespace Framework\Database\Driver;

class Result
{
	/**
	 * @var \mysqli_result
	 */
	protected $result;

	public function __construct(\mysqli_result $result)
	{
		$this->result = $result;
	}

	public function __destruct()
	{
		$this->result->free();
	}

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
	}

	public function dataSeek(int $offset) : bool
	{
		return $this->result->data_seek($offset);
	}

	/**
	 * @param string $class_name
	 * @param mixed  ...$constructor_params
	 *
	 * @return mixed|null
	 */
	public function fetch(string $class_name = 'stdClass', ...$constructor_params)
	{
		if ($constructor_params) {
			return $this->result->fetch_object($class_name, $constructor_params);
		}
		return $this->result->fetch_object($class_name);
	}

	/**
	 * @param string $class_name
	 * @param mixed  ...$constructor_params
	 *
	 * @return array|mixed[]
	 */
	public function fetchAll(string $class_name = 'stdClass', ...$constructor_params) : array
	{
		$this->result->data_seek(0);
		$all = [];
		for ($i = $this->result->num_rows; $i > 0; $i--) {
			$all[] = $this->fetch($class_name, ...$constructor_params);
		}
		return $all;
	}

	/**
	 * @param int    $row
	 * @param string $class_name
	 * @param mixed  ...$params
	 *
	 * @return mixed|null
	 */
	public function fetchRow(int $row, string $class_name = 'stdClass', ...$params)
	{
		$this->result->data_seek($row);
		return $this->fetch($class_name, ...$params);
	}

	public function fetchArray() : ?array
	{
		return $this->result->fetch_assoc();
	}

	public function fetchArrayAll() : array
	{
		$this->result->data_seek(0);
		return $this->result->fetch_all(\MYSQLI_ASSOC);
	}

	public function fetchArrayRow(int $row) : array
	{
		$this->result->data_seek($row);
		return $this->result->fetch_assoc();
	}

	public function numRows() : int
	{
		return $this->result->num_rows;
	}
}
