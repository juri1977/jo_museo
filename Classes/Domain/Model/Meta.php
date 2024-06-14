<?php
namespace JO\JoMuseo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Meta
 *
 */
class Meta extends AbstractEntity
{

    /**
     * view
     *
     * @var string
     */
    protected $view;

    /**
     * title
     *
     * @var string
     */
    protected $title;

    /**
     * description
     *
     * @var string
     */
    protected $description;

    /**
     * image
     *
     * @var ObjectStorage<FileReference>
     */
    protected $image;

    /**
     * audio
     *
     * @var ObjectStorage<FileReference>
     */
    protected $audio;

    /**
     * video
     *
     * @var ObjectStorage<FileReference>
     */
    protected $video;

    /**
     * videolink
     *
     * @var string
     */
    protected $videolink;

    /**
     * audiolink
     *
     * @var string
     */
    protected $audiolink;

    /**
     * title
     *
     * @var string
     */
    protected $otherlinktext;

    /**
     * title
     *
     * @var string
     */
    protected $otherlink;

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
        $this->image = new ObjectStorage();
        $this->video = new ObjectStorage();
        $this->audio = new ObjectStorage();
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

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setVideolink($videolink)
    {
        $this->videolink = $videolink;
    }

    public function getVideolink()
    {
        return $this->videolink;
    }

    public function setAudiolink($audiolink)
    {
        $this->audiolink = $audiolink;
    }

    public function getAudiolink()
    {
        return $this->audiolink;
    }

    public function setOtherlinktext($otherlinktext)
    {
        $this->otherlinktext = $otherlinktext;
    }

    public function getOtherlinktext()
    {
        return $this->otherlinktext;
    }

    public function setOtherlink($otherlink)
    {
        $this->otherlink = $otherlink;
    }

    public function getOtherlink()
    {
        return $this->otherlink;
    }

    /**
     * Returns image
     *
     * @return ObjectStorage<FileReference> $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param ObjectStorage $image
     */
    public function setImage(ObjectStorage $image)
    {
        $this->image = $image;
    }

    /**
     * Adds a image
     *
     * @param FileReference $image
     * @return void
     */
    public function addImage(FileReference $image)
    {
        $this->image->attach($image);
    }

    /**
     * Removes a image
     *
     * @param FileReference $image The image to be removed
     * @return void
     */
    public function removeImage(FileReference $image)
    {
        $this->image->detach($image);
    }

    /**
     * Returns video
     *
     * @return ObjectStorage<FileReference> $video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set video
     *
     * @param ObjectStorage $video
     */
    public function setVideo(ObjectStorage $video)
    {
        $this->video = $video;
    }

    /**
     * Adds a video
     *
     * @param FileReference $video
     * @return void
     */
    public function addVideo(FileReference $video)
    {
        $this->video->attach($video);
    }

    /**
     * Removes a video
     *
     * @param FileReference $video The video to be removed
     * @return void
     */
    public function removeVideo(FileReference $video)
    {
        $this->video->detach($video);
    }

    /**
     * Returns audio
     *
     * @return ObjectStorage<FileReference> $audio
     */
    public function getAudio()
    {
        return $this->audio;
    }

    /**
     * Set audio
     *
     * @param ObjectStorage $audio
     */
    public function setAudio(ObjectStorage $audio)
    {
        $this->audio = $audio;
    }

    /**
     * Adds a audio
     *
     * @param FileReference $audio
     * @return void
     */
    public function addAudio(FileReference $audio)
    {
        $this->audio->attach($audio);
    }

    /**
     * Removes a audio
     *
     * @param FileReference $audio The audio to be removed
     * @return void
     */
    public function removeAudio(FileReference $audio)
    {
        $this->audio->detach($audio);
    }
}
