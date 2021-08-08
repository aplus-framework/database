<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns\Numeric;

use Framework\Database\Definition\Table\Columns\Traits\DecimalLength;

/**
 * Class DecimalColumn.
 *
 * @package database
 */
final class DecimalColumn extends NumericDataType
{
    use DecimalLength;
    protected string $type = 'decimal';
    protected int $maxLength = 11;
    protected float $decimal;
}
