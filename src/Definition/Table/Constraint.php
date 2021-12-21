<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table;

/**
 * Trait Constraint.
 *
 * @package database
 */
trait Constraint
{
    protected ?string $constraint = null;

    /**
     * @param string $name
     *
     * @return static
     */
    public function constraint(string $name) : static
    {
        $this->constraint = $name;
        return $this;
    }

    protected function renderConstraint() : ?string
    {
        if ($this->constraint === null) {
            return null;
        }
        $constraint = $this->database->protectIdentifier($this->constraint);
        return " CONSTRAINT {$constraint}";
    }
}
