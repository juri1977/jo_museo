<?php
namespace JO\JoMuseo\Utility\Fuzzysearchutils;

class Jomakeindex
{

    public $joDelimiter = ""; //    Delimiter zischen den einzelnen Bestandteilen des Strings

    public $joSpalten = 5; //    Anzahl der Spalten, auf die die Begriffe aufgeteilt werden sollen

    public $joSimpleSplit = false; //    Flag für die einfache Zerteilung des Arrays anhand der Anzahl der Keys

    public $joGetKeyOrValue = "value"; //    Columsplit bezieht sich auf Key oder Values eines Arrays

    public $joSpecifiedLetterOnly = '*'; //    Wenn eine Facette alphabetisch aggregiert werden soll, werden hier abweichende Werte aus Multivaluefields rausgeworfen

    public $joSearchArray = []; //    Array mit den eigegebenen Suchbegriffen (ggf. obsolete - prüfen)

    /**
     *    Funktion zum Erzeugen der Facettennavigation
     *
     *     @var $joFacetteResult array - Array mit den enthaltenen Facetten (facet_counts->facet_fields)
     *    @var $joSearcharraycomplete array - Array mit den eingegebenen Suchtreffern
     *    @return array -> modifiziertes Facettenarray
     */
    public function joMakeFacette($joFacetteResult = [], $joSearcharraycomplete = [])
    {
        $joFacetteArray = [];
        if (!empty($joFacetteResult)) {
            foreach ($joFacetteResult as $mainkey => $mainvalue) {
                foreach ($mainvalue as $key => $value) {
                    if (($key % 2) === 0) {
                        $joFacetteArray[$mainkey][$key]['name'] = $value;
                        if (!empty($joSearcharraycomplete[$mainkey]) && in_array($value, $joSearcharraycomplete[$mainkey])) {
                            $joFacetteArray[$mainkey][$key]['remove'] = 1;
                        }
                    } else {
                        $joFacetteArray[$mainkey][$key - 1]['count'] = $value;
                    }
                }
            }
        }                   
        return $joFacetteArray;
    }

    /**
     *    Entwicklung eines alphabetischen indexes aus einem redundanten Array
     */
    public function joMakeIndex($joItemIndexArray = [])
    {
        $joIndexAndFull = [];
        if (!empty($joItemIndexArray)) {
            $joItemIndexArray = array_unique($joItemIndexArray);
            $joIndexAlphabet = range('A', 'Z');
            $joIndexAndFull = array_flip($joIndexAlphabet);

            foreach ($joIndexAndFull as $key => $value) {
                $joIndexAndFull[$key] = [];
            }
            $joSimple = false;
            foreach ($joItemIndexArray as $value) {
                //$joFirstletter = substr($value, 0, 1);
                if($value->getTitle()){
                    $joFirstletter = substr($value->getTitle(), 0 , 1);
                }else{
                    $joFirstletter = substr($value, 0 , 1);
                }
                if (in_array($joFirstletter, $joIndexAlphabet)) {
                    $joIndexAndFull[$joFirstletter][] = $value;
                }
            }
            foreach ($joIndexAndFull as $key => $value) {
                sort($value);
                $joIndexAndFull[$key] = $this->joColumSplit($value);
            }
        }
        return $joIndexAndFull;
    }

