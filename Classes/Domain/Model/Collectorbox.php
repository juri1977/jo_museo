<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Collectorbox
 *
 */
class Collectorbox extends AbstractEntity
{
    /**
     * feuserid
     *
     * @var int
     */
    protected $feuserid;

    /**
     * boxdata
     *
     * @var string
     */
    protected $boxdata;

     /**
     * project
     *
     * @var string
     */
    protected $project;


    /**
     * uri
     *
     * @var feuserid
     */
    protected $uri;

    public function setFeuserid($feuserid)
    {
        $this->feuserid = $feuserid;
    }

    public function getFeuserid()
    {
        return $this->feuserid;
    }

    public function setBoxdata($boxdata)
    {
        $this->boxdata = $boxdata;
    }

    public function getBoxdata()
    {
        return $this->boxdata;
    }

    public function setProject($project)
    {
        $this->project = $project;
    }

    public function getProject()
    {
        return $this->project;
    }
}
