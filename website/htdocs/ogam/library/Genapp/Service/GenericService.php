<?php
/**
 * © French National Forest Inventory
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 */

/**
 * The Generic Service.
 *
 * This service handles transformations between data objects and generate generic SQL requests from the metadata.
 *
 * @package classes
 */
class Genapp_Service_GenericService {

	/**
	 * The logger.
	 */
	var $logger;

	/**
	 * The models.
	 */
	var $metadataModel;

	/**
	 * The projection systems.
	 */
	var $databaseSRS;
	var $visualisationSRS;

	/**
	 * Constructor.
	 */
	function Genapp_Service_GenericService() {

		// Initialise the logger
		$this->logger = Zend_Registry::get("logger");

		// Initialise the metadata models
		$this->metadataModel = new Genapp_Model_DbTable_Metadata_Metadata();

		// Configure the projection systems
		$configuration = Zend_Registry::get("configuration");
		$this->visualisationSRS = $configuration->srs_visualisation;
		$this->databaseSRS = $configuration->srs_raw_data;
	}

	/**
	 * Serialize the data object as a JSON string.
	 *
	 * @param DataObject $data the data object we're looking at.
	 * @param String $dataset the dataset identifier (optional), limit the children to the current dataset.
	 * @return JSON
	 */
	public function datumToDetailJSON($data, $datasetId = null) {

		$this->logger->info('datumToDetailJSON');

		// Get children for the current dataset
		$this->genericModel = new Genapp_Model_DbTable_Generic_Generic();
		$children = $this->genericModel->getChildren($data, $datasetId);

		$childrenCount = 0;
		if(!empty($children)){
			$childrenCount = count(current($children));
		}
		$json = '{"title":'.json_encode($data->tableFormat->label, JSON_HEX_APOS).', "children_count":'.$childrenCount.', "id":"'.$this->getIdFromData($data).'", "fields":[';
		$fields = '';
		// Get the form field corresponding to the table field
		$formFields = $this->getFormFieldsOrdered($data->getFields());
		foreach ($formFields as $formField) {
			// Add the corresponding JSON
			$fields .= $formField->toDetailJSON().",";
		}
		// remove last comma
		if ($fields != '') {
			$fields = substr($fields, 0, -1);
		} else {
			return '';
		}
		$json .= $fields."]}";

		return $json;
	}

	/**
	 * Build and return the datum id
	 *
	 * @param DataObject $datum The datum
	 */
	public function getIdFromData($datum) {
		$datumId = 'SCHEMA/'.$datum->tableFormat->schemaCode.'/FORMAT/'.$datum->tableFormat->format;
		foreach ($datum->infoFields as $field) {
			$datumId .= '/'.$field->data.'/'.$field->value;
		}
		return $datumId;
	}

	/**
	 * Serialize a list of data objects as a JSON array for a display into a Ext.GridPanel.
	 *
	 * @param String $id the id for the returned dataset
	 * @param List[DataObject] $data the data object we're looking at.
	 * @return JSON
	 */
	public function dataToGridDetailJSON($id, $data) {

		$this->logger->info('dataToDetailJSON');

		$json = "";

		if (!empty($data)) {

			// The columns config to setup the grid columnModel
			$columns = array();
			// The columns max length to setup the column width
			$columnsMaxLength = array();
			// The fields config to setup the store reader
			$locationFields = array('id');
			// The data to full the store
			$locationsData = array();
			$firstData = $data[0];

			// Dump each row values
			foreach ($data as $datum) {
				$locationData = array();
				// Addition of the row id
				$locationData[0] = $this->getIdFromData($datum);
				$formFields = $this->getFormFieldsOrdered($datum->getFields());
				foreach ($formFields as $formField) {
					// We keep only the result fields (The columns availables)
					array_push($locationData, $formField->valueLabel);
					if (empty($columnsMaxLength[$formField->data])) {
						$columnsMaxLength[$formField->data] = array();
					}
					array_push($columnsMaxLength[$formField->data], strlen($formField->valueLabel));
				}
				array_push($locationsData, $locationData);
			}

			// Add the colums description
			foreach ($formFields as $field) {
				// Set the column model and the location fields
				$dataIndex = $firstData->tableFormat->format.'__'.$field->data;
				// Adds the column header to prevent it from being truncated too
				array_push($columnsMaxLength[$field->data], strlen($field->label));
				$column = array(
                    'header' => $field->label,
                    'dataIndex' => $dataIndex,
                    'editable' => false,
                    'tooltip' => $field->definition,
                    'width' => 150 //max($columnsMaxLength[$field->data]) * 7
				);
				array_push($columns, $column);
				array_push($locationFields, $dataIndex);
			}

			// Check if the table has a child table
			$hasChild = false;
			$children = $this->metadataModel->getChildrenTableLabels($firstData->tableFormat);
			if (!empty($children)) {
				$hasChild = true;
			}
			return '{'.'success:true'.', id:'.json_encode($id).', title:'.json_encode($firstData->tableFormat->label.' ('.count($locationsData).')').', hasChild:'.json_encode($hasChild).', columns:'.json_encode(array_values($columns)).', fields:'.json_encode(array_values($locationFields)).', data:'.json_encode(array_values($locationsData)).'}';
		} else {
			return '{success:true, id:null, title:null, hasChild:false, columns:[], fields:[], data:[]}';
		}
	}

