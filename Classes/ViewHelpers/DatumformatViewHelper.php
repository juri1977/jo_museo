<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class DatumformatViewHelper extends AbstractViewHelper
{
    /**
     * @param date $string
     * @param string $formstring
     *
     * @return string
     */
    public function render($string, $formstring)
    {
        $string = $string->getTimestamp();
        $string = strftime($formstring, $string);
        if ('de' == $GLOBALS['TSFE']->config['config']['language']) {
            $string = str_replace('Sunday', 'Sonntag', $string);
            $string = str_replace('Monday', 'Montag', $string);
            $string = str_replace('Tuesday', 'Dienstag', $string);
            $string = str_replace('Wednesday', 'Mittwoch', $string);
            $string = str_replace('Thursday', 'Donnerstag', $string);
            $string = str_replace('Friday', 'Freitag', $string);
            $string = str_replace('Saturday', 'Samstag', $string);
        }
        return $string;
    }
}
