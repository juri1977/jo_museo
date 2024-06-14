<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference as FileReferenceOrig;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class FileReference extends FileReferenceOrig
{
    /**
     * related
     *
     * @var ObjectStorage<FileReferenceOrig>
     */
    protected $related;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->setRelated(new ObjectStorage);
    }

    /**
     * Returns the related
     *
     * @return ObjectStorage<FileReferenceOrig> $related
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Sets the related
     *
     * @param ObjectStorage $related
     * @return void
     */
    public function setRelated($related)
    {
        $this->related = $related;
    }

    /**
     * Adds a related
     *
     * @param FileReferenceOrig $related
     * @return void
     */
    public function addRelated(FileReferenceOrig $related)
    {
        $this->related->attach($related);
    }

    /**
     * Removes a related
     *
     * @param FileReferenceOrig $relatedToRemove The related to be removed
     * @return void
     */
    public function removeRelated(FileReferenceOrig $related)
    {
        $this->related->detach($related);
    }
}
