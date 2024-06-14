<?php
namespace JO\JoMuseo\Utility\Controller;

class QueryUtility
{
	protected $request = null; // Requestobjekt des MuseoControllers (Arguments und Parameter)

	protected $searchsession = array();   // Alle in der Suchsession abgelegten Suchparameter

	protected $selectedvalues = array();   // Alle Steuervariablen, die nicht in die Suchsession überführt werden

    protected $config = array();  // Globale Konfiguration des Museo-Frameworks (Adminstrative und redaktionelle TS Settings)

    public function __construct()
    {
    	
    }

    public function getConfig() : object
    {
        return $this->config;
    }

    public function setConfig($config = array()) : object
    {
        $this->config = $config;
        return $this;
    }

    public function getRequestObject() : object{
    	return $this->request;
    }

    public function setRequestObject($object = object) : object
    {
    	if(is_object($object)){
    		$this->request = $object;
    	}
    	return $this;
    }

    public function getSearchsession() : array{
    	return $this->searchsession;
    }

    public function setSearchsession($searchsession = array()) : object
    {
    	if(is_array($searchsession)){
    		$this->searchsession = $searchsession;
    	}
    	return $this;
    }

    public function getSelectedvalues() : array{
    	return $this->selectedvalues;
    }

    public function setSelectedvalues($selectedvalues = array()) : object
    {
    	if(is_array($selectedvalues)){
    		$this->selectedvalues = $selectedvalues;
    	}
    	return $this;
    }

    
    public function makequerypart($items = [], $concat = "OR", $fieldname = 'id', $fuzzy = false) : string
    {
        $query = '';
        $concat = ' ' . $concat . ' ';  // Leerzeichen für den logischen Operator hinzufügen
        if (!empty($items)) {
            $fuzzysuffix = "";
            // Wenn der Begriff unscharf gesicht werden soll, wird dieser Flag hier gesetzt. Es wird dann der Begriff mit Rechts- und Linkstunkierung gesucht (*suchwort*)
            if ($fuzzy) {
                $fuzzysuffix = "*";
            }
            // Wenn es eine Expertensuche ist, wird der begriff "Expert" aus dem Feldnamen extrahiert, um das korrekte Feld anzusprechen
            if (strpos ( $fieldname , "Expert") > 0) {
                $fieldname = str_replace("Expert", "", $fieldname);
            }
            /**
             *  Truncated Search
             *  @all -> das funktioniert, berücksichtigt aber noch nicht alle Optionen
             *  berücksichtigt *, ? und "
             *  * - Beliebige Zeichen können folgen (Rechtstrunkierung)
             *  ? - Exakt ein Zeichen MUSS folgen
             *  "Suchwort1 Suchwort2" - beide Worte werden als Phrase interpretiert
             */
            foreach ($items as $key => $value) {
                if ($value != "*") {
                    $phrasearray = explode(" ", $value);
                    $lastLetter = substr(end($phrasearray), -1, 1);
                    if (!empty($phrasearray) && count($phrasearray) == 1) {
                        if ($lastLetter == "*") { 
                            $valueWithout = str_replace("*", "", $value);
                            $value = "(" . $value . " OR " . $valueWithout . ")";
                        }
                        if ($lastLetter != "?" && $lastLetter != "*" && $fuzzy == true) {
                            $value = "(" . $value . " OR *" . $value . "*)";
                            // $value = "*".$value."*";
                        }
                    }
                    //  Bei Suche mit o Fragezeichen wird auch bei unscharfer Suche kein * angehängt
                    if ($lastLetter != "?" && $lastLetter != "*") {
                        // $value = $value.$fuzzysuffix;
                    }
                    //  Wenn nach einer Phrase gesucht wird, funktionieren * und ? nur mit dem Complex Phrase Query Parser
                    if (!empty($phrasearray) && count($phrasearray) > 1) {
                        $value = '"'.$value.'"';
                    }
                    $items[$key] = $value;
                }
            }
            $searchquerydata = implode($concat, $items);
            if (count($items) > 1) {
                // Einklammern wenn mehrere Datentypen erfasst werden sollen
                $searchquerydata = "(" . $searchquerydata . ")";    
            }
            $query = $fieldname . ':' . $searchquerydata;
            // wenn es eine Anfrage an das Fulltextfeld ist, wird der Query ohne dieses Feld gebaut
            // @all -> das kann evtl generell so gemacht werden, weil sonst der Score der Solr Anfrage nicht greift
            if ($fieldname == "fulltext") {
                $query = $searchquerydata;
            }
        }
        return $query;
    }