    /**
     *    Facetten reorganisieren
     *    Daten kommen in der Form array(0 => "facettenname", 1 => facettencount)
     *    Rückgabe: array("facettenname" => facettencount)
     */
    public function reorderFacetteArray($joFacette = [], $joFacetteType = "simple", $facette_context = null)
    {
        /*
        echo "<pre>";
        print_r($facette_context);
        echo "</pre>";
        */
        
        if (!empty($joFacette)) {
            $joFacetteReordered = [];
            $i = 0;
            foreach ($joFacette as $value) {
				$value = \Normalizer::normalize($value, \Normalizer::FORM_C);
                switch ($joFacetteType) {
                    case "simple":
                        if ($i % 2 == 0) {
                            if ('*' == $this->joSpecifiedLetterOnly || ('*' != $this->joSpecifiedLetterOnly && ord(strtolower($this->joSpecifiedLetterOnly)) == ord(strtolower($value[0])))) {
                                $joFacetteReordered[$value] = $joFacette[$i + 1];
                            }
                            // Umlaute und Sonderzeichen beachten bei Alphabetischen Facetten 'Ł'=> 'L' durch iconv
                            if ('*' != $this->joSpecifiedLetterOnly && strtolower($this->joSpecifiedLetterOnly) == strtolower(substr(iconv('UTF-8', 'ASCII//TRANSLIT', $value), 0, 1))) {
                                $joFacetteReordered[$value] = $joFacette[$i + 1];
                            }
							//@all -> das muss noch verbessert werden - nur das vorangestellte Sonderzeichen ist anders 
							if ('*' != $this->joSpecifiedLetterOnly && strtolower("(".$this->joSpecifiedLetterOnly) == strtolower(substr(iconv('UTF-8', 'ASCII//TRANSLIT', $value), 0, 2))) {
                                $joFacetteReordered[$value] = $joFacette[$i + 1];
                            }
							if ('*' != $this->joSpecifiedLetterOnly && strtolower("[".$this->joSpecifiedLetterOnly) == strtolower(substr(iconv('UTF-8', 'ASCII//TRANSLIT', $value), 0, 2))) {
                                $joFacetteReordered[$value] = $joFacette[$i + 1];
                            }
							if ('*' != $this->joSpecifiedLetterOnly && strtolower("'".$this->joSpecifiedLetterOnly) == strtolower(substr(iconv('UTF-8', 'ASCII//TRANSLIT', $value), 0, 2))) {
                                $joFacetteReordered[$value] = $joFacette[$i + 1];
                            }
							if ('*' != $this->joSpecifiedLetterOnly && strtolower("'".$this->joSpecifiedLetterOnly) == strtolower(substr(iconv('UTF-8', 'ASCII//TRANSLIT', $value), 0, 2))) {
                                $joFacetteReordered[$value] = $joFacette[$i + 1];
                            }
							if ('*' != $this->joSpecifiedLetterOnly && strtolower("ʿ" . $this->joSpecifiedLetterOnly) == strtolower(substr( $value, 0, 3))) {
                                $joFacetteReordered[$value] = $joFacette[$i + 1];
                            }
                        }
                        $i++;
                        break;
                    case "timeline":
                        if ($i % 2 == 0) {
                            // Es werden nur Zeiten berücksichtigt, die zwischen -2500 und 2300 liegen
                            if($value < 2300 && $value > -2500){
                                $joFacetteReordered[$value] = $joFacette[$i + 1];    
                            }
                        }
                        $i++;
                        break;
                    case "pivot":
                        foreach ($value->pivot as $subvaluevalue) {
                            $joFacetteReordered[$subvaluevalue->value][$value->value] = $subvaluevalue->count;
                        }
                        break;
                    case "range":
                        if ($i % 2 == 0) {
                            $range_end = $facette_context->end;
                            if(isset($joFacette[$i + 2])) {
                                $range_end = $joFacette[$i + 2];
                            }
                            $joFacetteReordered[$value . ' - ' . $range_end] = $joFacette[$i + 1];
                            if(!isset($joFacette[$i + 2])){
                                if($facette_context->after > 0){
                                    $joFacetteReordered[$facette_context->end . ' - ' . '*'] = $facette_context->after;
                                    $joFacetteReordered['1_scalemetric_novalue'] = 'x';
                                }
                            }
                        }
                        $i++;
                        break;
                }
            }
            return $joFacetteReordered;
        }
    }
    

    /**
     *    Leeres Array mit allen Buchstaben des Alphabetes erstellen, umkehren und jeden Buchstaben initial auf 0 setzen
     *
     *    @return array -> Array mit allen vorhanden Anfangsbuchstaben
     */
    public function initAlphabet() : array
    {
        $index_alphabet = array_flip(range('A', 'Z'));
        $null_it_all = function ($value) {return 0;};
        return array_map($null_it_all, $index_alphabet);
    }

    /**
     *    Entwicklung eines alphabetischen indexes aus den alphabetischen Facetten
     *
     *    @var array $item_index_array -> Array mit den Buchstabenfacetten und Zahlen, die modifiziert werden sollen
     *    @return array -> Array mit allen vorhanden Anfangsbuchstaben
     */
    public function makeIndexAlphabet($item_index_array = []) : array
    {
        $index_merged = array();
        if (!empty($item_index_array)) {
            /**
             *    Indexarray mit den Facetten umsortieren - Aufbau:
             *    Array
             *    (
             *        [0] => C -> Buchstabe
             *        [1] => 1977 -> Anzahl, wie oft der Buchstabe vorkommen im Suchergebnis
             *    )
             */
            $i = 0;
            $index_sorted = [];
            foreach ($item_index_array as $value) {
                if ($i % 2 == 0) {
                    // Nur alphanumerische Werte und das ? werden bei den alphabetischen Facetten berücksichtigt - zum Reinigen des Solrindexes müsste diese Prüfung rausgenommen werden
                    if(ctype_alnum($value) || $value == "?"){
                        $index_sorted[$value] = $item_index_array[$i + 1];
                    }
                }
                $i++;
            }
            // Leeren Alphabetindex erzeugen
            $index_alphabet = $this->initAlphabet();

            // Array aus den gefundenen Buchstabenfacetten mit dem leeren Array aller Buchstaben zusammenführen und sortieren
            $index_merged = array_replace($index_alphabet, $index_sorted);
            uksort($index_merged, [\Collator::create('de_DE'), 'compare']);
        }
        return $index_merged;
    }

    /**
     *    Achtung -> es werden nur die Keys analysiert!!
     */
    public function joColumSplit($joSortObject = null)
    {
        if (null != $joSortObject) {
            uksort($joSortObject, [\Collator::create('de_DE'), 'compare']);
            $i = 0;
            $index = 0;
            $joSpalten = $this->joSpalten;
            if (true == $this->joSimpleSplit) {
                $joSortObject = array_filter($joSortObject);
            }
            $joModulo = count($joSortObject) % $joSpalten;
            $joArrayMinSize = (count($joSortObject) - $joModulo) / $joSpalten;

            if (!empty($joSortObject)) {
                $joTempObjects = [];
                for ($x = 0; $x < $joSpalten; $x++) {
                    $joAdditionalElement = 0;
                    if ($joModulo > 0) {
                        $joAdditionalElement = 1;
                        $joModulo--;
                    }
                    if (true == $this->joSimpleSplit) {
                        $joItemsResultModified['columns'][$x] = array_splice($joSortObject, 0, $joArrayMinSize + $joAdditionalElement);
                    } else {
                        $joTempObjects[$x] = array_splice($joSortObject, 0, $joArrayMinSize + $joAdditionalElement);
                        foreach ($joTempObjects[$x] as $key => $value) {
                            $valueArray = [];
                            $valueArray = explode($this->joDelimiter, $key); //    Uid und Wert voneinander trennen
                            $valueArray[] = $key;
                            $valueArray[0] = $valueArray[0] . " (" . $value . ")"; // Zahl hinzufügen
                            $valueArray['fullsearchstring'] = $key;
                            $joItemsResultModified[$x][] = $valueArray;
                        }
                    }
                }
            }
            return $joItemsResultModified;
        }
    }
	
