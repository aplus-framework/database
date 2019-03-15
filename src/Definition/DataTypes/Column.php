<?php namespace Framework\Database\Definition\DataTypes;

use Framework\Database\Database;

abstract class Column
{
	/**
	 * @var Database
	 */
	protected $database;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var array|int|null
	 */
	protected $length;
	/**
	 * @var bool|null
	 */
	protected $null;
	/**
	 * @var bool|null
	 */
	protected $uniqueKey;
	/**
	 * @var bool|null
	 */
	protected $primaryKey;
	/**
	 * @var string|null
	 */
	protected $default;
	/**
	 * @var string|null
	 */
	protected $comment;

	/**
	 * Column constructor.
	 *
	 * @param string   $name     Column name
	 * @param Database $database
	 */
	public function __construct(string $name, Database $database)
	{
		$this->name = $name;
		$this->database = $database;
	}

	public function __toString()
	{
		return $this->sql();
	}

	protected function renderName() : string
	{
		if ( ! isset($this->name)) {
			throw new \LogicException('Column name is empty');
		}
		return ' ' . $this->database->protectIdentifier($this->name);
	}

	protected function renderType() : string
	{
		if ( ! isset($this->type)) {
			throw new \LogicException('Column type is empty');
		}
		return ' ' . $this->type;
	}

	protected function renderLength() : ?string
	{
		if ( ! isset($this->length)) {
			return null;
		}
		$length = $this->database->quote($this->length);
		return "({$length})";
	}

	public function null()
	{
		$this->null = true;
		return $this;
	}

	public function notNull()
	{
		$this->null = false;
		return $this;
	}

	protected function renderNull() : ?string
	{
		if ( ! isset($this->null)) {
			return null;
		}
		return $this->null ? ' NULL' : ' NOT NULL';
	}

	/**
	 * @param bool|\Closure|float|int|string|null $default
	 *
	 * @return $this
	 */
	public function default($default)
	{
		$this->default = $default;
		return $this;
	}

	protected function renderDefault() : ?string
	{
		if ( ! isset($this->default)) {
			return null;
		}
		$default = $this->default instanceof \Closure
			? '(' . ($this->default)($this->database) . ')'
			: $this->database->quote($this->default);
		return ' DEFAULT ' . $default;
	}

	public function comment(string $comment)
	{
		$this->comment = $comment;
		return $this;
	}

	protected function renderComment() : ?string
	{
		if ( ! isset($this->comment)) {
			return null;
		}
		return ' COMMENT ' . $this->database->quote($this->comment);
	}

	public function primaryKey()
	{
		$this->primaryKey = true;
		return $this;
	}

	protected function renderPrimaryKey() : ?string
	{
		if ( ! $this->primaryKey) {
			return null;
		}
		return ' PRIMARY KEY';
	}

	public function uniqueKey()
	{
		$this->uniqueKey = true;
		return $this;
	}

	protected function renderUniqueKey() : ?string
	{
		if ( ! $this->uniqueKey) {
			return null;
		}
		return ' UNIQUE KEY';
	}

	protected function renderTypeAttributes() : ?string
	{
		return null;
	}

	protected function sql() : string
	{
		$sql = $this->renderName();
		$sql .= $this->renderType();
		$sql .= $this->renderLength();
		$sql .= $this->renderTypeAttributes();
		$sql .= $this->renderNull();
		$sql .= $this->renderDefault();
		$sql .= $this->renderComment();
		$sql .= $this->renderUniqueKey();
		$sql .= $this->renderPrimaryKey();
		return $sql;
	}
}
