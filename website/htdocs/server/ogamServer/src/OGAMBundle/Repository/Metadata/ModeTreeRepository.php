<?php
namespace OGAMBundle\Repository\Metadata;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use OGAMBundle\Entity\Metadata\Unit;
use OGAMBundle\Entity\Metadata\ModeTree;

/**
 * ModeTreeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ModeTreeRepository extends \Doctrine\ORM\EntityRepository {

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
		$sql .= " FROM mode_tree as mt ";
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
		$sql = "SELECT unit, code, COALESCE(t.label, mt.label) as label, COALESCE(t.definition, mt.definition) as definition, position, parent_code, is_leaf";
		$sql .= " FROM mode_tree mt";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TREE' AND row_pk = mt.unit || ',' || mt.code) ";
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
		$sql = "SELECT unit, code, COALESCE(t.label, mt.label) as label, COALESCE(t.definition, mt.definition) as definition, position, parent_code, is_leaf";
		$sql .= " FROM mode_tree mt";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TREE' AND row_pk = mt.unit || ',' || mt.code) ";
		$sql .= " WHERE unit = :unit AND COALESCE(t.label, mt.label) ilike :query ";
		$sql .= " ORDER BY position, code";
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameters($parameters);
		
		return $query->getResult();
	}

	/**
	 * Returns the mode(s) whose label contains the searched text
	 *
	 * @param Unit $unit
	 *        	The unit
	 * @param String $query
	 *        	The filter query string
	 * @param String $locale
	 *        	The locale
	 * @param int|null $start
	 *        	the offset (works with $limit)
	 * @param int|null $limit
	 *        	max # mode to return
	 * @return [Mode] The filtered mode(s)
	 */
	public function getTreeModesSimilareTo(Unit $unit, $query, $locale, $start = null, $limit = null) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 'mt');
		$parameters = array(
			'unit' => $unit->getUnit(),
			'lang' => $locale,
			'query' => $query,
			'querypattern' => '%' . $query . '%'
		);
		$sql = "SELECT unit, code, COALESCE(t.label, mt.label) as label, COALESCE(t.definition, mt.definition) as definition, position, parent_code, is_leaf";
		$sql .= " FROM mode_tree mt";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TREE' AND row_pk = mt.unit || ',' || mt.code) ";
		$sql .= " WHERE unit = :unit AND COALESCE(t.label, mt.label) ilike :querypattern ";
		$sql .= " ORDER BY similarity(COALESCE(t.label, mt.label), :query) DESC, position, code";
		
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
	 * Get the count of modes for a tree unit filtered by query.
	 *
	 * @param Unit $unit
	 *        	The unit
	 * @param String $query
	 *        	the searched text (optional)
	 * @param String $locale
	 *        	the locale
	 * @return Integer
	 */
	public function getTreeModesSimilareToCount(Unit $unit, $query, $locale) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addScalarResult('count', 'count', 'integer');
		$parameters = array(
			'unit' => $unit->getUnit(),
			'lang' => $locale,
			'query' => '%' . $query . '%'
		);
		$sql = "SELECT count(*)";
		$sql .= " FROM mode_tree mt";
		$sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TREE' AND row_pk = mt.unit || ',' || mt.code) ";
		$sql .= " WHERE unit = :unit AND COALESCE(t.label, mt.label) ilike :query ";
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameters($parameters);
		
		return $query->getSingleScalarResult();
	}

	/**
	 * Get all the children Modes from a node of a tree.
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
	 * @return Array[ModeTree]
	 */
	public function getTreeChildrenModes($unit, $code = '*', $levels = 1, $locale) {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->_entityName, 'mt');
		
		if ($code === '*') { // fakeroot
			$firstNode = " SELECT '*'::character varying, 1 ";
		} else {
			$firstNode = " SELECT code, 1 ";
			$firstNode .= "	FROM mode_tree mt ";
			$firstNode .= "	WHERE unit = :unit ";
			$firstNode .= "	AND code = :code ";
		}
		
		$sql = "WITH RECURSIVE node_list( code, level) AS ( ";
		$sql .= $firstNode;
		$sql .= "	UNION ALL ";
		$sql .= "		SELECT child.code, level + 1 ";
		$sql .= "		FROM mode_tree child ";
		$sql .= "		INNER JOIN node_list on (child.parent_code = node_list.code) ";
		$sql .= "		WHERE child.unit = :unit ";
		if ($levels != 0) {
			$sql .= " AND level < :levels ";
		}
		$sql .= "	) ";
		$sql .= "	SELECT mt.unit, mt.code, COALESCE(t.label, mt.label) as label, COALESCE(t.definition, mt.definition) as definition, position, parent_code, is_leaf";
		$sql .= "	FROM node_list nl ";
		$sql .= "   LEFT JOIN mode_tree mt ON mt.code = nl.code AND mt.unit = :unit ";
		$sql .= "   LEFT JOIN translation t ON (lang = :lang AND table_format = 'MODE_TREE' AND row_pk = mt.unit || ',' || mt.code) ";
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
