<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Institute
 *
 */
class Institute extends AbstractEntity
{
    /**
     * active
     *
     * @var int
     */
    protected $active;

    /**
     * title
     *
     * @var string
     */
    protected $title;

    /**
     * shorttitle
     *
     * @var string
     */
    protected $shorttitle;

    /**
     * topic
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Topic>
     */
    protected $topicdb;


    /**
     * timerange
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Times>
     */
    protected $times;

    /**
     * datatype
     *
     * @var string
     */
    protected $datatype;

    /**
     * subtitle
     *
     * @var string
     */
    protected $subtitle;

    /**
     * externlink
     *
     * @var string
     */
    protected $externlink;

    /**
     * outerlink
     *
     * @var string
     */
    protected $outerlink;

    /**
     * description
     *
     * @var string
     */
    protected $description;

     /**
     * descriptionlong
     *
     * @var string
     */
    protected $descriptionlong;

    /**
     * contact
     *
     * @var string
     */
    protected $contact;

    /**
     * geodata
     *
     * @var string
     */
    protected $geodata;

    /**
     * geotext
     *
     * @var string
     */
    protected $geotext;

    /**
     * gnddata
     *
     * @var string
     */
    protected $gnddata;

    /**
     * isilnummer
     *
     * @var string
     */
    protected $isilnummer;

    /**
     * barrierfree
     *
     * @var string
     */
    protected $barrierfree;

    /**
     * externalstock
     *
     * @var string
     */
    protected $externalstock;

    /**
     * relateditems
     *
     * @var string
     */
    protected $relateditems;

    /**
     * relatedsingleitemsobjectstorage
     *
     * @var string
     */
    protected $relatedsingleitemsobjectstorage;

    /**
     * datastorage
     *
     * @var string
     */
    protected $datastorage;

    /**
     * tenantreference
     *
     * @var string
     */
    protected $tenantreference;

    /**
     * idreference
     *
     * @var string
     */
    protected $idreference;

    /**
     * website
     *
     * @var string
     */
    protected $website;

    /**
     * relatedsingleitems
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Institute>
     */
    protected $relatedsingleitems;

    /**
     * keywords
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Keywords>
     */
    protected $keywords;

    /**
     * classfication
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Instituteclass>
     */
    protected $classfication;

    /**
     * image
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $image;

    /**
     * image
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $moreimages;

    /**
     * bannerimage
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $bannerimage;

    /**
     * morelinklabel
     *
     * @var string
     */
    protected $morelinklabel;

    /**
     * morelinkimg
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $morelinkimg;

    /**
     * metas
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Meta>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $metas;

    /**
     * social
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JO\JoMuseo\Domain\Model\Social>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $social;

    /**
     * day
     *
     * @var \DateTime
     */
    protected $day;

    /**
     * Partner institution?
     *
     * @var bool
     */
    protected $notlocalstatus;

    /**
     * subobjectstitle
     *
     * @var string
     */
    protected $subobjectstitle;

    /**
     * moreimagestitle
     *
     * @var string
     */
    protected $moreimagestitle;

    /**
     * relatedsingleitemstitle
     *
     * @var string
     */
    protected $relatedsingleitemstitle;

    /**
     * Erstes Bild Banner
     *
     * @var bool
     */
    protected $bannerimg;

    public function getTimes()
    {
        return $this->times;
    }

    public function getTopicdb()
    {
        return $this->topicdb;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getShorttitle()
    {
        return $this->shorttitle;
    }

    public function getSubtitle()
    {
        return $this->subtitle;
    }

    public function getSubobjectstitle()
    {
        return $this->subobjectstitle;
    }

    public function getMoreimagestitle()
    {
        return $this->moreimagestitle;
    }

    public function getRelatedsingleitemstitle()
    {
        return $this->relatedsingleitemstitle;
    }
    
    public function getDatatype()
    {
        return $this->datatype;
    }

    public function getExternlink()
    {
        return $this->externlink;
    }

    public function getOuterlink()
    {
        return $this->outerlink;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDescriptionlong()
    {
        return $this->descriptionlong;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function getGeodata()
    {
        return $this->geodata;
    }

    public function getGeotext()
    {
        return $this->geotext;
    }

    public function getGnddata()
    {
        return $this->gnddata;
    }

    public function getIsilnummer()
    {
        return $this->isilnummer;
    }

    public function getBarrierfree()
    {
        return $this->barrierfree;
    }

    public function getExternalstock()
    {
        return $this->externalstock;
    }

    public function getDatastorage()
    {
        return $this->datastorage;
    }

    public function getRelateditems()
    {
        return $this->relateditems;
    }

    public function getRelatedsingleitems()
    {
        return $this->relatedsingleitems;
    }

    public function getRelatedsingleitemsobjectstorage()
    {
        return $this->relatedsingleitemsobjectstorage;
    }

    public function getTenantreference()
    {
        return $this->tenantreference;
    }

    public function getIdreference()
    {
        return $this->idreference;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function getClassfication()
    {
        return $this->classfication;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getMoreimages()
    {
        return $this->moreimages;
    }

    public function getBannerimage()
    {
        return $this->bannerimage;
    }

    public function getMorelinklabel()
    {
        return $this->morelinklabel;
    }

    public function getMorelinkimg()
    {
        return $this->morelinkimg;
    }

    public function getDay()
    {
        return $this->day;
    }

    public function getNotlocalstatus()
    {
        return $this->notlocalstatus;
    }

    public function getBannerimg()
    {
        return $this->bannerimg;
    }

    public function getMetas()
    {
        return $this->metas;
    }

    public function getSocial()
    {
        return $this->social;
    }
}
