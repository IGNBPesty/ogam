<?php
/**
 * © French National Forest Inventory
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 */

/**
 * This is a model allowing generic access to the RAW_DATA tables.
 * @package models
 */
class Model_Generic extends Zend_Db_Table_Abstract {

	var $logger;

	/**
	 * Initialisation
	 */
	public function init() {

		// Initialise the logger
		$this->logger = Zend_Registry::get("logger");

	}

	/**
	 * Execute the request.
	 *
	 * @param string the SQL Request
	 * @return Array[]
	 */
	public function executeRequest($sql) {
		$db = $this->getAdapter();
		$db->getConnection()->setAttribute(PDO::ATTR_TIMEOUT, 480);

		Zend_Registry::get("logger")->info('executeRequest : '.$sql);

		$result = $db->fetchAll($sql);

		return $result;

	}

	/**
	 * Get a line of data from a table, given its primary key.
	 *
	 * @param DataObject $data the shell of the data object with the values for the primary key.
	 * @return DataObject The complete data object.
	 */
	public function getData($data) {
		$db = $this->getAdapter();

		/* @var $data DataObject */
		$tableFormat = $data->tableFormat;
		/* @var $tableFormat TableFormat */

		Zend_Registry::get("logger")->info('getData');

		// Get the values from the data table
		$sql = "SELECT *";
		$sql .= " FROM ".$tableFormat->schemaCode.".".$tableFormat->tableName;
		$sql .= " WHERE(1 = 1)";

		// Build the WHERE clause with the info from the PK.
		foreach ($data->primaryKeys as $primaryKey) {
			/* @var $primaryKey TableField */

			// Hardcoded value : We ignore the submission_id info (we should have an unicity constraint that allow this)
			if (!($tableFormat->schemaCode == "RAW_DATA" && $primaryKey->data == "SUBMISSION_ID")) {

				if ($primaryKey->type == "NUMERIC" || $primaryKey->type == "INTEGER") {
					$sql .= " AND ".$primaryKey->columnName." = ".$primaryKey->value;
				} else {
					$sql .= " AND ".$primaryKey->columnName." = '".$primaryKey->value."'";
				}
			}
		}

		Zend_Registry::get("logger")->info('getData : '.$sql);

		$select = $db->prepare($sql);
		$select->execute();
		$row = $select->fetch();

		// Fill the values with data from the table
		foreach ($data->fields as $field) {
			/* @var $value TableField */
			$field->value = $row[strtolower($field->columnName)];
		}

		return $data;

	}

	/**
	 * Update a line of data from a table.
	 *
	 * @param DataObject $data the shell of the data object with the values for the primary key.
	 */
	public function updateData($data) {
		$db = $this->getAdapter();

		/* @var $data DataObject */
		$tableFormat = $data->tableFormat;
		/* @var $tableFormat TableFormat */

		Zend_Registry::get("logger")->info('updateData');

		// Get the values from the data table
		$sql = "UPDATE ".$tableFormat->schemaCode.".".$tableFormat->tableName;
		$sql .= " SET ";

		// updates of the data.
		foreach ($data->fields as $field) {
			/* @var $field TableField */

			if ($field->data != "LINE_NUMBER") { // Hardcoded value
				if ($field->type == "NUMERIC" || $field->type == "INTEGER") {
					$sql .= $field->columnName." = ".$field->value;
				} else {
					$sql .= $field->columnName." = '".$field->value."'";
				}
				$sql .= ",";
			}
		}
		// remove last comma
		$sql = substr($sql, 0, -1);

		$sql .= " WHERE(1 = 1)";

		// Build the WHERE clause with the info from the PK.
		foreach ($data->primaryKeys as $primaryKey) {
			/* @var $primaryKey TableField */

			// Hardcoded value : We ignore the submission_id info (we should have an unicity constraint that allow this)
			if (!($tableFormat->schemaCode == "RAW_DATA" && $primaryKey->data == "SUBMISSION_ID")) {

				if ($primaryKey->type == "NUMERIC" || $primaryKey->type == "INTEGER") {
					$sql .= " AND ".$primaryKey->columnName." = ".$primaryKey->value;
				} else {
					$sql .= " AND ".$primaryKey->columnName." = '".$primaryKey->value."'";
				}
			}
		}

		Zend_Registry::get("logger")->info('updateData : '.$sql);

		$select = $db->prepare($sql);
		$select->execute();

	}

}
