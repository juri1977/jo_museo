<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class StringcontainsViewHelper extends AbstractViewHelper
{

	/**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('str', 'string', '', false, '');
        $this->registerArgument('neddle', 'string', '', false, '');
    }
	
    /**
     *    Prüft, string in string
     *
     *    @param String $str
     *    @param String $neddle
     *    @return bool
     */
    public function render()
    {
		$str = $this->arguments['str'];
		$neddle = $this->arguments['neddle'];
        if (false !== strpos($str, $neddle)) {
            return true;
        }
        return false;
    }
}
