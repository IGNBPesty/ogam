<?php

/**
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 *
 * © European Union, 2008-2012
 *
 * Reuse is authorised, provided the source is acknowledged. The reuse policy of the European Commission is implemented by a Decision of 12 December 2011.
 *
 * The general principle of reuse can be subject to conditions which may be specified in individual copyright notices.
 * Therefore users are advised to refer to the copyright notices of the individual websites maintained under Europa and of the individual documents.
 * Reuse is not applicable to documents subject to intellectual property rights of third parties.
 */

/**
 * This is a model allowing generic access to the RAW_DATA tables.
 *
 * @package Application_Model
 * @subpackage Generic
 */
class Application_Model_Generic_Generic {

	/**
	 * The system of projection for the visualisation.
	 *
	 * @var String
	 */
	var $visualisationSRS;

	/**
	 * The logger.
	 *
	 * @var Zend_Log
	 */
	var $logger;

	/**
	 * The generic service.
	 *
	 * @var Application_Service_GenericService
	 */
	var $genericService;

	/**
	 * The database connections
	 *
	 * @var Zend_Db
	 */
	var $rawdb;

	var $metadatadb;

	/**
	 * Initialisation
	 */
	public function __construct() {

		// Initialise the logger
		$this->logger = Zend_Registry::get("logger");

		// Initialise the projection system
		$configuration = Zend_Registry::get("configuration");
		$this->visualisationSRS = $configuration->srs_visualisation;

		// Initialise the metadata model
		$this->metadataModel = new Application_Model_Metadata_Metadata();

		// Initialise the generic service
		$this->genericService = new Application_Service_GenericService();

		// The database connection
		$this->rawdb = Zend_Registry::get('raw_db');
		$this->metadatadb = Zend_Registry::get('metadata_db');
	}

	/**
	 * Destuction.
	 */
	function __destruct() {
		$this->rawdb->closeConnection();
		$this->metadatadb->closeConnection();
	}

	/**
	 * Execute the request.
	 *
	 * @param
	 *        	string the SQL Request
	 * @return Array[]
	 */
	public function executeRequest($sql) {
		$this->rawdb->getConnection()->setAttribute(PDO::ATTR_TIMEOUT, 480);

		$this->logger->info('executeRequest : ' . $sql);

		$result = $this->rawdb->fetchAll($sql);

		return $result;
	}

	/**
	 * Fill a line of data with the values a table, given its primary key.
	 * Only one object is expected in return.
	 *
	 * @param Application_Object_Generic_DataObject $data
	 *        	the shell of the data object with the values for the primary key.
	 * @return Application_Object_Generic_DataObject The complete data object.
	 */
	public function getDatum($data) {
		$tableFormat = $data->tableFormat;

		$this->logger->info('getDatum : ' . $tableFormat->format);

		$schema = $this->metadataModel->getSchema($tableFormat->schemaCode);

		// Get the values from the data table
		$sql = "SELECT " . $this->genericService->buildSelect($data->getFields());
		$sql .= " FROM " . $schema->name . "." . $tableFormat->tableName . " AS " . $tableFormat->format;
		$sql .= " WHERE (1 = 1)" . $this->genericService->buildWhere($schema->code, $data->infoFields);

		$this->logger->info('getDatum : ' . $sql);

		$select = $this->rawdb->prepare($sql);
		$select->execute();
		$row = $select->fetch();

		// Fill the values with data from the table
		foreach ($data->editableFields as $field) {
			$key = strtolower($field->getName());
			$field->value = $row[$key];

			// Store additional info for geometry type
			if ($field->type === "GEOM") {
				$field->xmin = $row[strtolower($key) . '_x_min'];
				$field->xmax = $row[strtolower($key) . '_x_max'];
				$field->ymin = $row[strtolower($key) . '_y_min'];
				$field->ymax = $row[strtolower($key) . '_y_max'];
			} else if ($field->type === "ARRAY") {
				// For array field we transform the value in a array object
				$field->value = $this->genericService->stringToArray($field->value);
			}
		}

		// Fill the values with data from the table
		foreach ($data->getFields() as $field) {

			// Fill the value labels for the field
			$field->valueLabel = $this->genericService->getValueLabel($field, $field->value);
		}

		return $data;
	}

