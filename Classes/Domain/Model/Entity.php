<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Entity extends AbstractEntity 
{
    /**
     * title
     *
     * @var string
     */
    protected $title;

    /**
     * stipendiat
     *
     * @var string
     */
    protected $stipendiat;

    /**
     * shorttext
     *
     * @var string
     */
    protected $shorttext;

     /**
     * bodytext
     *
     * @var string
     */
    protected $bodytext;

    /**
     * objecttype
     *
     * @var string
     */
    protected $objecttype;

    /**
     * datierungstart
     *
     * @var string
     */
    protected $datierungstart;

    /**
     * datierungend
     *
     * @var string
     */
    protected $datierungend;

    /**
     * geoplacegeojson
     *
     * @var string
     */
    protected $geoplacegeojson;    

    /**
     * linktosite
     *
     * @var string
     */
    protected $linktosite;

    /**
     * linkvideo
     *
     * @var string
     */
    protected $linkvideo;

    /**
     * exhibitcta
     *
     * @var string
     */
    protected $exhibitcta;
    
    /**
     * geoplace
     *
     * @var string
     */
    protected $geoplace;

    /**
     * roommodel
     *
     * @var string
     */
    protected $roommodel;

    /**
     * jsonstring
     *
     * @var string
     */
    protected $jsonstring;

    /**
     * additionalproperties
     *
     * @var string
     */
    protected $additionalproperties;

    /**
     * audio
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $audio;

    /**
     * video
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $video;


    /**
     * exhibits
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Exhibit>
     */
    protected $exhibit;
    // getter

     /**
     * Initializes all ObjectStorage properties
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->setExhibit(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setAudio(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
    }

    /**
     * Returns video
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $video
     */
    public function getVideo()
    {
        return $this->video;
    }


    /**
     * Returns Audio
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $audio
     */
    public function getAudio()
    {
        return $this->audio;
    }

    /**
     * Set Audio
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $audio
     */
    public function setAudio(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $audio)
    {
        $this->audio = $audio;
    }

    /**
     * Adds a Audio
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $audio
     * @return void
     */
    public function addAudio(\TYPO3\CMS\Extbase\Domain\Model\FileReference $audio)
    {
        $this->audio->attach($audio);
    }

    /**
     * Removes a Audio
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $audioToRemove The Audio to be removed
     * @return void
     */
    public function removeAudio(\TYPO3\CMS\Extbase\Domain\Model\FileReference $audio)
    {
        $this->audio->detach($audio);
    }

    /**
     * Returns the Exhibit
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Exhibit> $exhibit
     */
    public function getExhibit()
    {
        return $this->exhibit;
    }

    /**
     * Sets the Exhibit
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Exhibit> $exhibit
     * @return void
     */
    public function setExhibit(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $exhibit)
    {
        $this->exhibit = $exhibit;
    }

    /**
     * Adds a Exhibit
     *
     * @param \JO\JoMuseo\Domain\Model\Exhibit $exhibit
     * @return void
     */
    public function addExhibit(\JO\JoMuseo\Domain\Model\Exhibit $exhibit)
    {
        $this->exhibit->attach($exhibit);
    }

    /**
     * Removes a Exhibit
     *
     * @param \JO\JoMuseo\Domain\Model\Exhibit $exhibitToRemove The Exhibit to be removed
     * @return void
     */
    public function removeExhibit(\JO\JoMuseo\Domain\Model\Exhibit $exhibit)
    {
        $this->exhibit->detach($exhibit);
    }
    
    public function getAdditionalproperties()
    {
        // return $this->additionalproperties;
        $confArray = GeneralUtility::xml2array($this->additionalproperties);
        if(isset($confArray['data']['sDEF']['lDEF'])) return $confArray['data']['sDEF']['lDEF'];
    }

    public function getTitle()
    {
        return $this->title;
    }

     public function getExhibitcta()
    {
        return $this->exhibitcta;
    }

    public function getStipendiat()
    {
        return $this->stipendiat;
    }
    public function getLinktosite()
    {
        return $this->linktosite;
    }

    public function getLinkVideo()
    {
        return $this->linkvideo;
    }

    public function getGeoPlace()
    {
        return $this->geoplace;
    }

    public function getRoommodel()
    {
        return $this->roommodel;
    }

    public function getJsonstring()
    {
        return $this->jsonstring;
    }

    public function getShorttext()
    {
        return $this->shorttext;
    }

    public function getBodytext()
    {
        return $this->bodytext;
    }

    public function getObjecttype()
    {
        return $this->objecttype;
    }

    public function getDatierungstart()
    {
        return $this->datierungstart;
    }

    public function getDatierungend()
    {
        return $this->datierungend;
    }

    public function getGeoplacegeojson()
    {
        return $this->geoplacegeojson;
    }

    // setter

    public function setAdditionalproperties($additionalproperties)
    {
        $this->additionalproperties = $additionalproperties;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setStipendiat($stipendiat)
    {
        $this->stipendiat = $stipendiat;
    }

    public function setLinktosite($linktosite)
    {
        $this->linktosite = $linktosite;
    }

    public function setLinkVideo($linkvideo)
    {
        $this->linkvideo = $linkvideo;
    }

    public function setGeoPlace($geoplace)
    {
        $this->geoplace = $geoplace;
    }

    public function setJsonstring($jsonstring)
    {
        $this->jsonstring = $jsonstring;
    }
}