<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation\Traits;

use Framework\Database\Manipulation\Traits\Having;
use Tests\Database\Manipulation\StatementMock;

class HavingMock extends StatementMock
{
	use Having {
		renderHaving as public;
	}
}
