<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation\Traits;

use Framework\Database\Manipulation\Traits\GroupBy;
use Tests\Database\Manipulation\StatementMock;

class GroupByMock extends StatementMock
{
    use GroupBy {
        renderGroupBy as public;
    }
}