	/**
	 * Return the form fields mapped to the table fields and ordered by position
	 *
	 * @param array $tableFields The table fields
	 * @return array The form fields ordered
	 */
	public function getFormFieldsOrdered(array $tableFields){
		$fieldsOrdered = array();
		foreach ($tableFields as $tableField) {
			// Get the form field corresponding to the table field
			$formField = $this->getTableToFormMapping($tableField, true);
			if ($formField != null && $formField->isResult) {
				$fieldsOrdered[] = $formField;
			}
		}
		return array_values($fieldsOrdered);
	}

	/**
	 * Get the form field corresponding to the table field.
	 *
	 * @param TableField $tableField the table field
	 * @param Boolean $copyValues is true the values will be copied
	 * @return FormField
	 */
	public function getTableToFormMapping($tableField, $copyValues = false) {

		// Get the description of the form field
		$formField = $this->metadataModel->getTableToFormMapping($tableField);

		// Clone the object to avoid modifying existing object
		if ($formField != null) {
			$formField = clone $formField;
		}

		// Copy the values
		if ($copyValues == true && $formField != null && $tableField->value != null) {

			// Copy the value
			$formField->value = $tableField->value;

			// Fill the label
			if ($formField->type == "CODE") {
				$formField->valueLabel = $this->metadataModel->getMode($tableField->unit, $tableField->value);
			} else {
				$formField->valueLabel = $tableField->value;
			}

		}

		return $formField;
	}

	/**
	 * Get the table field corresponding to a form field.
	 *
	 * @param String $schema the schema
	 * @param FormField $formField the form field
	 * @param Boolean $copyValues is true the values will be copied
	 * @return TableField
	 */
	public function getFormToTableMapping($schema, $formField, $copyValues = false) {

		// Get the description of the corresponding table field
		$tableField = $this->metadataModel->getFormToTableMapping($schema, $formField);

		// Clone the object to avoid modifying existing object
		if ($tableField != null) {
			$tableField = clone $tableField;
		}

		// Copy the values
		if ($copyValues == true && $tableField != null && $formField->value != null) {

			// Copy the value
			$tableField->value = $formField->value;
		}

		return $tableField;
	}

	/**
	 * Generate the SQL request corresponding to a list of parameters
	 *
	 * @param String $schema the schema
	 * @param DataObject $dataObject the query object (list of TableFields)
	 * @return String a SQL request
	 */
	public function generateSQLFromWhereRequest($schema, $dataObject) {

		$this->logger->debug('generateSQLFromWhereRequest');

		//
		// Prepare the FROM clause
		//

		// Prepare the list of needed tables
		$tables = $this->getAllFormats($schema, $dataObject);

		// Add the root table;
		$rootTable = array_shift($tables);
		$from = " FROM ".$rootTable->tableName." ".$rootTable->getLogicalName();

		// Add the joined tables
		$i = 0;
		foreach ($tables as $tableFormat => $tableTreeData) {
			$i++;

			// Join the table
			$from .= " JOIN ".$tableTreeData->tableName." ".$tableTreeData->getLogicalName()." on (";

			// Add the join keys
			$keys = explode(',', $tableTreeData->keys);
			foreach ($keys as $key) {
				$from .= $tableTreeData->getLogicalName().".".trim($key)." = ".$tableTreeData->parentTable.".".trim($key)." AND ";
			}
			$from = substr($from, 0, -5);
			$from .= ") ";

		}

		//
		// Prepare the WHERE clause
		//
		$where = " WHERE (1 = 1) ";
		foreach ($dataObject->infoFields as $tableField) {
			$where .= $this->buildWhereItem($tableField, true);
		}

		$sql = $from.$where;

		// Return the completed SQL request
		return $sql;
	}

