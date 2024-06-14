<?php
namespace JO\JoMuseo\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class AnnotationRepository extends Repository
{
    public function findDemanded($demand = [])
    {
        $query = $this->createQuery();
        if (!empty($demand)) {
        	if (isset($demand['data'])) {
        		$subquery = [];
        		if (isset($demand['data']['feuser'])) {
		            $subquery[] = $query->equals('feuser', $demand['data']['feuser']);
		        }
		        if (isset($demand['data']['entityid'])) {
		            $subquery[] = $query->equals('entityid', $demand['data']['entityid']);
		        }
	        	$query->matching(
			        $query->logicalAnd(
			        	$subquery
			        )
			    );
			}
	        if (isset($demand['limit'])) {
	            $query->setLimit((int) $demand['limit']);
	        }
	       	if (isset($demand['order'])) {
	        	$query->setOrderings($demand['order']);
	        }
        }
        return $query->execute();
    }
}
