<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns;

use Framework\Database\Database;
use Framework\Database\Definition\Table\Columns\DateTime\DateColumn;
use Framework\Database\Definition\Table\Columns\DateTime\DatetimeColumn;
use Framework\Database\Definition\Table\Columns\DateTime\TimeColumn;
use Framework\Database\Definition\Table\Columns\DateTime\TimestampColumn;
use Framework\Database\Definition\Table\Columns\DateTime\YearColumn;
use Framework\Database\Definition\Table\Columns\Geometry\GeometryCollectionColumn;
use Framework\Database\Definition\Table\Columns\Geometry\GeometryColumn;
use Framework\Database\Definition\Table\Columns\Geometry\LinestringColumn;
use Framework\Database\Definition\Table\Columns\Geometry\MultilinestringColumn;
use Framework\Database\Definition\Table\Columns\Geometry\MultipointColumn;
use Framework\Database\Definition\Table\Columns\Geometry\MultipolygonColumn;
use Framework\Database\Definition\Table\Columns\Geometry\PointColumn;
use Framework\Database\Definition\Table\Columns\Geometry\PolygonColumn;
use Framework\Database\Definition\Table\Columns\Numeric\BigintColumn;
use Framework\Database\Definition\Table\Columns\Numeric\BitColumn;
use Framework\Database\Definition\Table\Columns\Numeric\BooleanColumn;
use Framework\Database\Definition\Table\Columns\Numeric\DecimalColumn;
use Framework\Database\Definition\Table\Columns\Numeric\FloatColumn;
use Framework\Database\Definition\Table\Columns\Numeric\IntColumn;
use Framework\Database\Definition\Table\Columns\Numeric\MediumintColumn;
use Framework\Database\Definition\Table\Columns\Numeric\SmallintColumn;
use Framework\Database\Definition\Table\Columns\Numeric\TinyintColumn;
use Framework\Database\Definition\Table\Columns\String\BinaryColumn;
use Framework\Database\Definition\Table\Columns\String\BlobColumn;
use Framework\Database\Definition\Table\Columns\String\CharColumn;
use Framework\Database\Definition\Table\Columns\String\EnumColumn;
use Framework\Database\Definition\Table\Columns\String\JsonColumn;
use Framework\Database\Definition\Table\Columns\String\LongblobColumn;
use Framework\Database\Definition\Table\Columns\String\LongtextColumn;
use Framework\Database\Definition\Table\Columns\String\MediumblobColumn;
use Framework\Database\Definition\Table\Columns\String\MediumtextColumn;
use Framework\Database\Definition\Table\Columns\String\SetColumn;
use Framework\Database\Definition\Table\Columns\String\TextColumn;
use Framework\Database\Definition\Table\Columns\String\TinyblobColumn;
use Framework\Database\Definition\Table\Columns\String\TinytextColumn;
use Framework\Database\Definition\Table\Columns\String\VarbinaryColumn;
use Framework\Database\Definition\Table\Columns\String\VarcharColumn;
use Framework\Database\Definition\Table\DefinitionPart;

/**
 * Class ColumnDefinition.
 *
 * @see https://mariadb.com/kb/en/create-table/#index-definitions
 *
 * @package database
 */
class ColumnDefinition extends DefinitionPart
{
    protected Database $database;
    protected Column $column;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function int(int $maximum = null) : IntColumn
    {
        return $this->column = new IntColumn($this->database, $maximum);
    }

    public function bigint(int $maximum = null) : BigintColumn
    {
        return $this->column = new BigintColumn($this->database, $maximum);
    }

    public function tinyint(int $maximum = null) : TinyintColumn
    {
        return $this->column = new TinyintColumn($this->database, $maximum);
    }

    public function decimal(int $maximum = null, int $decimals = null) : DecimalColumn
    {
        return $this->column = new DecimalColumn($this->database, $maximum, $decimals);
    }

