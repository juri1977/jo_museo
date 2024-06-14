<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Section
 *
 */
class Topic extends AbstractEntity
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
