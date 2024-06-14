<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
/**
 * 	Viewhelper zum Highlighten bestimmter Wörter im Text
 *	Die Wörter werden via Flexform den entsprechenden Daten zugeordnet
 *
 */
 
class SplitrolesViewHelper extends AbstractViewHelper
{
	

	/**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('position', 'int', 'Position in der Zeichenkette, an der die Rolle steht. Beginnt mit 1: Max Mustermann$Author$1901-1987 -> Pos: 2', false, 0);
        $this->registerArgument('delimiter', 'string', 'Trennzeichen, zwischen den Inhaltlichen Blöcken', false, '$');
    }
	/**
	 * Splitted die Rollen einzelner Personen
	 * @param $delimiter string Trennzeichen, zwischen den Inhaltlichen Bl�cken
	 * @param $position int Position in der Zeichenkette, an der die Rolle steht. Beginnt mit 1: Max Mustermann$Author$1901-1987 -> Pos: 2
	 * @return array	nach Rollen gesplittetes Array
	 */
	public function render()
	{
		$position = $this->arguments['position'];
		$delimiter = $this->arguments['delimiter'];
		$fullarray = $this->renderChildren(); // Wert des Feldes
		
		$returnvalue = [];
		if (!empty($fullarray)) {
			foreach ($fullarray as $value) {
				$tempArray = explode($delimiter, $value);
				$returnvalue[$tempArray[($position - 1)]][] = $value;
			}
		}
		return $returnvalue;
	}
	
}
