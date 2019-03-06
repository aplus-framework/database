<?php namespace Framework\Database;

class Database
{
	protected $pdo;

	public function __construct(\PDO $pdo = null)
	{
		$this->pdo = $pdo;
	}

	public function protectIdentifier(string $identifier) : string
	{
		$identifier = \strtr($identifier, ['`' => '``', '.' => '`.`']);
		$identifier = '`' . $identifier . '`';
		return \strtr($identifier, ['`*`' => '*']);
	}

	public function quote($value)
	{
		if (\is_string($value)) {
			if ($this->pdo) {
				return $this->pdo->quote($value);
			}
			$value = \addslashes($value);
			return "'{$value}'";
		}
		if (\is_int($value) || \is_float($value)) {
			return $value;
		}
		if ($value === null) {
			return 'NULL';
		}
		throw new \InvalidArgumentException('Invalid value type: ' . \gettype($value));
	}
}