	/**
	 * Generate the SQL request corresponding the distinct locations of the query result.
	 *
	 * @param String $schema the schema
	 * @param DataObject $dataObject the query object (list of TableFields)
	 * @return String a SQL request
	 */
	public function generateSQLSelectRequest($schema, $dataObject) {

		$this->logger->debug('generateSQLSelectRequest');

		//
		// Prepare the SELECT clause
		//
		$select = "SELECT DISTINCT "; // distinct for the case where we have some criterias but no result columns selected o the last table
		foreach ($dataObject->editableFields as $tableField) {
			$select .= $this->buildSelectItem($tableField).", ";
		}
		$select = substr($select, 0, -2);

		//
		// Create a unique identifier for each line
		// We use the last column of the leaf table
		//
		// Get the left table;
		$tables = $this->getAllFormats($schema, $dataObject);
		$reversedTable = array_reverse($tables); // Only variables should be passed by reference
		$leftTable = array_shift($reversedTable);

		$uniqueId = "'SCHEMA/".$schema."/FORMAT/".$leftTable->getLogicalName()."'";

		$identifiers = explode(',', $leftTable->identifiers);
		foreach ($identifiers as $identifier) {
			$identifier = trim($identifier);
			// Concatenate the column to create a unique Id
			$uniqueId .= " || '/' || '".$identifier."/' ||".$leftTable->getLogicalName().".".trim($identifier);
		}
		$select .= ", ".$uniqueId." as id";

		// Detect the column containing the geographical information
		$locationField = $this->metadataModel->getLocationTableFields($schema, array_keys($tables));

		// Add the location centroid (for zooming on the map)
		$select .= ", astext(centroid(st_transform(".$locationField->format.".".$locationField->columnName.",".$this->visualisationSRS."))) as location_centroid ";

		// Return the completed SQL request
		return $select;
	}

	/**
	 * Build the WHERE clause corresponding to a list of criterias.
	 *
	 * @param Array[TableField] $criterias the criterias.
	 * @return String the WHERE part of the SQL query
	 */
	public function buildWhere($criterias) {

		$sql = "";

		// Build the WHERE clause with the info from the PK.
		foreach ($criterias as $tableField) {
			$sql .= $this->buildWhereItem($tableField, false); // exact match
		}

		return $sql;
	}

	/**
	 * Build a WHERE criteria for a single numeric value.
	 *
	 * @param TableField $tableField a criteria field.
	 * @param String $value a numeric criterium.
	 *
	 * Examples of values :
	 * 12
	 * 12.5
	 * 12.5 - 17.9  (will generate a min - max criteria)
	 */
	private function _buildNumericWhereItem($tableField, $value) {

		$sql = "";
		$pos = strpos($value, " - ");
		if ($pos != false) {

			$minValue = substr($value, 0, $pos);
			$maxValue = substr($value, $pos + 3);

			if (!empty($minValue)) {
				$sql .= $tableField->format.".".$tableField->columnName." >= ".$minValue." ";
			}
			if (!empty($maxValue)) {
				if ($sql != "") {
					$sql .= ' AND ';
				}
				$sql .= $tableField->format.".".$tableField->columnName." <= ".$maxValue." ";
			}

		} else {
			// One value, we make an equality comparison
			$sql .= $tableField->format.".".$tableField->columnName." = ".$value;

		}

		return $sql;

	}

	/**
	 * Return the SQL String representation of an array.
	 *
	 * Example : Array ( [0] => Boynes, [1] => Ascoux ) =>  {Boynes, Vrigny}
	 *
	 * @param Array[String] $value an array of values.
	 * @return the String representation of the array
	 */
	public function arrayToSQLString($arrayValues) {
		$string = "{";
		foreach ($arrayValues as $value) {
			$string .= $value.",";
		}
		$string = substr($string, 0, -1); // Remove last comma
		$string .= "}";

		return $string;
	}

