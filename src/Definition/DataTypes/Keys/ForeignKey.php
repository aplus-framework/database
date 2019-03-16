<?php namespace Framework\Database\Definition\DataTypes\Keys;

use Framework\Database\Definition\DataTypes\Key;

class ForeignKey extends Key
{
	use Traits\Constraint;
	protected $type = 'FOREIGN KEY';
	/**
	 * @var string
	 */
	protected $referenceTable;
	/**
	 * @var array
	 */
	protected $referenceColumns;
	/**
	 * @var string|null
	 */
	protected $onDelete;
	/**
	 * @var string|null
	 */
	protected $onUpdate;

	public function references(string $table, string $column, string ...$columns)
	{
		$this->referenceTable = $table;
		$this->referenceColumns = $columns ? \array_merge([$column], $columns) : [$column];
		return $this;
	}

	protected function renderReferences() : string
	{
		if ($this->referenceTable === null) {
			throw new \LogicException('REFERENCES clause was not set');
		}
		$table = $this->database->protectIdentifier($this->referenceTable);
		$columns = [];
		foreach ($this->referenceColumns as $column) {
			$columns[] = $this->database->protectIdentifier($column);
		}
		$columns = \implode(', ', $columns);
		return " REFERENCES {$table} ({$columns})";
	}

	public function onDelete(string $reference)
	{
		$this->onDelete = $reference;
		return $this;
	}

	protected function renderOnDelete() : ?string
	{
		if ($this->onDelete === null) {
			return null;
		}
		$reference = $this->makeReference($this->onDelete);
		return " ON DELETE {$reference}";
	}

	public function onUpdate(string $reference)
	{
		$this->onUpdate = $reference;
		return $this;
	}

	protected function renderOnUpdate() : ?string
	{
		if ($this->onUpdate === null) {
			return null;
		}
		$reference = $this->makeReference($this->onUpdate);
		return " ON UPDATE {$reference}";
	}

	private function makeReference(string $reference) : string
	{
		$result = \strtoupper($reference);
		if (\in_array($result, ['RESTRICT', 'CASCADE', 'SET NULL', 'NO ACTION'], true)) {
			return $result;
		}
		throw new \InvalidArgumentException("Invalid reference: {$reference}");
	}

	protected function renderTypeAttributes() : ?string
	{
		return $this->renderReferences() . $this->renderOnDelete() . $this->renderOnUpdate();
	}
}
