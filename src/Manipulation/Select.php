<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Manipulation;

use Closure;
use Framework\Database\Result;
use InvalidArgumentException;
use LogicException;

/**
 * Class Select.
 *
 * @see https://mariadb.com/kb/en/select/
 *
 * @package database
 */
class Select extends Statement
{
    use Traits\Join;
    use Traits\Having;
    use Traits\OrderBy;

    /**
     * Option to retrieve identical rows.
     *
     * @see Select::renderOptions()
     *
     * @var string
     */
    public const OPT_ALL = 'ALL';
    /**
     * Option to not retrieve identical rows. Remove duplicates from the result set.
     *
     * @see Select::renderOptions()
     * @see https://mariadb.com/kb/en/select/#distinct
     *
     * @var string
     */
    public const OPT_DISTINCT = 'DISTINCT';
    /**
     * Alias of OPT_DISTINCT.
     *
     * @see Select::renderOptions()
     *
     * @var string
     */
    public const OPT_DISTINCTROW = 'DISTINCTROW';
    /**
     * If the table is locked, HIGH_PRIORITY will be executed as soon as the lock is released,
     * even if other statements are queued.
     * Only supports table-level locking (MyISAM, MEMORY, MERGE).
     *
     * @see Select::renderOptions()
     * @see https://mariadb.com/kb/en/high_priority-and-low_priority/
     *
     * @var string
     */
    public const OPT_HIGH_PRIORITY = 'HIGH_PRIORITY';
    /**
     * Applicable to the JOIN queries. Tells the optimizer that
     * the tables must be read in the order they appear.
     * For const and system table this option is sometimes ignored.
     *
     * @see Select::renderOptions()
     * @see https://mariadb.com/kb/en/join-syntax/
     * @see https://mariadb.com/kb/en/index-hints-how-to-force-query-plans/#forcing-join-order
     *
     * @var string
     */
    public const OPT_STRAIGHT_JOIN = 'STRAIGHT_JOIN';
    /**
     * Forces the optimizer to use a temporary table.
     *
     * @see https://mariadb.com/kb/en/optimizer-hints/#sql_small_result-sql_big_result
     *
     * @var string
     */
    public const OPT_SQL_SMALL_RESULT = 'SQL_SMALL_RESULT';
    /**
     * Forces the optimizer to avoid usage of a temporary table.
     *
     * @see Select::renderOptions()
     * @see https://mariadb.com/kb/en/optimizer-hints/#sql_small_result-sql_big_result
     *
     * @var string
     */
    public const OPT_SQL_BIG_RESULT = 'SQL_BIG_RESULT';
    /**
     * Forces the optimizer to use a temporary table to process the result.
     * This is useful to free locks as soon as possible.
     *
     * @see Select::renderOptions()
     * @see https://mariadb.com/kb/en/optimizer-hints/#sql_buffer_result
     *
     * @var string
     */
    public const OPT_SQL_BUFFER_RESULT = 'SQL_BUFFER_RESULT';
    /**
     * If the query_cache_type system variable is set to 2 or DEMAND, and the current statement is
     * cacheable, SQL_CACHE causes the query to be cached.
     *
     * @see Select::renderOptions()
     * @see https://mariadb.com/kb/en/server-system-variables/#query_cache_type
     * @see https://mariadb.com/kb/en/query-cache/
     *
     * @var string
     */
    public const OPT_SQL_CACHE = 'SQL_CACHE';
    /**
     * If the query_cache_type system variable is set to 2 or DEMAND, and the current statement is
     * cacheable, SQL_NO_CACHE causes the query not to be cached.
     *
     * @see Select::renderOptions()
     * @see https://mariadb.com/kb/en/server-system-variables/#query_cache_type
     * @see https://mariadb.com/kb/en/query-cache/
     *
     * @var string
     */
    public const OPT_SQL_NO_CACHE = 'SQL_NO_CACHE';
    /**
     * SQL_CALC_FOUND_ROWS is only applied when using the LIMIT clause. If this option is used,
     * MariaDB will count how many rows would match the query, without the LIMIT clause.
     * That number can be retrieved in the next query, using FOUND_ROWS().
     *
     * @see Select::renderOptions()
     * @see https://mariadb.com/kb/en/found_rows/
     *
     * @var string
     */
    public const OPT_SQL_CALC_FOUND_ROWS = 'SQL_CALC_FOUND_ROWS';
    /**
     * Clause to set the character of separation between fields. Default is \t.
     *
     * @see Select::intoOutfile()
     *
     * @var string
     */
    public const EXP_FIELDS_TERMINATED_BY = 'TERMINATED BY';
    /**
     * Clause to set the enclosure character of the fields. Default is ".
     *
     * @see Select::intoOutfile()
     *
     * @var string
     */
    public const EXP_FIELDS_ENCLOSED_BY = 'ENCLOSED BY';
    /**
     * @see Select::intoOutfile()
     *
     * @var string
     */
    public const EXP_FIELDS_OPTIONALLY_ENCLOSED_BY = 'OPTIONALLY ENCLOSED BY';
    /**
     * @see Select::intoOutfile()
     *
     * @var string
     */
    public const EXP_FIELDS_ESCAPED_BY = 'ESCAPED BY';
    /**
     * @see Select::intoOutfile()
     *
     * @var string
     */
    public const EXP_LINES_STARTING_BY = 'STARTING BY';
    /**
     * Clause to set the file End-Of-Line character. Default is \n.
     *
     * @see Select::intoOutfile()
     *
     * @var string
     */
    public const EXP_LINES_TERMINATED_BY = 'TERMINATED BY';

