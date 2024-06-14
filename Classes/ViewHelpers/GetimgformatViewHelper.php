<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 *     Viewhelper zum Highlighten bestimmter Wörter im Text
 *    Die Wörter werden via Flexform den entsprechenden Daten zugeordnet
 *
 */
class GetimgformatViewHelper extends AbstractFormFieldViewHelper
{
    /**
     * Splitted die Rollen einzelner Personen
     * @param $delimiter string Trennzeichen, zwischen den Inhaltlichen Blöcken
     * @param $position int Position in der Zeichenkette, an der die Rolle steht. Beginnt mit 1: Max Mustermann$Author$1901-1987 -> Pos: 2
     * @return array    nach Rollen gesplittetes Array
     */
    public function render()
    {
        $img = $this->renderChildren(); // Wert des Feldes
        $returnvalue = 'q';
        if ($img) {
            //$size = getimagesize($img);
            $size = [];
            $raw = $this->getImg($img);
            $im = imagecreatefromstring($raw);
            $size[0] = imagesx($im);
            $size[1] = imagesy($im);
            if ($size[0] > $size[1]) {
                $returnvalue = 'l';
                // Nur wirklich landscapebilder werden auch so dargestellt - verhältnis 16/9
                if ($size[0] / $size[1] < 1.6) {
                    $returnvalue = 'p';
                }
            }if ($size[0] < $size[1]) {
                $returnvalue = 'p';
            }
        }
        return $returnvalue;
    }

    public function getImg($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}