    public function buildQuery() : array
    {
    	$return = array();

        // Sessionvariablen importieren
        $searchsession = $this->getSearchsession();

        // Arguments aus dem Controller importieren
        $arguments = $this->getRequestObject()->getArguments();

        // Timelinequery
        if (isset($searchsession['content']['timeline']['start']) && isset($searchsession['content']['timeline']['end'])) {
           $return[] = 'timeline:[' . $searchsession['content']['timeline']['start'] . ' TO ' . $searchsession['content']['timeline']['end'] . ']';
        }
        /**
         *  Queries, die sich aus Flexformeinstellungen ergeben
         */
        // Zeige nur Objekte mit Abbildungen wenn der Nutzer den Flag in der Flexform gesetzt hat (showimageswitch)
        if (!empty($searchsession['content']['imageonly']) && isset($this->config['showimageswitch']) && $this->config['showimageswitch'] > 0) {
            $return[] = 'images:*';
        }
        // Blende Objekte aus, die als versteckt markiert sind: (hidefromsearch: 1 - unsichtbar / 0 - sichtbar)
        if (isset($this->config['hidefromsearch']) && $this->config['hidefromsearch'] > 0) {
            $return[] = 'hiddenFromSearch:"0"';
        }
        // Zeige Objekte die, bestimmten Portalen zugeordnet werden (Daten kommen aus Flexform)
        if (!empty($this->config["portal"])) {
            $query_items = explode("," ,  $this->config["portal"]);
            $return[] = $this->makequerypart($query_items, "OR", "classPortal", false);
        }
        // Projektrelationen berücksichtigen (Daten kommen aus Flexform)
        if (!empty($this->config["aggregate"])) {
            $querieparts_togenerate = explode(",", $this->config['aggregate']);
            $query_items = [];
            // @all -> das kann raus, wenn in der Flexform die Werte so hinterlegt werden, dass NUR die Projektnamen drin stehen 
            foreach ($querieparts_togenerate as $value) {
                $query_config = explode("$", $value);
                array_push($query_items, str_replace('#', '$', $query_config[0]));
            }
            $return[] = $this->makequerypart($query_items, "OR", "classProject", false);
        }
        // Besitzende Institution/Datengeber selektieren (Daten kommen aus Flexform)
        if (!empty($this->config["dataprovider"])) {
            $querieparts_togenerate = explode(",", $this->config['dataprovider']);
            $query_items = [];
             // @all -> das kann raus, wenn in der Flexform die Werte so hinterlegt werden, dass NUR die Datengeber drin stehen 
            foreach ($querieparts_togenerate as $value) {
                $query_config = explode("$", $value);
                array_push($query_items, str_replace('#', '$', $query_config[0]));
            }
            $return[] = $this->makequerypart($query_items, "OR", "tenant", false);
        }

        /**
         *  Queries für die Expertensuche
         */
        foreach ($searchsession['content'] as $key => $value) {
            if (strpos ( $key , "Expert" ) > 0) { 
                $concat = "OR";
                $fuzzy = true;
                if(isset($searchsession['logical_concat'][$key])){
                    $concat = $searchsession['logical_concat'][$key];    
                }
                $return[] = $this->makequerypart($value, $concat, $key, $fuzzy);
            }   
        }
    	// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($return);
    	return $return;
    }
}
