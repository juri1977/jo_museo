<?php
namespace JO\JoMuseo\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class CollectorboxRepository extends Repository
{
    public function findDemanded($demand = [])
    {
        $query = $this->createQuery();

        if (!empty($demand)) {
        	if (isset($demand['data'])) {
		        $pidList = explode(',', $demand['data']['pid']);
		        $query->getQuerySettings()->setStoragePageIds($pidList);
        		
        		$subquery = [];
        		if (isset($demand['data']['feuserid'])) {
		            $subquery[] = $query->equals('feuserid', $demand['data']['feuserid']);
		        }
		        if (isset($demand['data']['project'])) {
		            $subquery[] = $query->equals('project', $demand['data']['project']);
		        }
	        	$query->matching(
			        $query->logicalAnd(
			        	$subquery
			        )
			    );
			}

        }
        return $query->execute();
    }
}
