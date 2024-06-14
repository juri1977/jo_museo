<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

class ReturnarrayvalueViewHelper extends AbstractFormFieldViewHelper
{
	/**
	 *
	 * @param	string	$keyname		Name des Arrayschl�ssels den ich ben�tige
	 * @param	array	$arrayname		Name des Arrays von dem ich einen Schl�sel ben�tige
	 * @return 	mixed 	Array value zu einem Schl�ssel
	 */
	public function render($keyname = '', $arrayname = [])
	{
		$keyvalue = null;
		if ($arrayname && $keyname != '') {
			if($keyname == "last"){
				$keyvalue = end($arrayname);
			} else if ($keyname == "first") {
				$keyvalue = reset($arrayname);
			} else if ($keyname == "penultimate") {
				end($arrayname);
				$keyvalue = prev($arrayname);
			} else {
				$keyvalue = $arrayname[$keyname];
			}
		}
		return $keyvalue;
	}
}
