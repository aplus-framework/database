<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table;

class TableStatementMock extends \Framework\Database\Definition\Table\TableStatement
{
    public function sql() : string
    {
        return $this->renderOptions();
    }

    public function run() : mixed
    {
        return null;
    }
}
