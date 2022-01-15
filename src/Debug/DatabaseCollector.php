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

use Framework\Debug\Collector;

/**
 * Class DatabaseCollector.
 *
 * @package database
 */
class DatabaseCollector extends Collector
{
    protected string $serverInfo;

    public function setServerInfo(string $serverInfo) : void
    {
        $this->serverInfo = $serverInfo;
    }

    public function getServerInfo() : string
    {
        return $this->serverInfo;
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
                <th>Order</th>
                <th title="Seconds">Time</th>
                <th>Statement</th>
                <th title="Affected rows or Rows in set">Rows</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->data as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= \round($item['end'] - $item['start'], 3) ?></td>
                    <td>
                        <pre><?= \htmlentities($item['statement']) ?></pre>
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