	/**
	 * Return an Array object corresponding to a SQL string.
	 *
	 * Example : {Boynes, Vrigny} => Array ( [0] => Boynes, [1] => Ascoux )
	 *
	 * @param String $value an array of values.
	 * @return the String representation of the array
	 */
	public function stringToArray($value) {
		$values = str_replace("{", "", $value);
		$values = str_replace("}", "", $values);
		$values = trim($values);
		$valuesArray = explode(",", $values);

		foreach ($valuesArray as $v) {
			$v = trim($v);
		}

		return $valuesArray;

	}

	/**
	 * Build a WHERE criteria for a single date value.
	 *
	 * @param TableField $tableField a criteria field.
	 * @param String $value a date criterium.
	 *
	 * Examples of values :
	 * YYYY/MM/DD : for equality
	 * >= YYYY/MM/DD : for the superior value
	 * <= YYYY/MM/DD : for the inferior value
	 * YYYY/MM/DD - YYYY/MM/DD : for the interval
	 */
	private function _buildDateWhereItem($tableField, $value) {

		$sql = "";
		$value = trim($value);
		$column = $tableField->format.".".$tableField->columnName;

		if (!empty($value)) {
			if (strlen($value) == 10) {
				// Case "YYYY/MM/DD"
				if (Zend_Date::isDate($value, 'YYYY/MM/DD')) {
					// One value, we make an equality comparison
					$sql .= $column." = to_date('".$value."', 'YYYY/MM/DD') ";
				}
			} else if (strlen($value) == 13 && substr($value, 0, 2) == '>=') {
				// Case ">= YYYY/MM/DD"
				$beginDate = substr($value, 3, 10);
				if (Zend_Date::isDate($beginDate, 'YYYY/MM/DD')) {
					$sql .= $column." >= to_date('".$beginDate."', 'YYYY/MM/DD') ";
				}
			} else if (strlen($value) == 13 && substr($value, 0, 2) == '<=') {
				// Case "<= YYYY/MM/DD"
				$endDate = substr($value, 3, 10);
				if (Zend_Date::isDate($endDate, 'YYYY/MM/DD')) {
					$sql .= $column." <= to_date('".$endDate."', 'YYYY/MM/DD') ";
				}
			} else if (strlen($value) == 23) {
				// Case "YYYY/MM/DD - YYYY/MM/DD"
				$beginDate = substr($value, 0, 10);
				$endDate = substr($value, 13, 10);
				if (Zend_Date::isDate($beginDate, 'YYYY/MM/DD') && Zend_Date::isDate($endDate, 'YYYY/MM/DD')) {
					$sql .= "(".$column." >= to_date('".$beginDate."', 'YYYY/MM/DD') AND  ".$column." <= to_date('".$endDate."', 'YYYY/MM/DD')) ";
				}
			}
		}

		if ($sql == "") {
			throw new Exception("Invalid data format");
		}

		return $sql;
	}

