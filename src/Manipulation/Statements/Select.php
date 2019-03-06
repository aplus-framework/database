<?php namespace Framework\Database\Manipulation\Statements;

/**
 * Class Select.
 *
 * @see https://mariadb.com/kb/en/library/select/
 */
class Select extends Statement
{
	use Traits\Join;
	use Traits\Having;
	use Traits\OrderBy;
	/**
	 * Option to retrieve identical rows.
	 */
	public const OPT_ALL = 'ALL';
	/**
	 * Option to not retrieve identical rows. Remove duplicates from the resultset.
	 */
	public const OPT_DISTINCT = 'DISTINCT';
	/**
	 * Alias of OPT_DISTINCT.
	 */
	public const OPT_DISTINCTROW = 'DISTINCTROW';
	/**
	 * If the table is locked, HIGH_PRIORITY will be executed as soon as the lock is released,
	 * even if other statements are queued.
	 * Only supports table-level locking (MyISAM, MEMORY, MERGE).
	 *
	 * @see https://mariadb.com/kb/en/library/high_priority-and-low_priority/
	 */
	public const OPT_HIGH_PRIORITY = 'HIGH_PRIORITY';
	/**
	 * STRAIGHT_JOIN applies to the JOIN queries, and tells the optimizer that
	 * the tables must be read in the order they appear.
	 * For const and system table this options is sometimes ignored.
	 *
	 * @see https://mariadb.com/kb/en/library/join-syntax/
	 */
	public const OPT_STRAIGHT_JOIN = 'STRAIGHT_JOIN';
	public const OPT_SQL_SMALL_RESULT = 'SQL_SMALL_RESULT';
	public const OPT_SQL_BIG_RESULT = 'SQL_BIG_RESULT';
	/**
	 * SQL_BUFFER_RESULT forces the optimizer to use a temporary table to process the result.
	 * This is useful to free locks as soon as possible.
	 */
	public const OPT_SQL_BUFFER_RESULT = 'SQL_BUFFER_RESULT';
	/**
	 * If the query_cache_type system variable is set to 2 or DEMAND, and the current statement is
	 * cacheable, SQL_CACHE causes the query to be cached.
	 *
	 * @see https://mariadb.com/kb/en/library/server-system-variables/#query_cache_type
	 * @see https://mariadb.com/kb/en/library/query-cache/
	 */
	public const OPT_SQL_CACHE = 'SQL_CACHE';
	/**
	 * If the query_cache_type system variable is set to 2 or DEMAND, and the current statement is
	 * cacheable, SQL_NO_CACHE causes the query not to be cached.
	 *
	 * @see https://mariadb.com/kb/en/library/server-system-variables/#query_cache_type
	 * @see https://mariadb.com/kb/en/library/query-cache/
	 */
	public const OPT_SQL_NO_CACHE = 'SQL_NO_CACHE';
	/**
	 * SQL_CALC_FOUND_ROWS is only applied when using the LIMIT clause. If this option is used,
	 * MariaDB will count how many rows would match the query, without the LIMIT clause.
	 * That number can be retrieved in the next query, using FOUND_ROWS().
	 *
	 * @see https://mariadb.com/kb/en/library/found_rows/
	 */
	public const OPT_SQL_CALC_FOUND_ROWS = 'SQL_CALC_FOUND_ROWS';
	/**
	 * Clause to set the character of separation between fields. Default is \t.
	 */
	public const EXP_FIELD_TERMINATED_BY = 'TERMINATED BY';
	/**
	 * Clause to set the enclosure character of the fields. Default is ".
	 */
	public const EXP_FIELD_ENCLOSED_BY = 'ENCLOSED BY';
	public const EXP_FIELD_OPTIONALLY_ENCLOSED_BY = 'OPTIONALLY ENCLOSED BY';
	public const EXP_FIELD_ESCAPED_BY = 'ESCAPED BY';
	public const EXP_LINE_STARTING_BY = 'STARTING BY';
	/**
	 * Clause to set the file End-Of-Line character. Default is \n.
	 */
	public const EXP_LINE_TERMINATED_BY = 'TERMINATED BY';

	/**
	 * Set the statement options.
	 *
	 * @param mixed $options Each option value must be one of the OPT_* constants
	 *
	 * @see https://mariadb.com/kb/en/library/optimizer-hints/
	 *
	 * @return $this
	 */
	public function options(...$options)
	{
		foreach ($options as $option) {
			$this->sql['options'][] = $option;
		}
		return $this;
	}

	public function renderOptions() : ?string
	{
		if ( ! isset($this->sql['options'])) {
			return null;
		}
		return \implode(' ', $this->sql['options']);
	}

	/**
	 * Set expressions.
	 *
	 * Gerally used with the FROM clause as column names.
	 *
	 * @param mixed $expressions Each expresion must be of type: array, string or \Closure
	 *
	 * @return $this
	 */
	public function expressions(...$expressions)
	{
		foreach ($expressions as $expression) {
			$this->sql['expressions'][] = $expression;
		}
		return $this;
	}

	/**
	 * Alias of the expressions method.
	 *
	 * @param mixed $expressions Each expresion must be of type: array, string or \Closure
	 *
	 * @return $this
	 */
	public function columns(...$expressions)
	{
		return $this->expressions(...$expressions);
	}

	protected function renderExpressions() : ?string
	{
		if ( ! isset($this->sql['expressions'])) {
			return null;
		}
		$expressions = [];
		foreach ($this->sql['expressions'] as $expression) {
			$expressions[] = $this->renderColumn($expression);
		}
		return \implode(', ', $expressions);
	}

