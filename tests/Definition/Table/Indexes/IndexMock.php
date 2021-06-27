<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Indexes;

use Framework\Database\Definition\Table\Indexes\Index;

class IndexMock extends Index
{
	public string $type = 'index_mock';
}