	/**
	 * Build the WHERE clause corresponding to one criteria.
	 *
	 * @param TableField $tableField a criteria.
	 * @param Boolean $useLike if true, use a like %% instead of an exact equal.
	 * @return String the WHERE part of the SQL query (ex : 'AND BASAL_AREA = 6.05')
	 */
	public function buildWhereItem($tableField, $useLike = false) {

		$sql = "";

		$value = $tableField->value;
		$column = $tableField->format.".".$tableField->columnName;

		if ($value != null && $value != '' && $value != array()) {

			switch ($tableField->type) {

				case "BOOLEAN":
					// Value is 1 or 0, stored in database as a char(1)
					if (is_array($value)) {
						$value = $value[0];
					}
					if (is_bool($value)) {
						$sql .= " AND ".$column." = '".$value."'";
					}
					break;

				case "DATE":
					// Numeric values
					if (is_array($value)) {
						// Case of a list of values
						$sql2 = '';
						foreach ($value as $val) {
							if (!empty($val)) {
								$sql2 .= $this->_buildDateWhereItem($tableField, $val)." OR ";
							}
						}
						if ($sql2 != '') {
							$sql2 = substr($sql2, 0, -4); // remove the last OR
							$sql .= " AND (".$sql2.")";
						}
					} else {
						// Single value
						if (!empty($value)) {
							$sql .= " AND ".$this->_buildDateWhereItem($tableField, $value);
						}
					}
					break;
					break;
				case "INTEGER":
				case "NUMERIC":
					// Numeric values
					if (is_array($value)) {
						// Case of a list of values
						$sql2 = '';
						foreach ($value as $val) {
							if ($val != null && $val != '' && (is_numeric($val) || is_string($value))) {
								$sql2 .= $this->_buildNumericWhereItem($tableField, $val)." OR ";
							}
						}
						if ($sql2 != '') {
							$sql2 = substr($sql2, 0, -4); // remove the last OR
							$sql .= " AND (".$sql2.")";
						}
					} else {
						// Single value
						if (is_numeric($value) || is_string($value)) {
							$sql .= " AND ".$this->_buildNumericWhereItem($tableField, $value);
						}
					}
					break;
				case "ARRAY":
					if (is_array($value)) {
						// Case of a list of values
						$stringValue = $this->arrayToSQLString($value);
						$sql .= " AND ".$column." @> '".$stringValue."'";
					} else if (is_string($value)) {
						// Single value
						$sql .= " AND ANY(".$column.") = '".$value."'";
					}

					break;
				case "CODE":
					if ($tableField->subtype == 'TREE') {
						if (is_array($value)) {
							$value = $value[0];
						}

						// Get all the children of a selected node
						$nodeCodes = $this->metadataModel->getTreeChildrenCodes($tableField->unit, $value, 0);

						$sql2 = '';
						foreach ($nodeCodes as $nodeCode) {
							$sql2 .= "'".$nodeCode."',";
						}
						$sql2 = substr($sql2, 0, -1); // remove last comma

						$sql .= " AND ".$column." IN (".$sql2.")";

					} else {

						// String
						if (is_array($value)) {
							// Case of a list of values
							$values = '';
							foreach ($value as $val) {
								if ($val != null && $val != '' && is_string($val)) {
									$values .= "'".$val."', ";
								}
							}
							if ($values != '') {
								$values = substr($values, 0, -2); // remove the last comma
								$sql .= " AND ".$column." IN (".$values.")";
							}
						} else {
							// Single value
							$sql .= " AND ".$column." = '".$value."'";
						}
					}
					break;
				case "GEOM":
					if (is_array($value)) {
						$value = $value[0];
					}
					$sql .= " AND ST_intersects(".$column.", transform(ST_GeomFromText('".$value."', ".$this->visualisationSRS."), ".$this->databaseSRS."))";
					break;
				case "STRING":
				default:
					// String
					if (is_array($value)) {
						// Case of a list of values
						$sql .= " AND (";
						foreach ($value as $val) {
							if ($useLike) {
								$sql .= $column." ILIKE '%".$val."%'";
							} else {
								$sql .= $column." = '".$val."'";
							}
							$sql .= " OR ";
						}
						$sql = substr($sql, 0, -4); // remove the last OR
						$sql .= ")";
					} else {
						if (is_string($value)) {
							// Single value
							$sql .= " AND ".$column;
							if ($useLike) {
								$sql .= " LIKE '%".$value."%'";
							} else {
								$sql .= " = '".$value."'";
							}
						}
					}
					break;
			}

		}

		return $sql;
	}

