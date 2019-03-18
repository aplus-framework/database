<?php namespace Tests\Database\Definition\Table\Columns\Traits;

use Framework\Database\Definition\Table\Columns\Traits\DecimalLength;
use Tests\Database\Definition\Table\Columns\ColumnMock;

class DecimalLengthMock extends ColumnMock
{
	use DecimalLength;
}
