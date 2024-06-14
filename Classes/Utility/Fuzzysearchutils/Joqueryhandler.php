<?php
namespace JO\JoMuseo\Utility\Fuzzysearchutils;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class Joqueryhandler extends ActionController
{
	
	const JODELIMITER = '$';	//	Standarddelimiter für die Konkatination von IDs und Namen
	public $joReturnValue = "name";	// Standardreturnvalue für die Callbackmethode
	public $josearchmode = "fuzzy";	// Modus für die Solrqueries "fuzzy" oder "sharp"
	
	/**
	 *	Callbackmethode für Arrayoperationen
	 */
	public function joArrayCallback($value)
	{ 
		$valueArray = explode ( self::JODELIMITER , $value );
		switch ($this->joReturnValue) {
			case "name":				// $joModyfiedValue = "*".$valueArray[0]."*";	//	Volltextsuche
										$joModyfiedValue = "(*" . $valueArray[0] . "* OR " . $valueArray[0] . ")";	//	Volltextsuche
										break;
										
			case "idmitstern":			$joModyfiedValue = "*" . $valueArray[1] . "*";	//	Personen, Orts - und Typfilter
										break;
			
			case "id":					$joModyfiedValue = $valueArray[1];
										break;
									
			case "fullname":			$joModyfiedValue = $valueArray[0];
										break;	
										
			case "quotes":				if($valueArray[0] = '*'){
											$joModyfiedValue = $valueArray[0];
										}else{
											//$joModyfiedValue = '"'.$valueArray[0].'"';
											$joModyfiedValue = $valueArray[0];
										}
										break;	
										
			case "firstletter":			$joModyfiedValue = strtoupper(substr($valueArray[0], 0, 1));	//	erster Buchstabe des Namens	isoliert und groß
										break;		
										
			case "geofilt":				$joModyfiedValue = "&facet.query=" . urlencode("{!geofilt d=" . $valueArray[0] . " key='" . ($valueArray[0] * 1000) . "s'}");	//	Geofacette für Suche nach Lon/Lat Values - der Key wird von KM in Meter umgerechnet - Key muss ein string sein, da chrome das JSON array sonst sortiert wenn es numerisch ist
										// echo "&facet.query={!geofilt d=".$valueArray[0]." key='".($valueArray[0]*1000)."s'}";
										break;	
										
			case "geo":					$joModyfiedValue = "&facet=true&facet.query=" . urlencode("{!frange l=" . $valueArray[0] . " u='" . ($valueArray[0] + 100) . "'}geodist()");	//	Geofacette für Suche nach Lon/Lat Values - der Key wird von KM in Meter umgerechnet - Key muss ein string sein, da chrome das JSON array sonst sortiert wenn es numerisch ist
										break;	
		}
		return ($joModyfiedValue); 
	}
	
	/**
	 *	Funktion zum Erzeugen gehighlighterter Suchtreffer
	 *
	 *	hl.preserveMulti=true - erhält die Reihenfolge im Arrayindex bei Multivaluefields - ohne diesen Parameter würde NUR der gehighlightete Treffer mit einem neu nummerierten Index erscheinen
	 *
	 *	@param array $joHighlightArray - Array mit den Feldern, in denen gehighlighted werden soll
	 *	@param int $joFragsize - Größe der Texte, die in die Highlight Section einfließen
	 *	@param int $joSnippets - Anzahl der Textblöcke, die analysiert werden sollen innerhalbe eines Multivalue feldes
	 *	@return	string	- Querystringsequenz
	 *
	 */
	public function joSetHighlighter($joHighlightArray = array(), $joFragsize = 0, $joSnippets = 10)
	{ 
		$joHighlightQueryPart = '';
		if (!empty($joHighlightArray)) {
			$joHighlightQueryPart = '&hl=true&hl.usePhraseHighlighter=true&hl.preserveMulti=true';
			$joHighlightQueryPart .= '&hl.fragsize=' . $joFragsize;
			$joHighlightQueryPart .= '&hl.snippets=' . $joSnippets;
			$joHighlightQueryPart .= '&hl.fl=' . implode(',', $joHighlightArray); 
		}
		return $joHighlightQueryPart;
	}
	
