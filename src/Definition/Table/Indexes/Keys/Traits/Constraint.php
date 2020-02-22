<?php namespace Framework\Database\Definition\Table\Indexes\Keys\Traits;

trait Constraint
{
	protected ?string $constraint = null;

	public function constraint(string $name)
	{
		$this->constraint = $name;
		return $this;
	}

	protected function renderConstraint() : ?string
	{
		if ($this->constraint === null) {
			return null;
		}
		$constraint = $this->database->protectIdentifier($this->constraint);
		return " CONSTRAINT {$constraint}";
	}

	protected function renderType() : string
	{
		return $this->renderConstraint() . parent::renderType();
	}
}
