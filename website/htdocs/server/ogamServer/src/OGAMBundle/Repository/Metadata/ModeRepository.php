<?php

namespace OGAMBundle\Repository\Metadata;

use OGAMBundle\Entity\Metadata\Unit;

/**
 * ModeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ModeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
    * Returns the mode(s) corresponding to the code(s).
    *
    * @param Unit $unit The unit
    * @param String|Array $code The filter code(s)
    * @param String $locale The locale
    * @return [Mode] The filtered mode(s)
    * */
    public function getModesFilteredByCode(Unit $unit, $code, $locale){
        return $this->findBy(array(
            'unit' => $unit->getUnit(),
            'mode' => $code
        ));
    }

}
