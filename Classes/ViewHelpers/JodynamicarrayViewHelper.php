<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class JodynamicarrayViewHelper extends AbstractViewHelper
{

    public function initializeArguments()
    {
        $this->registerArgument('arrayname', 'array', 'Arrayname', false, []);
        $this->registerArgument('arraykey', 'string', 'Arraykey', false, null);
    }

    /**
     *     Erzeugt ein Array aus dynamischen Key/Value Paaren

     *    @param array $arrayname    - Array /Name des Arrays in dem die Daten abgelegt sind
     *    @param string $arraykey    -    String/key des Arrays, dessen Wert zur�ckgegeben werden soll
     *    @return mixed
     */
    public function render()
    {
        $arrayname =  $this->arguments['arrayname'];
        $arraykey =  $this->arguments['arraykey'];
        
        $joReturn = [];
        if (!empty($arrayname) && null !== $arraykey) {
            $joReturn = $arrayname[$arraykey];
        }
        return $joReturn;
    }
}
