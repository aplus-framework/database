<?php namespace Framework\Database\Definition\DataTypes\Binary;

use Framework\Database\Definition\DataTypes\Column;

abstract class BinaryDataType extends Column
{
	protected function sql() : string
	{
		$sql = $this->renderName();
		$sql .= $this->renderType();
		$sql .= $this->renderLength();
		$sql .= $this->renderNull();
		$sql .= $this->renderDefault();
		$sql .= $this->renderComment();
		$sql .= $this->renderUniqueKey();
		$sql .= $this->renderPrimaryKey();
		return $sql;
	}
}
