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

use Closure;
use Exception;
use Framework\Database\Debug\DatabaseCollector;
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
use Framework\Log\Logger;
use Framework\Log\LogLevel;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Language;
use LogicException;
use mysqli;
use mysqli_sql_exception;
use RuntimeException;
use SensitiveParameter;

/**
 * Class Database.
 *
 * @package database
 */
class Database
{
    protected ?mysqli $mysqli;
    /**
     * Connection configurations.
     *
     * Custom configs merged with the Base Connection configurations.
     *
     * @see Database::makeConfig()
     *
     * @var array<string,mixed>
     */
    protected array $config = [];
    /**
     * The current $config failover index to be used in a connection.
     *
     * @see Database::connect()
     *
     * @var int|null Integer representing the array index or null for none
     */
    protected ?int $failoverIndex = null;
    /**
     * @see Database::transaction()
     */
    protected bool $inTransaction = false;
    protected string $lastQuery = '';
    protected ?Logger $logger;
    protected DatabaseCollector $debugCollector;

    /**
     * Database constructor.
     *
     * @param array<string,mixed>|string $username
     * @param string|null $password
     * @param string|null $schema
     * @param string $host
     * @param int $port
     * @param Logger|null $logger
     *
     * @see Database::makeConfig()
     *
     * @throws mysqli_sql_exception if connections fail
     */
    public function __construct(
        #[SensitiveParameter] array | string $username,
        #[SensitiveParameter] string $password = null,
        string $schema = null,
        string $host = 'localhost',
        int $port = 3306,
        Logger $logger = null
    ) {
        $this->logger = $logger;
        $this->connect($username, $password, $schema, $host, $port);
    }

    public function __destruct()
    {
        $this->close();
    }

    protected function log(string $message, LogLevel $level = LogLevel::ERROR) : void
    {
        $this->logger?->log($level, $message);
    }

