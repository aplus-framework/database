<?php namespace Framework\Database\Definition\Table\Columns;

use Closure;
use Framework\Database\Database;
use Framework\Database\Definition\Table\DefinitionPart;
use LogicException;

abstract class Column extends DefinitionPart
{
	protected Database $database;
	protected string $type;
	protected array $length;
	protected bool $null = false;
	protected bool $uniqueKey = false;
	protected bool $primaryKey = false;
	/**
	 * @see default
	 *
	 * @var mixed
	 */
	protected $default;
	protected ?string $comment;
	protected bool $first = false;
	protected ?string $after;

	/**
	 * Column constructor.
	 *
	 * @param Database $database
	 * @param mixed    $length
	 */
	public function __construct(Database $database, ...$length)
	{
		$this->database = $database;
		$this->length = $length;
	}

	protected function renderType() : string
	{
		if (empty($this->type)) {
			throw new LogicException('Column type is empty');
		}
		return ' ' . $this->type;
	}

	protected function renderLength() : ?string
	{
		if ( ! isset($this->length[0])) {
			return null;
		}
		$length = $this->database->quote($this->length[0]);
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
		return $this->null ? ' NULL' : ' NOT NULL';
	}

	/**
	 * @param bool|Closure|float|int|string|null $default
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
		$default = $this->default instanceof Closure
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

	public function first()
	{
		$this->first = true;
		return $this;
	}

	protected function renderFirst() : ?string
	{
		if ( ! $this->first) {
			return null;
		}
		return ' FIRST';
	}

	public function after(string $column)
	{
		$this->after = $column;
		return $this;
	}

	protected function renderAfter() : ?string
	{
		if ( ! isset($this->after)) {
			return null;
		}
		if ($this->first) {
			throw new LogicException('Clauses FIRST and AFTER can not be used together');
		}
		return ' AFTER ' . $this->database->protectIdentifier($this->after);
	}

	protected function renderTypeAttributes() : ?string
	{
		return null;
	}

	protected function sql() : string
	{
		$sql = $this->renderType();
		$sql .= $this->renderLength();
		$sql .= $this->renderTypeAttributes();
		$sql .= $this->renderNull();
		$sql .= $this->renderDefault();
		$sql .= $this->renderUniqueKey();
		$sql .= $this->renderPrimaryKey();
		$sql .= $this->renderComment();
		$sql .= $this->renderFirst();
		$sql .= $this->renderAfter();
		return $sql;
	}
}