    public function float(int $maximum = null, int $decimals = null) : FloatColumn
    {
        return $this->column = new FloatColumn($this->database, $maximum, $decimals);
    }

    public function mediumint(int $maximum = null) : MediumintColumn
    {
        return $this->column = new MediumintColumn($this->database, $maximum);
    }

    public function smallint(int $maximum = null) : SmallintColumn
    {
        return $this->column = new SmallintColumn($this->database, $maximum);
    }

    public function boolean(int $maximum = null) : BooleanColumn
    {
        return $this->column = new BooleanColumn($this->database, $maximum);
    }

    public function varchar(int $maximum = null) : VarcharColumn
    {
        return $this->column = new VarcharColumn($this->database, $maximum);
    }

    public function char(int $maximum = null) : CharColumn
    {
        return $this->column = new CharColumn($this->database, $maximum);
    }

    public function enum(string $value, string ...$values) : EnumColumn
    {
        return $this->column = new EnumColumn($this->database, $value, ...$values);
    }

    public function set(string $value, string ...$values) : SetColumn
    {
        return $this->column = new SetColumn($this->database, $value, ...$values);
    }

    public function text(int $maximum = null) : TextColumn
    {
        return $this->column = new TextColumn($this->database, $maximum);
    }

    public function longtext() : LongtextColumn
    {
        return $this->column = new LongtextColumn($this->database);
    }

    public function mediumtext() : MediumtextColumn
    {
        return $this->column = new MediumtextColumn($this->database);
    }

    public function tinytext() : TinytextColumn
    {
        return $this->column = new TinytextColumn($this->database);
    }

    public function json() : JsonColumn
    {
        return $this->column = new JsonColumn($this->database);
    }

    public function blob() : BlobColumn
    {
        return $this->column = new BlobColumn($this->database);
    }

    public function tinyblob() : TinyblobColumn
    {
        return $this->column = new TinyblobColumn($this->database);
    }

    public function mediumblob() : MediumblobColumn
    {
        return $this->column = new MediumblobColumn($this->database);
    }

    public function longblob() : LongblobColumn
    {
        return $this->column = new LongblobColumn($this->database);
    }

    public function bit() : BitColumn
    {
        return $this->column = new BitColumn($this->database);
    }

    public function binary() : BinaryColumn
    {
        return $this->column = new BinaryColumn($this->database);
    }

    public function varbinary() : VarbinaryColumn
    {
        return $this->column = new VarbinaryColumn($this->database);
    }

    public function date() : DateColumn
    {
        return $this->column = new DateColumn($this->database);
    }

    public function time() : TimeColumn
    {
        return $this->column = new TimeColumn($this->database);
    }

    public function datetime() : DatetimeColumn
    {
        return $this->column = new DatetimeColumn($this->database);
    }

    public function timestamp() : TimestampColumn
    {
        return $this->column = new TimestampColumn($this->database);
    }

    public function year() : YearColumn
    {
        return $this->column = new YearColumn($this->database);
    }

    public function geometrycollection() : GeometryCollectionColumn
    {
        return $this->column = new GeometryCollectionColumn($this->database);
    }

    public function geometry() : GeometryColumn
    {
        return $this->column = new GeometryColumn($this->database);
    }

    public function linestring() : LinestringColumn
    {
        return $this->column = new LinestringColumn($this->database);
    }

    public function multilinestring() : MultilinestringColumn
    {
        return $this->column = new MultilinestringColumn($this->database);
    }

    public function multipoint() : MultipointColumn
    {
        return $this->column = new MultipointColumn($this->database);
    }

    public function multipolygon() : MultipolygonColumn
    {
        return $this->column = new MultipolygonColumn($this->database);
    }

    public function point() : PointColumn
    {
        return $this->column = new PointColumn($this->database);
    }

    public function polygon() : PolygonColumn
    {
        return $this->column = new PolygonColumn($this->database);
    }

    protected function sql() : string
    {
        return $this->column->sql();
    }
}
