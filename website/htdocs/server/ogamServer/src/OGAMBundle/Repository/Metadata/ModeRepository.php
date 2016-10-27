<?php

namespace OGAMBundle\Repository\Metadata;

use OGAMBundle\Entity\Metadata\Unit;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * ModeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ModeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Returns the mode(s) corresponding to the unit (50 max).
     * 
     * Note :
     *   Use that function only with units owning a short list of modes
     *   For units owning a long list of modes use the filtered functions (by code or query string)
     * 
     * @param Unit $unit The unit
     * @param String $locale The locale
     * return Mode[] The unit mode(s)
     */
    public function getModes(Unit $unit, $locale)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata($this->_entityName, 'm');
        $params = [
            'unit' => $unit->getUnit(),
            'lang' => $locale
        ];
    
        $sql = "SELECT unit, code, COALESCE(t.label, m.label) as label, COALESCE(t.definition, m.definition) as definition, position ";
        $sql .= " FROM mode as m ";
        $sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'DYNAMODE' AND row_pk = :unit || ',' || m.code) ";
        $sql .= " WHERE unit = :unit ";
        $sql .= " LIMIT 50 ";
    
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters($params);
    
        return $query->getResult();
    }

   /**
    * Returns the mode(s) corresponding to the code(s).
    *
    * @param Unit $unit The unit
    * @param String|Array $code The filter code(s)
    * @param String $locale The locale
    * @return [Mode] The filtered mode(s)
    * */
    public function getModesFilteredByCode(Unit $unit, $code, $locale){
        $qb = $this->createQueryBuilder('m');
        $qb->select('m','t.label as label')
        ->leftJoin('OGAMBundle:Metadata\Translation', 't', Join::WITH, 't.lang = :lang  AND t.tableFormat = \'METADATA_MODE\' AND t.rowPk = CONCAT(CONCAT(m.unit , \',\'), m.code)')
        ->where('m.unit = :unit and m.code IN (:code)')->orderBy('m.position, m.code');
        $req = $qb->getQuery();
        $req->setParameters(array('unit'=>$unit, 'code'=>$code, 'lang'=>$locale));
        if (is_array($code)){
            $req->setParameter('code', $code, Connection::PARAM_STR_ARRAY);
        }
        $res = $req->getResult();
        foreach($res as $lign){
            $lign[0]->setLabel($lign['label']);
        }
        return array_column($res, 0);
    }

    /**
     * Returns the mode(s) whose label contains a portion of the search text
     *
     * @param Unit $unit The unit
     * @param String $query The filter query string
     * @param String $locale The locale
     * @return [Mode] The filtered mode(s)
     */
    public function getModesFilteredByLabel(Unit $unit, $query, $locale){
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata($this->_entityName, 'mt');

        $sql = "SELECT unit, code, COALESCE(t.label, m.label) as label, position, COALESCE(t.definition, m.definition) ";
        $sql .= " FROM mode m";
        $sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'METADATA_MODE' AND row_pk = m.unit || ',' || m.code)";
        $sql .= " WHERE unit = :unit ";
        $sql .= " AND COALESCE(t.label, m.label) ilike :query";
        $sql .= " ORDER BY position, code";

        $req = $this->_em->createNativeQuery( $sql, $rsm );

        $req->setParameter('unit', $unit)
        ->setParameter('query', $query.'%')
        ->setParameter('lang', $locale);
        return $req->getResult();
    }
}
