<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ExplodeViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('delimiter', 'string', 'Char or string to split the string into pieces. Default is a comma sign(,)', false, ',');
        $this->registerArgument('last', 'bool', 'If TRUE empty items will be removed', false, false);
    }

    public function render()
    {
        $delimiter = $this->arguments['delimiter'];
        $last = $this->arguments['last'];

        $string = $this->renderChildren();
        /*delimiter = nl -> new line -> dann wird nach chr(10) umgebrochen */
        if ('nl' == $delimiter) {
            $delimiter = chr(10);
        }
        return $last ? end(explode($delimiter, $string)) : explode($delimiter, $string);
    }
}
