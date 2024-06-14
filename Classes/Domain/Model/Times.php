<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Section
 *
 */
class Times extends AbstractEntity
{
    /**
     * title
     *
     * @var string
     */
    protected $title;
	
    public function getTitle()
    {
        return $this->title;
    }	
}
