<?php namespace Framework\Database;

use Closure;
use Exception;
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
use InvalidArgumentException;
use LogicException;
use mysqli;
use mysqli_sql_exception;

/**
 * Class Database.
 */
class Database
{
	protected mysqli $mysqli;
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
	protected string $lastQuery = '';

	/**
	 * Database constructor.
	 *
	 * @param array|mixed[]|string $username
	 * @param string|null          $password
	 * @param string|null          $schema
	 * @param string               $host
	 * @param int                  $port
	 *
	 * @see Database::makeConfig
	 *
	 * @throws Exception if connections fail
	 */
	public function __construct(
		$username,
		string $password = null,
		string $schema = null,
		string $host = 'localhost',
		int $port = 3306
	) {
		\mysqli_report(\MYSQLI_REPORT_ALL & ~\MYSQLI_REPORT_INDEX);
		$this->mysqli = new mysqli();
		$this->mysqli->options(\MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
		$this->mysqli->options(\MYSQLI_OPT_CONNECT_TIMEOUT, 10);
		$this->connect($username, $password, $schema, $host, $port);
	}

	public function __destruct()
	{
		$this->mysqli->close();
	}

	/**
	 * Make Base Connection configurations.
	 *
	 * @param array|mixed[] $config
	 *
	 * @return array|mixed[]
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

	/**
	 * @param mixed[]|string $username
	 * @param string|null    $password
	 * @param string|null    $schema
	 * @param string         $host
	 * @param int            $port
	 *
	 * @throws Exception
	 *
	 * @return $this
	 */
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
		} catch (Exception $exception) {
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

	protected function setCollations(string $charset, string $collation) : bool
	{
		$this->mysqli->set_charset($charset);
		$charset = $this->quote($charset);
		$collation = $this->quote($collation);
		return $this->mysqli->real_query("SET NAMES {$charset} COLLATE {$collation}");
	}

	protected function setTimezone(string $timezone) : bool
	{
		$timezone = $this->quote($timezone);
		return $this->mysqli->real_query("SET time_zone = {$timezone}");
	}

	public function warnings() : int
	{
		return $this->mysqli->warning_count;
	}

	/**
	 * Get a list of latest errors.
	 *
	 * @return array|array[]
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
	 * @param string $schema
	 *
	 * @throws mysqli_sql_exception if schema is unknown
	 */
	public function use(string $schema) : void
	{
		$this->mysqli->select_db($schema);
	}

	/**
	 * Call a CREATE SCHEMA statement.
	 *
	 * @param string|null $schema_name
	 *
	 * @return CreateSchema
	 */
	public function createSchema(string $schema_name = null) : CreateSchema
	{
		$instance = new CreateSchema($this);
		if ($schema_name) {
			$instance->schema($schema_name);
		}
		return $instance;
	}

	/**
	 * Call a DROP SCHEMA statement.
	 *
	 * @param string|null $schema_name
	 *
	 * @return DropSchema
	 */
	public function dropSchema(string $schema_name = null) : DropSchema
	{
		$instance = new DropSchema($this);
		if ($schema_name) {
			$instance->schema($schema_name);
		}
		return $instance;
	}

	/**
	 * Call a ALTER SCHEMA statement.
	 *
	 * @param string|null $schema_name
	 *
	 * @return AlterSchema
	 */
	public function alterSchema(string $schema_name = null) : AlterSchema
	{
		$instance = new AlterSchema($this);
		if ($schema_name) {
			$instance->schema($schema_name);
		}
		return $instance;
	}

	/**
	 * Call a CREATE TABLE statement.
	 *
	 * @param string|null $table_name
	 *
	 * @return CreateTable
	 */
	public function createTable(string $table_name = null) : CreateTable
	{
		$instance = new CreateTable($this);
		if ($table_name) {
			$instance->table($table_name);
		}
		return $instance;
	}

	/**
	 * Call a DROP TABLE statement.
	 *
	 * @param string|null $table
	 * @param mixed       $tables
	 *
	 * @return DropTable
	 */
	public function dropTable(string $table = null, string ...$tables) : DropTable
	{
		$instance = new DropTable($this);
		if ($table) {
			$instance->table($table, ...$tables);
		}
		return $instance;
	}

	/**
	 * Call a ALTER TABLE statement.
	 *
	 * @param string|null $table_name
	 *
	 * @return AlterTable
	 */
	public function alterTable(string $table_name = null) : AlterTable
	{
		$instance = new AlterTable($this);
		if ($table_name) {
			$instance->table($table_name);
		}
		return $instance;
	}

	/**
	 * Call a DELETE statement.
	 *
	 * @param array|Closure|string $reference
	 * @param mixed                $references
	 *
	 * @return Delete
	 */
	public function delete($reference = null, ...$references) : Delete
	{
		$instance = new Delete($this);
		if ($reference) {
			$instance->table($reference, ...$references);
		}
		return $instance;
	}

	/**
	 * Call a INSERT statement.
	 *
	 * @param string|null $into_table
	 *
	 * @return Insert
	 */
	public function insert(string $into_table = null) : Insert
	{
		$instance = new Insert($this);
		if ($into_table) {
			$instance->into($into_table);
		}
		return $instance;
	}

	/**
	 * Call a LOAD DATA statement.
	 *
	 * @param string|null $into_table
	 *
	 * @return LoadData
	 */
	public function loadData(string $into_table = null) : LoadData
	{
		$instance = new LoadData($this);
		if ($into_table) {
			$instance->intoTable($into_table);
		}
		return $instance;
	}

	/**
	 * Call a REPLACE statement.
	 *
	 * @param string|null $into_table
	 *
	 * @return Replace
	 */
	public function replace(string $into_table = null) : Replace
	{
		$instance = new Replace($this);
		if ($into_table) {
			$instance->into($into_table);
		}
		return $instance;
	}

	/**
	 * Call a SELECT statement.
	 *
	 * @param array|Closure|string|null $reference
	 * @param mixed                     $references
	 *
	 * @return Select
	 */
	public function select($reference = null, ...$references) : Select
	{
		$instance = new Select($this);
		if ($reference) {
			$instance->from($reference, ...$references);
		}
		return $instance;
	}

	/**
	 * Call a UPDATE statement.
	 *
	 * @param array|Closure|string|null $reference
	 * @param mixed                     $references
	 *
	 * @return Update
	 */
	public function update($reference = null, ...$references) : Update
	{
		$instance = new Update($this);
		if ($reference) {
			$instance->table($reference, ...$references);
		}
		return $instance;
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

	public function lastQuery() : string
	{
		return $this->lastQuery;
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
		$this->lastQuery = $statement;
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
	 * @throws InvalidArgumentException if $statement does not return result
	 *
	 * @return Result
	 */
	public function query(string $statement) : Result
	{
		$this->lastQuery = $statement;
		$result = $this->mysqli->query($statement);
		if (\is_bool($result)) {
			throw new InvalidArgumentException(
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
	 *
	 * @throws Exception      if statements fail
	 * @throws LogicException if transaction already is active
	 */
	public function transaction(callable $statements) : void
	{
		if ($this->inTransaction) {
			throw new LogicException('Transaction already is active');
		}
		$this->inTransaction = true;
		$this->mysqli->autocommit(false);
		$this->mysqli->begin_transaction();
		try {
			$statements($this);
			$this->mysqli->commit();
		} catch (Exception $exception) {
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
	 * @throws InvalidArgumentException For invalid value type
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
		throw new InvalidArgumentException("Invalid value type: {$type}");
	}
}