    protected function renderOptions() : ?string
    {
        if ( ! $this->hasOptions()) {
            return null;
        }
        $options = $this->sql['options'];
        foreach ($options as &$option) {
            $input = $option;
            $option = \strtoupper($option);
            if ( ! \in_array($option, [
                static::OPT_ALL,
                static::OPT_DISTINCT,
                static::OPT_DISTINCTROW,
                static::OPT_HIGH_PRIORITY,
                static::OPT_STRAIGHT_JOIN,
                static::OPT_SQL_SMALL_RESULT,
                static::OPT_SQL_BIG_RESULT,
                static::OPT_SQL_BUFFER_RESULT,
                static::OPT_SQL_CACHE,
                static::OPT_SQL_NO_CACHE,
                static::OPT_SQL_CALC_FOUND_ROWS,
            ], true)) {
                throw new InvalidArgumentException("Invalid option: {$input}");
            }
        }
        unset($option);
        $intersection = \array_intersect(
            $options,
            [static::OPT_ALL, static::OPT_DISTINCT, static::OPT_DISTINCTROW]
        );
        if (\count($intersection) > 1) {
            throw new LogicException('Options ALL and DISTINCT can not be used together');
        }
        $intersection = \array_intersect(
            $options,
            [static::OPT_SQL_CACHE, static::OPT_SQL_NO_CACHE]
        );
        if (\count($intersection) > 1) {
            throw new LogicException('Options SQL_CACHE and SQL_NO_CACHE can not be used together');
        }
        return \implode(' ', $options);
    }

    /**
     * Set expressions.
     *
     * Generally used with the FROM clause as column names.
     *
     * @param array<string,Closure|string>|Closure|string $expression
     * @param array<string,Closure|string>|Closure|string ...$expressions
     *
     * @see https://mariadb.com/kb/en/select/#select-expressions
     *
     * @return static
     */
    public function expressions(
        array | Closure | string $expression,
        array | Closure | string ...$expressions
    ) : static {
        foreach ([$expression, ...$expressions] as $expression) {
            $this->sql['expressions'][] = $expression;
        }
        return $this;
    }

    /**
     * Alias of the expressions method.
     *
     * @param array<string,Closure|string>|Closure|string $expression
     * @param array<string,Closure|string>|Closure|string ...$expressions
     *
     * @return static
     */
    public function columns(
        array | Closure | string $expression,
        array | Closure | string ...$expressions
    ) : static {
        return $this->expressions($expression, ...$expressions);
    }

