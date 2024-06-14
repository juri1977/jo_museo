<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class SortarrayViewHelper extends AbstractViewHelper
{

	public function initializeArguments()
    {
        $this->registerArgument('ascdesc', 'string', 'String', false, false);
    }
   /**
	 * @param $ascdesc string Sortierreihenfolge
	 * @return array Sortiertes Array
	 */
	public function render()
	{
		$ascdesc = 'asc';
		$arraykey = $this->arguments['ascdesc']; 
		$fullarray = $this->renderChildren(); // Wert des Feldes
		if (!empty($fullarray)) {
			usort($fullarray, [\Collator::create('de_DE'), 'compare']);
		}
		return $fullarray;
	}
}
