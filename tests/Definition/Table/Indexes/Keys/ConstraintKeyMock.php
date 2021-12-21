<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\ConstraintKey;

class ConstraintKeyMock extends ConstraintKey
{
    public string $type = 'constraint_mock';
}
