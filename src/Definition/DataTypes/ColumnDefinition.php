<?php namespace Framework\Database\Definition\DataTypes;

use Framework\Database\Database;
use Framework\Database\Definition\DataTypes\Binary\BinaryColumn;
use Framework\Database\Definition\DataTypes\Binary\BitColumn;
use Framework\Database\Definition\DataTypes\Binary\BlobColumn;
use Framework\Database\Definition\DataTypes\Binary\LongblobColumn;
use Framework\Database\Definition\DataTypes\Binary\MediumblobColumn;
use Framework\Database\Definition\DataTypes\Binary\TinyblobColumn;
use Framework\Database\Definition\DataTypes\Binary\VarbinaryColumn;
use Framework\Database\Definition\DataTypes\Dates\DateColumn;
use Framework\Database\Definition\DataTypes\Dates\DatetimeColumn;
use Framework\Database\Definition\DataTypes\Dates\TimeColumn;
use Framework\Database\Definition\DataTypes\Dates\TimestampColumn;
use Framework\Database\Definition\DataTypes\Dates\YearColumn;
use Framework\Database\Definition\DataTypes\Lists\EnumColumn;
use Framework\Database\Definition\DataTypes\Lists\SetColumn;
use Framework\Database\Definition\DataTypes\Numerics\BigintColumn;
use Framework\Database\Definition\DataTypes\Numerics\BooleanColumn;
use Framework\Database\Definition\DataTypes\Numerics\DecimalColumn;
use Framework\Database\Definition\DataTypes\Numerics\FloatColumn;
use Framework\Database\Definition\DataTypes\Numerics\IntColumn;
use Framework\Database\Definition\DataTypes\Numerics\MediumintColumn;
use Framework\Database\Definition\DataTypes\Numerics\SmallintColumn;
use Framework\Database\Definition\DataTypes\Numerics\TinyintColumn;
use Framework\Database\Definition\DataTypes\Strings\CharColumn;
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

	public function __toString()
	{
		return $this->sql();
	}

	protected function setLength($column, ?int $length)
	{
		if ($length !== null) {
			$column->length($length);
		}
		return $column;
	}

	public function int(string $name, int $length = null) : IntColumn
	{
		return $this->columns[] = $this->setLength(
			new IntColumn($name, $this->database),
			$length
		);
	}

	public function bigint(string $name, int $length = null) : BigintColumn
	{
		return $this->columns[] = $this->setLength(
			new BigintColumn($name, $this->database),
			$length
		);
	}

	public function tinyint(string $name, int $length = null) : TinyintColumn
	{
		return $this->columns[] = $this->setLength(
			new TinyintColumn($name, $this->database),
			$length
		);
	}

	public function decimal(string $name, int $length = null) : DecimalColumn
	{
		return $this->columns[] = new DecimalColumn($name, $this->database);
	}

	public function float(string $name, int $length = null) : FloatColumn
	{
		return $this->columns[] = new FloatColumn($name, $this->database);
	}

	public function mediumint(string $name, int $length = null) : MediumintColumn
	{
		return $this->columns[] = $this->setLength(
			new MediumintColumn($name, $this->database),
			$length
		);
	}

	public function smallint(string $name, int $length = null) : SmallintColumn
	{
		return $this->columns[] = $this->setLength(
			new SmallintColumn($name, $this->database),
			$length
		);
	}

	public function boolean(string $name, int $length = null) : BooleanColumn
	{
		return $this->columns[] = new BooleanColumn($name, $this->database);
	}

	public function varchar(string $name, int $length = null) : VarcharColumn
	{
		return $this->columns[] = $this->setLength(
			new VarcharColumn($name, $this->database),
			$length
		);
	}

	public function char(string $name, int $length = null) : CharColumn
	{
		return $this->columns[] = $this->setLength(
			new CharColumn($name, $this->database),
			$length
		);
	}

	public function enum(string $name, string $option = null, string ...$options) : EnumColumn
	{
		return $this->columns[] = new EnumColumn($name, $this->database);
	}

	public function set(string $name, string $option = null, string ...$options) : SetColumn
	{
		return $this->columns[] = new SetColumn($name, $this->database);
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

	public function blob(string $name) : BlobColumn
	{
		return $this->columns[] = new BlobColumn($name, $this->database);
	}

	public function tinyblob(string $name) : TinyblobColumn
	{
		return $this->columns[] = new TinyblobColumn($name, $this->database);
	}

	public function mediumblob(string $name) : MediumblobColumn
	{
		return $this->columns[] = new MediumblobColumn($name, $this->database);
	}

	public function longblob(string $name) : LongblobColumn
	{
		return $this->columns[] = new LongblobColumn($name, $this->database);
	}

	public function bit(string $name) : BitColumn
	{
		return $this->columns[] = new BitColumn($name, $this->database);
	}

	public function binary(string $name) : BinaryColumn
	{
		return $this->columns[] = new BinaryColumn($name, $this->database);
	}

	public function varbinary(string $name) : VarbinaryColumn
	{
		return $this->columns[] = new VarbinaryColumn($name, $this->database);
	}

	public function date(string $name) : DateColumn
	{
		return $this->columns[] = new DateColumn($name, $this->database);
	}

	public function time(string $name) : TimeColumn
	{
		return $this->columns[] = new TimeColumn($name, $this->database);
	}

	public function datetime(string $name) : DatetimeColumn
	{
		return $this->columns[] = new DatetimeColumn($name, $this->database);
	}

	public function timestamp(string $name) : TimestampColumn
	{
		return $this->columns[] = new TimestampColumn($name, $this->database);
	}

	public function year(string $name) : YearColumn
	{
		return $this->columns[] = new YearColumn($name, $this->database);
	}

	protected function sql() : string
	{
		$sql = [];
		foreach ($this->columns as $column) {
			$sql[] = ' ' . $column;
		}
		return \implode(',' . \PHP_EOL, $sql) . \PHP_EOL;
	}
}
