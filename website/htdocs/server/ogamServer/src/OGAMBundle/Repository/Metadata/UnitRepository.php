<?php
namespace OGAMBundle\Repository\Metadata;

use OGAMBundle\Entity\Metadata\Unit;
use OGAMBundle\OGAMBundle;
use OGAMBundle\Entity\Metadata\Mode;
use OGAMBundle\Entity\Metadata\Dynamode;
use OGAMBundle\Entity\Metadata\ModeTree;
use OGAMBundle\Entity\Metadata\ModeTaxref;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Selectable;
use phpDocumentor\Reflection\Types\Mixed;
use Doctrine\Common\Collections\Collection;

/**
 * UnitRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UnitRepository extends \Doctrine\ORM\EntityRepository
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
        /*
         * Must handle these cases :
         * CODE MODE
         * CODE DYNAMIC
         * CODE TREE
         * CODE TAXREF
         * ARRAY MODE
         * ARRAY DYNAMIC
         * ARRAY TREE
         * ARRAY TAXREF
         */
        if ($unit->getType() === "CODE" or $unit->getType() === "ARRAY") {
            switch ($unit->getSubtype()) {
                case "MODE":
                    return $this->_em->getRepository(Mode::class)->getModes($unit, $locale);
                case "DYNAMIC":
                    return $this->_em->getRepository(Dynamode::class)->getModes($unit, $locale);
                case "TREE":
                    return $this->_em->getRepository(ModeTree::class)->getModes($unit, $locale);
                case "TAXREF":
                    return $this->_em->getRepository(ModeTaxref::class)->getModes($unit, $locale);
                default:
                    return null;
            }
        }
    }

    /**
     * Returns the mode(s) corresponding to the code(s).
     *
     * @param Unit $unit The unit
     * @param String|Array $code The filter code(s)
     * @param String $locale The locale
     * @return Mode[] The filtered mode(s)
     */
    public function getModesFilteredByCode(Unit $unit, $code, $locale){
        /*
         * Must handle these cases :
         * CODE MODE
         * CODE DYNAMIC
         * CODE TREE
         * CODE TAXREF
         * ARRAY MODE
         * ARRAY DYNAMIC
         * ARRAY TREE
         * ARRAY TAXREF
         */
        if ($unit->getType() === "CODE" or $unit->getType() === "ARRAY") {
            switch ($unit->getSubtype()) {
                case "MODE":
                    return $this->_em->getRepository(Mode::class)->getModesFilteredByCode($unit, $code, $locale);
                case "DYNAMIC":
                    return $this->_em->getRepository(Dynamode::class)->getModesFilteredByCode($unit, $code, $locale);
                case "TREE":
                    return $this->_em->getRepository(ModeTree::class)->getModesFilteredByCode($unit, $code, $locale);
                case "TAXREF":
                    return $this->_em->getRepository(ModeTaxref::class)->getModesFilteredByCode($unit, $code, $locale);
                default:
                    return null;
            }
        }
    }

    /**
     * Returns the mode(s) whose label contains a portion of the search text
     *
     * @param Unit $unit The unit
     * @param String $query The filter query string
     * @param String $locale The locale
     * @return Mode[] The filtered mode(s)
     */
    public function getModesFilteredByLabel(Unit $unit, $query, $locale){
        /*
         * Must handle these cases :
         * CODE MODE
         * CODE DYNAMIC
         * CODE TREE
         * CODE TAXREF
         * ARRAY MODE
         * ARRAY DYNAMIC
         * ARRAY TREE
         * ARRAY TAXREF
         */
        if ($unit->getType() === "CODE" or $unit->getType() === "ARRAY") {
            switch ($unit->getSubtype()) {
                case "MODE":
                    return $this->_em->getRepository(Mode::class)->getModesFilteredByLabel($unit, $query, $locale);
                case "DYNAMIC":
                    return $this->_em->getRepository(Dynamode::class)->getModesFilteredByLabel($unit, $query, $locale);
                case "TREE":
                    return $this->_em->getRepository(ModeTree::class)->getModesFilteredByLabel($unit, $query, $locale);
                case "TAXREF":
                    return $this->_em->getRepository(ModeTaxref::class)->getModesFilteredByLabel($unit, $query, $locale);
                default:
                    return null;
            }
        }
    }

    /**
     * Returns an array of labels indexed by their codes (['code'=>'label']).
     * 
     * @param Unit $unit The unit
     * @param String|Array $code The filter code(s)
     * @param String $locale The locale
     * @return array The filtered modes labels
     */
    public function getModesLabelsFilteredByCode(Unit $unit, $code, $locale){
        $res = $this->getModesFilteredByCode($unit, $code, $locale);
        if ($res === null){
            return null;
        }

        if (false ===($res  instanceof  Collection)){
            $res= new ArrayCollection($res);
        }

        $array_res = $res->map(function ($element){
            return ['code'=>$element->getCode(), 'label'=>$element->getLabel()];
        });

        return  array_column($array_res->toArray(), 'label','code');
    }
}