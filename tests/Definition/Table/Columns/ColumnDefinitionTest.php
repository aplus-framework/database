<?php namespace Tests\Database\Definition\Table\Columns;

use Framework\Database\Definition\Table\Columns\ColumnDefinition;
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
use Tests\Database\TestCase;

class ColumnDefinitionTest extends TestCase
{
	protected ColumnDefinition $definition;

	protected function setUp() : void
	{
		$this->definition = new ColumnDefinition(static::$database);
	}

	public function testInstances() : void
	{
		$this->assertInstanceOf(BigintColumn::class, $this->definition->bigint());
		$this->assertInstanceOf(BinaryColumn::class, $this->definition->binary());
		$this->assertInstanceOf(BitColumn::class, $this->definition->bit());
		$this->assertInstanceOf(BlobColumn::class, $this->definition->blob());
		$this->assertInstanceOf(BooleanColumn::class, $this->definition->boolean());
		$this->assertInstanceOf(CharColumn::class, $this->definition->char());
		$this->assertInstanceOf(DateColumn::class, $this->definition->date());
		$this->assertInstanceOf(DatetimeColumn::class, $this->definition->datetime());
		$this->assertInstanceOf(DecimalColumn::class, $this->definition->decimal());
		$this->assertInstanceOf(EnumColumn::class, $this->definition->enum('a'));
		$this->assertInstanceOf(FloatColumn::class, $this->definition->float());
		$this->assertInstanceOf(GeometryColumn::class, $this->definition->geometry());
		$this->assertInstanceOf(
			GeometryCollectionColumn::class,
			$this->definition->geometrycollection()
		);
		$this->assertInstanceOf(IntColumn::class, $this->definition->int());
		$this->assertInstanceOf(JsonColumn::class, $this->definition->json());
		$this->assertInstanceOf(LinestringColumn::class, $this->definition->linestring());
		$this->assertInstanceOf(LongblobColumn::class, $this->definition->longblob());
		$this->assertInstanceOf(LongtextColumn::class, $this->definition->longtext());
		$this->assertInstanceOf(MediumblobColumn::class, $this->definition->mediumblob());
		$this->assertInstanceOf(MediumintColumn::class, $this->definition->mediumint());
		$this->assertInstanceOf(MediumtextColumn::class, $this->definition->mediumtext());
		$this->assertInstanceOf(MultilinestringColumn::class, $this->definition->multilinestring());
		$this->assertInstanceOf(MultipointColumn::class, $this->definition->multipoint());
		$this->assertInstanceOf(MultipolygonColumn::class, $this->definition->multipolygon());
		$this->assertInstanceOf(PointColumn::class, $this->definition->point());
		$this->assertInstanceOf(PolygonColumn::class, $this->definition->polygon());
		$this->assertInstanceOf(SetColumn::class, $this->definition->set('b'));
		$this->assertInstanceOf(SmallintColumn::class, $this->definition->smallint());
		$this->assertInstanceOf(TextColumn::class, $this->definition->text());
		$this->assertInstanceOf(TimeColumn::class, $this->definition->time());
		$this->assertInstanceOf(TimestampColumn::class, $this->definition->timestamp());
		$this->assertInstanceOf(TinyblobColumn::class, $this->definition->tinyblob());
		$this->assertInstanceOf(TinyintColumn::class, $this->definition->tinyint());
		$this->assertInstanceOf(TinytextColumn::class, $this->definition->tinytext());
		$this->assertInstanceOf(VarbinaryColumn::class, $this->definition->varbinary());
		$this->assertInstanceOf(VarcharColumn::class, $this->definition->varchar());
		$this->assertInstanceOf(YearColumn::class, $this->definition->year());
	}

	public function testTypes() : void
	{
		$this->assertSame(' bigint NOT NULL', $this->definition->bigint()->sql());
		$this->assertSame(' binary NOT NULL', $this->definition->binary()->sql());
		$this->assertSame(' bit NOT NULL', $this->definition->bit()->sql());
		$this->assertSame(' blob NOT NULL', $this->definition->blob()->sql());
		$this->assertSame(' boolean NOT NULL', $this->definition->boolean()->sql());
		$this->assertSame(' char NOT NULL', $this->definition->char()->sql());
		$this->assertSame(' date NOT NULL', $this->definition->date()->sql());
		$this->assertSame(' datetime NOT NULL', $this->definition->datetime()->sql());
		$this->assertSame(' decimal NOT NULL', $this->definition->decimal()->sql());
		$this->assertSame(" enum('a') NOT NULL", $this->definition->enum('a')->sql());
		$this->assertSame(' float NOT NULL', $this->definition->float()->sql());
		$this->assertSame(' geometry NOT NULL', $this->definition->geometry()->sql());
		$this->assertSame(
			' geometrycollection NOT NULL',
			$this->definition->geometrycollection()->sql()
		);
		$this->assertSame(' int NOT NULL', $this->definition->int()->sql());
		$this->assertSame(' json NOT NULL', $this->definition->json()->sql());
		$this->assertSame(' linestring NOT NULL', $this->definition->linestring()->sql());
		$this->assertSame(' longblob NOT NULL', $this->definition->longblob()->sql());
		$this->assertSame(' longtext NOT NULL', $this->definition->longtext()->sql());
		$this->assertSame(' mediumblob NOT NULL', $this->definition->mediumblob()->sql());
		$this->assertSame(' mediumint NOT NULL', $this->definition->mediumint()->sql());
		$this->assertSame(' mediumtext NOT NULL', $this->definition->mediumtext()->sql());
		$this->assertSame(' multilinestring NOT NULL', $this->definition->multilinestring()->sql());
		$this->assertSame(' multipoint NOT NULL', $this->definition->multipoint()->sql());
		$this->assertSame(' multipolygon NOT NULL', $this->definition->multipolygon()->sql());
		$this->assertSame(' point NOT NULL', $this->definition->point()->sql());
		$this->assertSame(' polygon NOT NULL', $this->definition->polygon()->sql());
		$this->assertSame(" set('b') NOT NULL", $this->definition->set('b')->sql());
		$this->assertSame(' smallint NOT NULL', $this->definition->smallint()->sql());
		$this->assertSame(' text NOT NULL', $this->definition->text()->sql());
		$this->assertSame(' time NOT NULL', $this->definition->time()->sql());
		$this->assertSame(' timestamp NOT NULL', $this->definition->timestamp()->sql());
		$this->assertSame(' tinyblob NOT NULL', $this->definition->tinyblob()->sql());
		$this->assertSame(' tinyint NOT NULL', $this->definition->tinyint()->sql());
		$this->assertSame(' tinytext NOT NULL', $this->definition->tinytext()->sql());
		$this->assertSame(' varbinary NOT NULL', $this->definition->varbinary()->sql());
		$this->assertSame(' varchar NOT NULL', $this->definition->varchar()->sql());
		$this->assertSame(' year NOT NULL', $this->definition->year()->sql());
	}

	public function testBadMethod() : void
	{
		$this->expectException(\BadMethodCallException::class);
		$this->expectExceptionMessage('Method not found or not allowed: foo');
		$this->definition->foo(); // @phpstan-ignore-line
	}
}