    /**
     * Make Base Connection configurations.
     *
     * @param array<string,mixed> $config
     *
     * @return array<string,mixed>
     */
    #[ArrayShape([
        'host' => 'string',
        'port' => 'int',
        'username' => 'string|null',
        'password' => 'string|null',
        'schema' => 'string|null',
        'socket' => 'string|null',
        'persistent' => 'bool',
        'engine' => 'string',
        'charset' => 'string',
        'collation' => 'string',
        'timezone' => 'string',
        'ssl' => 'array',
        'failover' => 'array',
        'options' => 'array',
        'report' => 'int',
    ])]
    protected function makeConfig(array $config) : array
    {
        return \array_replace_recursive([
            'host' => 'localhost',
            'port' => 3306,
            'username' => null,
            'password' => null,
            'schema' => null,
            'socket' => null,
            'persistent' => false,
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'timezone' => '+00:00',
            'ssl' => [
                'enabled' => false,
                'verify' => true,
                'key' => null,
                'cert' => null,
                'ca' => null,
                'capath' => null,
                'cipher' => null,
            ],
            'failover' => [],
            'options' => [
                \MYSQLI_OPT_CONNECT_TIMEOUT => 10,
                \MYSQLI_OPT_INT_AND_FLOAT_NATIVE => true,
                \MYSQLI_OPT_LOCAL_INFILE => 1,
            ],
            'report' => \MYSQLI_REPORT_ALL & ~\MYSQLI_REPORT_INDEX,
        ], $config);
    }

    /**
     * @param array<string,mixed>|string $username
     * @param string|null $password
     * @param string|null $schema
     * @param string $host
     * @param int $port
     *
     * @throws mysqli_sql_exception if connection fail
     *
     * @return static
     */
    protected function connect(
        #[SensitiveParameter] array | string $username,
        #[SensitiveParameter] string $password = null,
        string $schema = null,
        string $host = 'localhost',
        int $port = 3306
    ) : static {
        if ( ! \is_array($username)) {
            $username = [
                'host' => $host,
                'port' => $port,
                'username' => $username,
                'password' => $password,
                'schema' => $schema,
            ];
        }
        $config = $this->makeConfig($username);
        if ($this->failoverIndex === null) {
            $this->config = $config;
        }
        \mysqli_report($config['report']);
        $this->mysqli = new mysqli();
        foreach ($config['options'] as $option => $value) {
            $this->mysqli->options($option, $value);
        }
        try {
            $flags = 0;
            if ($config['ssl']['enabled'] === true) {
                $this->mysqli->ssl_set(
                    $config['ssl']['key'],
                    $config['ssl']['cert'],
                    $config['ssl']['ca'],
                    $config['ssl']['capath'],
                    $config['ssl']['cipher']
                );
                $flags += \MYSQLI_CLIENT_SSL;
                if ($config['ssl']['verify'] === false) {
                    $flags += \MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
                }
            }
            $this->mysqli->real_connect(
                ($config['persistent'] ? 'p:' : '') . $config['host'],
                $config['username'],
                $config['password'],
                $config['schema'],
                $config['port'] === null ? null : (int) $config['port'],
                $config['socket'],
                $flags
            );
        } catch (mysqli_sql_exception $exception) {
            $log = "Database: Connection failed for '{$config['username']}'@'{$config['host']}'";
            $log .= $this->failoverIndex !== null ? " (failover: {$this->failoverIndex})" : '';
            $this->log($log);
            $this->failoverIndex = $this->failoverIndex === null
                ? 0
                : $this->failoverIndex + 1;
            if (empty($config['failover'][$this->failoverIndex])) {
                throw $exception;
            }
            $config = \array_replace_recursive(
                $config,
                $config['failover'][$this->failoverIndex]
            );
            return $this->connect($config);
        }
        $this->setCollations($config['charset'], $config['collation']);
        $this->setTimezone($config['timezone']);
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

    /**
     * Gets the MySQLi connection.
     *
     * @return mysqli
     */
    public function getConnection() : mysqli
    {
        return $this->mysqli;
    }

    /**
     * Tells if the connection is open.
     *
     * @return bool
     */
    public function isOpen() : bool
    {
        return isset($this->mysqli);
    }

    /**
     * Closes the connection if it is open.
     *
     * @return bool
     */
    public function close() : bool
    {
        if ( ! $this->isOpen()) {
            return true;
        }
        $closed = $this->mysqli->close();
        if ($closed) {
            $this->mysqli = null;
        }
        return $closed;
    }

    /**
     * Pings the server, or tries to reconnect if the connection has gone down.
     *
     * @return bool
     */
    public function ping() : bool
    {
        return $this->mysqli->ping();
    }

    /**
     * Closes the current and opens a new connection with the last config.
     *
     * @return static
     */
    public function reconnect() : static
    {
        $this->close();
        return $this->connect($this->getConfig());
    }

    /**
     * @return array<string,mixed>
     */
    #[ArrayShape([
        'host' => 'string',
        'port' => 'int',
        'username' => 'string|null',
        'password' => 'string|null',
        'schema' => 'string|null',
        'socket' => 'string|null',
        'persistent' => 'bool',
        'engine' => 'string',
        'charset' => 'string',
        'collation' => 'string',
        'timezone' => 'string',
        'ssl' => 'array',
        'failover' => 'array',
        'options' => 'array',
        'report' => 'int',
    ])]
    public function getConfig() : array
    {
        return $this->config;
    }

    public function warnings() : int
    {
        return $this->mysqli->warning_count;
    }

    /**
     * Get a list of the latest errors.
     *
     * @return array<int,array<string,mixed>>
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
     *
     * @return static
     */
    public function use(string $schema) : static
    {
        $this->mysqli->select_db($schema);
        return $this;
    }

    /**
     * Call a CREATE SCHEMA statement.
     *
     * @param string|null $schemaName
     *
     * @return CreateSchema
     */
    public function createSchema(string $schemaName = null) : CreateSchema
    {
        $instance = new CreateSchema($this);
        if ($schemaName !== null) {
            $instance->schema($schemaName);
        }
        return $instance;
    }

    /**
     * Call a DROP SCHEMA statement.
     *
     * @param string|null $schemaName
     *
     * @return DropSchema
     */
    public function dropSchema(string $schemaName = null) : DropSchema
    {
        $instance = new DropSchema($this);
        if ($schemaName !== null) {
            $instance->schema($schemaName);
        }
        return $instance;
    }

    /**
     * Call a ALTER SCHEMA statement.
     *
     * @param string|null $schemaName
     *
     * @return AlterSchema
     */
    public function alterSchema(string $schemaName = null) : AlterSchema
    {
        $instance = new AlterSchema($this);
        if ($schemaName !== null) {
            $instance->schema($schemaName);
        }
        return $instance;
    }

    /**
     * Call a CREATE TABLE statement.
     *
     * @param string|null $tableName
     *
     * @return CreateTable
     */
    public function createTable(string $tableName = null) : CreateTable
    {
        $instance = new CreateTable($this);
        if ($tableName !== null) {
            $instance->table($tableName);
        }
        return $instance;
    }

    /**
     * Call a DROP TABLE statement.
     *
     * @param string|null $table
     * @param string ...$tables
     *
     * @return DropTable
     */
    public function dropTable(string $table = null, string ...$tables) : DropTable
    {
        $instance = new DropTable($this);
        if ($table !== null) {
            $instance->table($table, ...$tables);
        }
        return $instance;
    }

    /**
     * Call a ALTER TABLE statement.
     *
     * @param string|null $tableName
     *
     * @return AlterTable
     */
    public function alterTable(string $tableName = null) : AlterTable
    {
        $instance = new AlterTable($this);
        if ($tableName !== null) {
            $instance->table($tableName);
        }
        return $instance;
    }

    /**
     * Call a DELETE statement.
     *
     * @param array<string,Closure|string>|Closure|string|null $reference
     * @param array<string,Closure|string>|Closure|string ...$references
     *
     * @return Delete
     */
    public function delete(
        array | Closure | string $reference = null,
        array | Closure | string ...$references
    ) : Delete {
        $instance = new Delete($this);
        if ($reference !== null) {
            $instance->table($reference, ...$references);
        }
        return $instance;
    }

    /**
     * Call a INSERT statement.
     *
     * @param string|null $intoTable
     *
     * @return Insert
     */
    public function insert(string $intoTable = null) : Insert
    {
        $instance = new Insert($this);
        if ($intoTable !== null) {
            $instance->into($intoTable);
        }
        return $instance;
    }

    /**
     * Call a LOAD DATA statement.
     *
     * @param string|null $intoTable
     *
     * @return LoadData
     */
    public function loadData(string $intoTable = null) : LoadData
    {
        $instance = new LoadData($this);
        if ($intoTable !== null) {
            $instance->intoTable($intoTable);
        }
        return $instance;
    }

    /**
     * Call a REPLACE statement.
     *
     * @param string|null $intoTable
     *
     * @return Replace
     */
    public function replace(string $intoTable = null) : Replace
    {
        $instance = new Replace($this);
        if ($intoTable !== null) {
            $instance->into($intoTable);
        }
        return $instance;
    }

    /**
     * Call a SELECT statement.
     *
     * @param array<string,Closure|string>|Closure|string|null $reference
     * @param array<string,Closure|string>|Closure|string ...$references
     *
     * @return Select
     */
    public function select(
        array | Closure | string $reference = null,
        array | Closure | string ...$references
    ) : Select {
        $instance = new Select($this);
        if ($reference !== null) {
            $instance->from($reference, ...$references);
        }
        return $instance;
    }

    /**
     * Call a UPDATE statement.
     *
     * @param array<string,Closure|string>|Closure|string|null $reference
     * @param array<string,Closure|string>|Closure|string ...$references
     *
     * @return Update
     */
    public function update(
        array | Closure | string $reference = null,
        array | Closure | string ...$references
    ) : Update {
        $instance = new Update($this);
        if ($reference !== null) {
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
     * @return int|string
     */
    public function exec(#[Language('SQL')] string $statement) : int | string
    {
        $this->lastQuery = $statement;
        isset($this->debugCollector)
            ? $this->addToDebug(fn () => $this->mysqli->real_query($statement))
            : $this->mysqli->real_query($statement);
        if ($this->mysqli->field_count) {
            $result = $this->mysqli->store_result();
            if ($result) {
                $result->free();
            }
        }
        return $this->mysqli->affected_rows;
    }

    /**
     * Executes an SQL statement, returning a result set as a Result object.
     *
     * Must be: SELECT, SHOW, DESCRIBE or EXPLAIN
     *
     * @param string $statement
     * @param bool $buffered
     *
     * @see https://www.php.net/manual/en/mysqlinfo.concepts.buffering.php
     *
     * @throws InvalidArgumentException if $statement does not return result
     *
     * @return Result
     */
    public function query(
        #[Language('SQL')] string $statement,
        bool $buffered = true
    ) : Result {
        $this->lastQuery = $statement;
        $resultMode = $buffered ? \MYSQLI_STORE_RESULT : \MYSQLI_USE_RESULT;
        $result = isset($this->debugCollector)
            ? $this->addToDebug(fn () => $this->mysqli->query($statement, $resultMode))
            : $this->mysqli->query($statement, $resultMode);
        if (\is_bool($result)) {
            throw new InvalidArgumentException(
                "Statement does not return result: {$statement}"
            );
        }
        return new Result($result, $buffered);
    }

    /**
     * Prepares a statement for execution and returns a PreparedStatement object.
     *
     * @param string $statement
     *
     * @throws RuntimeException if prepared statement fail
     *
     * @return PreparedStatement
     */
    public function prepare(#[Language('SQL')] string $statement) : PreparedStatement
    {
        $prepared = $this->mysqli->prepare($statement);
        if ($prepared === false) {
            throw new RuntimeException('Prepared statement failed: ' . $statement);
        }
        return new PreparedStatement($prepared);
    }

    /**
     * Run statements in a transaction.
     *
     * @param callable $statements
     *
     * @throws Exception if statements fail
     * @throws LogicException if transaction already is active
     *
     * @return static
     */
    public function transaction(callable $statements) : static
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
        return $this;
    }

    /**
     * Gets the LAST_INSERT_ID().
     *
     * Note: When an insert has many rows, this function returns the id of the
     * first row inserted!
     * That is default on MySQL.
     *
     * @return int|string
     */
    public function insertId() : int | string
    {
        return $this->mysqli->insert_id;
    }

    /**
     * Protect identifier.
     *
     * @param string $identifier
     *
     * @see https://mariadb.com/kb/en/identifier-names/
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
     * @see https://mariadb.com/kb/en/quote/
     *
     * @throws InvalidArgumentException For invalid value type
     *
     * @return float|int|string If the value is null, returns a string containing
     * the word "NULL". If is false, "FALSE". If is true, "TRUE". If is a string,
     * returns the quoted string. The types int or float returns the same input value.
     */
    public function quote(float | bool | int | string | null $value) : float | int | string
    {
        $type = \gettype($value);
        if ($type === 'string') {
            // @phpstan-ignore-next-line
            $value = $this->mysqli->real_escape_string($value);
            return "'{$value}'";
        }
        if ($type === 'integer' || $type === 'double') {
            return $value; // @phpstan-ignore-line
        }
        if ($type === 'boolean') {
            return $value ? 'TRUE' : 'FALSE';
        }
        if ($value === null) {
            return 'NULL';
        }
        // @codeCoverageIgnoreStart
        // Should never throw - all accepted types have been verified
        throw new InvalidArgumentException("Invalid value type: {$type}");
        // @codeCoverageIgnoreEnd
    }

    public function setDebugCollector(DatabaseCollector $collector) : static
    {
        $collector->setDatabase($this);
        $this->debugCollector = $collector;
        return $this;
    }

    protected function addToDebug(Closure $function) : mixed
    {
        $start = \microtime(true);
        try {
            $result = $function();
        } catch (Exception $exception) {
            $this->finalizeAddToDebug($start, $exception->getMessage());
            throw $exception;
        }
        $this->finalizeAddToDebug($start);
        return $result;
    }

    protected function finalizeAddToDebug(
        float $start,
        string|null $description = null
    ) : void {
        $end = \microtime(true);
        $rows = $this->mysqli->affected_rows;
        $rows = $rows < 0 ? 'error' : $rows;
        $this->debugCollector->addData([
            'start' => $start,
            'end' => $end,
            'statement' => $this->lastQuery(),
            'rows' => $rows,
            'description' => $description,
        ]);
    }
}
