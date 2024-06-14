<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Annotation extends AbstractEntity
{
    /**
     * koordinaten
     *
     * @var string
     */
    protected $coordinates;

    /**
     * asset
     *
     * @var string
     */
    protected $asset;

    /**
     * comment
     *
     * @var string
     */
    protected $comment;

    /**
     * frontend user
     *
     * @var int
     */
    protected $feuser;

    /**
     * entity id
     *
     * @var string
     */
    protected $entityid;

    /**
     * public
     *
     * @var int
     */
    protected $public;

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getFeuser()
    {
        return $this->feuser;
    }

    public function getEntityid()
    {
        return $this->entityid;
    }

    public function getPublic()
    {
        return $this->public;
    }

    public function getAsset()
    {
        return $this->asset;
    }

    // Setter

    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function setFeuser($feuser)
    {
        $this->feuser = $feuser;
    }

    public function setEntityid($entityid)
    {
        $this->entityid = $entityid;
    }

    public function setPublic($public)
    {
        $this->public = $public;
    }

    public function setAsset($asset)
    {
        $this->asset = $asset;
    }
}
