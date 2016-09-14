<?php

namespace OGAMBundle\Repository\Metadata;

use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * FormFieldRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FormFieldRepository extends \Doctrine\ORM\EntityRepository {
	/**
	 * Get the fields for a given form.
	 *
	 * @param String $dataset
	 *        	the name of the dataset
	 * @param String $formFormat
	 *        	the name of the form format
	 * @param String $schema
	 *        	the name of the database schema
	 * @param String $locale
	 *        	the locale
	 * @param String $query
	 *        	the filter text entered by the user (optional)
	 * @param Integer $start
	 *        	the number of the first row to return (optional)
	 * @param Integer $limit
	 *        	the max number of row to return (optional)
	 * @return Array[Application_Object_Metadata_FormField]
	 */
	public function getFormFields($dataset, $formFormat, $schema, $locale, $query = null, $start = null, $limit = null) {
		
		$rsm = new ResultSetMappingBuilder ( $this->_em );
		$rsm->addRootEntityFromClassMetadata ( 'OGAMBundle\Entity\Metadata\FormField', 'ff' );
		
		$param = array ();
		
		// Select the list of available fields for the table (excepted the FK)
		$sql = " SELECT DISTINCT form_field.*";
		$sql .= " FROM form_field ";
		$sql .= " LEFT JOIN data using (data) ";
		$sql .= " LEFT JOIN translation t ON (lang = ? AND table_format = 'DATA' AND row_pk = data.data) ";
		$param [] = $locale;

		// Check the field format
		$sql .= " WHERE format = ?";
		$param [] = $formFormat;
		if ($query != null) {
			$sql .= " AND unaccent(COALESCE(t.label, data.label)) ilike unaccent(?)";
			$param [] = '%' . $query . '%';
		}
		
		// If a dataset has been selected, filter the available options
		if (! empty ( $dataset )) {
			$sql .= " AND (data IN ( ";
			$sql .= " SELECT data ";
			$sql .= " FROM dataset_fields ";
			$sql .= " LEFT JOIN field_mapping on (dataset_fields.format = field_mapping.dst_format AND dataset_fields.data = field_mapping.dst_data AND mapping_type='FORM') ";
			$sql .= " WHERE dataset_id = ? ";
			$sql .= " AND schema_code = ? ";
			$sql .= " AND src_format = ? ";
			$sql .= " ) )";
			$param [] = $dataset;
			$param [] = $schema;
			$param [] = $formFormat;
		}
		
		$sql .= " ORDER BY form_field.position";
		
		if ($start !== null && $limit !== null) {
		    $sql .= " LIMIT ? OFFSET ? ";
		    $param [] = $limit;
		    $param [] = $start;
		}
		
		$query = $this->_em->createNativeQuery ( $sql, $rsm );
		$query->setParameters ( $param );
		
		return $query->getResult ();
	}
}
