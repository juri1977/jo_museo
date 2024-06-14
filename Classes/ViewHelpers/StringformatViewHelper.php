<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class StringformatViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('string', 'string', 'If TRUE empty items will be removed', false, '');
    }
    /**
    * @param string $string
    *
    * @return string
    */
    public function render()
    {
        $string = $this->arguments['string'];
		$string = str_replace(' ', '', strtolower($string));
		//umlaute entfernen
		$search = array("ä", "ö", "ü", "ß"); 
        $replace = array("ae", "oe", "ue", "ss"); 
		$string = str_replace($search, $replace, $string);  
        return $string;
    }
}
