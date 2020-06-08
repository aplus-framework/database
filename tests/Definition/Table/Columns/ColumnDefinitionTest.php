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
		$this->assertEquals(' bigint', $this->definition->bigint()->sql());
		$this->assertEquals(' binary', $this->definition->binary()->sql());
		$this->assertEquals(' bit', $this->definition->bit()->sql());
		$this->assertEquals(' blob', $this->definition->blob()->sql());
		$this->assertEquals(' boolean', $this->definition->boolean()->sql());
		$this->assertEquals(' char', $this->definition->char()->sql());
		$this->assertEquals(' date', $this->definition->date()->sql());
		$this->assertEquals(' datetime', $this->definition->datetime()->sql());
		$this->assertEquals(' decimal', $this->definition->decimal()->sql());
		$this->assertEquals(" enum('a')", $this->definition->enum('a')->sql());
		$this->assertEquals(' float', $this->definition->float()->sql());
		$this->assertEquals(' geometry', $this->definition->geometry()->sql());
		$this->assertEquals(
			' geometrycollection',
			$this->definition->geometrycollection()->sql()
		);
		$this->assertEquals(' int', $this->definition->int()->sql());
		$this->assertEquals(' json', $this->definition->json()->sql());
		$this->assertEquals(' linestring', $this->definition->linestring()->sql());
		$this->assertEquals(' longblob', $this->definition->longblob()->sql());
		$this->assertEquals(' longtext', $this->definition->longtext()->sql());
		$this->assertEquals(' mediumblob', $this->definition->mediumblob()->sql());
		$this->assertEquals(' mediumint', $this->definition->mediumint()->sql());
		$this->assertEquals(' mediumtext', $this->definition->mediumtext()->sql());
		$this->assertEquals(' multilinestring', $this->definition->multilinestring()->sql());
		$this->assertEquals(' multipoint', $this->definition->multipoint()->sql());
		$this->assertEquals(' multipolygon', $this->definition->multipolygon()->sql());
		$this->assertEquals(' point', $this->definition->point()->sql());
		$this->assertEquals(' polygon', $this->definition->polygon()->sql());
		$this->assertEquals(" set('b')", $this->definition->set('b')->sql());
		$this->assertEquals(' smallint', $this->definition->smallint()->sql());
		$this->assertEquals(' text', $this->definition->text()->sql());
		$this->assertEquals(' time', $this->definition->time()->sql());
		$this->assertEquals(' timestamp', $this->definition->timestamp()->sql());
		$this->assertEquals(' tinyblob', $this->definition->tinyblob()->sql());
		$this->assertEquals(' tinyint', $this->definition->tinyint()->sql());
		$this->assertEquals(' tinytext', $this->definition->tinytext()->sql());
		$this->assertEquals(' varbinary', $this->definition->varbinary()->sql());
		$this->assertEquals(' varchar', $this->definition->varchar()->sql());
		$this->assertEquals(' year', $this->definition->year()->sql());
	}

	public function testBadMethod()
	{
		$this->expectException(\BadMethodCallException::class);
		$this->expectExceptionMessage('Method not found: foo');
		$this->definition->foo();
	}
}
