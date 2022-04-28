<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class PreparedStatement.
 *
 * @package database
 */
class PreparedStatement
{
    protected \mysqli_stmt $statement;
    protected bool $sendingBlob = false;

    public function __construct(\mysqli_stmt $statement)
    {
        $this->statement = $statement;
    }

    /**
     * Executes the prepared statement, returning a result set as a Result object.
     *
     * @param bool|float|int|string|null ...$params Parameters sent to the prepared statement
     *
     * @throws RuntimeException if it cannot obtain a result set from the prepared statement
     *
     * @return Result
     */
    public function query(bool | float | int | string | null ...$params) : Result
    {
        $this->bindParams($params);
        $this->statement->execute();
        $result = $this->statement->get_result();
        if ($result === false) {
            throw new RuntimeException('Failed while trying to obtain a result set from the prepared statement');
        }
        return new Result($result, true);
    }

    /**
     * Executes the prepared statement and return the number of affected rows.
     *
     * @param bool|float|int|string|null ...$params Parameters sent to the prepared statement
     *
     * @return int|string
     */
    public function exec(bool | float | int | string | null ...$params) : int|string
    {
        $this->bindParams($params);
        $this->statement->execute();
        if ($this->statement->field_count) {
            $this->statement->free_result();
        }
        return $this->statement->affected_rows;
    }

    /**
     * @param array|mixed[] $params Values types: bool, float, int, string or null
     */
    protected function bindParams(array $params) : void
    {
        $this->sendingBlob = false;
        if (empty($params)) {
            return;
        }
        $types = '';
        foreach ($params as &$param) {
            $type = \gettype($param);
            switch ($type) {
                case 'boolean':
                    $types .= 'i';
                    $param = (int) $param;
                    break;
                case 'double':
                    $types .= 'd';
                    break;
                case 'integer':
                    $types .= 'i';
                    break;
                case 'NULL':
                case 'string':
                    $types .= 's';
                    break;
                default:
                    throw new InvalidArgumentException(
                        "Invalid param data type: {$type}"
                    );
            }
        }
        unset($param);
        $this->statement->bind_param($types, ...$params);
    }

    public function sendBlob(string $chunk) : bool
    {
        if ( ! $this->sendingBlob) {
            $this->sendingBlob = true;
            $null = null;
            $this->statement->bind_param('b', $null);
        }
        return $this->statement->send_long_data(0, $chunk);
    }
}
