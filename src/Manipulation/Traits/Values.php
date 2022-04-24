<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Manipulation\Traits;

use Closure;
use LogicException;

/**
 * Trait Values.
 *
 * @package database
 */
trait Values
{
    /**
     * Adds a row of values to the VALUES clause.
     *
     * @param array<array<mixed>>|Closure|float|int|string|null $value
     * @param Closure|float|int|string|null ...$values
     *
     * @return static
     */
    public function values(
        array | Closure | float | int | string | null $value,
        Closure | float | int | string | null ...$values
    ) : static {
        if ( ! \is_array($value)) {
            $this->sql['values'][] = [$value, ...$values];
            return $this;
        }
        if ($values) {
            throw new LogicException(
                'The method ' . static::class . '::values'
                . ' must have only one argument when the first parameter is passed as array'
            );
        }
        foreach ($value as $row) {
            $this->sql['values'][] = $row;
        }
        return $this;
    }

    /**
     * Renders the VALUES clause.
     *
     * @return string|null The VALUES part or null if none was set
     */
    protected function renderValues() : ?string
    {
        if ( ! isset($this->sql['values'])) {
            return null;
        }
        $values = [];
        foreach ($this->sql['values'] as $value) {
            foreach ($value as &$item) {
                $item = $this->renderValue($item);
            }
            unset($item);
            $values[] = ' (' . \implode(', ', $value) . ')';
        }
        $values = \implode(',' . \PHP_EOL, $values);
        return " VALUES{$values}";
    }
}