	/**
	 *	Funktion zum Erzeugen eines Facettenqueries
	 *
	 *	@param array $query_params - Array aus den einzelnen Suchfacetten und Konfigurationen
	 *	@return	string	- nicht url-encoded Querystring
	 */
	public function generateFacetteQuery($query_params = []) : string
 	{
		$facette_query = '';
		if (!empty($query_params && isset($query_params['f']))) {
			/**
			 *	Default Sorting für die Facetten
			 *	index -> alphabetisch
			 *	count -> Anzahl Treffer pro Kategorie
			 *	Einzelne Facetten können abweichend vom Default wie folgt sortiert werden:
			 *	&f.Fieldname.facet.sort=index (oder eben "count")
			 */ 
			$sort_facet = "index";									// Kann noch dynamisch gemacht werden
			$query_params['f'][] = "facet=true";					// Facettierung aktivieren
			$query_params['f'][] = "facet.mincount=1";				// Ab diesem Wert, wird die Facette ausgespielt
			$query_params['f'][] = "facet.sort=" . $sort_facet;		// Sortierung der Facette
			//if(isset($query_params['facette_limit'])){
			if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('test1')) {
				// Geht noch nicht
				$query_params['f'][] = 'facet.limit=' . $query_params['facette_limit'];			// Anzahl der Facettenwerte, die ausgegeben werden sollen
				if($query_params['facette_offset']){
					$query_params['f'][] = 'facet.offset=' . $query_params['facette_offset'];	// Start der Zählung der Facetten (Paginierung)
				}
            } else {
            	$query_params['f'][] = 'facet.limit=-1';			// Alle Werte der Facette sollen ausgegeben werden
            }
            if($query_params['facette_prefix'] && $query_params['facette_prefix'] != '?' && 1== 2){
            	// Hier können mehrere Prefixe benutzt werden - muss noch ausgearbeitet werden
            	// https://stackoverflow.com/questions/31340400/multiple-facet-prefix-on-a-single-facet-field
            	// $query_params['f'][] = '&facet.field={!key=classificationtags_a+facet.prefix=44}classificationtags&facet.field={!key=classificationtags_b+facet.prefix=' . $query_params['facette_prefix'] . '}classificationtags';
				if(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('cr')){
					// $query_params['f'][] = $this->generateFacettePrefix($query_params);
					$query_params['f'][] = "facet.field={!key=classificationtags_a+facet.prefix=(p}classificationtags";
					$query_params['f'][] = "facet.field={!key=classificationtags_b+facet.prefix=p}classificationtags";
					$query_params['f'][] = "facet.field={!key=classificationtags_c+facet.prefix=P}classificationtags";
					$query_params['f'][] = "facet.field={!key=classificationtags_d+facet.prefix=(P}classificationtags";
					// $query_params['f'][] = 'facet.prefix=' .  '(p';
					\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($query_params);

				} else {
					$query_params['f'][] = 'facet.prefix=' .  $query_params['facette_prefix'];
				}		
			}
			$facette_query = '&' . implode('&' , $query_params['f']);
			
		}
		return $facette_query;
	}

	// Spatial Search für Geodata - @all -> finale Struktur der Methode - es sind aktuel lat1 und lat2 vertauscht, liegt an der falschen übergabe der parameter
	public function makeRangeQuery($field_name = 'geoLocation', $bounds = [])
	{
		 if (!empty($bounds) && $bounds['x1'] && $bounds['x2']  && $bounds['y1'] && $bounds['y2']) {
			// @all -> der querytype kann/sollte an anderer stelle vergeben werden
			$query_array = [ 
				'query_type' => 'fq=',
				'field_name' => $field_name,
				'colon' => ':',
				'start_bracket' => '[',
				'bounds_start' => implode(",", [ 
					'lat_start' => $bounds['y2'],
					'lon_start' => $bounds['x1']
				]),
				'to' => '+TO+',
				'bounds_end' => implode(",", [
					'lat_end' => $bounds['y1'],
					'lon_end' => $bounds['x2']
				]),
				'end_bracket' => ']'
			];
			return implode('', $query_array);
        } else {
			exit('Die Rangeparameter sind fehlerhaft. Bitte prüfen Sie die übergebenen Variablen.');
		}
	}
	/**
	 *	Funktion zur Erstellung von partiellen Solr Queries 
	 *
	 *	@param array $joItems - Array aus Werten eines Feldes, die in den Querystring einbezogen werden sollen
	 *	@param string $joConcat - String, mit dem die Werte konkatiniert werden sollen (z.B. " OR " oder " AND ")
	 *	@param string $joFieldname - Feldname aus dem die Werte ermittelt werden sollen
	 *	@param string $joWildcardSearch - Suche nach inkrementellen Daten nötig
	 *	@return	string	- nicht url-encoded Querystring
	 */
	public function joMakeQuerypart($joItems = [], $joConcat = " OR ", $joFieldname = 'id', $joWildcardSearch = FALSE)
	{
		if (!empty($joItems)) {
			if ($joWildcardSearch == TRUE) {
				$joCallback = array($this, 'joArrayCallback');
				$joItems = array_map($joCallback, $joItems);	//Wildcardsuche vorbereiten -> *string*
			} else {
				/**
				 *	@all -> das besser umsetzen
				 */
				foreach ($joItems as $key => $value) {
					if ($value != "*") {
						$value = str_replace('"', '\"', $value);
						$joItems[$key] = '"' . $value . '"';
					}
				}
			}
			$joMainQueryDataToSearch = implode($joConcat, $joItems);
			if (count($joItems) > 1) {
				$joMainQueryDataToSearch = "(" . $joMainQueryDataToSearch . ")";	// Einklammern wenn mehrere Datentypen erfasst werden sollen
			}
			$joQuery = $joFieldname . ':' . $joMainQueryDataToSearch;
		}
		return $joQuery;
	}
	
	

	/**
	 *	Funktion zum Generieren des dismax/edismax queries
	 *	bei Bedarf können hier noch weitere Parameter hinzugefügt werden
	 *	@param array $conf - Array mit den Parametern
	 *	$conf = array(
	 *		'defType' => 'edismax',	
	 *		'qf' => array(		// qf => query fields (Felder die berücksichtigt werden)
	 *			'title' => 4,	// Feldname => boost (Gewicht der entsprechenden Felder)
	 *				'entity' => 2,
	 *				'fulltext' => 1
	 *			),
	 *			'pf' => array(		// pf => phrase fields (Wenn zu dem Suchquery passende Dokumente identifiziert wurden, werden alle Objekte, bei denen die Suchbegriffe nah beieinander liegen höher gerankt)
	 *				'title' => 50,	// Feldname => boost (Gewicht der entsprechenden Felder in denen die Suchbegriffe GEMEINSAM vorkommen)
	 *				'entity' => 25		
	 *			),
	 *			'ps' => 15		// ps => phrase slop (Bezug zu pf - gibt den maximalen erlaubten Abstand zwischen den Suchbegriffen an, um sie als noch zusammengehörig zu definieren)
	 *			'mm' => -1,		// Anzahl der Begriffe, die mindestens zutreffen müssen -1 => immer ein Begriff weniger als eingegeben muss im Treffer vorhanden sein
	 *			'tie' => 0.1	// Wichtung, wie stark die niedrig gerankten Felder die höher gerankten tatsächlich beeinflussen
	 *	);
	 *	@param string $parser_type - edismax/dismax 
	 *	@return	string	- Querystringsequenz/URL-Encoded
	 */
	public function setDisMaxQuery($conf = [])
	{
		$return = NULL;
		if (!empty($conf)) {
			if ($conf['defType'] === 'edismax' || $conf['defType'] === 'dismax') {
				// Transformation des Conf-Arrays: http_build_query() - deutlich performanter als implode und foreach!
				if (!empty($conf['qf']))$conf['qf'] = str_replace('=', '^', http_build_query($conf['qf'],'',' '));
				if (!empty($conf['pf']))$conf['pf'] = str_replace('=', '^', http_build_query($conf['pf'],'',' '));
				$return = http_build_query($conf,'','&');
			} else {
				exit('Parsertyp ist nicht spezifiziert');
			}
		}
		return $return;
	}

	
	/**
	 *	Funktion zum Reinigen der Useranfragen
	 *
	 *	Kommata, Semikolon und Punkte werden aus der Anfrage entfernt
	 *
	 *	@param array $joSearchEntry - Usereingabe, die um Sonderzeichen bereinigt werden soll
	 *	@return	string	- nicht url-encoded Querystring
	 */
	public function joCleanSearchstringFull($joSearchEntry = '')
	{
		if (!empty($joSearchEntry)) {
			$search = ['.', ',', ';','"'];
			$replace = ['\.', '\,', '', ''];
			$joSearchEntry = filter_var(str_replace($search, $replace, $joSearchEntry), FILTER_SANITIZE_STRING); 		
		}
		return $joSearchEntry;
	}
	
	/**
	 *	String analysieren und Teilstrings auswählen
	 *	Alles was zwischen zwei Anführungszeichen steht wird als Phrase behandelt
	 */
	public function splitSearchEntry($josearchentry)
	{
		$betweenQuotes = [];
		$noQuotes = [];
		$searchpattern = '/("|\')(.*)("|\')/U';
		$josearchentry = str_replace("+", " ", $josearchentry);	// + in Leerzeichen konvertieren
		preg_match_all($searchpattern, $josearchentry, $betweenQuotes);
		$josearchentry = preg_replace($searchpattern, '', $josearchentry);
		$noQuotes = explode(' ', $josearchentry);
		$josearchentry = array_merge($betweenQuotes[0], $noQuotes);
		$joCallback = array($this, 'joCleanSearchstringFull');
		$josearchentry = array_map($joCallback, $josearchentry);
		$josearchentry = array_values(array_filter($josearchentry));
		return $josearchentry;
	}


	/**
	 *	Funktion zum Generieren der Sortierreihenfolge
	 *
	 *	@param array $sorting - Array mit den Feldern, nach denen sortiert werden soll
	 *	@return	string	- Querystringsequenz
	 */
	public function joSetOrdering($sorting = [])
	{ 
		$return = '';  
		$joSortQueryPart = [];
		if (!empty($sorting['active'])) {
			$score_params = '';	//@all -> das evtl noch dynamisch machen
			//if (key($sorting['active']) == 'score') {
				$conf = [
					'defType' =>'edismax',
					'qf' => [ 
						'title' => 1800,
						'inventarnummer' => 1900,
						'shelfmark' => 1900,
						'titleAlt' => 200,
						'titleExact' => 1800,	
						'entity' => 800,
						'location' => 800,
						'tenant' => 2,
						'fulltext' => 0.05
					],
					'pf' => [	
						'title' => 1800,
						'inventarnummer' => 1900,
						'shelfmark' => 1900,
						'titleAlt' => 100,
						'titleExact' => 2600,	
						'entity' => 400,
						'location' => 400,
						'tenant' => 1,
						'fulltext' => 0.01	
					],
					'ps' => 1,
					'mm' => -1,
					'tie' => 0.1
				];
				$score_params = "&" . $this->setDisMaxQuery($conf);
			//}
			// @all -> Aktuell geht das noch nicht - wir müssen zunächst den Index komplett löschen
			/*
			'pf' => array(		
						'titleTokenized' => 150000,	
						'entityTokenized' => 4,
						'locationTokenized' => 4,
						'fulltext' => 0.01						
					),
			
			*/
			foreach ($sorting['active'] as $key => $value) {
				$joSortQueryPart[] = $key . " " . $value[0];	
			}
			$return = '&sort=' . urlencode(implode(", ", $joSortQueryPart)) . $score_params;
		}
		return $return;
	}

	/**
	 *	Funktion zur Ausgabe der gehighlighterter Suchtreffer
	 *
	 *	@param stdClass $solrobjects - Solr Responseobject mit dem Highlight object $solrobjects->highlighting
	 */
	public function returnHighlightString($solrobjects)
	{ 
		if (isset($solrobjects) && isset($solrobjects->response->docs) && isset($solrobjects->highlighting)) {
			$joHighlightObject = $solrobjects->highlighting;
			if (is_object($solrobjects->highlighting)) {
				$joHighlightObject = array_filter(json_decode(json_encode($joHighlightObject), true));
			}
			// print_r($solrobjects->highlighting);
			if (!empty($joHighlightObject)) {
				foreach ($solrobjects->response->docs as $key => $value) {
					$refId = $value->id;
					if ($joHighlightObject[$refId] && count($joHighlightObject[$refId]) > 0) {
						foreach ($joHighlightObject[$refId] as $subkey => $subvalue) {
							if (!empty($subvalue)) {
								// wenn es ein Volltextfeld ist, wird es IMMER ein Array
								//@all -> das kann ruhig dynamischer gemacht werden - aktuell wird nur dieses eine Feld berüksihtigt
								if ($subkey == "fulltextClean") {
									$value->$subkey = $subvalue;
									// print_r($value->$subkey);
								} else {
									if (is_array($value->$subkey)) {
										// Multivalue - Field
										$value->$subkey = $subvalue;
									} else {
										// Singlevalue - Field
										$value->$subkey = $subvalue[0];
									}
								}
							}
						}
						$solrobjects->response->docs[$key] = $value;
					}					
				}
			}
		}
		return $solrobjects;
	}

	public function joMakeQuerypartBetter($joItems = [], $joConcat = " OR ", $joFieldname = 'id')
	{
		$joQuery = '';
		if (!empty($joItems)) {
			$fuzzysuffix = "";
			if ($this->josearchmode == "fuzzy") {
				$fuzzysuffix = "*";
			}

			// Wenn es eine Expertensuche ist, wird der begriff "Expert" aus dem Feldnamen extrahiert
			if (strpos ( $joFieldname , "Expert") > 0) {
				$joFieldname = str_replace("Expert", "", $joFieldname);
			}
			/**
			 *	Truncated Search
			 *	berücksichtigt *, ? und "
			 *	* - Beliebige Zeichen können folgen (Rechtstrunkierung)
			 *	? - Exakt ein Zeichen MUSS folgen
			 *	"Suchwort1 Suchwort2" - beide Worte werden als Phrase interpretiert
			 */
			foreach ($joItems as $key => $value) {
				if ($value != "*") {
					$phrasearray = explode(" ", $value);
					$lastLetter = substr(end($phrasearray), -1, 1);
					if (!empty($phrasearray) && count($phrasearray) == 1) {
						if ($lastLetter == "*") { 
							$valueWithout = str_replace("*", "", $value);
							$value = "(" . $value . " OR " . $valueWithout . ")";
						}
						if ($lastLetter != "?" && $lastLetter != "*") {
							$value = "(" . $value . " OR *" . $value . "*)";
							// $value = "*".$value."*";
						}
					}
					//	Bei Suche mit o Fragezeichen wird auch bei unscharfer Suche kein * angehängt
					if ($lastLetter != "?" && $lastLetter != "*") {
						// $value = $value.$fuzzysuffix;
					}
					//	Wenn nach einer Phrase gesucht wird, funktionieren * und ? nur mit dem Complex Phrase Query Parser
					if (!empty($phrasearray) && count($phrasearray) > 1) {
						$value = '"'.$value.'"';
					}
					$joItems[$key] = $value;
				}
			}
			$joMainQueryDataToSearch = implode($joConcat, $joItems);
			if (count($joItems) > 1) {
				// Einklammern wenn mehrere Datentypen erfasst werden sollen
				$joMainQueryDataToSearch = "(" . $joMainQueryDataToSearch . ")";	
			}
			$joQuery = $joFieldname . ':' . $joMainQueryDataToSearch;
			// wenn es eine Anfrage an das Fulltextfeld ist, wird der Query ohne dieses Feld gebaut
			// @all -> das kann evtl generell so gemacht werden, weil sonst der Score der Solr Anfrage nicht greift
			if ($joFieldname == "fulltext") {
				$joQuery = $joMainQueryDataToSearch;
			}
		}
		return $joQuery;
	}
}