    protected function renderExpressions() : ?string
    {
        if ( ! isset($this->sql['expressions'])) {
            if ($this->hasFrom()) {
                return ' *';
            }
            return null;
        }
        $expressions = [];
        foreach ($this->sql['expressions'] as $expression) {
            $expressions[] = $this->renderAliasedIdentifier($expression);
        }
        return ' ' . \implode(', ', $expressions);
    }

    /**
     * Sets the LIMIT clause.
     *
     * @param int $limit
     * @param int|null $offset
     *
     * @see https://mariadb.com/kb/en/limit/
     *
     * @return static
     */
    public function limit(int $limit, int $offset = null) : static
    {
        return $this->setLimit($limit, $offset);
    }

    /**
     * @param string $name
     * @param bool|float|int|string|null ...$arguments
     *
     * @see https://mariadb.com/kb/en/procedure/
     *
     * @return static
     */
    public function procedure(string $name, bool | float | int | string | null ...$arguments) : static
    {
        $this->sql['procedure'] = [
            'name' => $name,
            'arguments' => $arguments,
        ];
        return $this;
    }

    protected function renderProcedure() : ?string
    {
        if ( ! isset($this->sql['procedure'])) {
            return null;
        }
        $arguments = [];
        foreach ($this->sql['procedure']['arguments'] as $argument) {
            $arguments[] = $this->database->quote($argument);
        }
        $arguments = \implode(', ', $arguments);
        return " PROCEDURE {$this->sql['procedure']['name']}({$arguments})";
    }

    /**
     * Exports the result to an external file.
     *
     * @param string $filename
     * @param string|null $charset
     * @param array<string,string> $fieldsOptions Each key must be one of the EXP_FIELDS_* constants
     * @param array<string,string> $linesOptions Each key must be one of the EXP_LINES_* constants
     *
     * @see https://mariadb.com/kb/en/select-into-outfile/
     *
     * @return static
     */
    public function intoOutfile(
        string $filename,
        string $charset = null,
        array $fieldsOptions = [],
        array $linesOptions = []
    ) : static {
        $this->sql['into_outfile'] = [
            'filename' => $filename,
            'charset' => $charset,
            'fields_options' => $fieldsOptions,
            'lines_options' => $linesOptions,
        ];
        return $this;
    }

    protected function renderIntoOutfile() : ?string
    {
        if ( ! isset($this->sql['into_outfile'])) {
            return null;
        }
        if (\is_file($this->sql['into_outfile']['filename'])) {
            throw new LogicException(
                "INTO OUTFILE filename must not exist: {$this->sql['into_outfile']['filename']}"
            );
        }
        $definition = $this->database->quote($this->sql['into_outfile']['filename']);
        if ($this->sql['into_outfile']['charset']) {
            $definition .= ' CHARACTER SET '
                . $this->database->quote(
                    $this->sql['into_outfile']['charset']
                );
        }
        $definition .= $this->partIntoOutfileFields();
        $definition .= $this->partIntoOutfileLines();
        return " INTO OUTFILE {$definition}";
    }

    private function partIntoOutfileFields() : ?string
    {
        $definition = null;
        if ($this->sql['into_outfile']['fields_options']) {
            $definition .= ' FIELDS';
            foreach ($this->sql['into_outfile']['fields_options'] as $option => $value) {
                $fieldsOption = \strtoupper($option);
                if ( ! \in_array($fieldsOption, [
                    static::EXP_FIELDS_TERMINATED_BY,
                    static::EXP_FIELDS_ENCLOSED_BY,
                    static::EXP_FIELDS_OPTIONALLY_ENCLOSED_BY,
                    static::EXP_FIELDS_ESCAPED_BY,
                ], true)) {
                    throw new InvalidArgumentException(
                        "Invalid INTO OUTFILE fields option: {$option}"
                    );
                }
                $definition .= " {$fieldsOption} " . $this->database->quote($value);
            }
        }
        return $definition;
    }

    private function partIntoOutfileLines() : ?string
    {
        $definition = null;
        if ($this->sql['into_outfile']['lines_options']) {
            $definition .= ' LINES';
            foreach ($this->sql['into_outfile']['lines_options'] as $option => $value) {
                $linesOption = \strtoupper($option);
                if ( ! \in_array($linesOption, [
                    static::EXP_LINES_STARTING_BY,
                    static::EXP_LINES_TERMINATED_BY,
                ], true)) {
                    throw new InvalidArgumentException(
                        "Invalid INTO OUTFILE lines option: {$option}"
                    );
                }
                $definition .= " {$linesOption} " . $this->database->quote($value);
            }
        }
        return $definition;
    }

