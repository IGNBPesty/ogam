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
 * A Form Query is the list of criterias and result columns composing a request from the use.
 *
 * @package objects
 * @SuppressWarnings checkUnusedVariables
 */
class Genapp_Object_Generic_FormQuery {

	/**
	 * The dataset identifier.
	 * String.
	 */
	var $datasetId;

	/**
	 * The criterias.
	 * Array[FormField].
	 */
	var $criterias = array();

	/**
	 * The asked results.
	 * Array[FormField].
	 */
	var $results = array();

	/**
	 * Add a new criteria.
	 *
	 * @param String $format the criteria form format
	 * @param String $data the criteria form data
	 * @param String $value the criteria value
	 */
	public function addCriteria($format, $data, $value) {
		$field = new Genapp_Object_Metadata_FormField();
		$field->format = $format;
		$field->data = $data;
		$field->value = $value;
		$this->criterias[] = $field;
	}

	/**
	 * Add a new result.
	 *
	 * @param String $format the result form format
	 *  @param String $data the result form data
	 */
	public function addResult($format, $data) {
		$field = new Genapp_Object_Metadata_FormField();
		$field->format = $format;
		$field->data = $data;
		$this->results[] = $field;
	}

	/**
	 * Get all table fields.
	 *
	 * @return Array[FormField] the form fields
	 */
	public function getFields() {
		return array_merge($this->criterias, $this->results);
	}
	
	/**
	 * Get the criterias.
	 *
	 * @return Array[FormField] the form fields
	 */
	public function getCriterias() {
		return $this->criterias;
	}
	
	/**
	 * Get the result columns.
	 *
	 * @return Array[FormField] the form fields
	 */
	public function getResults() {
		return $this->results;
	}

}
