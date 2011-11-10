<?php
/**
 * © French National Forest Inventory
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 */

/**
 * Model used to access the system tables of PostgreSQL.
 * @package models
 */
class Application_Model_System_Postgresql extends Zend_Db_Table_Abstract {

	var $logger;

	/**
	 * Initialisation
	 */
	public function init() {

		// Initialise the logger
		$this->logger = Zend_Registry::get("logger");
	}



	/**
	 * List the tables.
	 *
	 * @return The list of tables
	 * @throws an exception if the request is not found
	 */
	public function getTables() {
		$db = $this->getAdapter();

		$tables = array();

		// Get the request
		$req = " SELECT     UPPER(table_name) AS table, ";
		$req .= "           UPPER(table_schema) AS schema, ";
		$req .= "           UPPER(constraint_name) as primary_key, ";
		$req .= "           string_agg(UPPER(c.column_name),',') as pk_columns ";
		$req .= " FROM information_schema.tables ";
		$req .= " LEFT JOIN information_schema.table_constraints USING (table_catalog, table_schema, table_name) ";
		$req .= " LEFT JOIN information_schema.constraint_column_usage AS c USING (table_catalog, table_schema, table_name, constraint_name)  ";
		$req .= " WHERE table_type = 'BASE TABLE' ";
		$req .= " AND table_schema NOT IN ('pg_catalog', 'information_schema', 'metadata') ";
		$req .= " AND constraint_type = 'PRIMARY KEY' ";
		$req .= " GROUP BY table_name, table_schema, constraint_name ";

		$this->logger->info('getTables : '.$req);

		$query = $db->prepare($req);
		$query->execute(array());

		$results = $query->fetchAll();
		foreach ($results as $result) {
				
			$table = new Application_Object_System_Table();

			$table->tableName = $result['table'];
			$table->schemaName = $result['schema'];
			$table->setPrimaryKeys($result['pk_columns']);

			$tables[$table->schemaName.'_'.$table->tableName] = $table;

		}

		return $tables;

	}

	/**
	 * List the data columns.
	 *
	 * @return The list of data
	 * @throws an exception if the request is not found
	 */
	public function getFields() {
		$db = $this->getAdapter();

		$fields = array();

		// Get the request
		$req = " SELECT 	UPPER(column_name) AS column, ";
		$req .= "           UPPER(table_schema) AS schema, ";
		$req .= "           UPPER(table_name) AS table, ";
		$req .= "           UPPER(data_type) AS type ";
		$req .= " FROM information_schema.columns ";
		$req .= " INNER JOIN information_schema.tables using (table_catalog, table_schema, table_name) ";
		$req .= " WHERE table_type = 'BASE TABLE' ";
		$req .= " AND table_schema NOT IN ('pg_catalog', 'information_schema', 'metadata') ";

		$this->logger->info('getFields : '.$req);

		$query = $db->prepare($req);
		$query->execute(array());

		$results = $query->fetchAll();
		foreach ($results as $result) {

			$field = new Application_Object_System_Field();

			$field->columnName = $result['column'];
			$field->tableName = $result['table'];
			$field->schemaName = $result['schema'];
			$field->type = $result['type'];

			$fields[$field->schemaName.'_'.$field->tableName.'_'.$field->columnName] = $field;

		}

		return $fields;

	}


	/**
	 * List the foreign keys in the database.
	 *
	 * @return The list of data
	 * @throws an exception if the request is not found
	 */
	public function getForeignKeys() {
		$db = $this->getAdapter();

		$keys = array();

		// Get the request
		$req = " SELECT UPPER(tc.table_name) as table, UPPER(ccu.table_name) as source_table, string_agg(UPPER(kcu.column_name),',') as keys ";
		$req .= " FROM information_schema.table_constraints tc ";
		$req .= " LEFT JOIN information_schema.key_column_usage kcu USING (constraint_catalog, constraint_schema, constraint_name) ";
		$req .= " LEFT JOIN information_schema.referential_constraints rc USING (constraint_catalog, constraint_schema, constraint_name) ";
		$req .= " LEFT JOIN information_schema.constraint_column_usage ccu USING (constraint_catalog, constraint_schema, constraint_name, column_name) ";
		$req .= " WHERE constraint_type = 'FOREIGN KEY' ";
		$req .= " AND tc.table_schema NOT IN ('pg_catalog', 'information_schema', 'metadata', 'website') ";
		$req .= " GROUP BY tc.table_name, ccu.table_name ";

		$this->logger->info('getForeignKeys : '.$req);

		$query = $db->prepare($req);
		$query->execute(array());

		$results = $query->fetchAll();
		foreach ($results as $result) {

			$key = new Application_Object_System_ForeignKey();
			
			$this->logger->info('found  : '.$result['table']);

			$key->table = $result['table'];
			$key->sourceTable = $result['source_table'];
			$key->setForeignKeys($result['keys']);

			$keys[$key->table.'__'.$key->sourceTable] = $key;

		}

		return $keys;

	}



}
