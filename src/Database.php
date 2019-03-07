<?php namespace Framework\Database;

/**
 * Class Database.
 */
class Database
{
	/**
	 * @var \PDO|null
	 */
	protected $pdo;

	/**
	 * Database constructor.
	 *
	 * @param \PDO|null $pdo
	 */
	public function __construct(\PDO $pdo = null)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Protect identifiers.
	 *
	 * @param string $identifier Table, column, database name or a fully-qualified identifier
	 *                           separated by dots
	 *
	 * @see https://mariadb.com/kb/en/library/identifier-qualifiers/
	 *
	 * @return string
	 */
	public function protectIdentifier(string $identifier) : string
	{
		$identifier = \strtr($identifier, ['`' => '``', '.' => '`.`']);
		$identifier = '`' . $identifier . '`';
		return \strtr($identifier, ['`*`' => '*']);
	}

	/**
	 * Quote SQL values.
	 *
	 * @param float|int|string|null $value Value to be quoted
	 *
	 * @see https://mariadb.com/kb/en/library/quote/
	 *
	 * @throws \InvalidArgumentException For invalid value type
	 *
	 * @return float|int|string If the value is null, returns a string containing the word "NULL".
	 *                          If is a string, returns the quoted string. The types int or float
	 *                          returns the same input value.
	 */
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