    public function joColumSplitNew($joSortObject = null)
    {
        if (null != $joSortObject) {
		/*
			echo "<pre>";
			print_r($joSortObject);
			echo "</pre>";
		*/
                       // ksort($joSortObject, SORT_NATURAL);
            // uksort($joSortObject, [\Collator::create('de_DE'), 'compare']);
            /*
            setlocale(LC_ALL, "de_DE.UTF-8");
            $trans = $joSortObject;
            array_walk($trans, function (&$data) {
                $data =  iconv("UTF-8", 'ASCII//TRANSLIT//IGNORE', $data);
            });
            ksort($trans, SORT_NATURAL);
            $joSortObject = array_replace($trans, $joSortObject);
            print_r($joSortObject);
            */

            //ksort($joSortObject, SORT_NATURAL);
			//setlocale(LC_COLLATE, 'de_DE.UTF-8');
            // die nächste Zeile sortiert die Zahlen falsch - müssen wir ändern
            // [$this,'Sortify']
           // uksort($joSortObject, [$this,'Sortify']);
            // echo $firstelement = (reset($joSortObject));
            // Nur Sortieren, wenn es KEINE Zahlen sind -  die Sortierung entsprechend der Sprachspezifikation ist nicht kompatibel mit der numerischen Sortierung - kann noch optimiert werden
            end($joSortObject);
            $last = (key($joSortObject)!==false) ? key($joSortObject) : null;
            if($last != null){
                $last = intval($last);
                if($last == 0){
                   uksort($joSortObject, 'strcoll');
                }
            }
            reset($joSortObject);
			
            $joSpalten = 3;
			$i = 0;
            if ($this->joSpalten > 0) {
                $joSpalten = $this->joSpalten;
            }
            $joModulo = count($joSortObject) % $joSpalten;
            $joArrayMinSize = (count($joSortObject) - $joModulo) / $joSpalten;

            if (!empty($joSortObject)) {
                $joTempObjects = [];
                for ($x = 0; $x < $joSpalten; $x++) {
                    $joAdditionalElement = 0;
                    if ($joModulo > 0) {
                        $joAdditionalElement = 1;
                        $joModulo--;
                    }
                    /*
                    echo "<pre>";
            print_r( $joSortObject);
            echo "</pre>";
                     */ 
                    // @all -> die Funktion behält die Keys nicht bei - das array wird neu nummeriert und das führt dazu, dass bei ganzzahligen werten diese zahlen neu nummierert werden - array splice problem
                    $joTempObjects[$x] = array_splice($joSortObject, 0, $joArrayMinSize + $joAdditionalElement);
                    /*
                    echo "<pre>";
            print_r( $joTempObjects);
            echo "</pre>";
            */
                    if (!empty($joTempObjects[$x])) {
                        foreach ($joTempObjects[$x] as $key => $value) {
                            $joColumSplitBase = $value; //    Split bezieht sich auf den Arrayvalue
                            if ("key" == $this->joGetKeyOrValue) {
                                $joColumSplitBase = $key;
                            }
                            $valueArray = [];
                            /*
                            echo "<pre>";
                            print_r( $joColumSplitBase);
                            echo "</pre>";
                            */
                            $valueArray = explode($this->joDelimiter, $joColumSplitBase); //    Uid und Wert voneinander trennen
                            $valueArray[] = $joColumSplitBase;
                            $valueArray["orig"] = $joColumSplitBase;
                            $valueArray["number"] = $value;
                            $joItemsResultModified[$x][] = $valueArray;
							$i++;
                        }
                    }
                }
            }
			/*
			echo "<pre>";
			print_r( $joItemsResultModified);
			echo "</pre>";
			*/
            return $joItemsResultModified;
        }
    }

    

