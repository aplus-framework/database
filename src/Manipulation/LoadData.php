<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Manipulation;

use InvalidArgumentException;
use LogicException;

/**
 * Class LoadData.
 *
 * @see https://mariadb.com/kb/en/load-data-infile/
 *
 * @package database
 */
class LoadData extends Statement
{
    use Traits\Set;

    /**
     * @see https://mariadb.com/kb/en/high_priority-and-low_priority/
     *
     * @var string
     */
    public const OPT_LOW_PRIORITY = 'LOW_PRIORITY';
    /**
     * @see https://mariadb.com/kb/en/load-data-infile/#priority-and-concurrency
     *
     * @var string
     */
    public const OPT_CONCURRENT = 'CONCURRENT';
    /**
     * @see https://mariadb.com/kb/en/load-data-infile/#load-data-local-infile
     *
     * @var string
     */
    public const OPT_LOCAL = 'LOCAL';

    protected function renderOptions() : ?string
    {
        if ( ! $this->hasOptions()) {
            return null;
        }
        $options = $this->sql['options'];
        foreach ($options as &$option) {
            $input = $option;
            $option = \strtoupper($option);
            if ( ! \in_array($option, [
                static::OPT_LOW_PRIORITY,
                static::OPT_CONCURRENT,
                static::OPT_LOCAL,
            ], true)) {
                throw new InvalidArgumentException("Invalid option: {$input}");
            }
        }
        unset($option);
        $intersection = \array_intersect(
            $options,
            [static::OPT_LOW_PRIORITY, static::OPT_CONCURRENT]
        );
        if (\count($intersection) > 1) {
            throw new LogicException('Options LOW_PRIORITY and CONCURRENT can not be used together');
        }
        return \implode(' ', $options);
    }

    /**
     * @param string $filename
     *
     * @return static
     */
    public function infile(string $filename) : static
    {
        $this->sql['infile'] = $filename;
        return $this;
    }

    protected function renderInfile() : string
    {
        if (empty($this->sql['infile'])) {
            throw new LogicException('INFILE statement is required');
        }
        $filename = $this->database->quote($this->sql['infile']);
        return " INFILE {$filename}";
    }

    /**
     * @param string $table
     *
     * @return static
     */
    public function intoTable(string $table) : static
    {
        $this->sql['table'] = $table;
        return $this;
    }

    protected function renderIntoTable() : string
    {
        if (empty($this->sql['table'])) {
            throw new LogicException('Table is required');
        }
        return ' INTO TABLE ' . $this->database->protectIdentifier($this->sql['table']);
    }

    /**
     * @param string $charset
     *
     * @see https://mariadb.com/kb/en/supported-character-sets-and-collations/
     *
     * @return static
     */
    public function charset(string $charset) : static
    {
        $this->sql['charset'] = $charset;
        return $this;
    }

    protected function renderCharset() : ?string
    {
        if ( ! isset($this->sql['charset'])) {
            return null;
        }
        return " CHARACTER SET {$this->sql['charset']}";
    }

    /**
     * @param string $str
     *
     * @return static
     */
    public function columnsTerminatedBy(string $str) : static
    {
        $this->sql['columns_terminated_by'] = $this->database->quote($str);
        return $this;
    }

    /**
     * @param string $char
     * @param bool $optionally
     *
     * @return static
     */
    public function columnsEnclosedBy(string $char, bool $optionally = false) : static
    {
        $this->sql['columns_enclosed_by'] = $this->database->quote($char);
        $this->sql['columns_enclosed_by_opt'] = $optionally;
        return $this;
    }

    /**
     * @param string $char
     *
     * @return static
     */
    public function columnsEscapedBy(string $char) : static
    {
        $this->sql['columns_escaped_by'] = $this->database->quote($char);
        return $this;
    }

    protected function renderColumns() : ?string
    {
        if ( ! isset($this->sql['columns_terminated_by'])
            && ! isset($this->sql['columns_enclosed_by'])
            && ! isset($this->sql['columns_escaped_by'])) {
            return null;
        }
        $part = ' COLUMNS' . \PHP_EOL;
        if (isset($this->sql['columns_terminated_by'])) {
            $part .= '  TERMINATED BY ' . $this->sql['columns_terminated_by'] . \PHP_EOL;
        }
        if (isset($this->sql['columns_enclosed_by'])) {
            if (isset($this->sql['columns_enclosed_by_opt'])) {
                $part .= '  OPTIONALLY';
            }
            $part .= ' ENCLOSED BY ' . $this->sql['columns_enclosed_by'] . \PHP_EOL;
        }
        if (isset($this->sql['columns_escaped_by'])) {
            $part .= '  ESCAPED BY ' . $this->sql['columns_escaped_by'] . \PHP_EOL;
        }
        return $part;
    }

    /**
     * @param string $str
     *
     * @return static
     */
    public function linesStartingBy(string $str) : static
    {
        $this->sql['lines_starting_by'] = $this->database->quote($str);
        return $this;
    }

    /**
     * @param string $str
     *
     * @return static
     */
    public function linesTerminatedBy(string $str) : static
    {
        $this->sql['lines_terminated_by'] = $this->database->quote($str);
        return $this;
    }

    protected function renderLines() : ?string
    {
        if ( ! isset($this->sql['lines_starting_by'])
            && ! isset($this->sql['lines_terminated_by'])) {
            return null;
        }
        $part = ' LINES' . \PHP_EOL;
        if (isset($this->sql['lines_starting_by'])) {
            $part .= '  STARTING BY ' . $this->sql['lines_starting_by'] . \PHP_EOL;
        }
        if (isset($this->sql['lines_terminated_by'])) {
            $part .= '  TERMINATED BY ' . $this->sql['lines_terminated_by'] . \PHP_EOL;
        }
        return $part;
    }

    /**
     * @param int $number
     *
     * @return static
     */
    public function ignoreLines(int $number) : static
    {
        $this->sql['ignore_lines'] = $number;
        return $this;
    }

    protected function renderIgnoreLines() : ?string
    {
        if ( ! isset($this->sql['ignore_lines'])) {
            return null;
        }
        return " IGNORE {$this->sql['ignore_lines']} LINES";
    }

    /**
     * @return string
     */
    public function sql() : string
    {
        $sql = 'LOAD DATA' . \PHP_EOL;
        $part = $this->renderOptions();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $sql .= $this->renderInfile() . \PHP_EOL;
        $sql .= $this->renderIntoTable() . \PHP_EOL;
        $part = $this->renderCharset();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $sql .= $this->renderColumns();
        $sql .= $this->renderLines();
        $part = $this->renderIgnoreLines();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        return $sql;
    }

    public function run() : int|string
    {
        return $this->database->exec($this->sql());
    }
}
