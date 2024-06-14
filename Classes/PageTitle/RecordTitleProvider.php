<?php
namespace JO\JoMuseo\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

class RecordTitleProvider extends AbstractPageTitleProvider
{
    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}