	/**
	 * Build the SELECT part for one field.
	 *
	 * @param TableField $field a table field descriptor.
	 * @return String the SELECT part corresponding to the field.
	 */
	public function buildSelectItem($field) {

		$sql = "";

		if ($field->type == "DATE") {
			$sql .= "to_char(".$field->format.".".$field->columnName.", 'YYYY/MM/DD') as ".$field->format."__".$field->data;
		} else if ($field->unit == "GEOM") {
			// Special case for THE_GEOM
			$sql .= "asText(st_transform(".$field->format.".".$field->columnName.",".$this->visualisationSRS.")) as location, ";
			$sql .= "asText(st_transform(".$field->format.".".$field->columnName.",".$this->visualisationSRS.")) as ".$field->format."__".$field->data.", ";
			$sql .= 'ymin(box2d(transform('.$field->format.".".$field->columnName.','.$this->visualisationSRS.'))) as '.$field->format."__".$field->data.'_y_min, ';
			$sql .= 'ymax(box2d(transform('.$field->format.".".$field->columnName.','.$this->visualisationSRS.'))) as '.$field->format."__".$field->data.'_y_max, ';
			$sql .= 'xmin(box2d(transform('.$field->format.".".$field->columnName.','.$this->visualisationSRS.'))) as '.$field->format."__".$field->data.'_x_min, ';
			$sql .= 'xmax(box2d(transform('.$field->format.".".$field->columnName.','.$this->visualisationSRS.'))) as '.$field->format."__".$field->data.'_x_max ';

		} else {
			$sql .= $field->format.".".$field->columnName." as ".$field->format."__".$field->data;
		}

		return $sql;

	}

	/**
	 * Build the SELECT clause.
	 *
	 * @param Array[TableFields] $tableFields a list of result columns.
	 * @return String the SELECT part of the SQL query
	 */
	public function buildSelect($tableFields) {

		$sql = "";

		// Iterate through the fields
		foreach ($tableFields as $field) {
			$sql .= $this->buildSelectItem($field).", ";
		}

		// Remove the last comma
		$sql = substr($sql, 0, -2);

		return $sql;
	}

	/**
	 * Build an empty data object.
	 *
	 * @param String $schema the name of the schema
	 * @param String $format the name of the format
	 * @param String $datasetId the dataset identifier
	 * @param Boolean $isForDisplay indicate if we only want to display the data or if for update/insert
	 * @return DataObject the DataObject structure (with no values set)
	 */
	public function buildDataObject($schema, $format, $datasetId = null, $isForDisplay = false) {

		// Prepare a data object to be filled
		$data = new Genapp_Model_Generic_DataObject();

		$data->datasetId = $datasetId;

		// Get the description of the table
		$data->tableFormat = $this->metadataModel->getTableFormat($schema, $format);

		// Get all the description of the Table Fields corresponding to the format
		$tableFields = $this->metadataModel->getTableFields($datasetId, $schema, $format);

		// Separate the keys from other values
		foreach ($tableFields as $tableField) {
			if (in_array($tableField->data, $data->tableFormat->primaryKeys)) {
				// Primary keys are displayed as info fields
				$data->addInfoField($tableField);
			} else {
				if ($isForDisplay || !$tableField->isCalculated) {
					// Fields that are calculated by a trigger should not be edited
					$data->addEditableField($tableField);
				}
			}
		}

		return $data;
	}

	/**
	 *  Transform the form request object into a table data object.
	 *
	 * @param String $schema the schema
	 * @param FormQuery $formQuery the list of form fields
	 * @return DataObject $dataObject a data object (with data from different tables)
	 */
	public function getFormQueryToTableData($schema, $formQuery) {

		$result = new Genapp_Model_Generic_DataObject();

		$result->datasetId = $formQuery->datasetId;

		foreach ($formQuery->criterias as $formField) {
			$tableField = $this->getFormToTableMapping($schema, $formField, true);
			$result->addInfoField($tableField);
		}

		foreach ($formQuery->results as $formField) {
			$tableField = $this->getFormToTableMapping($schema, $formField);
			$result->addEditableField($tableField);
		}

		return $result;
	}

	/**
	 *  Get the hierarchy of tables needed for a data object.
	 *
	 * @param String $schema the schema
	 * @param DataObject $dataObject the list of table fields
	 * @return Array[String => TableTreeData] The list of formats (including ancestors) potentially used
	 */
	public function getAllFormats($schema, $dataObject) {

		// Prepare the list of needed tables
		$tables = array();
		foreach ($dataObject->getFields() as $tableField) {

			if (!in_array($tableField->format, $tables)) {

				// Get the ancestors of the table
				$ancestors = $this->metadataModel->getTablesTree($tableField->format, $tableField->data, $schema);

				// Reverse the order of the list and store by indexing with the table name
				// The root table (LOCATION) should appear first
				$ancestors = array_reverse($ancestors);
				foreach ($ancestors as $ancestor) {
					$tables[$ancestor->getLogicalName()] = $ancestor;
				}

			}
		}

		return $tables;
	}

}