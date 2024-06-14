<?php
namespace JO\JoMuseo\Utility\Arrayfunc;

class Joarrayfunctions
{
	 
	/**
	 *	Entfernt einen spezifischen Wert aus einem gegebenen Array
	 *
	 *	@var array $joArray -> Array aus dem der entsprechenden Wert entfernt werden soll
	 *	@var string $joElementToDelete -> Element, das entfernt werden soll
	 *
	 *	@return	Array -> bereinigtes Array
	 */
	public function joEliminateArrayValueAndKey($joArray = [], $joElementToDelete = "")
	{
		if (!empty($joArray) && !empty($joElementToDelete)) {
			$joKeyToDelete = array_search($joElementToDelete, $joArray);
			if ($joKeyToDelete !== FALSE) {
				unset($joArray[$joKeyToDelete]);
				$joArray = array_values($joArray);
			}
		}
		return $joArray;
	}
	
	/**
	 *	Fügt einem gegebenen Array einen Wert hinzu und macht es unique
	 *
	 *	@var array $joArray -> Array aus dem der entsprechenden Wert hinzugefügt werden soll
	 *	@var string $joElementToAdd -> Element, das hinzugefügt werden soll
	 *
	 *	@return	Array -> bereinigtes Array
	 */
	public function joAddToArrayAndMakeUnique($joArray = [], $joElementToAdd = "")
	{
		if (empty($joArray)) {
			$joArray = [];
		}
		if (!empty($joElementToAdd)) {
			array_push($joArray, $joElementToAdd);
			$joArray = array_unique($joArray);
			$joArray = array_values($joArray);
		}
		return $joArray;
	}
	
	/**
	 *	Löscht aus einem Array alle Keys außer den übergebenen
	 *
	 *	@var array $joArray -> Array das geleert werden soll
	 *	@var string $joKeysToKeep -> Elemente, die beibehalten werden sollen
	 *
	 *	@return	Array -> bereinigtes Array
	 */
	public function joDeleteArrayExcept($joArray = [], $joKeysToKeep = [])
	{
		if (!empty($joArray)) {
			if (!empty($joKeysToKeep)) {
				$joTempArray = [];
				foreach ($joKeysToKeep as $value) {
					$joTempArray[$value] = $joArray[$value];
				}
				$joArray = [];
				$joArray = $joTempArray;
			} else {
				$joArray = [];
			}
		}
		return $joArray;
	}
}
