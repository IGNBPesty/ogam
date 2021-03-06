<?php
namespace Ign\Bundle\OGAMBundle\Repository\Metadata;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Ign\Bundle\OGAMBundle\Entity\Generic\GenericField;

/**
 * TableFieldRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TableFieldRepository extends \Doctrine\ORM\EntityRepository {

	/**
	 * Get the list of table fields for a given table format.
	 * If the dataset is specified, we filter on the fields of the dataset.
	 *
	 * @param String $schema
	 *        	the schema identifier
	 * @param String $format
	 *        	the format
	 * @param String $datasetID
	 *        	the dataset identifier (optional)
	 * @param
	 *        	String
	 *        	The locale
	 * @return Array[TableField]
	 */
	public function getTableFields($schema, $format, $datasetID = null, $lang) {
		$binds = array(
			'schema' => $schema,
			'format' => $format
		);
		
		// Get the fields specified by the format
		$sql = "SELECT DISTINCT table_field.*, COALESCE(t.label, data.label) as label, data.unit, unit.type, unit.subtype, COALESCE(t.definition, data.definition) as definition ";
		$sql .= " FROM table_field ";
		if ($datasetID != null) {
			$sql .= " LEFT JOIN dataset_fields on (table_field.format = dataset_fields.format AND table_field.data = dataset_fields.data) ";
		}
		$sql .= " LEFT JOIN table_format on (table_format.format = table_field.format) ";
		$sql .= " LEFT JOIN data on (table_field.data = data.data) ";
		$sql .= " LEFT JOIN unit on (data.unit = unit.unit) ";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'DATA' AND row_pk = data.data) ";
		$sql .= " WHERE (1=1)";
		$binds['lang'] = $lang;
		if ($datasetID != null) {
			$sql .= " AND dataset_fields.dataset_id = :dataset ";
			$binds['dataset'] = $datasetID;
		}
		$sql .= " AND table_format.schema_code = :schema";
		$sql .= " AND table_field.format = :format";
		$sql .= " ORDER BY table_field.position ";
		
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 't');
		
		$query = $this->_em->createNativeQuery($sql, $rsm->addIndexBy('t', 'data'));
		/*
		 * //$dql = "SELECT f FROM $this->_entityName f ";
		 * $query = $this->createQueryBuilder('t', 't.data.id');
		 * $query->leftJoin('t.format', 'tf', Join::WITH, 'tf.is_primary = 1');
		 * $query->where('t.format = :format')->andWhere('tf.schema = :schema');
		 *
		 * $query->setParameters(array('schema' => $schema, 'format' => $format));
		 *
		 * if ($datasetID != null) {
		 * $query->leftJoin('DatasetField', 'df', Join::WITH, 'f.format = df.format AND f.format = df.format');
		 * $query->andWhere('df.id = :dataset');
		 * $query->setParameter('dataset', $datasetID);
		 * }
		 */
		
		$query->setParameters($binds);
		return $query->getResult();
	}

	/**
	 * Get the database field corresponding to the asked form field.
	 *
	 * @param String $schema
	 *        	the name of the schema (RAW_DATA or HARMONIZED_DATA)
	 * @param Application_Object_Metadata_FormField $formField
	 *        	the form field
	 * @param
	 *        	String
	 *        	The locale
	 * @return Application_Object_Metadata_TableField
	 */
	public function getFormToTableMapping($schema, GenericField $formField, $lang) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 't');
		
		$sql = " SELECT table_field.*, COALESCE(t.label, data.label) as label, COALESCE(t.definition, data.definition) as definition, unit.unit, unit.type, unit.subtype ";
		$sql .= " FROM field_mapping ";
		$sql .= " LEFT JOIN table_field on (field_mapping.dst_format = table_field.format AND field_mapping.dst_data = table_field.data) ";
		$sql .= " LEFT JOIN dataset_fields on (dataset_fields.format = table_field.format AND dataset_fields.data = table_field.data) ";
		$sql .= " LEFT JOIN data on (table_field.data = data.data)";
		$sql .= " LEFT JOIN unit on (data.unit = unit.unit)";
		$sql .= " LEFT JOIN translation t ON (lang = ? AND table_format = 'DATA' AND row_pk = data.data) ";
		$sql .= " WHERE src_format = ? ";
		$sql .= " AND src_data = ? ";
		$sql .= " AND schema_code = ? ";
		$sql .= " AND mapping_type = 'FORM'";
		$sql .= " ORDER BY table_field.position ";
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameters(array(
			$lang,
			$formField->getFormat(),
			$formField->getData(),
			$schema
		));
		
		return $query->getSingleResult();
	}

	/**
	 * Detect the column getting the geographical information in a list of tables.
	 * If the dataset is specified, we filter on the fields of the dataset.
	 * We always take the GEOM column the lowest in the hierarchy of tables.
	 *
	 * @param String $schema
	 *        	the schema identifier
	 * @param Array[String] $tables
	 *        	a list of table formats
	 * @return Application_Object_Metadata_TableField
	 * @throws an exception if the tables contain no geographical information
	 */
	public function getGeometryField($schema, $tables, $lang) {
		$tableFieldArray = $this->getGeometryFields($schema, $tables, $lang);
		return $tableFieldArray[0];
	}

	/**
	 * Detect the column getting the geographical information in a list of tables.
	 * If the dataset is specified, we filter on the fields of the dataset.
	 * We always take the GEOM column the lowest in the hierarchy of tables.
	 *
	 * @param String $schema
	 *        	the schema identifier
	 * @param Array[String] $tables
	 *        	a list of table formats
	 * @param
	 *        	String
	 *        	The locale
	 * @return Application_Object_Metadata_TableField
	 * @throws an exception if the tables contain no geographical information
	 */
	public function getGeometryFields($schema, $tables, $lang) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 't');
		
		// Get the fields specified by the format
		$sql = "SELECT DISTINCT table_field.*, COALESCE(t.label, data.label) as label, data.unit, unit.type, unit.subtype, COALESCE(t.definition, data.definition) as definition ";
		$sql .= " FROM table_field ";
		$sql .= " LEFT JOIN table_format on (table_field.format = table_format.format) ";
		$sql .= " LEFT JOIN data on (table_field.data = data.data) ";
		$sql .= " LEFT JOIN unit on (data.unit = unit.unit) ";
		$sql .= " LEFT JOIN translation t ON (t.lang = ? AND t.table_format = table_field.format AND t.row_pk = data.data)";
		$sql .= " WHERE table_field.format = ? ";
		$sql .= " AND table_format.schema_code = ? ";
		$sql .= " AND unit.type = 'GEOM' ";
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		
		// We do the seach table by table in the inverse order
		$tableFieldArray = array();
		foreach (array_reverse($tables) as $tableName) {
			$query->setParameters(array(
				$lang,
				$tableName,
				$schema
			));
			
			$tableField = $query->getResult();
			if ($tableField) {
				$tableFieldArray[] = $tableField[0];
			}
		}
		
		if (!empty($tableFieldArray)) {
			return $tableFieldArray;
		} else {
			// No GEOM column found
			throw new \Exception("No geographical information detected");
		}
	}
}
