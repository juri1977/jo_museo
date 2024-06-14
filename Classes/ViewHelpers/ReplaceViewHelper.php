<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ReplaceViewHelper extends AbstractViewHelper
{
	/**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('search', 'string', '', false, null);
        $this->registerArgument('replace', 'string', '', false, null);
    }
	
    /**
    * @param string $search
	* @param string $replace
    *
    * @return string
    */
    public function render()
    {
		$search = $this->arguments['search'];
		$replace = $this->arguments['replace'];
		$string = $this->renderChildren();
		if ($search != null) {
			$string = str_replace($search, $replace, $string);
		}
        return $string;
    }
}
