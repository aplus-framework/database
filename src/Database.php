<?php namespace Framework\Database;

use Framework\Database\Definition\AlterSchema;
use Framework\Database\Definition\AlterTable;
use Framework\Database\Definition\CreateSchema;
use Framework\Database\Definition\CreateTable;
use Framework\Database\Definition\DropSchema;
use Framework\Database\Definition\DropTable;
use Framework\Database\Manipulation\Delete;
use Framework\Database\Manipulation\Insert;
use Framework\Database\Manipulation\LoadData;
use Framework\Database\Manipulation\Replace;
use Framework\Database\Manipulation\Select;
use Framework\Database\Manipulation\Update;
use Framework\Database\Manipulation\With;

/**
 * Class Database.
 */
class Database
{
	protected \mysqli $mysqli;
	/**
	 * Connection configurations.
	 *
	 * Custom configs merged with the Base Connection configurations.
	 *
	 * @see makeConfig
	 */
	protected array $config = [];
	/**
	 * The current $config failover index to be used in a connection.
	 *
	 * @see connect
	 *
	 * @var int|null Integer representing the array index or null for none
	 */
	protected ?int $failoverIndex = null;
	/**
	 * @see transaction
	 */
	protected bool $inTransaction = false;

	/**
	 * Database constructor.
	 *
	 * @param array|string $username
	 * @param string|null  $password
	 * @param string|null  $schema
	 * @param string       $host
	 * @param int          $port
	 *
	 * @see Database::makeConfig
	 */
	public function __construct(
		$username,
		string $password = null,
		string $schema = null,
		string $host = 'localhost',
		int $port = 3306
	) {
		\mysqli_report(\MYSQLI_REPORT_ALL & ~\MYSQLI_REPORT_INDEX);
		$this->mysqli = new \mysqli();
		$this->mysqli->options(\MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
		$this->mysqli->options(\MYSQLI_OPT_CONNECT_TIMEOUT, 10);
		$this->connect($username, $password, $schema, $host, $port);
	}

	public function __destruct()
	{
		if ($this->mysqli) {
			$this->mysqli->close();
		}
	}

	/**
	 * Make Base Connection configurations.
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	protected function makeConfig(array $config) : array
	{
		return \array_replace_recursive([
			'host' => 'localhost',
			'port' => 3306,
			'username' => null,
			'password' => null,
			'schema' => null,
			'socket' => null,
			'engine' => 'InnoDB',
			'charset' => 'utf8mb4',
			'collation' => 'utf8mb4_general_ci',
			'timezone' => '+00:00',
			'ssl' => [
				'key' => null,
				'cert' => null,
				'ca' => null,
				'capath' => null,
				'cipher' => null,
			],
			'failover' => [],
		], $config);
	}

	protected function connect(
		$username,
		string $password = null,
		string $schema = null,
		string $host = 'localhost',
		int $port = 3306
	) {
		if ( ! \is_array($username)) {
			$username = [
				'host' => $host,
				'port' => $port,
				'username' => $username,
				'password' => $password,
				'schema' => $schema,
			];
		}
		$username = $this->makeConfig($username);
		if ($this->failoverIndex === null) {
			$this->config = $username;
		}
		try {
			// $this->mysqli->ssl_set();
			$this->mysqli->real_connect(
				$username['host'],
				$username['username'],
				$username['password'],
				$username['schema'],
				$username['port'],
				$username['socket']
			);
		} catch (\Exception $exception) {
			$this->failoverIndex = $this->failoverIndex === null
				? 0
				: $this->failoverIndex + 1;
			if (empty($username['failover'][$this->failoverIndex])) {
				throw $exception;
			}
			$username = \array_replace_recursive(
				$username,
				$username['failover'][$this->failoverIndex]
			);
			// TODO: Log connection error
			$this->connect($username);
		}
		$this->setCollations($username['charset'], $username['collation']);
		$this->setTimezone($username['timezone']);
		return $this;
	}

	protected function setCollations(string $charset, string $collation)
	{
		$this->mysqli->set_charset($charset);
		$charset = $this->quote($charset);
		$collation = $this->quote($collation);
		$this->mysqli->real_query("SET NAMES {$charset} COLLATE {$collation}");
	}

	protected function setTimezone(string $timezone)
	{
		$timezone = $this->quote($timezone);
		$this->mysqli->real_query("SET time_zone = {$timezone}");
	}

	public function warnings()
	{
		return $this->mysqli->warning_count;
	}

	/**
	 * Get a list of latest errors.
	 *
	 * @return array
	 */
	public function errors() : array
	{
		return $this->mysqli->error_list;
	}

	/**
	 * Get latest error.
	 *
	 * @return string|null
	 */
	public function error() : ?string
	{
		return $this->mysqli->error ?: null;
	}

	/**
	 * @throws \mysqli_sql_exception if schema is unknown
	 */
	public function use(string $schema) : void
	{
		$this->mysqli->select_db($schema);
	}

	/**
	 * Call a CREATE SCHEMA statement.
	 *
	 * @return CreateSchema
	 */
	public function createSchema() : CreateSchema
	{
		return new CreateSchema($this);
	}

	/**
	 * Call a DROP SCHEMA statement.
	 *
	 * @return DropSchema
	 */
	public function dropSchema() : DropSchema
	{
		return new DropSchema($this);
	}

	/**
	 * Call a ALTER SCHEMA statement.
	 *
	 * @return AlterSchema
	 */
	public function alterSchema() : AlterSchema
	{
		return new AlterSchema($this);
	}

	/**
	 * Call a CREATE TABLE statement.
	 *
	 * @return CreateTable
	 */
	public function createTable() : CreateTable
	{
		return new CreateTable($this);
	}

	/**
	 * Call a DROP TABLE statement.
	 *
	 * @return DropTable
	 */
	public function dropTable() : DropTable
	{
		return new DropTable($this);
	}

	/**
	 * Call a ALTER TABLE statement.
	 *
	 * @return AlterTable
	 */
	public function alterTable() : AlterTable
	{
		return new AlterTable($this);
	}

	/**
	 * Call a DELETE statement.
	 *
	 * @return Delete
	 */
	public function delete() : Delete
	{
		return new Delete($this);
	}

	/**
	 * Call a INSERT statement.
	 *
	 * @return Insert
	 */
	public function insert() : Insert
	{
		return new Insert($this);
	}

	/**
	 * Call a LOAD DATA statement.
	 *
	 * @return LoadData
	 */
	public function loadData() : LoadData
	{
		return new LoadData($this);
	}

	/**
	 * Call a REPLACE statement.
	 *
	 * @return Replace
	 */
	public function replace() : Replace
	{
		return new Replace($this);
	}

	/**
	 * Call a SELECT statement.
	 *
	 * @return Select
	 */
	public function select() : Select
	{
		return new Select($this);
	}

	/**
	 * Call a UPDATE statement.
	 *
	 * @return Update
	 */
	public function update() : Update
	{
		return new Update($this);
	}

	/**
	 * Call a WITH statement.
	 *
	 * @return With
	 */
	public function with() : With
	{
		return new With($this);
	}

	/**
	 * Executes an SQL statement and return the number of affected rows.
	 *
	 * @param string $statement
	 *
	 * @return int
	 */
	public function exec(string $statement) : int
	{
		$this->mysqli->real_query($statement);
		if ($this->mysqli->field_count) {
			$this->mysqli->store_result()->free();
		}
		return $this->mysqli->affected_rows;
	}

	/**
	 * Executes an SQL statement, returning a result set as a Result object.
	 *
	 * Must be: SELECT, SHOW, DESCRIBE or EXPLAIN
	 *
	 * @param string $statement
	 *
	 * @throws \InvalidArgumentException if $statement does not return result
	 *
	 * @return Result
	 */
	public function query(string $statement) : Result
	{
		$result = $this->mysqli->query($statement);
		if (\is_bool($result)) {
			throw new \InvalidArgumentException(
				"Statement does not return result: {$statement}"
			);
		}
		return new Result($result);
	}

	/**
	 * Prepares a statement for execution and returns a PreparedStatement object.
	 *
	 * @param string $statement
	 *
	 * @return PreparedStatement
	 */
	public function prepare(string $statement) : PreparedStatement
	{
		return new PreparedStatement($this->mysqli->prepare($statement));
	}

	/**
	 * Run statements in a transaction.
	 *
	 * @param callable $statements
	 */
	public function transaction(callable $statements) : void
	{
		if ($this->inTransaction) {
			throw new \LogicException('Transaction already is active');
		}
		$this->inTransaction = true;
		$this->mysqli->autocommit(false);
		$this->mysqli->begin_transaction();
		try {
			$statements($this);
			$this->mysqli->commit();
		} catch (\Exception $exception) {
			$this->mysqli->rollback();
			throw $exception;
		} finally {
			$this->inTransaction = false;
		}
	}

	/**
	 * Gets the LAST_INSERT_ID().
	 *
	 * Note: When a insert has many rows, this function returns the id of the first row inserted!
	 * That is default on MySQL.
	 *
	 * @return int|string
	 */
	public function insertId()
	{
		return $this->mysqli->insert_id;
	}

	/**
	 * Protect identifier.
	 *
	 * @param string $identifier
	 *
	 * @see https://mariadb.com/kb/en/library/identifier-names/
	 *
	 * @return string
	 */
	public function protectIdentifier(string $identifier) : string
	{
		if ($identifier === '*') {
			return '*';
		}
		$identifier = \strtr($identifier, ['`' => '``', '.' => '`.`']);
		$identifier = "`{$identifier}`";
		return \strtr($identifier, ['`*`' => '*']);
	}

	/**
	 * Quote SQL values.
	 *
	 * @param bool|float|int|string|null $value Value to be quoted
	 *
	 * @see https://mariadb.com/kb/en/library/quote/
	 *
	 * @throws \InvalidArgumentException For invalid value type
	 *
	 * @return bool|float|int|string If the value is null, returns a string containing the word
	 *                               "NULL". If is false, "FALSE". If is true, "TRUE". If is a
	 *                               string, returns the quoted string. The types int or float
	 *                               returns the same input value.
	 */
	public function quote($value)
	{
		$type = \gettype($value);
		if ($type === 'string') {
			$value = $this->mysqli->real_escape_string($value);
			return "'{$value}'";
		}
		if ($type === 'integer' || $type === 'double') {
			return $value;
		}
		if ($type === 'boolean') {
			return $value ? 'TRUE' : 'FALSE';
		}
		if ($value === null) {
			return 'NULL';
		}
		throw new \InvalidArgumentException("Invalid value type: {$type}");
	}
}
