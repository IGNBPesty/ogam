<?php
/**
 * © French National Forest Inventory
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 */
require_once 'metadata/Format.php';

/**
 * Represent a Table Format (a table in a database).
 * @package classes
 */
class TableFormat extends Format {

	/**
	 * Indicate if the table is "column-oriented" (one line for each value).
	 */
	var $isColumnOriented;

	/**
	 * The real name of the table
	 */
	var $tableName;

	/**
	 * The schema.
	 */
	var $schemaCode;

	/**
	 * The primary key.
	 */
	var $primaryKeys = array();

}
