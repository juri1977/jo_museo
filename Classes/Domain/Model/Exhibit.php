<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Exhibit
 *
 */
class Exhibit extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     * @Validate("NotEmpty")
     */
    protected $title;


    /**
     * subtitle
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Data>
     * @Cascade("remove")
     */
    protected $subtitle;

    /**
     * shorttext
     *
     * @var string
     */
    protected $shorttext;

     /**
     * configuration
     *
     * @var string
     */
    protected $configuration;

    /**
     * datapage
     *
     * @var string
     */
    protected $datapage;

    /**
     * objektnummer
     *
     * @var string
     */
    protected $objektnummer;


    /**
     * bodytext
     *
     * @var string
     */
    protected $bodytext;

    /**
     * transkript
     *
     * @var string
     */
    protected $transkript;

    /**
     * publikation
     *
     * @var string
     */
    protected $publikation;

    /**
     * ctatext
     *
     * @var string
     */
    protected $ctatext;

    /**
     * kontextinformation
     *
     * @var string
     */
    protected $kontextinformation;

    /**
     * links
     *
     * @var string
     */
    protected $links;

    /**
     * derivate
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $derivate;

    /**
     * morederivate
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $morederivate;

    /**
     * booksites
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $booksites;

    /**
     * bookfolder
     *
     * @var string
     */
    protected $bookfolder;

    /**
     * videotitel
     *
     * @var string
     */
    protected $videotitel;

    /**
     * videotext
     *
     * @var string
     */
    protected $videotext;

    /**
     * audiotitel
     *
     * @var string
     */
    protected $audiotitel;

    /**
     * audiotext
     *
     * @var string
     */
    protected $audiotext;

    /**
     * moreaudio
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $moreaudio;

    /**
     * audio
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $audio;

    /**
     * morevideo
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $morevideo;

    /**
     * video
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $video;

    /**
     * tags
     *
     * @var string
     */
    protected $tags;

    /**
     * location
     *
     * @var string
     */
    protected $location;

    /**
     * locationprocessed
     *
     * @var string
     */
    protected $locationprocessed;

    /**
     * entity
     *
     * @var string
     */
    protected $entity;

    /**
     * entityprocessed
     *
     * @var string
     */
    protected $entityprocessed;

    

     /**
     * datasubtype
     *
     * @var string
     */
    protected $datasubtype;

    /**
     * jsonfile
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $jsonfile;

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
        $this->setSubtitle(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setLocation(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setEntity(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setJsonfile(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setDerivate(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setBooksites(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setAudio(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
    }

    /**
     * Returns the datasubtype
     *
     * @return string $datasubtype
     */
    public function getDatasubtype()
    {
        return $this->datasubtype;
    }

    /**
     * Returns the configuration
     *
     * @return string $configuration
     *
     */
    public function getConfiguration()
    {
        $confArray = GeneralUtility::xml2array($this->configuration);
        if(isset($confArray['data']['sDEF']['lDEF'])) return $confArray['data']['sDEF']['lDEF'];
    }
    
    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

        /**
     * Returns the ctatext
     *
     * @return string $ctatext
     */
    public function getCtatext()
    {
        return $this->ctatext;
    }

    /**
     * Sets the ctatext
     *
     * @param string $ctatext
     * @return void
     */
    public function setCtatext($ctatext)
    {
        $this->ctatext = $ctatext;
    }

    /**
     * Returns the subtitle
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Data> $subtitle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Sets the subtitle
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Data> $subtitle
     * @return void
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * Adds a Subtitle
     *
     * @param \JO\JoMuseo\Domain\Model\Data $subtitle
     * @return void
     */
    public function addSubtitle(\JO\JoMuseo\Domain\Model\Data $subtitle)
    {
        $this->subtitle->attach($subtitle);
    }

    /**
     * Removes a Subtitle
     *
     * @param \JO\JoMuseo\Domain\Model\Data $subtitleToRemove The Subtitle to be removed
     * @return void
     */
    public function removeSubtitle(\JO\JoMuseo\Domain\Model\Data $subtitle)
    {
        $this->subtitle->detach($subtitle);
    }

    /**
     * Returns the shorttext
     *
     * @return string $shorttext
     */
    public function getShorttext()
    {
        return $this->shorttext;
    }

    /**
     * Returns the objektnummer
     *
     * @return string $objektnummer
     */
    public function getObjektnummer()
    {
        return $this->objektnummer;
    }

    /**
     * Sets the shorttext
     *
     * @param string $shorttext
     * @return void
     */
    public function setShorttext($shorttext)
    {
        $this->shorttext = $shorttext;
    }



    /**
     * Returns the datapage
     *
     * @return string $datapage
     */
    public function getDatapage()
    {
        return $this->datapage;
    }

    /**
     * Sets the datapage
     *
     * @param string $datapage
     * @return void
     */
    public function setDatapage($datapage)
    {
        $this->datapage = $datapage;
    }

    /**
     * Returns the bodytext
     *
     * @return string $bodytext
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * Returns the transkript
     *
     * @return string $transkript
     */
    public function getTranskript()
    {
        return $this->transkript;
    }

    /**
     * Sets the bodytext
     *
     * @param string $bodytext
     * @return void
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Returns the publikation
     *
     * @return string $publikation
     */
    public function getPublikation()
    {
        return $this->publikation;
    }

    /**
     * Sets the publikation
     *
     * @param string $publikation
     * @return void
     */
    public function setPublikation($publikation)
    {
        $this->publikation = $publikation;
    }

    /**
     * Returns the links
     *
     * @return string $links
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Sets the links
     *
     * @param string $links
     * @return void
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * Returns the bookfolder
     *
     * @return string $bookfolder
     */
    public function getBookfolder()
    {
        return $this->bookfolder;
    }

    /**
     * Sets the bookfolder
     *
     * @param string $bookfolder
     * @return void
     */
    public function setBookfolder($bookfolder)
    {
        $this->bookfolder = $bookfolder;
    }

    /**
     * Returns the tags
     *
     * @return string $tags
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Sets the tags
     *
     * @param string $tags
     * @return void
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Returns the Location
     *
     * @return string $location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Returns the Locationprocessed
     *
     * @return string $locationprocessed
     */
    public function getLocationprocessed()
    {
        return $this->locationprocessed;
    }

    /**
     * Returns the Entity
     *
     * @return string $entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

        /**
     * Returns the Entityprocessed
     *
     * @return string $entityprocessed
     */
    public function getEntityprocessed()
    {
        return $this->entityprocessed;
    }

    /**
     * Returns Jsonfile
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $jsonfile
     */
    public function getJsonfile()
    {
        return $this->jsonfile;
    }

    /**
     * Set Jsonfile
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $jsonfile
     */
    public function setJsonfile(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $jsonfile)
    {
        $this->jsonfile = $jsonfile;
    }

    /**
     * Adds a Jsonfile
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $jsonfile
     * @return void
     */
    public function addJsonfile(\TYPO3\CMS\Extbase\Domain\Model\FileReference $jsonfile)
    {
        $this->jsonfile->attach($jsonfile);
    }

    /**
     * Removes a Jsonfile
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $jsonfile The jsonfile to be removed
     * @return void
     */
    public function removeJsonfile(\TYPO3\CMS\Extbase\Domain\Model\FileReference $jsonfile)
    {
        $this->jsonfile->detach($jsonfile);
    }

    /**
     * Returns derivate
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $derivate
     */
    public function getDerivate()
    {
        return $this->derivate;
    }

    /**
     * Returns morederivate
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $morederivate
     */
    public function getMorederivate()
    {
        return $this->morederivate;
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
     * Returns morevideo
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $morevideo
     */
    public function getMorevideo()
    {
        return $this->morevideo;
    }

    /**
     * Set derivate
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $derivate
     */
    public function setDerivate(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $derivate)
    {
        $this->derivate = $derivate;
    }

    /**
     * Adds a Derivate
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $derivate
     * @return void
     */
    public function addDerivate(\TYPO3\CMS\Extbase\Domain\Model\FileReference $derivate)
    {
        $this->derivate->attach($derivate);
    }

    /**
     * Removes a Derivate
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $derivateToRemove The Derivate to be removed
     * @return void
     */
    public function removeDerivate(\TYPO3\CMS\Extbase\Domain\Model\FileReference $derivate)
    {
        $this->derivate->detach($derivate);
    }

    /**
     * Returns booksites
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $booksites
     */
    public function getBooksites()
    {
        return $this->booksites;
    }

    /**
     * Set booksites
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $booksites
     */
    public function setBooksites(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $booksites)
    {
        $this->booksites = $booksites;
    }

    /**
     * Adds a booksites
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $booksites
     * @return void
     */
    public function addBooksites(\TYPO3\CMS\Extbase\Domain\Model\FileReference $booksites)
    {
        $this->booksites->attach($booksites);
    }

    /**
     * Removes a booksites
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $booksitesToRemove The booksites to be removed
     * @return void
     */
    public function removeBooksites(\TYPO3\CMS\Extbase\Domain\Model\FileReference $booksites)
    {
        $this->booksites->detach($booksites);
    }


    /**
     * Returns the videotitel
     *
     * @return string $videotitel
     */
    public function getVideotitel()
    {
        return $this->videotitel;
    }


    /**
     * Returns the videotext
     *
     * @return string $videotext
     */
    public function getVideotext()
    {
        return $this->videotext;
    }

    /**
     * Returns the audiotitel
     *
     * @return string $audiotitel
     */
    public function getAudiotitel()
    {
        return $this->audiotitel;
    }

    /**
     * Sets the audiotitel
     *
     * @param string $audiotitel
     * @return void
     */
    public function setAudiotitel($audiotitel)
    {
        $this->audiotitel = $audiotitel;
    }

    /**
     * Returns the audiotext
     *
     * @return string $audiotext
     */
    public function getAudiotext()
    {
        return $this->audiotext;
    }

    /**
     * Sets the audiotext
     *
     * @param string $audiotext
     * @return void
     */
    public function setAudiotext($audiotext)
    {
        $this->audiotext = $audiotext;
    }

    /**
     * Returns moreaudio
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $moreaudio
     */
    public function getMoreaudio()
    {
        return $this->moreaudio;
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
}
