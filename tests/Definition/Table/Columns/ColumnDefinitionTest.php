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

	public function testInstances()
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

	public function testTypes()
	{
		$this->assertEquals(' bigint NOT NULL', $this->definition->bigint()->sql());
		$this->assertEquals(' binary NOT NULL', $this->definition->binary()->sql());
		$this->assertEquals(' bit NOT NULL', $this->definition->bit()->sql());
		$this->assertEquals(' blob NOT NULL', $this->definition->blob()->sql());
		$this->assertEquals(' boolean NOT NULL', $this->definition->boolean()->sql());
		$this->assertEquals(' char NOT NULL', $this->definition->char()->sql());
		$this->assertEquals(' date NOT NULL', $this->definition->date()->sql());
		$this->assertEquals(' datetime NOT NULL', $this->definition->datetime()->sql());
		$this->assertEquals(' decimal NOT NULL', $this->definition->decimal()->sql());
		$this->assertEquals(" enum('a') NOT NULL", $this->definition->enum('a')->sql());
		$this->assertEquals(' float NOT NULL', $this->definition->float()->sql());
		$this->assertEquals(' geometry NOT NULL', $this->definition->geometry()->sql());
		$this->assertEquals(
			' geometrycollection NOT NULL',
			$this->definition->geometrycollection()->sql()
		);
		$this->assertEquals(' int NOT NULL', $this->definition->int()->sql());
		$this->assertEquals(' json NOT NULL', $this->definition->json()->sql());
		$this->assertEquals(' linestring NOT NULL', $this->definition->linestring()->sql());
		$this->assertEquals(' longblob NOT NULL', $this->definition->longblob()->sql());
		$this->assertEquals(' longtext NOT NULL', $this->definition->longtext()->sql());
		$this->assertEquals(' mediumblob NOT NULL', $this->definition->mediumblob()->sql());
		$this->assertEquals(' mediumint NOT NULL', $this->definition->mediumint()->sql());
		$this->assertEquals(' mediumtext NOT NULL', $this->definition->mediumtext()->sql());
		$this->assertEquals(' multilinestring NOT NULL', $this->definition->multilinestring()->sql());
		$this->assertEquals(' multipoint NOT NULL', $this->definition->multipoint()->sql());
		$this->assertEquals(' multipolygon NOT NULL', $this->definition->multipolygon()->sql());
		$this->assertEquals(' point NOT NULL', $this->definition->point()->sql());
		$this->assertEquals(' polygon NOT NULL', $this->definition->polygon()->sql());
		$this->assertEquals(" set('b') NOT NULL", $this->definition->set('b')->sql());
		$this->assertEquals(' smallint NOT NULL', $this->definition->smallint()->sql());
		$this->assertEquals(' text NOT NULL', $this->definition->text()->sql());
		$this->assertEquals(' time NOT NULL', $this->definition->time()->sql());
		$this->assertEquals(' timestamp NOT NULL', $this->definition->timestamp()->sql());
		$this->assertEquals(' tinyblob NOT NULL', $this->definition->tinyblob()->sql());
		$this->assertEquals(' tinyint NOT NULL', $this->definition->tinyint()->sql());
		$this->assertEquals(' tinytext NOT NULL', $this->definition->tinytext()->sql());
		$this->assertEquals(' varbinary NOT NULL', $this->definition->varbinary()->sql());
		$this->assertEquals(' varchar NOT NULL', $this->definition->varchar()->sql());
		$this->assertEquals(' year NOT NULL', $this->definition->year()->sql());
	}

	public function testBadMethod()
	{
		$this->expectException(\BadMethodCallException::class);
		$this->expectExceptionMessage('Method not found or not allowed: foo');
		$this->definition->foo();
	}
}
