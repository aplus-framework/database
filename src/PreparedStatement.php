<?php namespace Framework\Database;

class PreparedStatement
{
	/**
	 * @var \mysqli_stmt
	 */
	protected $statement;

	public function __construct(\mysqli_stmt $statement)
	{
		$this->statement = $statement;
	}

	/**
	 * Executes the prepared statement, returning a result set as a Result object.
	 *
	 * @param mixed ...$params Parameters sent to the prepared statement
	 *
	 * @return Result
	 */
	public function query(...$params) : Result
	{
		$this->bindParams($params);
		$this->statement->execute();
		return new Result($this->statement->get_result());
	}

	/**
	 * Executes the prepared statement and return the number of affected rows.
	 *
	 * @param mixed ...$params Parameters sent to the prepared statement
	 *
	 * @return int
	 */
	public function exec(...$params) : int
	{
		$this->bindParams($params);
		$this->statement->execute();
		if ($this->statement->field_count) {
			$this->statement->free_result();
		}
		return $this->statement->affected_rows;
	}

	protected function bindParams(array $params) : void
	{
		if (empty($params)) {
			return;
		}
		$types = '';
		$blobs = [];
		foreach ($params as $n => &$param) {
			$type = \gettype($param);
			switch ($type) {
				case 'boolean':
					$types .= 'i';
					$param = (int) $param;
					break;
				case 'double':
					$types .= 'd';
					break;
				case 'integer':
					$types .= 'i';
					break;
				case 'string':
					if (\strlen($param) > 8000000) {
						$types .= 'b';
						$blobs[$n] = $param;
						$param = null;
						break;
					}
					$types .= 's';
					break;
				case 'NULL':
					$types .= 's';
					break;
				default:
					throw new \InvalidArgumentException(
						"Invalid param data type: {$type}"
					);
			}
		}
		unset($param);
		$this->statement->bind_param($types, ...$params);
		foreach ($blobs as $n => $blob) {
			// - https://mariolurig.com/coding/mysqli-and-blob-binary-database-fields/
			// - https://blogs.oracle.com/oswald/phps-mysqli-extension:-storing-and-retrieving-blobs
			foreach (\str_split($blob, 8000000) as $chunk) {
				$this->statement->send_long_data($n, $chunk);
			}
		}
	}
}