    /**
     * @param string $filepath
     * @param string ...$variables
     *
     * @see https://mariadb.com/kb/en/select-into-dumpfile/
     *
     * @return static
     */
    public function intoDumpfile(string $filepath, string ...$variables) : static
    {
        $this->sql['into_dumpfile'] = [
            'filepath' => $filepath,
            'variables' => $variables,
        ];
        return $this;
    }

    protected function renderIntoDumpfile() : ?string
    {
        if ( ! isset($this->sql['into_dumpfile'])) {
            return null;
        }
        if (\is_file($this->sql['into_dumpfile']['filepath'])) {
            throw new LogicException(
                "INTO DUMPFILE filepath must not exist: {$this->sql['into_dumpfile']['filepath']}"
            );
        }
        $definition = $this->database->quote($this->sql['into_dumpfile']['filepath']);
        if ($this->sql['into_dumpfile']['variables']) {
            $variables = [];
            foreach ($this->sql['into_dumpfile']['variables'] as $variable) {
                $variables[] = "@{$variable}";
            }
            $definition .= ' INTO ' . \implode(', ', $variables);
        }
        return " INTO DUMPFILE {$definition}";
    }

    /**
     * @param int|null $wait
     *
     * @see https://mariadb.com/kb/en/for-update/
     *
     * @return static
     */
    public function lockForUpdate(int $wait = null) : static
    {
        $this->sql['lock'] = [
            'type' => 'FOR UPDATE',
            'wait' => $wait,
        ];
        return $this;
    }

    /**
     * @param int|null $wait
     *
     * @see https://mariadb.com/kb/en/lock-in-share-mode/
     *
     * @return static
     */
    public function lockInShareMode(int $wait = null) : static
    {
        $this->sql['lock'] = [
            'type' => 'LOCK IN SHARE MODE',
            'wait' => $wait,
        ];
        return $this;
    }

    protected function renderLock() : ?string
    {
        if ( ! isset($this->sql['lock'])) {
            return null;
        }
        $wait = '';
        if ($this->sql['lock']['wait'] !== null) {
            if ($this->sql['lock']['wait'] < 0) {
                throw new InvalidArgumentException(
                    "Invalid {$this->sql['lock']['type']} WAIT value: {$this->sql['lock']['wait']}"
                );
            }
            $wait .= " WAIT {$this->sql['lock']['wait']}";
        }
        return " {$this->sql['lock']['type']}{$wait}";
    }

    /**
     * Renders the SELECT statement.
     *
     * @return string
     */
    public function sql() : string
    {
        $sql = 'SELECT' . \PHP_EOL;
        $part = $this->renderOptions();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderExpressions();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderFrom();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderJoin();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderWhere();
        if ($part) {
            $this->hasFrom('WHERE');
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderHaving();
        if ($part) {
            $this->hasFrom('HAVING');
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderOrderBy();
        if ($part) {
            $this->hasFrom('ORDER BY');
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderLimit();
        if ($part) {
            $this->hasFrom('LIMIT');
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderProcedure();
        if ($part) {
            $this->hasFrom('PROCEDURE');
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderIntoOutfile();
        if ($part) {
            $this->hasFrom('INTO OUTFILE');
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderIntoDumpfile();
        if ($part) {
            $intoDump = true;
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderLock();
        if ($part) {
            if (empty($intoDump)) {
                $this->hasFrom($this->sql['lock']['type']);
            }
            $sql .= $part . \PHP_EOL;
        }
        return $sql;
    }

    /**
     * Runs the SELECT statement.
     *
     * @return Result
     */
    public function run() : Result
    {
        return $this->database->query($this->sql());
    }

    /**
     * Runs the SELECT statement unbuffered.
     *
     * @return Result
     */
    public function runUnbuffered() : Result
    {
        return $this->database->query($this->sql(), false);
    }
}
