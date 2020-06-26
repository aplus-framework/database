<?php namespace Framework\Database\Definition\Table;

use BadMethodCallException;

abstract class DefinitionPart
{
	public function __call($method, $arguments)
	{
		if ($method === 'sql') {
			return $this->sql(...$arguments);
		}
		throw new BadMethodCallException("Method not found: {$method}");
	}

	abstract protected function sql() : string;
}
