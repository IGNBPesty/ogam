<?php
namespace OGAMBundle\Repository\Website;

use OGAMBundle\Entity\Metadata\FormField;
use OGAMBundle\Entity\Metadata\Unit;

/**
 * PredefinedRequestCriterionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PredefinedRequestCriterionRepository extends \Doctrine\ORM\EntityRepository {

	/**
	 * Get the criteria of a predefined request.
	 *
	 * @param String $requestName
	 *        	the name of the request
	 * @return Array[PredefinedField] The list of request criterias
	 */
	public function getPredefinedRequestCriteria($requestName, $locale) {
		$qb = $this->_em->createQueryBuilder();
		$qb->select('prc')
			->from('OGAMBundle:Website\PredefinedRequestCriterion', 'prc')
			->where('prc.requestName = :request_name')
			->setParameters([
			'request_name' => $requestName
		]);
		
		$criteria = $qb->getQuery()->getResult();
		
		// Get the form fields associated to the criteria
		$formFieldRepository = $this->_em->getRepository(FormField::class);
		foreach ($criteria as $criterion) {
			$formField = $formFieldRepository->getFormField($criterion->getFormat(), $criterion->getData(), $locale);
			
			// Get the default value(s) Mode(s)
			$inputType = $formField->getInputType();
			$inputTypes = [
				'SELECT',
				'PAGINED_SELECT',
				'TREE',
				'TAXREF'
			];
			if (in_array($inputType, $inputTypes) && $criterion->getValue() !== null) {
				$unit = $formField->getData()->getUnit();
				$valuesModes = $this->_em->getRepository(Unit::class)->getModesFilteredByCode($unit, $criterion->getValue(), $locale);
				$unit->setModes($valuesModes);
			}
			$criterion->setFormField($formField);
		}
		
		return $criteria;
	}
}