	public function limit(int $limit, int $offset = null)
	{
		return parent::limit($limit, $offset);
	}

	/**
	 * @param string $name
	 * @param mixed  $arguments
	 *
	 * @see https://mariadb.com/kb/en/library/procedure/
	 *
	 * @return $this
	 */
	public function procedure(string $name, ...$arguments)
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
			$arguments[] = $this->manipulation->database->quote($argument);
		}
		$arguments = \implode(', ', $arguments);
		return " PROCEDURE {$this->sql['procedure']['name']}({$arguments})";
	}

	/**
	 * Exports the result to an external file.
	 *
	 * @param string      $filename
	 * @param string|null $charset
	 * @param array       $fields_options Each key must be one of the EXP_FIELD_* constants
	 * @param array       $lines_options  Each key must be one of the EXP_LINES_* constants
	 *
	 * @see https://mariadb.com/kb/en/library/select-into-outfile/
	 *
	 * @return $this
	 */
	public function intoOutfile(
		string $filename,
		string $charset = null,
		array $fields_options = [],
		array $lines_options = []
	) {
		$this->sql['into_outfile'] = [
			'filename' => $filename,
			'charset' => $charset,
			'fields_options' => $fields_options,
			'lines_options' => $lines_options,
		];
		return $this;
	}

	protected function renderIntoOutfile() : ?string
	{
		if ( ! isset($this->sql['into_outfile'])) {
			return null;
		}
		if (\is_file($this->sql['into_outfile']['filename'])) {
			throw new \InvalidArgumentException(
				"INTO OUTFILE filename must not exist: {$this->sql['into_outfile']['filename']}"
			);
		}
		$definition = $this->manipulation->database->quote($this->sql['into_outfile']['filename']);
		if ($this->sql['into_outfile']['charset']) {
			$definition .= ' CHARACTER SET '
				. $this->manipulation->database->quote(
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
				$fields_option = \strtoupper($option);
				if ( ! \in_array($fields_option, [
					'TERMINATED BY',
					'ENCLOSED BY',
					'OPTIONALLY ENCLOSED BY',
					'ESCAPED BY',
				], true)) {
					throw new \InvalidArgumentException(
						"Invalid INTO OUTFILE fields option: {$option}"
					);
				}
				$definition .= " {$fields_option} " . $this->manipulation->database->quote($value);
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
				$lines_option = \strtoupper($option);
				if ( ! \in_array($lines_option, [
					'STARTING BY',
					'TERMINATED BY',
				], true)) {
					throw new \InvalidArgumentException(
						"Invalid INTO OUTFILE lines option: {$option}"
					);
				}
				$definition .= " {$lines_option} " . $this->manipulation->database->quote($value);
			}
		}
		return $definition;
	}

	/**
	 * @param string $filepath
	 * @param mixed  $variables
	 *
	 *@see https://mariadb.com/kb/en/library/select-into-dumpfile/
	 *
	 * @return $this
	 */
	public function intoDumpfile(string $filepath, ...$variables)
	{
		$this->sql['into_dumpfile'] = [
			'filepath' => $filepath,
			'variables' => $variables,
		];
		return $this;
		return $this;
	}

	protected function renderIntoDumpfile() : ?string
	{
		if ( ! isset($this->sql['into_dumpfile'])) {
			return null;
		}
		if (\is_file($this->sql['into_dumpfile']['filepath'])) {
			throw new \InvalidArgumentException(
				"INTO DUMPFILE filepath must not exist: {$this->sql['into_dumpfile']['filepath']}"
			);
		}
		$definition = $this->manipulation->database->quote($this->sql['into_dumpfile']['filepath']);
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
	 * @see https://mariadb.com/kb/en/library/for-update/
	 *
	 * @return $this
	 */
	public function lockForUpdate(int $wait = null)
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
	 * @see https://mariadb.com/kb/en/library/lock-in-share-mode/
	 *
	 * @return $this
	 */
	public function lockInShareMode(int $wait = null)
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
			$wait .= " WAIT {$this->sql['lock']['wait']}";
		}
		return "{$this->sql['lock']['type']}{$wait}";
	}

	public function sql() : string
	{
		$sql = 'SELECT' . \PHP_EOL;
		if ($part = $this->renderOptions()) {
			$sql .= '-- options ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderExpressions()) {
			$sql .= '-- expressions ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderFrom()) {
			$sql .= '-- from ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderJoin()) {
			$sql .= '-- join ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderWhere()) {
			$this->checkFrom('WHERE');
			$sql .= '-- where ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderHaving()) {
			$this->checkFrom('HAVING');
			$sql .= '-- having ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderOrderBy()) {
			$this->checkFrom('ORDER BY');
			$sql .= '-- order by ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderLimit()) {
			$this->checkFrom('LIMIT');
			$sql .= '-- limit ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderProcedure()) {
			$this->checkFrom('PROCEDURE');
			$sql .= '-- procedure ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderIntoOutfile()) {
			$this->checkFrom('INTO OUTFILE');
			$sql .= '-- into outfile ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderIntoDumpfile()) {
			$into_dump = true;
			$sql .= '-- into dumpfile ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderLock()) {
			if (empty($into_dump)) {
				$this->checkFrom($this->sql['lock']['type']);
			}
			$sql .= '-- lock ' . \PHP_EOL;
			$sql .= $part . \PHP_EOL;
		}
		return $sql;
	}
}