    /**
     *    Anzahl der Treffer an das Template geben
     *    Pagination ermitteln
     */
    public function joMakePagination($joSolrObjects, $joLimitPreset, $joPaginatePagesShow, $joAction, $jopaginatecenter)
    {
        $joPaginateDataArray = [];
        $joSolrObjectsFound = $joSolrObjects->numFound;
        if ($joLimitPreset > 0 && $joSolrObjectsFound > 0) {
            $joAnzahlPages = ceil($joSolrObjectsFound / $joLimitPreset);
            /**
             *    Paginatornavigation berechnen insofern mehr Paginatordaten ausgegeben werden als gewünscht sind
             */
            $joStartSequenz = 1; //    An welcher Stelle beginnt die Pagination
            $joEndSequenz = $joAnzahlPages; //    Letzte Paginatorseite
            $joPaginatePagesShowHalf = $joPaginatePagesShow / 2; //    Die Hälfte der anzuzeigenden Paginationsdaten

            if ($joPaginatePagesShow < $joAnzahlPages) {
                $joStartSequenz = $jopaginatecenter - $joPaginatePagesShowHalf;
                $joEndSequenz = $jopaginatecenter + $joPaginatePagesShowHalf;

                if ($joStartSequenz <= 0) {
                    $joStartOffset = abs($joStartSequenz);
                    $joStartSequenz = 1;
                }
                if ($joEndSequenz > $joAnzahlPages) {
                    $joEndOffset = abs($joAnzahlPages - $joEndSequenz);
                    $joEndSequenz = $joAnzahlPages;
                }
                $joStartSequenz = $joStartSequenz - $joEndOffset;
                $joEndSequenz = $joEndSequenz + $joStartOffset;
            }

            /**
             *    Relevante Daten sammeln und an Template geben
             */
            /*
            echo "<pre>";
            print_r($joStartSequenz);
            print_r($joEndSequenz);
            echo "</pre>";
            */
            $joRangeArray = range($joStartSequenz, $joEndSequenz);
            $joPaginateDataArray["action"] = $joAction;
            $joPaginateDataArray["range"] = $joRangeArray;
            $joPaginateDataArray["aktiv"] = $jopaginatecenter;

            $joPaginateDataArray["links"] = $jopaginatecenter - 1;
            if ($joPaginateDataArray["links"] < 1) {
                $joPaginateDataArray["links"] = 1;
            }
            $joPaginateDataArray["rechts"] = $jopaginatecenter + 1;
            if ($joPaginateDataArray["rechts"] > $joEndSequenz) {
                $joPaginateDataArray["rechts"] = $joEndSequenz;
            }
            if (1 != $joRangeArray[0]) {
                $joPaginateDataArray["first"] = 1;
            }
            if ($joRangeArray[(count($joRangeArray) - 1)] != $joAnzahlPages) {
                $joPaginateDataArray["last"] = $joAnzahlPages;
            }
            $joPaginateDataArray["numbershownfrom"] = $joSolrObjects->start;
            $joPaginateDataArray["numbershowntill"] = $joSolrObjects->start + $joLimitPreset;
            if ($joPaginateDataArray["numbershowntill"] > $joSolrObjectsFound) {
                $joPaginateDataArray["numbershowntill"] = $joSolrObjectsFound;
            }
            $joPaginateDataArray["numbershown"] = $joPaginateDataArray["numbershowntill"] - $joPaginateDataArray["numbershownfrom"];
        }
        return $joPaginateDataArray;
    }

    /**
     *    Extrahiert den ersten Buchstaben von Array-Values
     *
     *     @var $n string - Zeichenkette (Values des Arrays)
     *    @return string -> Erster Buchstabe des entsprechenden Array-Values
     */
    public function firstletter($n)
    {
        return (substr($n, 0, 1));
    }

	
	/**
	 *	Methoden zur aufbereitung Hierarchische Facetten
	 * 	Die Facetten werden wie folgt aus Solr übernommen
	 *	Zeitungen (10)
	 * 	Zeitungen/Zeitschriften (2)
	 *	Zeitungen/Bände (5)
	 *	Zeitungen/Artikel (3)
	 *	Zeitungen/Artikel/Gute Artikel (2)
	 *	Zeitungen/Artikel/Schlechte Artikel (1)
	 * 	Delimiter ist: / - In Solr wird hierfür das entsprechende Feld wie folgt konfiguriert - <tokenizer class="solr.PathHierarchyTokenizerFactory" delimiter="/" /> 
	 */
	
	/**
	 *	Methode prüft ob ein hierachisches Element aktiv ist oder nicht
	 *	a => 0 Element ist nicht aktiv - leeres Viereck
	 *	a => 1 Element ist aktiv - der User hat es explizit ausgewählt - roter Haken
	 *	a => 2 Element ist mittelbar aktiv -> weil der User ein übergeordnetes Element ausgewählt hat - grauer Haken
	 *	a => 3 Element enthält ein aktiv ausgewähltes Element in der Downline - roter Kreis
	 */
	public function isActive($search_array_fieldname, $rootline_session, $value, $row){
		$active = 0;
		if (in_array($value, $search_array_fieldname)) {
			// Objekt originär gewählt vom User
			$active = 1;
		} else {
			// Objekt enthält aktives Element in der Downline
			if (!empty($rootline_session)) {
				foreach ($rootline_session as $sub) {
					if ($value == $sub['name'][$row]) {
						$active = 3;
					}
				}
			}
			// Wenn der aktuelle Menüpunkt zu einem aktiv gewählten Hauptmenüpunkt gehört
			if ($active != 3 && !empty($search_array_fieldname)) {
				foreach ($search_array_fieldname as $subvalue) {
					if (substr_count($value, $subvalue . "/") > 0) {
						$active = 2;
					}
				}
			}
		}
		return $active;
	}
	
