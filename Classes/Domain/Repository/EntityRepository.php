<?php
namespace JO\JoMuseo\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EntityRepository extends Repository
{

    public function findAllByPid($pid)
    {
        if (!$pid) return null;

        $query = $this->createQuery();

        $pidList = GeneralUtility::intExplode(',', $pid, true);
        $query->getQuerySettings()->setStoragePageIds($pidList);

        return $query->execute();
    }
}
