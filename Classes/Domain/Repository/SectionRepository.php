<?php
namespace JO\JoMuseo\Domain\Repository;

/**
 * The repository for Section
 */
class SectionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
	protected $defaultOrderings = array(
		'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
	);
}
