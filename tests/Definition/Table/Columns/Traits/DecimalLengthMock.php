<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Columns\Traits;

use Framework\Database\Definition\Table\Columns\Traits\DecimalLength;
use Tests\Database\Definition\Table\Columns\ColumnMock;

class DecimalLengthMock extends ColumnMock
{
    use DecimalLength;
}
