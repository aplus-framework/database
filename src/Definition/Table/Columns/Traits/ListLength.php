<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns\Traits;

trait ListLength
{
	protected function renderLength() : ?string
	{
		if (empty($this->length)) {
			return null;
		}
		$values = [];
		foreach ($this->length as $length) {
			$values[] = $this->database->quote($length);
		}
		$values = \implode(', ', $values);
		return "({$values})";
	}
}
