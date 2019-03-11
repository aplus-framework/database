<?php namespace Framework\Database\Manipulation\Statements\Traits;

/**
 * Trait Join.
 *
 * @see  https://mariadb.com/kb/en/library/joins/
 *
 * @todo https://mariadb.com/kb/en/library/index-hints-how-to-force-query-plans/
 */
trait Join
{
	/**
	 * Sets the FROM clause.
	 *
	 * @param array|\Closure|string $reference  table reference
	 * @param mixed                 $references
	 *
	 * @see https://mariadb.com/kb/en/library/join-syntax/
	 *
	 * @return $this
	 */
	public function from($reference, ...$references)
	{
		$this->sql['from'] = [];
		$references = $this->mergeExpressions($reference, $references);
		foreach ($references as $reference) {
			$this->sql['from'][] = $reference;
		}
		return $this;
	}

	protected function renderFrom() : ?string
	{
		if ( ! isset($this->sql['from'])) {
			return null;
		}
		$tables = [];
		foreach ($this->sql['from'] as $table) {
			$tables[] = $this->renderAliasedIdentifier($table);
		}
		// TODO: throw if empty
		return ' FROM ' . \implode(', ', $tables);
	}

	protected function hasFrom(string $clause = null) : bool
	{
		if ( ! isset($this->sql['from'])) {
			if ($clause === null) {
				return false;
			}
			throw new \RuntimeException("Clause {$clause} only works with FROM");
		}
		return true;
	}

	/**
	 * Sets the JOIN clause as "$type JOIN $table $clause $conditional".
	 *
	 * @param \Closure|string $table       Table factor
	 * @param string          $type        JOIN type. One of: CROSS, INNER, LEFT, LEFT OUTER,
	 *                                     RIGHT, RIGHT OUTER, NATURAL, NATURAL LEFT, NATURAL LEFT
	 *                                     OUTER, NATURAL RIGHT, NATURAL RIGHT OUTE or empty (same
	 *                                     as INNER)
	 * @param string|null     $clause      Condition clause. NULL if has a NATURAL type otherwise
	 *                                     ON or USING
	 * @param array|\Closure  $conditional A conditional expression as \Closure or the columns list
	 *                                     as array
	 *
	 * @return $this
	 */
	public function join($table, string $type = '', string $clause = null, $conditional = null)
	{
		return $this->setJoin($table, $type, $clause, $conditional);
	}

	/**
	 * Sets the JOIN clause as "JOIN $table ON $conditional".
	 *
	 * @param \Closure|string $table       Table factor
	 * @param \Closure        $conditional Conditional expression
	 *
	 * @return $this
	 */
	public function joinOn($table, \Closure $conditional)
	{
		return $this->setJoin($table, '', 'ON', $conditional);
	}

	/**
	 * Sets the JOIN clause as "JOIN $table USING ...$columns".
	 *
	 * @param \Closure|string $table   Table factor
	 * @param mixed           $columns Columns list
	 *
	 * @return $this
	 */
	public function joinUsing($table, ...$columns)
	{
		return $this->setJoin($table, '', 'USING', $columns);
	}

	/**
	 * Sets the JOIN clause as "INNER JOIN $table ON $conditional".
	 *
	 * @param \Closure|string $table       Table factor
	 * @param \Closure        $conditional Conditional expression
	 *
	 * @return $this
	 */
	public function innerJoinOn($table, \Closure $conditional)
	{
		return $this->setJoin($table, 'INNER', 'ON', $conditional);
	}

	/**
	 * Sets the JOIN clause as "INNER JOIN $table USING ...$columns".
	 *
	 * @param \Closure|string $table   Table factor
	 * @param mixed           $columns Columns list
	 *
	 * @return $this
	 */
	public function innerJoinUsing($table, ...$columns)
	{
		return $this->setJoin($table, 'INNER', 'USING', $columns);
	}

	/**
	 * Sets the JOIN clause as "CROSS JOIN $table ON $conditional".
	 *
	 * @param \Closure|string $table       Table factor
	 * @param \Closure        $conditional Conditional expression
	 *
	 * @return $this
	 */
	public function crossJoinOn($table, \Closure $conditional)
	{
		return $this->setJoin($table, 'CROSS', 'ON', $conditional);
	}