	/**
	 * Get the join keys
	 *
	 * @param Application_Object_Generic_DataObject $data
	 *        	the shell of the data object.
	 * @return Array[String] The join keys.
	 */
	public function getJoinKeys($data) {
		$tableFormat = $data->tableFormat;

		$sql = "SELECT join_key";
		$sql .= " FROM TABLE_TREE";
		$sql .= " WHERE schema_code = '" . $tableFormat->schemaCode . "'";
		$sql .= " AND child_table = '" . $tableFormat->format . "'";

		$select = $this->metadatadb->prepare($sql);
		$select->execute();
		$row = $select->fetch();

		$joinKeys = $row['join_key'];
		$joinKeys = explode(',', $joinKeys);
		$joinKeys = array_map('trim', $joinKeys);

		return $joinKeys;
	}

	/**
	 * Get a list of data objects from a table, given an incomplete primary key.
	 * A list of data objects is expected in return.
	 *
	 * @param Application_Object_Generic_DataObject $data
	 *        	the shell of the data object with the values for the primary key.
	 * @return Array[Application_Object_Generic_DataObject] The complete data objects.
	 */
	private function _getDataList($data) {
		$this->logger->info('_getDataList');

		$result = array();

		// The table format descriptor
		$tableFormat = $data->tableFormat;

		$schema = $this->metadataModel->getSchema($tableFormat->schemaCode);

		// Get the values from the data table
		$sql = "SELECT " . $this->genericService->buildSelect($data->getFields());
		$sql .= " FROM " . $schema->name . "." . $tableFormat->tableName . " AS " . $tableFormat->format;
		$sql .= " WHERE (1 = 1)" . $this->genericService->buildWhere($schema->code, array_merge($data->infoFields, $data->editableFields));

		$this->logger->info('_getDataList : ' . $sql);

		$select = $this->rawdb->prepare($sql);
		$select->execute();
		foreach ($select->fetchAll() as $row) {

			// Build an new empty data object
			$child = $this->genericService->buildDataObject($tableFormat->schemaCode, $data->tableFormat->format);

			// Fill the values with data from the table
			foreach ($child->getFields() as $field) {

				$field->value = $row[strtolower($field->getName())];

				if ($field->type === "ARRAY") {
					// For array field we transform the value in a array object
					$field->value = $this->genericService->stringToArray($field->value);
				}

				// Fill the value labels for the field
				$field->valueLabel = $this->genericService->getValueLabel($field, $field->value);
			}

			$result[] = $child;
		}

		return $result;
	}

	/**
	 * Update a line of data from a table.
	 *
	 * @param Application_Object_Generic_DataObject $data
	 *        	the shell of the data object with the values for the primary key.
	 * @throws an exception if an error occur during update
	 */
	public function updateData($data) {

		/* @var $data Application_Object_Generic_DataObject */
		$tableFormat = $data->tableFormat;
		/* @var $tableFormat TableFormat */

		$schema = $this->metadataModel->getSchema($tableFormat->schemaCode);

		// Get the values from the data table
		$sql = "UPDATE " . $schema->name . "." . $tableFormat->tableName . " " . $tableFormat->format;
		$sql .= " SET ";

		// updates of the data.
		foreach ($data->editableFields as $field) {
			/* @var $field Application_Object_Metadata_TableField */

			if ($field->data != "LINE_NUMBER" && $field->isEditable) {
				// Hardcoded value
				$sql .= $field->columnName . " = " . $this->genericService->buildSQLValueItem($schema, $field);
				$sql .= ", ";
			}
		}
		// remove last comma
		$sql = substr($sql, 0, -2);

		$sql .= " WHERE (1 = 1)";

		// Build the WHERE clause with the info from the PK.
		foreach ($data->infoFields as $primaryKey) {
			// Hardcoded value : We ignore the submission_id info (we should have an unicity constraint that allow this)
			$sql .= $this->genericService->buildWhereItem($schema, $primaryKey, true);
		}

		$this->logger->info('updateData : ' . $sql);

		$request = $this->rawdb->prepare($sql);

		try {
			$request->execute();
		} catch (Exception $e) {
			$this->logger->err('Error while updating data  : ' . $e->getMessage());
			throw new Exception("Error while updating data  : " . $e->getMessage());
		}
	}

