<?php

class SQLBuilder {

	private $relTypes = array('onetoone', 'onetomany', 'manytoone', 'manytomany');
	private $fields;
	private $relations;
	private $table;
	private $pdoConnection;

	function __construct($model, $classname, $pdoConnection) {
		foreach ($model as $key => $value) {
			if (in_array($value['type'], $this->relTypes)) {
				$this->relations[$key] = $value;
			} else {
				$this->fields[$key] = $value;
			}
		}
		$this->table = $this->underScored($classname);
		$this->pdoConnection = $pdoConnection;
	}

	private static function fieldFilter($field) {
		return !(array_key_exists('increment', $field) && $field['increment'] == 'auto');
	}

	private function fieldData($fields, $entity) {
		foreach ($fields as $key => $value) {
			$fieldData[$key] = $entity->$key;
		}
		return $fieldData;
	}

	function drop() {
		return $this->pdoConnection->exec("DROP TABLE " . $this->table);
	}

	function create() {
		foreach ($this->fields as $key => $value) {
			$fields[] = $key . ' ' . $this->buildFieldType($value); 
		}
		$statement = "CREATE TABLE " . $this->table . " (" . implode(', ', $fields) . ")";
		return $this->pdoConnection->exec($statement);
	}

	private function buildFieldType($field) {
		if ($field['type'] == 'string') {
			$type ='TEXT';
		} elseif ($field['type'] == 'int') {
			$type = 'INTEGER';
		} elseif ($field['type'] == 'datetime') {
			$type = 'TEXT';
		}
		if ($field['notnull'] == true) {
			$type .= ' NOT NULL';  
		}
		if ($field['unique'] == true) {
			$type .= ' UNIQUE';
		}
		if ($field['primary'] == true) {
			$type .= ' PRIMARY KEY';
		}
		if ($field['increment'] == 'auto') {
			$type .= ' AUTOINCREMENT';
		}
		return $type;
	}

	function insert($entity) {
		$fields = array_filter($this->fields, 'SQLBuilder::fieldFilter');
		$fieldsParams = array_map(function($value) { return ':' . $value; }, array_keys($fields));
		$statement = "INSERT INTO " . $this->table . " (" . implode(', ', array_keys($fields)) . ") VALUES (" . implode(', ', $fieldsParams) . ")";
		$pdoStatement = $this->pdoConnection->prepare($statement);
		$pdoStatement->execute($this->fieldData($fields, $entity));
		$entity->id = $this->pdoConnection->lastInsertId();
		return $entity;
	}

	function update($entity) {
		$fields = array_filter($this->fields, 'SQLBuilder::fieldFilter');
		$setFields = array_map(function($value) { return $value . ' = :' . $value; }, array_keys($fields));
		$statement = "UPDATE " . $this->table . " SET " . $setFields . " WHERE id = :id";
		$pdoStatement = $this->pdoConnection->prepare($statement);
		$pdoStatement->execute(fieldData($this->fields, $entity));
		return $entity;
	}

	function delete($entity) {
		$statement = "DELETE FROM " . $this->table . " WHERE id = :id";
		$pdoStatement = $this->pdoConnection->prepare($statement);
		return $pdoStatement->execute(array('id' => $entity->id));
	}

	function select($query=null, $order=null, $params=null) {
		$queryArray = explode('_', $this->underScored($query));
		$result = '';
		if ($queryArray[0] == 'by') {
			for ($i = 1; $i < count($queryArray); $i++) { 
				if($i % 2 == 1) {
					$result .= $queryArray[$i] . ' =  :' . $queryArray[$i];
				} else {
					$result .= ' ' . strtoupper($queryArray[$i]) . ' ';
				}
			}
			$query = $result;
		}
		$statement = "SELECT * FROM " . $this->table . (!is_null($query) ? " WHERE " . $query : "") . (!is_null($order) ? " ORDER BY " . $order :"");
		$pdoStatement = $this->pdoConnection->prepare($statement);
		$pdoStatement->execute($params);
		return $pdoStatement->fetchAll();
	}

	private function underScored($camelCased) {
		return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $camelCased)); 
	}

}