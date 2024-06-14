<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Social
 *
 */
class Social extends AbstractEntity
{
    /**
     * type
     *
     * @var string
     */
    protected $type;

    /**
     * title
     *
     * @var string
     */
    protected $title;

    /**
     * uri
     *
     * @var string
     */
    protected $uri;

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }
}