	/**
	 * Delete a line of data from a table.
	 *
	 * @param Application_Object_Generic_DataObject $data
	 *        	the shell of the data object with the values for the primary key.
	 * @throws an exception if an error occur during delete
	 */
	public function deleteData($data) {

		/* @var $data Application_Object_Generic_DataObject */
		$tableFormat = $data->tableFormat;
		/* @var $tableFormat TableFormat */

		$this->logger->info('deleteData');

		$schema = $this->metadataModel->getSchema($tableFormat->schemaCode);

		// Get the values from the data table
		$sql = "DELETE FROM " . $schema->name . "." . $tableFormat->tableName;
		$sql .= " WHERE (1 = 1) ";

		// Build the WHERE clause with the info from the PK.
		foreach ($data->infoFields as $primaryKey) {
			/* @var $primaryKey Application_Object_Metadata_TableField */

			// Hardcoded value : We ignore the submission_id info (we should have an unicity constraint that allow this)
			if (!($tableFormat->schemaCode === "RAW_DATA" && $primaryKey->data === "SUBMISSION_ID")) {

				if ($primaryKey->type === "NUMERIC" || $primaryKey->type === "INTEGER") {
					$sql .= " AND " . $primaryKey->columnName . " = " . $primaryKey->value;
				} else if ($primaryKey->type === "ARRAY") {
					// Arrays not handlmed as primary keys
					throw new Exception("A primary key should not be of type ARRAY");
				} else {
					$sql .= " AND " . $primaryKey->columnName . " = '" . $primaryKey->value . "'";
				}
			}
		}

		$this->logger->info('deleteData : ' . $sql);

		$request = $this->rawdb->prepare($sql);

		try {
			$request->execute();
		} catch (Exception $e) {
			$this->logger->err('Error while deleting data  : ' . $e->getMessage());
			throw new Exception("Error while deleting data  : " . $e->getMessage());
		}
	}

