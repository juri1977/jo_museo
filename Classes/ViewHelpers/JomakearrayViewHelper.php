<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class JomakearrayViewHelper extends AbstractViewHelper
{
	public function initializeArguments()
    {
        $this->registerArgument('joDel', 'bool', 'Arrayname', false, false);
        $this->registerArgument('arraykey', 'string', 'Arraykey', false, null);
        $this->registerArgument('arrayvalue', 'string', 'Arraykey', false, null);
    }

	/**
	 * 	Erzeugt ein Array aus dynamischen Key/Value Paaren
	 
	 *	@param string $arraykey	- String/Key des Arrays
	 *	@param string $arrayvalue	-	String/Value des Arrays
	 *	@param string $joDel	-	joDel Hinzufügen oder nicht
	 *	@return array 
	 */
	
	public function render()
	{
		$arraykey = $this->arguments['arraykey']; 
		$arrayvalue = $this->arguments['arrayvalue'];
		$joDel = $this->arguments['joDel'];
		$joReturn = [];
		if ($arraykey !== NULL && $arrayvalue !== NULL) {
			$joReturn[$arraykey] = $arrayvalue;
		}
		if ($joDel) {
			$joReturn['joDel'] = 1;
		}
		return $joReturn;
	}
}