	/**
	 * Sets the JOIN clause as "CROSS JOIN $table USING ...$columns".
	 *
	 * @param \Closure|string $table   Table factor
	 * @param mixed           $columns Columns list
	 *
	 * @return $this
	 */
	public function crossJoinUsing($table, ...$columns)
	{
		return $this->setJoin($table, 'CROSS', 'USING', $columns);
	}

	/**
	 * Sets the JOIN clause as "LEFT JOIN $table ON $conditional".
	 *
	 * @param \Closure|string $table       Table factor
	 * @param \Closure        $conditional Conditional expression
	 *
	 * @return $this
	 */
	public function leftJoinOn($table, \Closure $conditional)
	{
		return $this->setJoin($table, 'LEFT', 'ON', $conditional);
	}

	/**
	 * Sets the JOIN clause as "LEFT JOIN $table USING ...$columns".
	 *
	 * @param \Closure|string $table   Table factor
	 * @param mixed           $columns Columns list
	 *
	 * @return $this
	 */
	public function leftJoinUsing($table, ...$columns)
	{
		return $this->setJoin($table, 'LEFT', 'USING', $columns);
	}

	/**
	 * Sets the JOIN clause as "LEFT OUTER JOIN $table ON $conditional".
	 *
	 * @param \Closure|string $table       Table factor
	 * @param \Closure        $conditional Conditional expression
	 *
	 * @return $this
	 */
	public function leftOuterJoinOn($table, \Closure $conditional)
	{
		return $this->setJoin($table, 'LEFT OUTER', 'ON', $conditional);
	}

	/**
	 * Sets the JOIN clause as "LEFT OUTER JOIN $table USING ...$columns".
	 *
	 * @param \Closure|string $table   Table factor
	 * @param mixed           $columns Columns list
	 *
	 * @return $this
	 */
	public function leftOuterJoinUsing($table, ...$columns)
	{
		return $this->setJoin($table, 'LEFT OUTER', 'USING', $columns);
	}

	/**
	 * Sets the JOIN clause as "RIGHT JOIN $table ON $conditional".
	 *
	 * @param \Closure|string $table       Table factor
	 * @param \Closure        $conditional Conditional expression
	 *
	 * @return $this
	 */
	public function rightJoinOn($table, \Closure $conditional)
	{
		return $this->setJoin($table, 'RIGHT', 'ON', $conditional);
	}

	/**
	 * Sets the JOIN clause as "RIGHT JOIN $table USING ...$columns".
	 *
	 * @param \Closure|string $table   Table factor
	 * @param mixed           $columns Columns list
	 *
	 * @return $this
	 */
	public function rightJoinUsing($table, ...$columns)
	{
		return $this->setJoin($table, 'RIGHT', 'USING', $columns);
	}

	/**
	 * Sets the JOIN clause as "RIGHT OUTER JOIN $table ON $conditional".
	 *
	 * @param \Closure|string $table       Table factor
	 * @param \Closure        $conditional Conditional expression
	 *
	 * @return $this
	 */
	public function rightOuterJoinOn($table, \Closure $conditional)
	{
		return $this->setJoin($table, 'RIGHT OUTER', 'ON', $conditional);
	}

	/**
	 * Sets the JOIN clause as "RIGHT OUTER JOIN $table USING ...$columns".
	 *
	 * @param \Closure|string $table   Table factor
	 * @param mixed           $columns Columns list
	 *
	 * @return $this
	 */
	public function rightOuterJoinUsing($table, ...$columns)
	{
		return $this->setJoin($table, 'RIGHT OUTER', 'USING', $columns);
	}

	/**
	 * Sets the JOIN clause as "NATURAL JOIN $table".
	 *
	 * @param \Closure|string $table Table factor
	 *
	 * @return $this
	 */
	public function naturalJoin($table)
	{
		return $this->setJoin($table, 'NATURAL');
	}

	/**
	 * Sets the JOIN clause as "NATURAL LEFT JOIN $table".
	 *
	 * @param \Closure|string $table Table factor
	 *
	 * @return $this
	 */
	public function naturalLeftJoin($table)
	{
		return $this->setJoin($table, 'NATURAL LEFT');
	}