	/**
     *    Funktion zum Generieren der Sortierreihenfolge
     *
     *    @param array $searcharray - Array mit den ausgewählten Segmenten der Hierachie: array("Menu1/Punkt1/Punkt2/Punkt3","Menu2/Punkt1")
     *    @return    array    - Querystringsequenz
     */
    public function makeRootline($searcharray = array())
    {
		$rootline = array();
		$delimiter = "/";
		if(!empty($searcharray)){
			foreach($searcharray as $value){
				$rootline_array = explode($delimiter, $value);
				if(!empty($rootline_array)){
					$u = 0;
					$root_item = array();
					foreach($rootline_array as $sub){
						$prev_item = '';
						if($u > 0){
							$prev_item = $root_item['name'][($u - 1)]."/";
						}
						$root_item['name'][$u] = $prev_item.$sub;
						$root_item['id'][$u] = md5($root_item['name'][$u]);
						$root_item['name_single'][$u + 1] = $sub;	// +1 weil wir die Rootline für die Spaltenüberschrifen benötigen - hier wird immer der übergeordnete Menüpunkt angezeigt
						$u++;
					}
					$rootline[] = $root_item;
				}
			}
		}
		return $rootline;
	}
	
	/**
	 *	@param object $facettes - entsprechende Facette: array(0 => 'Facettename 1', 1 => Anzahl der Funde Facette 1, 2 => 'Facettename 2', 3 => Anzahl der Funde Facette 2)
	 *	@param string $field_name - Feldname der gewünschten Facette
	 *	@param int $rows_to_show - Welche Hierarchieebenen sollen ausgegeben werden?  - (1 bis x)
	 *	@param array $selected_value - letzter ausgewählter Menüpunkt der Hierarchie array(0 => 'Zeitungen/Artikel')
	 *	@param array $search_array - alle zwischengespeicherten ausgewählten Menüpunkte der Hierarchie array(0 => 'Zeitungen/Artikel', 1 => 'Zeitschriften/Parlamentaria/Artikel')
	 */
	public function getFacetteStructure($facettes = array(), $field_name = NULL, $rows_to_show = 1, $selected_value = array(), $search_array = array()){
       
		$hierarchie_segments = array();
		if($field_name != NULL && !empty($facettes)){
			// rootline ermitteln für die letzte Auswahl (joSelectedValues) und Inhalt der Suchsession($this->joSearcharraycomplete)
			$rootline = end($this->makeRootline($selected_value[$field_name]));		// Rootline des aktuell gewählten Elements
			$rootline_session = $this->makeRootline($search_array[$field_name]);	// Rootline(s) der in der Suchsession gespeicherten Elemente
			$resultcount = count($facettes);
			for ($i = 0; $i < $resultcount; $i += 2) {
				$value = $facettes[$i];
				$row = substr_count($value, '/');
				if ($row >= $rows_to_show) {
					continue;
				}
				if ($i % 2 == 0 && $row < $rows_to_show) {
					if (empty($selected_value[$field_name]) ||
						(!empty($rootline) && $rootline['name'][$row] == $value) ||
						(!empty($rootline) && 0 == $row) ||
						(!empty($rootline) && strpos($value, $rootline['name'][$row - 1]) === 0)
					) {
                        // Prüfen, ob es unter dem jeweiligen Element weitere Hierarchie gibt:
                        // Deutschland/Sachsen/ wird in Deutschland/Sachsen/Dresden gefunden
                        // preg_quote maskiert den Slash zwischen den Elementen
						$sub_elements = 0;
						if (count(preg_grep("/^" . preg_quote( $facettes[$i] . '/', '/') . "/i", $facettes)) > 0) {
							$sub_elements = 1;
						}
                        /*
                        echo "<pre>";
                        print_r(preg_grep("/^" . preg_quote( $facettes[$i] . '/', '/') . "/i", $facettes));
                        echo "</pre>";
                        */
						$active = 0;
						if (is_array($search_array) &&  array_key_exists($field_name, $search_array)) {
							$active = $this->isActive($search_array[$field_name], $rootline_session, $value, $row);
						}
						// Prüfen welche Menüpunkte geöffnet werden soll
						$open = 0;
						if (!empty($rootline['name']) && substr_count($rootline['name'][$row], $value) && end($rootline['name']) != $value) {
							// echo $rootline['name'][$row]." ".$value." ".$row." - ".substr_count($value, '/')."<br>";
							$open = 1;
						}
						$hierarchie_segments['content'][$row][$value] = [
							"v" => explode("/",$value)[$row],
							"c" => $facettes[$i + 1],
							"a" => $active,
							"id" => md5($value),
							"o" => $open,
							"ch" => $sub_elements,
							"f" => $field_name
						];
					}
				}
			}
			// Array keys umbenennen - es werden die IDs der Rootline nach dem ersten Element ersetzt
			// @all -> kann ggf. noch optimiert und/oder in eine eigene methode überführt werden
			if (!empty($rootline)) {
				$i = 0;
				foreach ($rootline['id'] as $value) {
					// hier wird teilweise ein überflüssiges Element generiert - woher auch immer - muss man noch prüfen
					if (is_array($hierarchie_segments['content']) && array_key_exists(($i + 1), $hierarchie_segments['content'])) {
						$hierarchie_segments['content'][$value] = $hierarchie_segments['content'][$i + 1];
						unset($hierarchie_segments['content'][$i + 1]);
					}
					$i++;
				}
				$hierarchie_segments['rootline'] = $rootline['name_single'];
                $hierarchie_segments['rootlinefull'] = $rootline['name'];
                // Vorletztes, ausgewähltes Element als Basis für den Zurückbutton beim Browsen durch die Strukturen hinterlegen
                $hierarchie_segments['rootlineprevious'] = 'root';
                if(isset($hierarchie_segments['rootline'][count($hierarchie_segments['rootline']) - 1])){
                    $hierarchie_segments['rootlineprevious'] = $hierarchie_segments['rootline'][count($hierarchie_segments['rootline']) - 1];
                }
			}
			$hierarchie_segments['level'] = $rows_to_show;
            if(is_array($hierarchie_segments['rootline'])) $hierarchie_segments['rootline_item'] = end($hierarchie_segments['rootline']);
		}
		return $hierarchie_segments;
	}
	

