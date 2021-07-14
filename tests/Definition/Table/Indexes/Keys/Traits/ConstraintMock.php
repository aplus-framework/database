<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Indexes\Keys\Traits;

use Framework\Database\Definition\Table\Indexes\Keys\Traits\Constraint;
use Tests\Database\Definition\Table\Indexes\IndexMock;

class ConstraintMock extends IndexMock
{
    use Constraint;
    public string $type = 'constraint_mock';
}
