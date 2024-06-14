<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class JoinarrayViewHelper extends AbstractViewHelper
{

	/**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('arraycontains', 'array', 'Char or string to split the string into pieces. Default is a comma sign(,)', false, array());
        $this->registerArgument('keytofind', 'string', 'If TRUE empty items will be removed', false, '');
    }
	/**
	 * 	Prüft, on ein Array ein spezifischen Wert enthält
	 
	 *	@param array $arraycontains	-	Array, das den betreffenden Key enthalten soll 
	 *	@param string $keytofind	-	Wert, der im Array gefunden werden soll
	 *	@return bool 
	 */
	public function render()
	{
		$arraycontains = $this->arguments['arraycontains'];
		$keytofind = $this->arguments['keytofind'];
		$joReturnValue = false;
		if (!empty($arraycontains) && $keytofind != '') {
			if (in_array($keytofind, $arraycontains)) {
				$joReturnValue = true;
			}
		}
		return $joReturnValue;
	}
}
