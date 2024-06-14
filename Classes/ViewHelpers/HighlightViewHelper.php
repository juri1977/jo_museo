<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class HighlightViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('highlightarray', 'stdClass', 'Arrayobjekt in dem die markierten Datensätze liegen', false, null);
        $this->registerArgument('id', 'string', 'ID des Datensatzes', false, '');
        $this->registerArgument('fieldname', 'string', 'Solr-Feldname', false, '');
        $this->registerArgument('highlightedvalue', 'string', 'Wert des Feldes mit hervorgehobenen Suchtreffer', false, '');
    }

    /**
     * Return Highligted String
     *
     * @param    stdClass $highlightarray    Arrayobjekt in dem die markierten Datensätze liegen
     * @param    string    $id    ID des Datensatzes
     * @param    string    $fieldname Solr-Feldname
     * @return     string    $highlightedvalue Wert des Feldes mit hervorgehobenen Suchtreffer
     */
    public function render()
    {
        $highlightarray = $this->arguments['highlightarray'];
        $highlightedvalue = $this->arguments['highlightedvalue'];
        $id = $this->arguments['id'];
        $fieldname = $this->arguments['fieldname'];

        $fieldvalue = $this->renderChildren(); // Wert des Feldes
        $returnvalue = $fieldvalue;
        if (null != $fieldvalue && null != $id && null != $fieldname && !empty($highlightarray->$id->$fieldname)) {
            foreach ($highlightarray->$id->$fieldname as $value) {
                if (strip_tags($value) == $fieldvalue) {
                    $returnvalue = $value;
                }
            }
        }
        return $returnvalue;
    }
}
