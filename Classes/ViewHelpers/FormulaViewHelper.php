<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class FormulaViewHelper extends AbstractViewHelper
{
    /**
     *
     * @return string
     */
    public function render()
    {
        $string = $this->renderChildren();
        if (!empty($string)) {
            $suchmuster = '/\{t\}(\d)+/';
            $ersetzung = '<sub>$1</sub>';
            $string = preg_replace($suchmuster, $ersetzung, $string);
        }
        return $string;
    }
}
