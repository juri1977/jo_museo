<?php
namespace JO\JoMuseo\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class InstituteRepository extends Repository
{

    public $demanded = array();

    protected $defaultOrderings = [
        'active' => QueryInterface::ORDER_DESCENDING,
        'sorting' => QueryInterface::ORDER_ASCENDING,
    ];

    public function findCategories($categories)
    {
        $query = $this->createQuery();
        $query->matching($query->in('classfication.uid', $categories));
        return $query->execute();
    }

    public function findWithLimit($property, $argument, $limit = 0, $order = [])
    {
        $query = $this->createQuery();
        $query->matching($query->equals($property, $argument));
        if ($limit) {
            $query->setLimit((int) $limit);
        }
        if (count($order) > 0) {
        	$query->setOrderings($order);
        }
        return $query->execute();
    }

    public function findDemanded($demand = []) : QueryResultInterface
    {
        $query = $this->createQuery();
        if (!empty($demand)) {
            if (isset($demand['data'])) {
                $subquery = [];
                if (isset($demand['data']['classfication'])) {
                    $subquery[] = $query->in('classfication.title', $demand['data']['classfication']);
                }
                if (isset($demand['data']['topicdb'])) {
                    $subquery[] = $query->in('topicdb.title', $demand['data']['topicdb']);
                }
                if (isset($demand['data']['times'])) {
                    $subquery[] = $query->in('times.title', $demand['data']['times']);
                }
                if (isset($demand['data']['keywords'])) {
                    $subquery[] = $query->in('keywords.title', $demand['data']['keywords']);
                }
                if (isset($demand['data']['active'])) {
                    $subquery[] = $query->in('active', $demand['data']['active']);
                }
                if(!empty($subquery)){
                    $query->matching(
                        $query->logicalAnd(
                            $subquery
                        )
                    );
                }
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
