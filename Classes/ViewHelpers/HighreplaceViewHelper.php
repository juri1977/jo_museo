<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class HighReplaceViewHelper extends AbstractViewHelper
{
/**
 * @param string $param
 */
    public function render($param)
    {
        $string = $this->renderChildren();
        $replace = '';
        if (strpos($string, $param) !== false) {
            $string = '<span class="joFixedPopover">' . $string;
            $replace = "</span>";
        }

        if (null != $param) {
            $string = str_replace($param, $replace, $string);
        }
        return $string;
    }
}
