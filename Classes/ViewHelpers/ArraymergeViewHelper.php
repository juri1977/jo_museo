<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ArraymergeViewHelper extends AbstractViewHelper
{
	public function initializeArguments()
    {
        $this->registerArgument('primaryarray', 'array', 'Array', false, false);
        $this->registerArgument('arraytoadd', 'array', 'Array', false, false);
    }

	/**
	 * 	Führt zwei Arrays zusammen
     *	@param array $primaryarray - Arrays, das ergänzt werden soll	 
	 *	@param array $arraytoadd - Arrays, das hinzugefügt werden soll
	 *	@return array 
	 */
	public function render()
	{	
		$primaryarray = $this->arguments['primaryarray']; 
		$arraytoadd = $this->arguments['arraytoadd'];
		if (is_array($arraytoadd) && is_array($arraytoadd)) {
			$new_array = array_merge($primaryarray, $arraytoadd);
			return array_filter($new_array, array($this, "removeFalseButNotZero"));
		}
	}
	
	// sorgt dafür, dass Einträge mit dem Wert 0 nicht entfernt werden, sondern nur false und null
	public function removeFalseButNotZero($value) {
		return ($value || is_numeric($value));
	}
}
