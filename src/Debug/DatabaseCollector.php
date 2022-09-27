<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Debug;

use Framework\Database\Database;
use Framework\Debug\Collector;

/**
 * Class DatabaseCollector.
 *
 * @package database
 */
class DatabaseCollector extends Collector
{
    protected Database $database;
    /**
     * @var string
     *
     * @deprecated Use {@see Database::getConnection()}
     */
    protected string $serverInfo;

    public function setDatabase(Database $database) : static
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @param string $serverInfo
     *
     * @deprecated Use {@see Database::getConnection()}
     *
     * @codeCoverageIgnore
     */
    public function setServerInfo(string $serverInfo) : void
    {
        \trigger_error(
            'Method ' . __METHOD__ . ' is deprecated',
            \E_USER_DEPRECATED
        );
        $this->serverInfo = $serverInfo;
    }

    /**
     * @return string
     *
     * @deprecated Use {@see Database::getConnection()}
     *
     * @codeCoverageIgnore
     */
    public function getServerInfo() : string
    {
        \trigger_error(
            'Method ' . __METHOD__ . ' is deprecated',
            \E_USER_DEPRECATED
        );
        return $this->serverInfo;
    }

    public function getActivities() : array
    {
        $activities = [];
        foreach ($this->getData() as $index => $data) {
            $activities[] = [
                'collector' => $this->getName(),
                'class' => static::class,
                'description' => 'Run statement ' . ($index + 1),
                'start' => $data['start'],
                'end' => $data['end'],
            ];
        }
        return $activities;
    }

    public function getContents() : string
    {
        \ob_start();
        if ( ! isset($this->serverInfo)) {
            echo '<p>This collector has not been added to a Database instance.</p>';
            return \ob_get_clean(); // @phpstan-ignore-line
        } ?>
        <p><strong>Server Info:</strong> <?= $this->getServerInfo() ?></p>
        <?php
        if ( ! $this->hasData()) {
            echo '<p>Did not run statements.</p>';
            return \ob_get_clean(); // @phpstan-ignore-line
        }
        $count = \count($this->getData()); ?>
        <p>Ran <?= $count ?> statement<?= $count === 1 ? '' : 's' ?>:</p>
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th title="Seconds">Time</th>
                <th>Statement</th>
                <th title="Affected rows or Rows in set">Rows</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->getData() as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= \round($item['end'] - $item['start'], 6) ?></td>
                    <td>
                        <pre><code class="language-sql"><?=
                                \htmlentities($item['statement'])
                ?></code></pre>
                    </td>
                    <td><?= $item['rows'] ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php
        return \ob_get_clean(); // @phpstan-ignore-line
    }
}
