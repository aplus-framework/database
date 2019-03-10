<?php namespace Framework\Database\Manipulation\Statements\Traits;

trait Set
{
	public function set(array $columns)
	{
		$this->sql['set'] = $columns;
		return $this;
	}

	protected function renderSet() : ?string
	{
		if ( ! isset($this->sql['set'])) {
			return null;
		}
		$set = [];
		foreach ($this->sql['set'] as $column => $value) {
			$set[] = $this->renderAssignment($column, $value);
		}
		$set = \implode(', ', $set);
		return " SET {$set}";
	}
}
