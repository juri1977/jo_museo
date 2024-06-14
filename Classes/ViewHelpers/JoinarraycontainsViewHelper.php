<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class JoinarraycontainsViewHelper extends AbstractViewHelper
{
    /**
     *     Prüft, on ein Array ein spezifischen Wert enthält
     *
     *    @param array $arraycontains    -    Array, das den betreffenden Key enthalten soll
     *    @param string $keytofind    -    Wert, der im Array gefunden werden soll
     *    @param string $mode    -    Modus, default ist Rekursiv. Contains: Geht alles durch und prüft ob vorhanden
     *    @return bool
     */

    public function render($arraycontains = [], $keytofind = '', $mode = '')
    {
        if (!empty($arraycontains) && '' != $keytofind) {
            if ($mode == 'contains') {
                foreach ($arraycontains as $key => $value) {
                    if (false !== strpos($value, $keytofind)) {
                        return true;
                    }
                }
            } else {
                foreach ($arraycontains as $key => $value) {
                    if (false === stripos($keytofind, '/')) {
                        $teile = explode('/', $value);
                        foreach ($teile as $key => $value) {
                            if ($value === $keytofind) {
                                return true;
                            }
                        }
                    } else {
                        if (false !== stripos($value, $keytofind)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }
    }
}
