<?php
namespace JO\JoMuseo\Domain\Repository;

/**
 * The repository for Exhibition
 */
class ExhibitionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    protected $defaultOrderings = [
        'uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    ];
}
