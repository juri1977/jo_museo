<?php

namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ImplodeViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('delimiter', 'string', '');
    }

    public function render()
    {
        $return = $this->renderChildren();
        if (is_array($return)) {
            $delimiter = $this->arguments['delimiter'];
            $return = implode($delimiter, $return); 
        }
        return $return;
    }
}
