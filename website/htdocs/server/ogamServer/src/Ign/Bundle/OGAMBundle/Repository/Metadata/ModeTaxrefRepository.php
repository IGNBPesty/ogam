<?php
namespace Ign\Bundle\OGAMBundle\Repository\Metadata;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Ign\Bundle\OGAMBundle\Entity\Metadata\Unit;
use Ign\Bundle\OGAMBundle\Entity\Metadata\ModeTaxref;

/**
 * ModeTaxrefRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ModeTaxrefRepository extends \Doctrine\ORM\EntityRepository {

	/**
	 * Returns the mode(s) corresponding to the unit (50 max).
	 *
	 * Note :
	 * Use that function only with units owning a short list of modes
	 * For units owning a long list of modes use the filtered functions (by code or query string)
	 *
	 * @param Unit $unit
	 *        	The unit
	 * @param String $locale
	 *        	The locale
	 *        	return Mode[] The unit mode(s)
	 */
	public function getModes(Unit $unit, $locale) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 'm');
		$params = [
			'unit' => $unit->getUnit(),
			'lang' => $locale
		];
		
		$sql = "SELECT unit, code, COALESCE(t.label, mt.label) as label, COALESCE(t.definition, mt.definition) as definition, position ";
		$sql .= " FROM mode_taxref as mt ";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'DYNAMODE' AND row_pk = :unit || ',' || mt.code) ";
		$sql .= " WHERE unit = :unit ";
		$sql .= " ORDER BY position ";
		$sql .= " LIMIT 50 ";
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameters($params);
		
		return $query->getResult();
	}

	/**
	 * Returns the mode(s) corresponding to the code(s).
	 *
	 * @param Unit $unit
	 *        	The unit
	 * @param String|Array $code
	 *        	The filter code(s)
	 * @param String $locale
	 *        	The locale
	 * @return Mode|[Mode] The filtered mode(s)
	 */
	public function getModesFilteredByCode(Unit $unit, $code, $locale) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 'mt');
		$parameters = array(
			'unit' => $unit->getUnit(),
			'lang' => $locale,
			'code' => $code
		);
		$sql = "SELECT unit, code, COALESCE(t.label, mt.label) as label, COALESCE(t.definition, mt.definition) as definition, position, parent_code, is_leaf, complete_name, vernacular_name, is_reference";
		$sql .= " FROM mode_taxref mt";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TAXREF' AND row_pk = mt.unit || ',' || mt.code) ";
		$sql .= " WHERE unit = :unit";
		if ($code != null) {
			if (is_array($code)) {
				$sql .= " AND code IN ( :code )";
			} else {
				$sql .= " AND code = :code";
			}
		}
		$sql .= " ORDER BY position, code";
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameters($parameters);
		
		return $query->getResult();
	}

	/**
	 * Returns the mode(s) whose label contains a portion of the search text
	 *
	 * @param Unit $unit
	 *        	The unit
	 * @param String $query
	 *        	The filter query string
	 * @param String $locale
	 *        	The locale
	 * @return [Mode] The filtered mode(s)
	 */
	public function getModesFilteredByLabel(Unit $unit, $query, $locale) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 'mt');
		$parameters = array(
			'unit' => $unit->getUnit(),
			'lang' => $locale,
			'query' => $query . '%'
		);
		$sql = "SELECT unit, code, COALESCE(t.label, mt.label) as label, COALESCE(t.definition, mt.definition) as definition, position, parent_code, is_leaf, complete_name, vernacular_name, is_reference";
		$sql .= " FROM mode_taxref mt";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TAXREF' AND row_pk = mt.unit || ',' || mt.code) ";
		$sql .= " WHERE unit = :unit AND COALESCE(t.label, mt.label) ilike :query ";
		$sql .= " ORDER BY position, code";
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameters($parameters);
		
		return $query->getResult();
	}

	/**
	 * Returns the mode(s) whose an part of thme is similare to the searched text.
	 * parts explored : label, vernacular_name,complete_name
	 * 
	 * @param Unit $unit
	 *        	The unit
	 * @param string $query
	 *        	The filter query string
	 * @param string $locale
	 *        	The locale
	 * @param int $start
	 *        	the offset (works with $limit)
	 * @param int $limit
	 *        	max mode return
	 * @return [Mode] filtered mode (eventually partial =>bound [$start .. $start+$limit])
	 */
	public function getTaxrefModesSimilarTo(Unit $unit, $query, $locale, $start = null, $limit = null) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 'mt');
		$parameters = array(
			'unit' => $unit->getUnit(),
			'lang' => $locale,
			'query' => $query,
			'query_parttern' => '%' . $query . '%'
		);
		$sql = "SELECT unit, code, COALESCE(t.label, mt.label) as label, COALESCE(t.definition, mt.definition) as definition, position, parent_code, is_leaf, complete_name, vernacular_name, is_reference";
		$sql .= " FROM mode_taxref mt";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TAXREF' AND row_pk = mt.unit || ',' || mt.code) ";
		$sql .= " WHERE unit = :unit AND (
            unaccent(COALESCE(t.label, mt.label)) ilike unaccent(:query_parttern)
            OR unaccent(vernacular_name) ilike unaccent(:query_parttern)
            OR unaccent(complete_name) ilike unaccent(:query_parttern)
            )";
		$sql .= " ORDER BY GREATEST(similarity(COALESCE(t.label, mt.label), :query), similarity(vernacular_name,:query) , similarity(complete_name, :query)) DESC, position, code";
		
		if ($start !== null && $limit !== null) {
			$sql .= ' LIMIT :limit OFFSET :offset';
			$parameters['limit'] = $limit;
			$parameters['offset'] = $start;
		}
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameters($parameters);
		
		return $query->getResult();
	}

	/**
	 * Return the count of code for a taxref filtered by query.
	 *
	 * @param String $unit
	 *        	The unit
	 * @param String $query
	 *        	the searched text (optional)
	 * @param String $locale
	 *        	The locale
	 * @return Integer
	 */
	public function getTaxrefModesCount($unit, $query = null, $locale) {
		$sql = "SELECT count(*) as count";
		$sql .= " FROM mode_taxref mt";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TAXREF' AND row_pk = mt.unit || ',' || mt.code) ";
		$sql .= " WHERE unit = :unit AND (
            unaccent(COALESCE(t.label, mt.label)) ilike unaccent(:query)
            OR unaccent(vernacular_name) ilike unaccent(:query)
            OR unaccent(complete_name) ilike unaccent(:query)
            )";
		$parameters = array(
			'unit' => $unit->getUnit(),
			'lang' => $locale,
			'query' => '%' . $query . '%'
		);
		
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addScalarResult('count', 'count', 'integer');
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameters($parameters);
		
		return $query->getSingleScalarResult();
	}

	/**
	 * Get all the children Modes from the reference taxon of a taxon.
	 * Used when building an SQL WHERE clause for a node of the taxref.
	 *
	 * Return an array of codes.
	 *
	 * @param String $unit
	 *        	The unit
	 * @param String $code
	 *        	The identifier of the start node in the tree (by default the root node is *)
	 * @param Integer $levels
	 *        	The number of levels of depth (if 0 then no limitation)
	 * @param String $locale
	 *        	The locale
	 * @return Array[ModeTaxref]
	 */
	public function getTaxrefChildrenModes(Unit $unit, $code = '*', $levels = 1, $locale) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 'mt');
		
		if ($code === '*') { // fakeroot
			$firstNode = "	SELECT '*'::character varying, 1 ";
		} else {
			$firstNode = "	SELECT code, 1 ";
			$firstNode .= "	FROM mode_taxref mt ";
			$firstNode .= "	WHERE unit = :unit ";
			$firstNode .= "	AND code = :code ";
		}
		
		$sql = "WITH RECURSIVE node_list( code, level) AS ( ";
		$sql .= $firstNode;
		$sql .= "	UNION ALL ";
		$sql .= "		SELECT child.code, level + 1 ";
		$sql .= "		FROM mode_taxref child ";
		$sql .= "		INNER JOIN node_list on (child.parent_code = node_list.code) ";
		$sql .= "		WHERE child.unit = :unit ";
		if ($levels != 0) {
			$sql .= " AND level < :levels ";
		}
		$sql .= "	) ";
		$sql .= "	SELECT mt.unit, mt.code, COALESCE(t.label, mt.label) as label, COALESCE(t.definition, mt.definition) as definition, position, parent_code, is_leaf, complete_name, vernacular_name, is_reference ";
		$sql .= "	FROM node_list nl ";
		$sql .= "   LEFT JOIN mode_taxref mt ON mt.code = nl.code AND mt.unit = :unit ";
		$sql .= "   LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TAXREF' AND row_pk = mt.unit || ',' || mt.code) ";
		$sql .= "	ORDER BY level, code "; // level is used to ensure correct construction of the structure
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameters(array(
			'unit' => $unit,
			'code' => $code,
			'lang' => $locale,
			'levels' => $levels
		));
		
		return $query->getResult();
	}
}
