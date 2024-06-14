<?php
namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class PaginatorViewHelper extends AbstractViewHelper
{
	/**
	* @param string $string List of values
	* @param string $joitems articles per page
	* 
	* @return array
	*/
	public function render($string,$joitems)
	{
		$joPaginateCountArray = [];
		$joCountArray = $string;
		$joPaginateCount = ceil($joCountArray / $joitems);
		for ($i = 0; $i < $joPaginateCount; $i++) {
			$joPaginateCountArray[$i] = $i + 1;
		}
		return $joPaginateCountArray;
	}
}
