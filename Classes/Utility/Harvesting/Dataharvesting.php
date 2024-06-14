<?php
namespace JO\JoMuseo\Utility\Harvesting;

class Dataharvesting
{
    /**
	 *	Setz einen RDF Header und holt die Daten via Curl
	 */
	public function getRDFData($url = NULL)
	{
		if ($url != NULL) {
			$headers = [
				'Accept: application/rdf+xml',
				'Content-type: application/rdf+xml; charset=UTF-8'
			];
			return $this->getExternalData($url, $headers);
		}
	}
	
	 /**
	 *	Holt eine GeoNamesRessource als JSON
	 */
	public function getGeoNamesResource($id = NULL)
	{
		if ($id != NULL) {
			$geonames_api_username = "";
			return json_decode(file_get_contents("http://api.geonames.org/hierarchyJSON?geonameId=" .$id. "&username=" . $geonames_api_username));
		}
	}
	
	/**
	 *	Rückgabe eines Datenstroms
	 */
	public function getExternalData($url = NULL, $headers = [])
	{
		if ($url != NULL && !empty($headers)) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			$response = curl_exec($ch);
			return $response;
		}
	}
	
	/**
	 *	Erzeugen eines XML Strings aus einem String
	 */
	public function makeXMLContent($string = NULL)
	{
		if ($string != NULL) {
			$xmlcontent = simplexml_load_string($string);
			if (is_object($xmlcontent)) {
				$namespaces = $xmlcontent->getNamespaces(true);
				if (!empty($namespaces)) {
					foreach ($namespaces as $key => $value) {
						$xmlcontent->registerXPathNamespace($key, $value);
					}
				}
			}
			return $xmlcontent;
		}
	}
}