    /**
     *    Legt die grundlegende Struktur des Arrays an, in dem die verschiedenen Facetten vorgehalten und an das Template übergeben werden
     *
     *    @return array -> assoziatives, leeres Array
     */
    public function initFacettesContainer() : array
    {
        return $facettes_prepared = [
            'full_index' => [],
            'single_full_index' => [],
            'alpha_index' => [],
            'hierarchical_index' => [],
            'existing_index' => [],
            'selected_facettes' => [],
            'structure_index' => [],
            'start_key' => null
        ];
    }

    /**
     *    Mapping der zusammengehörenden Facetten (Welche Facetten sollen auf Basis der Alphabetischen Indexwerte ausgegeben werden?)
     *
     *    @return array -> assoziatives Array mit den Bezügen der alphabetischen Facetten
     */
    public function initAlphabeticfieldRelations() : array
    {
        return $alphafield_mapping = [
            "entityFirstletter" => "entitynorole",
            "entityAllFirstletter" => "entityAll",
            "locationFirstletter" => "location",
            "locationAllFirstletter" => "locationAll",
            "classificationtagsFirstletter" => "classificationtags",
            "classificationtagsAllFirstletter" => "classificationtagsAll",
            "publisherFirstletter" => "publisher"
        ];
    }

    public function initRelationMapping() : array 
    {
        $return = array(
            'classCollectionHierarchy' => array('classCollectionPrimaryHierarchy','classCollectionRelatedHierarchy')
        );
        return $return;
    }

