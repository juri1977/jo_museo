<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class JoisarrayViewHelper extends AbstractViewHelper
{


    public function initializeArguments()
    {
        $this->registerArgument('array', 'mixed', 'value to check', false, '');
    }

    public function render()
    {   
        $return = false;
        $array = $this->arguments['array'];
        if (is_array($array)) {
            $return = true;
        }
        return $return;
    }
}
