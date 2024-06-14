<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;
use TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * Exhibition
 *
 */
class Exhibition extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
     * configuration
     *
     * @var string
     */
    protected $configuration;

    /**
     * infotextetitle
     *
     * @var string
     */
    protected $infotextetitle;

    /**
     * infotexte
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Data>
     * @Cascade("remove")
     */
    protected $infotexte;

    /**
     * period
     *
     * @var string
     */
    protected $period;

    /**
     * place
     *
     * @var string
     */
    protected $place;

    /**
     * placecolor
     *
     * @var string
     */
    protected $placecolor;

    /**
     * aboutprojectcolor
     *
     * @var string
     */
    protected $aboutprojectcolor;

    /**
     * zitiervorschlagcolor
     *
     * @var string
     */
    protected $zitiervorschlagcolor;

    /**
     * infotextecolor
     *
     * @var string
     */
    protected $infotextecolor;

    /**
     * maincolor
     *
     * @var string
     */
    protected $maincolor;

    /**
     * maincolorborder
     *
     * @var string
     */
    protected $maincolorborder;

    /**
     * maincolorfont
     *
     * @var string
     */
    protected $maincolorfont;

    /**
     * maincolorlinkfont
     *
     * @var string
     */
    protected $maincolorlinkfont;

    /**
     * fontcolor
     *
     * @var string
     */
    protected $fontcolor;

    /**
     * bggradient_1
     *
     * @var string
     */
    protected $bggradient1;

    /**
     * bggradient_2
     *
     * @var string
     */
    protected $bggradient2;

    /**
     * fontcolor_detail_1
     *
     * @var string
     */
    protected $fontcolordetail1;

    /**
     * opening
     *
     * @var string
     */
    protected $opening;

    /**
     * openinghours
     *
     * @var string
     */
    protected $openinghours;

    /**
     * summary
     *
     * @var string
     */
    protected $summary;

    /**
     * aboutproject
     *
     * @var string
     */
    protected $aboutproject;

    /**
     * zitiervorschlag
     *
     * @var string
     */
    protected $zitiervorschlag;

    /**
     * vorschauseite
     *
     * @var string
     */
    protected $vorschauseite;

    /**
     * video
     *
     * @var string
     */
    protected $video;

    /**
     * sections
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Section>
     */
    protected $section;

    /**
     * intro
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $intro;

    /**
     * audio
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $audio;

    /**
     * flyer
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $flyer;

    /**
     * tosectionimg
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $tosectionimg;

    /**
     * tosectiondesc
     *
     * @var string
     */
    protected $tosectiondesc;

    /**
     * tosectionbtntext
     *
     * @var string
     */
    protected $tosectionbtntext;

    /**
     * links
     *
     * @var string
     */
    protected $links;

    /**
     * links
     *
     * @var string
     */
    protected $tags;

    /**
     * links
     *
     * @var string
     */
    protected $kontextinfo;

    /**
     * links
     *
     * @var string
     */
    protected $location;

    /**
     * links
     *
     * @var string
     */
    protected $entity;

    /**
     * jsonfile
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $jsonfile;

    /**
     * derivate
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $derivate;

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
        $this->setInfotexte(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setSection(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setIntro(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setAudio(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setFlyer(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setTosectionimg(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setJsonfile(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setDerivate(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
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
     * Returns the configuration
     *
     * @return string $configuration
     *
     */
    public function getConfiguration()
    {
        $confArray = GeneralUtility::xml2array($this->configuration);
        if(isset($confArray['data'])) return $confArray['data'];
    }

    /**
     * Returns the video
     *
     * @return string $video
     */
    public function getVideo()
    {
        return $this->video;
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
     * Returns the infotextetitle
     *
     * @return string $infotextetitle
     */
    public function getInfotextetitle()
    {
        return $this->infotextetitle;
    }

    /**
     * Sets the infotextetitle
     *
     * @param string $infotextetitle
     * @return void
     */
    public function setInfotextetitle($infotextetitle)
    {
        $this->infotextetitle = $infotextetitle;
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
     * Returns the infotexte
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Data> $infotexte
     */
    public function getInfotexte()
    {
        return $this->infotexte;
    }

    /**
     * Sets the infotexte
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Data> $infotexte
     * @return void
     */
    public function setInfotexte($infotexte)
    {
        $this->infotexte = $infotexte;
    }

    /**
     * Adds a infotexte
     *
     * @param \JO\JoMuseo\Domain\Model\Data $infotexte
     * @return void
     */
    public function addInfotexte(\JO\JoMuseo\Domain\Model\Data $infotexte)
    {
        $this->infotexte->attach($infotexte);
    }

    /**
     * Removes a infotexte
     *
     * @param \JO\JoMuseo\Domain\Model\Data $infotexteToRemove The infotexte to be removed
     * @return void
     */
    public function removeInfotexte(\JO\JoMuseo\Domain\Model\Data $infotexte)
    {
        $this->infotexte->detach($infotexte);
    }

    /**
     * Returns the period
     *
     * @return string $period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Sets the period
     *
     * @param string $period
     * @return void
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

    /**
     * Returns the place
     *
     * @return string $place
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Sets the place
     *
     * @param string $place
     * @return void
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

    /**
     * Returns the placecolor
     *
     * @return string $placecolor
     */
    public function getPlacecolor()
    {
        return $this->placecolor;
    }

    /**
     * Sets the placecolor
     *
     * @param string $placecolor
     * @return void
     */
    public function setPlacecolor($placecolor)
    {
        $this->placecolor = $placecolor;
    }

    /**
     * Returns the infotextecolor
     *
     * @return string $infotextecolor
     */
    public function getInfotextecolor()
    {
        return $this->infotextecolor;
    }

    /**
     * Sets the infotextecolor
     *
     * @param string $infotextecolor
     * @return void
     */
    public function setInfotextecolor($infotextecolor)
    {
        $this->infotextecolor = $infotextecolor;
    }

    /**
     * Returns the aboutprojectcolor
     *
     * @return string $aboutprojectcolor
     */
    public function getAboutprojectcolor()
    {
        return $this->aboutprojectcolor;
    }

    /**
     * Sets the aboutprojectcolor
     *
     * @param string $aboutprojectcolor
     * @return void
     */
    public function setAboutprojectcolor($aboutprojectcolor)
    {
        $this->aboutprojectcolor = $aboutprojectcolor;
    }

    /**
     * Returns the zitiervorschlagcolor
     *
     * @return string $zitiervorschlagcolor
     */
    public function getZitiervorschlagcolor()
    {
        return $this->zitiervorschlagcolor;
    }

    /**
     * Sets the zitiervorschlagcolor
     *
     * @param string $zitiervorschlagcolor
     * @return void
     */
    public function setZitiervorschlagcolor($zitiervorschlagcolor)
    {
        $this->zitiervorschlagcolor = $zitiervorschlagcolor;
    }

    /**
     * Returns the vorschauseite
     *
     * @return string $vorschauseite
     */
    public function getVorschauseite()
    {
        return $this->vorschauseite;
    }

    /**
     * Sets the vorschauseite
     *
     * @param string $vorschauseite
     * @return void
     */
    public function setVorschauseite($vorschauseite)
    {
        $this->vorschauseite = $vorschauseite;
    }

    /**
     * Returns the maincolor
     *
     * @return string $maincolor
     */
    public function getMaincolor()
    {
        return $this->maincolor;
    }

    /**
     * Sets the maincolor
     *
     * @param string $maincolor
     * @return void
     */
    public function setMaincolor($maincolor)
    {
        $this->maincolor = $maincolor;
    }

    /**
     * Returns the maincolorborder
     *
     * @return string $maincolorborder
     */
    public function getMaincolorborder()
    {
        return $this->maincolorborder;
    }

    /**
     * Sets the maincolorborder
     *
     * @param string $maincolorborder
     * @return void
     */
    public function setMaincolorborder($maincolorborder)
    {
        $this->maincolorborder = $maincolorborder;
    }

    /**
     * Returns the maincolorfont
     *
     * @return string $maincolorfont
     */
    public function getMaincolorfont()
    {
        return $this->maincolorfont;
    }

    /**
     * Sets the maincolorfont
     *
     * @param string $maincolorfont
     * @return void
     */
    public function setMaincolorfont($maincolorfont)
    {
        $this->maincolorfont = $maincolorfont;
    }

    /**
     * Returns the maincolorlinkfont
     *
     * @return string $maincolorlinkfont
     */
    public function getMaincolorlinkfont()
    {
        return $this->maincolorlinkfont;
    }

    /**
     * Sets the maincolorlinkfont
     *
     * @param string $maincolorlinkfont
     * @return void
     */
    public function setMaincolorlinkfont($maincolorlinkfont)
    {
        $this->maincolorlinkfont = $maincolorlinkfont;
    }

    /**
     * Returns the fontcolor
     *
     * @return string $fontcolor
     */
    public function getFontcolor()
    {
        return $this->fontcolor;
    }

    /**
     * Sets the fontcolor
     *
     * @param string $fontcolor
     * @return void
     */
    public function setFontcolor($fontcolor)
    {
        $this->fontcolor = $fontcolor;
    }

    /**
     * Returns the bggradient1
     *
     * @return string $bggradient1
     */
    public function getBggradient1()
    {
        return $this->bggradient1;
    }

    /**
     * Sets the bggradient1
     *
     * @param string $bggradient1
     * @return void
     */
    public function setBggradient1($bggradient1)
    {
        $this->bggradient1 = $bggradient1;
    }

    /**
     * Returns the bggradient2
     *
     * @return string $bggradient2
     */
    public function getBggradient2()
    {
        return $this->bggradient2;
    }

    /**
     * Sets the bggradient2
     *
     * @param string $bggradient2
     * @return void
     */
    public function setBggradient2($bggradient2)
    {
        $this->bggradient2 = $bggradient2;
    }

    /**
     * Returns the fontcolordetail1
     *
     * @return string $fontcolordetail1
     */
    public function getFontcolordetail1()
    {
        return $this->fontcolordetail1;
    }

    /**
     * Sets the fontcolordetail1
     *
     * @param string $fontcolordetail1
     * @return void
     */
    public function setFontcolordetail1($fontcolordetail1)
    {
        $this->fontcolordetail1 = $fontcolordetail1;
    }

    /**
     * Returns the opening
     *
     * @return string $opening
     */
    public function getOpening()
    {
        return $this->opening;
    }

    /**
     * Sets the opening
     *
     * @param string $opening
     * @return void
     */
    public function setOpening($opening)
    {
        $this->opening = $opening;
    }

    /**
     * Returns the openinghours
     *
     * @return string $openinghours
     */
    public function getOpeninghours()
    {
        return $this->openinghours;
    }

    /**
     * Sets the openinghours
     *
     * @param string $openinghours
     * @return void
     */
    public function setOpeninghours($openinghours)
    {
        $this->openinghours = $openinghours;
    }

    /**
     * Returns the summary
     *
     * @return string $summary
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Sets the summary
     *
     * @param string $summary
     * @return void
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * Returns the tosectiondesc
     *
     * @return string $tosectiondesc
     */
    public function getTosectiondesc()
    {
        return $this->tosectiondesc;
    }

    /**
     * Sets the tosectiondesc
     *
     * @param string $tosectiondesc
     * @return void
     */
    public function setTosectiondesc($tosectiondesc)
    {
        $this->tosectiondesc = $tosectiondesc;
    }

    /**
     * Returns the tosectionbtntext
     *
     * @return string $tosectionbtntext
     */
    public function getTosectionbtntext()
    {
        return $this->tosectionbtntext;
    }

    /**
     * Sets the tosectionbtntext
     *
     * @param string $tosectionbtntext
     * @return void
     */
    public function setTosectionbtntext($tosectionbtntext)
    {
        $this->tosectionbtntext = $tosectionbtntext;
    }

    /**
     * Returns the aboutproject
     *
     * @return string $aboutproject
     */
    public function getAboutproject()
    {
        return $this->aboutproject;
    }

    /**
     * Sets the aboutproject
     *
     * @param string $aboutproject
     * @return void
     */
    public function setAboutproject($aboutproject)
    {
        $this->aboutproject = $aboutproject;
    }

    /**
     * Returns the zitiervorschlag
     *
     * @return string $zitiervorschlag
     */
    public function getZitiervorschlag()
    {
        return $this->zitiervorschlag;
    }

    /**
     * Sets the zitiervorschlag
     *
     * @param string $zitiervorschlag
     * @return void
     */
    public function setZitiervorschlag($zitiervorschlag)
    {
        $this->zitiervorschlag = $zitiervorschlag;
    }

    /**
     * Returns the section
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Section> $section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Sets the section
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Section> $section
     * @return void
     */
    public function setSection(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $section)
    {
        $this->section = $section;
    }

    /**
     * Adds a Section
     *
     * @param \JO\JoMuseo\Domain\Model\Section $section
     * @return void
     */
    public function addSection(\JO\JoMuseo\Domain\Model\Section $section)
    {
        $this->section->attach($section);
    }

    /**
     * Removes a Section
     *
     * @param \JO\JoMuseo\Domain\Model\Section $sectionToRemove The Section to be removed
     * @return void
     */
    public function removeSection(\JO\JoMuseo\Domain\Model\Section $section)
    {
        $this->section->detach($section);
    }

    /**
     * Returns intro
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $intro
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * Set intro
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $intro
     */
    public function setIntro(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $intro)
    {
        $this->intro = $intro;
    }

    /**
     * Adds a Intro
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $intro
     * @return void
     */
    public function addIntro(\TYPO3\CMS\Extbase\Domain\Model\FileReference $intro)
    {
        $this->intro->attach($intro);
    }

    /**
     * Removes a Intro
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $introToRemove The Intro to be removed
     * @return void
     */
    public function removeIntro(\TYPO3\CMS\Extbase\Domain\Model\FileReference $intro)
    {
        $this->intro->detach($intro);
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
     * Returns Flyer
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $flyer
     */
    public function getFlyer()
    {
        return $this->flyer;
    }

    /**
     * Set Flyer
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $flyer
     */
    public function setFlyer(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $flyer)
    {
        $this->flyer = $flyer;
    }

    /**
     * Adds a Flyer
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $flyer
     * @return void
     */
    public function addFlyer(\TYPO3\CMS\Extbase\Domain\Model\FileReference $flyer)
    {
        $this->flyer->attach($flyer);
    }

    /**
     * Removes a Flyer
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $flyerToRemove The Flyer to be removed
     * @return void
     */
    public function removeFlyer(\TYPO3\CMS\Extbase\Domain\Model\FileReference $flyer)
    {
        $this->flyer->detach($flyer);
    }

    /**
     * Returns tosectionimg
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $tosectionimg
     */
    public function getTosectionimg()
    {
        return $this->tosectionimg;
    }

    /**
     * Set tosectionimg
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $tosectionimg
     */
    public function setTosectionimg(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $tosectionimg)
    {
        $this->tosectionimg = $tosectionimg;
    }

    /**
     * Adds a tosectionimg
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $tosectionimg
     * @return void
     */
    public function addTosectionimg(\TYPO3\CMS\Extbase\Domain\Model\FileReference $tosectionimg)
    {
        $this->tosectionimg->attach($tosectionimg);
    }

    /**
     * Removes a tosectionimg
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $tosectionimgToRemove The tosectionimg to be removed
     * @return void
     */
    public function removeTosectionimg(\TYPO3\CMS\Extbase\Domain\Model\FileReference $tosectionimg)
    {
        $this->tosectionimg->detach($tosectionimg);
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
     * Returns the kontextinfo
     *
     * @return string $kontextinfo
     */
    public function getKontextinfo()
    {
        return $this->kontextinfo;
    }

    /**
     * Sets the kontextinfo
     *
     * @param string $kontextinfo
     * @return void
     */
    public function setKontextinfo($kontextinfo)
    {
        $this->kontextinfo = $kontextinfo;
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
     * Sets the Location
     *
     * @return string $location
     */
    public function setLocation()
    {
        $this->location = $location;
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
     * Sets the Entity
     *
     * @return string $entity
     */
    public function setEntity()
    {
        $this->entity = $entity;
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
}