    public function prepare_facettes($joSolrFacettes = null, $settings = null, $search_array = [], $selected_value = [])
    {
        $all_facettes = $joSolrFacettes;
        // @all->muss angepasst werden - wir brauchen alle facetten, nicht nur die "normalen" - pivot und range würden aus der betrachtung rausfallen wenn wir NUR die facet_fields berücksichtigen
        $joSolrFacettes = $joSolrFacettes->facet_fields;
        // Leere Facettencontainer anlegen
        $facettes_prepared = $this->initFacettesContainer();
        // Mapping der alphabetischen Facetten initialisieren
        $alphafield_mapping = $this->initAlphabeticfieldRelations();
		
        $active_facettes = explode(",", $settings['facettenselect']);
        if (!empty($active_facettes)) {
            foreach ($active_facettes as $value) {
                $number = 0;
				$facette_full_data = explode("$", $value);
                $facette_name = $facette_full_data[0];
                $facette_type = $facette_full_data[1];
                //Alphabetische Facette
                if (strpos($facette_name, 'Firstletter')) {
                    $firstletter_callback = [$this, 'firstletter'];
                    $facettes_prepared['alpha_index'][$facette_name]['content'] = $this->makeIndexAlphabet($joSolrFacettes->$facette_name);
                    if ($settings['showNumbers']) {
                        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($facettes_prepared['alpha_index'][$facette_name]['content']);
                        foreach ($facettes_prepared['alpha_index'][$facette_name]['content'] as $val) {
                            //$number += $val;
                            if (0 !== $val) {
                                $number++;
                            }
                        }
                        $facettes_prepared['alpha_index'][$facette_name]['number'] = $number;
                    }
                    if (is_array($search_array[$alphafield_mapping[$facette_name]]) && 
                        !empty($search_array[$alphafield_mapping[$facette_name]])) {
                        $facettes_prepared['alpha_index'][$facette_name]['active'] = array_map($firstletter_callback, $search_array[$alphafield_mapping[$facette_name]]);
                        $facettes_prepared['selected_facettes'][$alphafield_mapping[$facette_name]] = 'active';
                    }
                } else if ("timeline" == $facette_name) {
                    $facette_temp = $this->reorderFacetteArray($joSolrFacettes->$facette_name, 'timeline');
                    // Start- und Enddatum ermitteln und setzen
                    if (is_array($facette_temp) && 
                        !empty($facette_temp)) {
                        ksort($facette_temp);
                        reset($facette_temp);
                        $joTimeFilterarray["startpunkt"] = (key($facette_temp) !== false) ? key($facette_temp) : null;
                        end($facette_temp);
                        $joTimeFilterarray["endpunkt"] = (key($facette_temp) !== false) ? key($facette_temp) : null;
                        if (!empty($search_array['timeline'])) {
                            $joTimeFilterarray["startpunkt"] = $search_array['timeline']['start'];
                            $joTimeFilterarray["endpunkt"] = $search_array['timeline']['end'];
                        }
                        $timeline_formated = $this->makeTimeline($facette_temp, $joTimeFilterarray);
                        $facettes_prepared['full_index'][$facette_name] = $timeline_formated;
                    }
                } else if ("lonlatidFacette" == $facette_name) {
                    //@all -> das muss noch dynamisch gemacht werden - die facette soll nur auf der karte aufgelöst werden und nicht in der facettenübersicht erscheinen
                // Hierarchische Facetten ermitteln
                //@all -> könnte sich mit anderen parametern überschneiden - muss besser ausgearbeitet werden
				} else if ("locationPolygonesHash" == $facette_name) {
					//@all -> das muss noch dynamisch gemacht werden - die facette soll nur auf der karte aufgelöst werden und nicht in der facettenübersicht erscheinen
				// Hierarchische Facetten ermitteln
				//@all -> könnte sich mit anderen parametern überschneiden - muss besser ausgearbeitet werden
				} else if($facette_full_data[3] == "hierarchical"){
					$rows_to_show = 1;
					if (!empty($selected_value[$facette_name])) {
						$rows_to_show = substr_count($selected_value[$facette_name][0], '/') + 1;
					}
					$facettes_prepared['hierarchical_index'][$facette_name] = $this->getFacetteStructure($joSolrFacettes->$facette_name, $facette_name, $rows_to_show, $selected_value, $search_array);
					//@all -> stimmt noch nicht
                    $number = 0;
                    if(is_array($facettes_prepared['hierarchical_index'][$facette_name]['content'][0])) $number = count($facettes_prepared['hierarchical_index'][$facette_name]['content'][0]);
				} else {
                    // Einzelne Facetten extrahieren und einzeln fürs Rendern bereitstellen
                    if($facette_type != 'structure'){
                        $indexname = 'full_index';
                        $this->joSpalten = 3;
                        if(isset($settings['init']['searchconfig']['extractfacettesfromfacettesblock']) &&
                            !empty($settings['init']['searchconfig']['extractfacettesfromfacettesblock']) &&
                            isset($settings['init']['searchconfig']['extractfacettesfromfacettesblock'][$facette_name])){
                            $indexname = 'single_full_index';
                            $this->joSpalten = 1;
                        }
                        $this->joDelimiter = "$";
                        $this->joGetKeyOrValue = "key";
                        // Rangefacetten werden hier gesondert betrachtet
                        // aktuell ist rangegap, start und end noch hardcodiert im utility - das muss da raus
                        if($facette_full_data[1] == "range"){ 
                            $facette_temp = $this->reorderFacetteArray($all_facettes->facet_ranges->$facette_name->counts, 'range', $all_facettes->facet_ranges->$facette_name);
                        }else{
                            $facette_temp = $this->reorderFacetteArray($joSolrFacettes->$facette_name);
                        }
                        
                        $facettes_prepared[$indexname][$facette_name]['content'] = $this->joColumSplitNew($facette_temp);
                        if(isset($facettes_prepared[$indexname][$facette_name]['content']) && empty($facettes_prepared[$indexname][$facette_name]['content'])){
                            unset($facettes_prepared[$indexname][$facette_name]);
                        }
                        if ($settings['showNumbers']) {
                            foreach ($facettes_prepared[$indexname][$facette_name]['content'] as $val) {
                                $number += sizeof($val);
                                /*foreach ($val as $val2) {
                                     $number += $val2['number'];
                                }*/
                            }
                            //$facettes_prepared[$indexname][$facette_name]['number'] = $number;
                        }
                    }
                }
                // Aktiv ausgewählte Facetten ermitteln
                if (is_array($search_array) && array_key_exists($facette_name, $search_array) && !empty($search_array[$facette_name])) {
                    $facettes_prepared['selected_facettes'][$facette_name] = 'active';
                }
                // bei alphabetischen Facetten müssen die zugehörigen Felder ermittelt werden - $alphafield_mapping
                if (is_array($search_array) && array_key_exists($alphafield_mapping[$facette_name], $search_array) && !empty($search_array[$alphafield_mapping[$facette_name]])) {
                    $facettes_prepared['selected_facettes'][$facette_name] = 'active';
                    $facettes_prepared['selected_facettes'][$alphafield_mapping[$facette_name]] = 'active';
                }
                // @all facetten die nicht gezeigt werden soll, sollten aus settings kommen
                // Prüfen welche Facetten überhaupt ausgegeben werden sind
                if(!isset($facettes_prepared['single_full_index'][$facette_name])){
                    if (($joSolrFacettes->$facette_name || $all_facettes->facet_ranges->$facette_name->counts) && $facette_name != "lonlatidFacette" && $facette_name != "locationPolygonesHash") {
                        if ($settings['showNumbers']) {
                            $facettes_prepared['existing_index'][] = ['name' => $facette_name, 'number' => $number];
                             $facettes_prepared['structure_index'][] = ['name' => $facette_name, 'number' => $number];
                        } else {
                            $facettes_prepared['existing_index'][] = ['name' => $facette_name];
                            $facettes_prepared['structure_index'][] = ['name' => $facette_name];
                        }
                    }
                }
                if($facette_type == 'structure'){
                    $current_structure = $facette_name;
                    $facettes_prepared['structure'][$facette_name] = array();
                    $facettes_prepared['structure_index'] = [];
                }
                if(isset($facettes_prepared['structure']) && $facette_type != 'structure'){
                    $facettes_prepared['structure'][$current_structure] = $facettes_prepared['structure_index'];
                }
            }
            $facettes_prepared = array_filter($facettes_prepared);
            // Mapping der Alphabetischen Facetten hinzufügen
            if(is_array($alphafield_mapping)){
                $facettes_prepared['alphafield_mapping_reverse'] = array_flip($alphafield_mapping);
            }
        }
        return array_filter($facettes_prepared);
    }

