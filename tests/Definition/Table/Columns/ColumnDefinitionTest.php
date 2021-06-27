<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Columns;

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

final class ColumnDefinitionTest extends TestCase
{
	protected ColumnDefinition $definition;

	protected function setUp() : void
	{
		$this->definition = new ColumnDefinition(static::$database);
	}

	public function testInstances() : void
	{
		self::assertInstanceOf(BigintColumn::class, $this->definition->bigint());
		self::assertInstanceOf(BinaryColumn::class, $this->definition->binary());
		self::assertInstanceOf(BitColumn::class, $this->definition->bit());
		self::assertInstanceOf(BlobColumn::class, $this->definition->blob());
		self::assertInstanceOf(BooleanColumn::class, $this->definition->boolean());
		self::assertInstanceOf(CharColumn::class, $this->definition->char());
		self::assertInstanceOf(DateColumn::class, $this->definition->date());
		self::assertInstanceOf(DatetimeColumn::class, $this->definition->datetime());
		self::assertInstanceOf(DecimalColumn::class, $this->definition->decimal());
		self::assertInstanceOf(EnumColumn::class, $this->definition->enum('a'));
		self::assertInstanceOf(FloatColumn::class, $this->definition->float());
		self::assertInstanceOf(GeometryColumn::class, $this->definition->geometry());
		self::assertInstanceOf(
			GeometryCollectionColumn::class,
			$this->definition->geometrycollection()
		);
		self::assertInstanceOf(IntColumn::class, $this->definition->int());
		self::assertInstanceOf(JsonColumn::class, $this->definition->json());
		self::assertInstanceOf(LinestringColumn::class, $this->definition->linestring());
		self::assertInstanceOf(LongblobColumn::class, $this->definition->longblob());
		self::assertInstanceOf(LongtextColumn::class, $this->definition->longtext());
		self::assertInstanceOf(MediumblobColumn::class, $this->definition->mediumblob());
		self::assertInstanceOf(MediumintColumn::class, $this->definition->mediumint());
		self::assertInstanceOf(MediumtextColumn::class, $this->definition->mediumtext());
		self::assertInstanceOf(MultilinestringColumn::class, $this->definition->multilinestring());
		self::assertInstanceOf(MultipointColumn::class, $this->definition->multipoint());
		self::assertInstanceOf(MultipolygonColumn::class, $this->definition->multipolygon());
		self::assertInstanceOf(PointColumn::class, $this->definition->point());
		self::assertInstanceOf(PolygonColumn::class, $this->definition->polygon());
		self::assertInstanceOf(SetColumn::class, $this->definition->set('b'));
		self::assertInstanceOf(SmallintColumn::class, $this->definition->smallint());
		self::assertInstanceOf(TextColumn::class, $this->definition->text());
		self::assertInstanceOf(TimeColumn::class, $this->definition->time());
		self::assertInstanceOf(TimestampColumn::class, $this->definition->timestamp());
		self::assertInstanceOf(TinyblobColumn::class, $this->definition->tinyblob());
		self::assertInstanceOf(TinyintColumn::class, $this->definition->tinyint());
		self::assertInstanceOf(TinytextColumn::class, $this->definition->tinytext());
		self::assertInstanceOf(VarbinaryColumn::class, $this->definition->varbinary());
		self::assertInstanceOf(VarcharColumn::class, $this->definition->varchar());
		self::assertInstanceOf(YearColumn::class, $this->definition->year());
	}

	public function testTypes() : void
	{
		self::assertSame(' bigint NOT NULL', $this->definition->bigint()->sql());
		self::assertSame(' binary NOT NULL', $this->definition->binary()->sql());
		self::assertSame(' bit NOT NULL', $this->definition->bit()->sql());
		self::assertSame(' blob NOT NULL', $this->definition->blob()->sql());
		self::assertSame(' boolean NOT NULL', $this->definition->boolean()->sql());
		self::assertSame(' char NOT NULL', $this->definition->char()->sql());
		self::assertSame(' date NOT NULL', $this->definition->date()->sql());
		self::assertSame(' datetime NOT NULL', $this->definition->datetime()->sql());
		self::assertSame(' decimal NOT NULL', $this->definition->decimal()->sql());
		self::assertSame(" enum('a') NOT NULL", $this->definition->enum('a')->sql());
		self::assertSame(' float NOT NULL', $this->definition->float()->sql());
		self::assertSame(' geometry NOT NULL', $this->definition->geometry()->sql());
		self::assertSame(
			' geometrycollection NOT NULL',
			$this->definition->geometrycollection()->sql()
		);
		self::assertSame(' int NOT NULL', $this->definition->int()->sql());
		self::assertSame(' json NOT NULL', $this->definition->json()->sql());
		self::assertSame(' linestring NOT NULL', $this->definition->linestring()->sql());
		self::assertSame(' longblob NOT NULL', $this->definition->longblob()->sql());
		self::assertSame(' longtext NOT NULL', $this->definition->longtext()->sql());
		self::assertSame(' mediumblob NOT NULL', $this->definition->mediumblob()->sql());
		self::assertSame(' mediumint NOT NULL', $this->definition->mediumint()->sql());
		self::assertSame(' mediumtext NOT NULL', $this->definition->mediumtext()->sql());
		self::assertSame(' multilinestring NOT NULL', $this->definition->multilinestring()->sql());
		self::assertSame(' multipoint NOT NULL', $this->definition->multipoint()->sql());
		self::assertSame(' multipolygon NOT NULL', $this->definition->multipolygon()->sql());
		self::assertSame(' point NOT NULL', $this->definition->point()->sql());
		self::assertSame(' polygon NOT NULL', $this->definition->polygon()->sql());
		self::assertSame(" set('b') NOT NULL", $this->definition->set('b')->sql());
		self::assertSame(' smallint NOT NULL', $this->definition->smallint()->sql());
		self::assertSame(' text NOT NULL', $this->definition->text()->sql());
		self::assertSame(' time NOT NULL', $this->definition->time()->sql());
		self::assertSame(' timestamp NOT NULL', $this->definition->timestamp()->sql());
		self::assertSame(' tinyblob NOT NULL', $this->definition->tinyblob()->sql());
		self::assertSame(' tinyint NOT NULL', $this->definition->tinyint()->sql());
		self::assertSame(' tinytext NOT NULL', $this->definition->tinytext()->sql());
		self::assertSame(' varbinary NOT NULL', $this->definition->varbinary()->sql());
		self::assertSame(' varchar NOT NULL', $this->definition->varchar()->sql());
		self::assertSame(' year NOT NULL', $this->definition->year()->sql());
	}

	public function testBadMethod() : void
	{
		$this->expectException(\BadMethodCallException::class);
		$this->expectExceptionMessage('Method not found or not allowed: foo');
		$this->definition->foo(); // @phpstan-ignore-line
	}
}
