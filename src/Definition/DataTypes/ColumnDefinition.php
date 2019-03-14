<?php namespace Framework\Database\Definition\DataTypes;

use Framework\Database\Database;
use Framework\Database\Definition\DataTypes\Numerics\BigintColumn;
use Framework\Database\Definition\DataTypes\Numerics\DecimalColumn;
use Framework\Database\Definition\DataTypes\Numerics\IntColumn;
use Framework\Database\Definition\DataTypes\Numerics\TinyintColumn;
use Framework\Database\Definition\DataTypes\Strings\CharColumn;
use Framework\Database\Definition\DataTypes\Strings\EnumColumn;
use Framework\Database\Definition\DataTypes\Strings\VarcharColumn;
use Framework\Database\Definition\DataTypes\Texts\JsonColumn;
use Framework\Database\Definition\DataTypes\Texts\LongtextColumn;
use Framework\Database\Definition\DataTypes\Texts\MediumtextColumn;
use Framework\Database\Definition\DataTypes\Texts\TextColumn;
use Framework\Database\Definition\DataTypes\Texts\TinytextColumn;

/**
 * Class ColumnDefinition.
 *
 * @see https://mariadb.com/kb/en/library/create-table/#column-and-index-definitions
 */
class ColumnDefinition
{
	/**
	 * @var Database
	 */
	protected $database;
	/**
	 * @var array|Column[]
	 */
	protected $columns = [];

	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	public function __call($name, $arguments)
	{
		if ($name === 'sql') {
			return $this->sql();
		}
		throw new \BadMethodCallException("Method not found: {$name}");
	}

	public function int(string $name, int $length = null) : IntColumn
	{
		return $this->columns[] = new IntColumn($name, $this->database);
	}

	public function bigint(string $name, int $length = null) : BigintColumn
	{
		return $this->columns[] = new BigintColumn($name, $this->database);
	}

	public function tinyint(string $name, int $length = null) : TinyintColumn
	{
		return $this->columns[] = new TinyintColumn($name, $this->database);
	}

	public function decimal(string $name, int $length = null) : DecimalColumn
	{
		return $this->columns[] = new DecimalColumn($name, $this->database);
	}

	public function varchar(string $name, int $length = null) : VarcharColumn
	{
		return $this->columns[] = new VarcharColumn($name, $this->database);
	}

	public function char(string $name, int $length = null) : CharColumn
	{
		return $this->columns[] = new CharColumn($name, $this->database);
	}

	public function enum(string $name, string $option, string ...$options) : EnumColumn
	{
		return $this->columns[] = new EnumColumn($name, $this->database);
	}

	public function text(string $name) : TextColumn
	{
		return $this->columns[] = new TextColumn($name, $this->database);
	}

	public function longtext(string $name) : LongtextColumn
	{
		return $this->columns[] = new LongtextColumn($name, $this->database);
	}

	public function mediumtext(string $name) : MediumtextColumn
	{
		return $this->columns[] = new MediumtextColumn($name, $this->database);
	}

	public function tinytext(string $name) : TinytextColumn
	{
		return $this->columns[] = new TinytextColumn($name, $this->database);
	}

	public function json(string $name) : JsonColumn
	{
		return $this->columns[] = new JsonColumn($name, $this->database);
	}

	protected function sql() : string
	{
		$sql = [];
		foreach ($this->columns as $column) {
			$sql[] = $column->sql();
		}
		return \implode(',' . \PHP_EOL, $sql) . \PHP_EOL;
	}
}
