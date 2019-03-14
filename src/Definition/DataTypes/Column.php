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
	 * @var bool|null
	 */
	protected $null;
	protected $uniqueKey = false;
	protected $primaryKey = false;
	/**
	 * @var string|null
	 */
	protected $default;
	/**
	 * @var string|null
	 */
	protected $comment;

	public function __construct(string $name, Database $database)
	{
		$this->name = $name;
		$this->database = $database;
	}

	public function __call($name, $arguments)
	{
		if ($name === 'sql') {
			return $this->sql();
		}
		throw new \BadMethodCallException("Method not found: {$name}");
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

	public function default(string $default)
	{
		$this->default = $default;
		return $this;
	}

	protected function renderDefault() : ?string
	{
		if ( ! isset($this->default)) {
			return null;
		}
		return ' DEFAULT ' . $this->database->quote($this->default);
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

	abstract protected function sql() : string;
}
