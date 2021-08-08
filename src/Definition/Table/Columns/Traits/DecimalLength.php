<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns\Traits;

/**
 * Trait DecimalLength.
 *
 * @package database
 */
trait DecimalLength
{
    protected function renderLength() : ?string
    {
        if ( ! isset($this->length[0])) {
            return null;
        }
        $maximum = $this->database->quote($this->length[0]);
        if (isset($this->length[1])) {
            $decimals = $this->database->quote($this->length[1]);
            $maximum .= ",{$decimals}";
        }
        return "({$maximum})";
    }
}