    /**
     *    todo -> modifiziert -> erwartet ein Array in der Form: array("1989" => "Anzahl der Einträge") - kann sicher noch optimiert werden
     */
    public function makeTimeline($joResultObject = [], $joTimeFilterarray = [])
    {

        $joObjekteResultClone = [];
        $joEndPoint = 0;
        $joStartPoint = 0;
        if (!empty($joResultObject)) {
            // Fehlende Jahre mit Null auffüllen
            $joResultObjectEmpty = array_fill($joTimeFilterarray["startpunkt"], $joTimeFilterarray["endpunkt"] - $joTimeFilterarray["startpunkt"] + 1, 0);
            $joResultObject = array_replace($joResultObjectEmpty, $joResultObject);

            foreach ($joResultObject as $key => $wert) {
                /**
                 *    Prüfen welches Datumsformat verwendet wurde
                 *    Teilweise werden Julianische Daten verwendet dann sieht die Zahl so aus: 2453056
                 *    Ab und an werden die Daten allerdings so übergeben:    1525-05-03 oder auch so: 1640 bzw. 1640-3
                 *    Wenn die Zeit weiter im die Vergangeheit zurückreicht, müsste die strlen Condition
                 *    korrigiert werden
                 */
                if (strpos($key, "-") !== false || strlen($key) < 5) {
                    $joJahresZahlArray = explode('-', $key);
                    $joNurJahr = $joJahresZahlArray[0];
                } else {
                    $joConvertedJulianDay = jdtogregorian($key);
                    $joJahresZahlArray = explode('/', $joConvertedJulianDay);
                    $joNurJahr = $joJahresZahlArray[2];
                }
                $joStartPoint = $joTimeFilterarray["startpunkt"];
                $joEndPoint = $joTimeFilterarray["endpunkt"];
                if ($joNurJahr >= $joTimeFilterarray["startpunkt"] && $joNurJahr <= $joTimeFilterarray["endpunkt"]) {
                    $joObjekteResultClone["value"][] = $wert;
                    $joObjekteResultClone["label"][] = $joNurJahr;
                }
            }
            $joRangeAbsolute = $joEndPoint - $joStartPoint + 1;

            $joOffset = $joStartPoint;
            $joRangeBegin = null;
            $joRangeEnd = null;
            $joTimelineReturnArray = [];

            if (!empty($joRangeAbsolute)) {
                $joTimelineReturnArray['headerdata']['absoluterangeinyears'] = $joRangeAbsolute;
                $joTimelineReturnArray['headerdata']['startpointinyears'] = $joOffset;
                $joTimelineReturnArray['headerdata']['endpointinyears'] = $joOffset + $joRangeAbsolute;
                $joRangeBegin = $joTimelineReturnArray['headerdata']['startpointinyears'];
                $joRangeEnd = $joTimelineReturnArray['headerdata']['endpointinyears'];
            }

            /**
             *    Sliderposition berechnen
             */
            $joTimelineReturnArray['headerdata']['sliderpositionstart'] = 0;
            $joTimelineReturnArray['headerdata']['sliderpositionend'] = 100;
            $joTimelineReturnArray['headerdata']['sliderpositionendright'] = 0;

            if (!empty($joTimeFilterarray)) {
                if (!empty($joTimeFilterarray["startpunkt"])) {
                    $joTimelineReturnArray['headerdata']['sliderpositionstart'] = ($joTimeFilterarray["startpunkt"] - $joOffset) / $joRangeAbsolute * 100;
                    $joRangeBegin = $joTimeFilterarray["startpunkt"];
                }
                if (!empty($joTimeFilterarray["endpunkt"])) {
                    $joRangeEnd = $joTimeFilterarray["endpunkt"];
                    /**
                     *    Start und Endpunkt auf denselben Punkt fallen, werden sie um eine Einheit voneinander getrennt
                     */
                    if ($joRangeEnd <= $joRangeBegin) {
                        $joRangeEnd = $joRangeBegin;
                    }

                    /**
                     *    Wenn der Slider am rechten Ende des Zeitstrahl ankommt, wird der Zeitstrahl um eins nach rechts eröht
                     */
                    $joSelectedRangeBase = $joRangeEnd + 1; //    Ausgewählte Jahre zur Berechnung der Sliderpositionen

                    if ($joRangeEnd >= $joTimelineReturnArray['headerdata']['endpointinyears'] - 1) {
                        $joRangeEnd = $joTimelineReturnArray['headerdata']['endpointinyears'];
                        $joSelectedRangeBase = $joRangeEnd;
                    }
                    $joTimelineReturnArray['headerdata']['endpointinyears'] = $joTimelineReturnArray['headerdata']['endpointinyears'] - 1;
                    $joTimelineReturnArray['headerdata']['sliderpositionend'] = ($joSelectedRangeBase - $joOffset) / $joRangeAbsolute * 100;
                    $joTimelineReturnArray['headerdata']['sliderpositionendright'] = 100 - ($joSelectedRangeBase - $joOffset) / $joRangeAbsolute * 100;
                }
            }
        }
        if (!empty($joObjekteResultClone)) {$joTimelineReturnArray['items'] = $joObjekteResultClone;}

        return $joTimelineReturnArray;
    }
}