	/**
	 * Insert a line of data from a table.
	 *
	 * @param Application_Object_Generic_DataObject $data
	 *        	the shell of the data object to insert.
	 * @return Application_Object_Generic_DataObject $data the eventually edited data object.
	 * @throws an exception if an error occur during insert
	 */
	public function insertData($data) {
		$this->logger->info('insertData');

		$tableFormat = $data->tableFormat;

		$schema = $this->metadataModel->getSchema($tableFormat->schemaCode);

		// Get the values from the data table
		$sql = "INSERT INTO " . $schema->name . "." . $tableFormat->tableName;
		$columns = "";
		$values = "";
		$return = "";

		// updates of the data.
		foreach ($data->infoFields as $field) {
			if ($field->value != null) {

				// Primary keys that are not set should be serials ...
				$columns .= $field->columnName . ", ";
				$values .= $this->genericService->buildSQLValueItem($schema, $field);
				$values .= ", ";
			} else {
				$this->logger->info('field ' . $field->columnName . " " . $field->isCalculated);

				// Case of a calculated PK (for example a serial)
				if ($field->isCalculated) {
					if ($return == "") {
						$return .= " RETURNING ";
					} else {
						$return .= ", ";
					}
					$return .= $field->columnName;
				}
			}
		}
		foreach ($data->editableFields as $field) {
			if ($field->value != null) {
				// Primary keys that are not set should be serials ...
				if ($field->data != "LINE_NUMBER") {
					$columns .= $field->columnName . ", ";
					$values .= $this->genericService->buildSQLValueItem($schema, $field);
					$values .= ", ";
				}
			}
		}
		// remove last commas
		$columns = substr($columns, 0, -2);
		$values = substr($values, 0, -2);

		$sql .= "(" . $columns . ") VALUES (" . $values . ")" . $return;

		$this->logger->info('insertData : ' . $sql);

		$request = $this->rawdb->prepare($sql);

		try {
			$request->execute();
		} catch (Exception $e) {
			$this->logger->err('Error while inserting data  : ' . $e->getMessage());
			throw new Exception("Error while inserting data  : " . $e->getMessage());
		}

		if ($return !== "") {

			foreach ($request->fetchAll() as $row) {
				foreach ($data->infoFields as $field) {
					if ($field->isCalculated) {
						$field->value = $row[strtolower($field->columnName)];
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Get the information about the ancestors of a line of data.
	 * The key elements in the parent tables must have an existing value in the child.
	 *
	 * @param Application_Object_Generic_DataObject $data
	 *        	the data object we're looking at.
	 * @return List[Application_Object_Generic_DataObject] The line of data in the parent tables.
	 */
	public function getAncestors($data) {
		$ancestors = array();

		/* @var $data Application_Object_Generic_DataObject */
		$tableFormat = $data->tableFormat;
		/* @var $tableFormat TableFormat */

		$this->logger->info('getAncestors');

		// Get the parent of the current table
		$sql = "SELECT *";
		$sql .= " FROM TABLE_TREE ";
		$sql .= " WHERE SCHEMA_CODE = '" . $tableFormat->schemaCode . "'";
		$sql .= " AND child_table = '" . $tableFormat->format . "'";

		$this->logger->info('getAncestors : ' . $sql);

		$select = $this->metadatadb->prepare($sql);
		$select->execute();
		$row = $select->fetch();

		$parentTable = $row['parent_table'];

		// Check if we are not the root table
		if ($parentTable != "*") {

			// Build an empty parent object
			$parent = $this->genericService->buildDataObject($tableFormat->schemaCode, $parentTable, null);

			// Fill the PK values (we hope that the child contain the fields of the parent pk)
			foreach ($parent->infoFields as $key) {
				$fieldName = $data->tableFormat->format . '__' . $key->data;
				$fields = $data->getFields();
				if (array_key_exists($fieldName, $fields)) {
					$keyField = $fields[$fieldName];
					if ($keyField != null && $keyField->value != null) {
						$key->value = $keyField->value;
					}
				}
			}

			// Get the line of data from the table
			$parent = $this->getDatum($parent);

			$ancestors[] = $parent;

			// Recurse
			$ancestors = array_merge($ancestors, $this->getAncestors($parent));
		}
		return $ancestors;
	}

	/**
	 * Get the information about the children of a line of data.
	 *
	 * @param Application_Object_Generic_DataObject $data
	 *        	the data object we're looking at.
	 * @param String $datasetId
	 *        	the dataset id
	 * @return Array[Format => List[Application_Object_Generic_DataObject]] The lines of data in the children tables, indexed by format.
	 */
	public function getChildren($data, $datasetId = null) {
		$children = array();

		/* @var $data Application_Object_Generic_DataObject */
		$tableFormat = $data->tableFormat;
		/* @var $tableFormat TableFormat */

		$this->logger->info('getChildren dataset : ' . $datasetId);

		// Get the children of the current table
		$sql = "SELECT *";
		$sql .= " FROM TABLE_TREE TT";
		if ($datasetId != null) {
			$sql .= " JOIN (SELECT DISTINCT DATASET_ID, SCHEMA_CODE, FORMAT FROM DATASET_FIELDS) as DF";
			$sql .= " ON DF.SCHEMA_CODE = TT.SCHEMA_CODE AND DF.FORMAT = TT.CHILD_TABLE";
		}
		$sql .= " WHERE TT.SCHEMA_CODE = '" . $tableFormat->schemaCode . "'";
		$sql .= " AND TT.PARENT_TABLE = '" . $tableFormat->format . "'";
		if ($datasetId != null) {
			$sql .= " AND DF.DATASET_ID = '" . $datasetId . "'";
		}

		$this->logger->info('getChildren : ' . $sql);

		$select = $this->metadatadb->prepare($sql);
		$select->execute();

		// For each potential child table listed, we search for the actual lines of data available
		foreach ($select->fetchAll() as $row) {
			$childTable = $row['child_table'];

			// Build an empty data object (for the query)
			$child = $this->genericService->buildDataObject($tableFormat->schemaCode, $childTable);

			// Fill the known primary keys (we hope the child contain the keys of the parent)
			foreach ($data->infoFields as $dataKey) {
				foreach ($child->infoFields as $childKey) {
					if ($dataKey->data == $childKey->data) {
						$childKey->value = $dataKey->value;
					}
				}
				foreach ($child->editableFields as $childKey) {
					if ($dataKey->data == $childKey->data) {
						$childKey->value = $dataKey->value;
					}
				}
			}

			// Get the lines of data corresponding to the partial key
			$childs = $this->_getDataList($child);

			// Add to the result
			$children[$child->tableFormat->format] = $childs;
		}

		return $children;
	}

	/**
	 * Get the number of lines of data linked to a provider.
	 *
	 * @param String $id
	 *        	the identifier of the provider
	 * @return Array[String => Integer] The count for each table name
	 */
	public function getProviderNbOfRawDatasByTable($id) {

		// Get all tables in raw_data, with a provider_id column, except technical tables :
		$req = "SELECT table_name ";
		$req .= " FROM information_schema.columns ";
		$req .= " WHERE column_name = 'provider_id' ";
		$req .= " AND table_schema='raw_data'";
		$req .= " AND table_name NOT IN ('submission', 'check_error');";

		$select = $this->rawdb->prepare($req);
		$select->execute(array());

		$rawDataCount = array();

		// For each table, count number of lines
		foreach ($select->fetchAll() as $row) {
			$tableName = $row['table_name'];

			$countReq = "SELECT count(*) FROM " . $tableName . " WHERE provider_id = ?";
			$count = $this->rawdb->prepare($countReq);
			$count->execute(array(
				$id
			));

			$nbLines = $count->fetchColumn();
			$rawDataCount[$tableName] = $nbLines;
		}

		return $rawDataCount;
	}
}
