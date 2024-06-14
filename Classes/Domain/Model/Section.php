<?php
namespace JO\JoMuseo\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2018 Alexander Miller <info@justorange.de>, JUSTORANGE
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Section
 *
 */
class Section extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
     * startdate
     *
     * @var string
     */
    protected $startdate;

    /**
     * view
     *
     * @var string
     */
    protected $view;

    /**
     * configuration
     *
     * @var string
     */
    protected $configuration;

    /**
     * padding
     *
     * @var int
     */
    protected $padding;

    /**
     * textpos
     *
     * @var int
     */
    protected $textpos;

    /**
     * description
     *
     * @var string
     */
    protected $description;

    /**
     * literature
     *
     * @var string
     */
    protected $literature;


    /**
     * teaser
     *
     * @var string
     */
    protected $teaser;

    /**
     * nextsectiontext
     *
     * @var string
     */
    protected $nextsectiontext;

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
     * exhibits
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Exhibit>
     */
    protected $exhibit;

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
     * audio
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $audio;

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
        $this->setExhibit(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setJsonfile(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setDerivate(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
        $this->setAudio(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage);
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
     * Returns the teaser
     *
     * @return string $teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Returns the startdate
     *
     * @return string $startdate
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Returns the view
     *
     * @return string $view
     */
    public function getView()
    {
        return $this->view;
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
     * Returns the padding
     *
     * @return int $padding
     */
    public function getPadding()
    {
        return $this->padding;
    }

    /**
     * Returns the textpos
     *
     * @return int $textpos
     */
    public function getTextpos()
    {
        return $this->textpos;
    }

    /**
     * Returns the nextsectiontext
     * 
     * @return string $nextsectiontext
     */
    public function getNextsectiontext()
    {
        return $this->nextsectiontext;
    }

    /**
     * Sets the teaser
     *
     * @param string $teaser
     * @return void
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Returns the literature
     *
     * @return string $literature
     */
    public function getLiterature()
    {
        return $this->literature;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