	/**
	 * Sets the JOIN clause as "NATURAL LEFT OUTER JOIN $table".
	 *
	 * @param \Closure|string $table Table factor
	 *
	 * @return $this
	 */
	public function naturalLeftOuterJoin($table)
	{
		return $this->setJoin($table, 'NATURAL LEFT OUTER');
	}

	/**
	 * Sets the JOIN clause as "NATURAL RIGHT JOIN $table".
	 *
	 * @param \Closure|string $table Table factor
	 *
	 * @return $this
	 */
	public function naturalRightJoin($table)
	{
		return $this->setJoin($table, 'NATURAL RIGHT');
	}

	/**
	 * Sets the JOIN clause as "NATURAL RIGHT OUTER JOIN $table".
	 *
	 * @param \Closure|string $table Table factor
	 *
	 * @return $this
	 */
	public function naturalRightOuterJoin($table)
	{
		return $this->setJoin($table, 'NATURAL RIGHT OUTER');
	}

	private function setJoin(
		$table,
		string $type,
		string $clause = null,
		$expression = null
	) {
		$this->sql['join'] = [
			'type' => $type,
			'table' => $table,
			'clause' => $clause,
			'expression' => $expression,
		];
		return $this;
	}

	protected function renderJoin() : ?string
	{
		if ( ! isset($this->sql['join'])) {
			return null;
		}
		$type = $this->renderJoinType($this->sql['join']['type']);
		$conditional = $this->renderJoinConditional(
			$type,
			$this->sql['join']['table'],
			$this->sql['join']['clause'],
			$this->sql['join']['expression']
		);
		if ($type) {
			$type .= ' ';
		}
		return " {$type}JOIN {$conditional}";
	}

	private function renderJoinConditional(
		string $type,
		string $table,
		$clause,
		$expression
	) : string {
		$table = $this->renderAliasedIdentifier($table);
		$is_natural = $this->checkNaturalJoinType($type, $clause, $expression);
		if ($is_natural) {
			return $table;
		}
		$conditional = '';
		$clause = $this->renderJoinConditionClause($clause);
		if ($clause) {
			$conditional .= ' ' . $clause;
		}
		$expression = $this->renderJoinConditionExpression($clause, $expression);
		if ($expression) {
			$conditional .= ' ' . $expression;
		}
		return $table . $conditional;
	}

	private function renderJoinType(string $type) : string
	{
		$result = \strtoupper($type);
		if (\in_array($result, [
			'',
			'CROSS',
			'INNER',
			'LEFT',
			'LEFT OUTER',
			'RIGHT',
			'RIGHT OUTER',
			'NATURAL',
			'NATURAL LEFT',
			'NATURAL LEFT OUTER',
			'NATURAL RIGHT',
			'NATURAL RIGHT OUTER',
		], true)) {
			return $result;
		}
		throw new \InvalidArgumentException("Invalid JOIN type: {$type}");
	}

	private function checkNaturalJoinType(string $type, ?string $clause, $expression) : bool
	{
		if (\in_array($type, [
			'NATURAL',
			'NATURAL LEFT',
			'NATURAL LEFT OUTER',
			'NATURAL RIGHT',
			'NATURAL RIGHT OUTER',
		], true)) {
			if ($clause !== null || $expression !== null) {
				throw new \InvalidArgumentException(
					"{$type} JOIN has not condition"
				);
			}
			return true;
		}
		return false;
	}

	private function renderJoinConditionClause(?string $clause) : ?string
	{
		if ($clause === null) {
			return null;
		}
		$result = \strtoupper($clause);
		if (\in_array($result, [
			'ON',
			'USING',
		], true)) {
			return $result;
		}
		throw new \InvalidArgumentException("Invalid JOIN condition clause: {$clause}");
	}

	private function renderJoinConditionExpression(?string $clause, $expression) : ?string
	{
		if ($clause === null) {
			return null;
		}
		if ($clause === 'ON') {
			return $this->subquery($expression);
		}
		foreach ($expression as &$column) {
			$column = $this->renderIdentifier($column);
		}
		return '(' . \implode(', ', $expression) . ')';
	}
}
