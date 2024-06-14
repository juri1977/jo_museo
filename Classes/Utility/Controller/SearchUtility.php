<?php
namespace JO\JoMuseo\Utility\Controller;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait SearchUtility
{

    public function dispatchSearchMode()
    {
        $standardsearch = 1; // Default wird im Katalog gesucht
        $target = null;
        if ($this->request->hasArgument('a1')) {
            $standardsearch = filter_var($this->request->getArgument('a1'));
        }

        if ($this->request->hasArgument('fulltext')) {
            $search = filter_var($this->request->getArgument('fulltext'));
        }

        switch ($standardsearch) {
            case 2:
                // @all -> muss dynamisch werden
                $target = 185;
                // Indexsuche
                // $target = $this->settings['indexedsearchPID'];
                if (null != $target) {
                    $uriBuilder = $this->controllerContext->getUriBuilder();
                    $uriBuilder->reset();
                    $uriBuilder->setTargetPageUid($target);
                    $uriBuilder->setArguments([
                        'tx_indexedsearch_pi2' => [
                            'action' => 'search',
                            'controller' => 'Search',
                            'search' => [
                                'sword' => $search,
                            ],
                        ],
                        'a1' => '2',
                        'no_cache' => '1',
                    ]);
                    $uri = $uriBuilder->build();
                    $this->redirectToUri($uri);
                }
                break;
            default: // geht in Portalsuche;
        }
    }

    /**
     * Ermittelt die Inhalte von Get/Post Variablen und reinigt sie
     *
     * @param string $varname - Name der Variable
     * @param string $vartype - Typ der Variable (extbase - mit prefixes, gp - ohne prefixes)
     * @param string $sanitize - Datentyp der Variable (int, string etc.)
     * @return mixed Inhalte der Variable
     *
     */
    public function getVariable($varname = null, $vartype = 'extbase', $sanitize = 'int'): mixed
    {
        $value = null;
        if (null != $varname) {
            switch ($vartype) {
                case 'extbase':
                    if ($this->request->hasArgument($varname)) {
                        $value = $this->request->getArgument($varname);
                    }
                    break;

                case 'gp':
                    if (GeneralUtility::_GP($varname)) {
                        $value = GeneralUtility::_GP($varname);
                    }
                    break;
            }
            switch ($sanitize) {
                case 'int':
                    $value = intval($value);
                    break;

                case 'string':
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                    break;

                default:
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
            }
        }
        return $value;
    }

    /**
     * Limit für die Auslese der Objekte aus dem Solr ermitteln - maximal 100 sind möglich
     *
     * @param int $limit - Anzahl der auszulesenden Objekte
     * @return int Limit
     *
     */
    public function getLimit($limit = 100): int
    {
        $max_limit = 100;
        $limit = intval($limit);
        if ($limit > $max_limit || 0 == $limit) {
            $limit = $max_limit;
        }
        return $limit;
    }

    /**
     * Offset für die Auslese der Objekte aus dem Solr ermitteln
     *
     * @param int $current_start - Aktueller Offset, ab dem die Objekte ausgelesen werden sollen
     * @param int $limit - Limit - wieviele Objekte sollen ausgelesen werden? - 100 ist hier das Maximum
     * @param int $max_items - Maximale Anzahl der Ergebnisse
     * @return int Offset
     *
     */
    public function getOffset($current_start = 0, $limit = 100, $max_items = 0): int
    {
        $offset = 0;
        if ($max_items > 0 &&
            ($current_start + $limit) < $max_items) {
            $offset = $current_start + $limit;
        }
        return $offset;
    }

    /**
     * Ermittelt die dem Objekt zugeordneten Kindobjekte
     *
     * @param string $relation_pid - ID des Elternelements
     * @param int $chl_start - Offset, ab dem die Objekte ausgelesen werden sollen (Pagination)
     * @param int $chl_limit - Limit - wieviele Objekte sollen ausgelesen werden? - 100 ist hier das Maximum
     * @return object gefundene Kindobjekte
     *
     */
    public function getChildobjects($relation_pid = null, $chl_start = 0, $chl_limit = 100): object
    {
        $related_objects = new \stdClass();
        if (null != $relation_pid) {
            $chl_fieldname = 'childobjects'; // Key für die Template/Objektkonfiguration - kommt z.B. aus $this->config['fieldlist']['childobjects']
            // Standardmäßig wird die ID der übergeordneten Einheit im Solr-Field pid gesucht - parent ist auch möglich aber da ist die Struktur pid$titel und es muss mit Wildcard gesucht werden
            $relatedfieldname = 'pid';
            $queryObject = GeneralUtility::makeInstance(\JO\JoMuseo\Utility\Fuzzysearchutils\Makesolrquery::class);
            $chl_query = [];
            // verstecke Objekte nicht ausgeben, wenn der Flag im Backend gesetzt ist
            if (isset($this->config['hidefromsearch']) && $this->config['hidefromsearch'] > 0) {
                $chl_query[] = $this->solrQueryhandler->joMakeQuerypart(['0'], " AND ", "hiddenFromSearch");
            }
            // Kindobjekte auf Basis der Parent-ID ermitteln
            //später auf pid umstellen -> aktuell ist parent das bezugsfeld
            // $related_query[] = $this->solrQueryhandler->joMakeQuerypart([$relation_pid], " OR ", $relatedfieldname);
            $chl_query[] = "parent:" . $relation_pid . '$' . '*';
            // Suchfilter werden für die Kindobjekte nicht berücksichtigt - Searchsession für diesen Fall wird auf ein leeres Array gesetzt, die originale Session bleibt erhalten
            $chl_searchsession = [];
            // Sortierung der Kindobjekte initialisieren, insofern Parameter in der TS Datei gesetzt wurden
            $chl_sorting = $this->initSorting($chl_searchsession, $this->config['fieldlist'][$chl_fieldname]['sortobjects']);
            // Felder, die aus dem Solr ausgegeben werden sollen
            $chl_fieldlist = '*';
            // Neuen Query für die Kindobjekte bauen
            $related_objects_query = $queryObject->setSolr($this->joSolrCore)
                ->setLimit($chl_limit)
                ->setStart($chl_start)
                ->setQuery($chl_query)
                ->setSorting($chl_sorting)
                ->setFieldlist($chl_fieldlist)
                ->generateQuery();

            // Anfrage an den Solr stellen
            $related_objects_json = $this->solrRepository->contactSolr($related_objects_query);
            if ($related_objects_json) {
                $related_objects = json_decode($related_objects_json);
                $related_objects->response->docs = $this->modifyResults($related_objects->response->docs);
            }
        }
        return $related_objects;
    }

    /**
     * Ermittelt die vom Nutzer eingegebenen Sortierkriterien und speichert sie in die Suchsession
     *
     *
     * @param array $search_session Alle Werte der Suchsession als Array
     * @return array aktualisierte Suchsession
     *
     */
    public function getSorting($search_session = []): array
    {
        if ($this->request->hasArgument('sortvar') && $this->request->hasArgument('direction')) {
            $sort_vars = [];
            $sort_vars[0] = filter_var($this->request->getArgument('sortvar'), FILTER_SANITIZE_STRING);
            $sort_vars[1] = filter_var($this->request->getArgument('direction'), FILTER_SANITIZE_STRING);
            $search_session['content']['sortvar'][0] = $sort_vars;
            $this->flash[] = $this->translate($this->extbase_config['lang_path'] . ':flash.set') . $this->translate($this->extbase_config['lang_path'] . ':flash.sortierung' . '-' . $sort_vars[0]);
        }
        if ($this->request->hasArgument('removesortvar')) {
            // @all - das Label scheint noch nicht integriert zu sein
            $search_session['content']['sortvar'] = [];
            $this->flash[] = $this->translate($this->extbase_config['lang_path'] . ':flash.remove') . $this->translate($this->extbase_config['lang_path'] . ':flash.sortierung' . '-' . $sort_vars[0]);
        }
        return $search_session;
    }

    /**
     * Initialisiert die Sortierung der Ergebnisse
     *
     *
     * @param array $search_session Alle Werte der Suchsession als Array
     * @param array $sort_config Initilale Konfiguration der gewünschten Reihenfolge der Sortierkriterien (Kommt aus Typoscriptdatei z.B. $this->config['init']['searchconfig']['sorting']['init']
     * @return array Sortierreihenfolge der Objekte
     *
     */
    public function initSorting($search_session = [], $sort_config = []): array
    {
        // Ordnen der Listen
        $sorting = [];
        // Inititial wird erst nach sorting und dann nach Titel sortiert
        $sorting['init'] = [
            'sorting' => ['asc', 'desc'],
            'titleSort' => ['asc', 'desc'],
        ];
        // Wenn im Typoscript Sortierungen definiert wurden, werden diese benutzt
        if (isset($sort_config) && is_array($sort_config)) {
            $sorting = [];
            foreach ($sort_config as $skey => $sort) {
                // Sort muss ein Array mit den Inhalten: array('asc','desc') oder array('desc','asc') sein
                if (is_array($sort)) {
                    $sorting['init'][$skey] = $sort;
                }
            }
        }
        // Insofern es eine Eingabe in dem Suchschlitz gab, wird ein dismax/edismaxfiler Filter zur Ordnung der Suchtreffer gezündet und nach Score gerankt
        if (!empty($search_session['content']['fulltext'])) {
            $sorting['init'] = [
                'score' => ['desc', 'asc'],
            ];
        }
        // Wenn es eine vom User initiierte Änderung der Sortierreihenfolge gab, dann wird diese primär berücksichtigt
        if (!empty($search_session['content']['sortvar'])) {
            // es wird nur das erste Element beachtet!
            $sortfield_name = $search_session['content']['sortvar'][0][0];
            $sortfield_direction = $search_session['content']['sortvar'][0][1];
            $sortfield_direction_array = ('asc' == $sortfield_direction) ? ['asc', 'desc'] : ['desc', 'asc'];
            // Konfiguration überschreiben zur weitergabe an das Template
            // @all -> prüfen, ob wir das brauchen: - wir geben eine Sortierreihenfolge an das Template und überschreiben die Konfiguration im globalen Museo-Konfig-Array
            $this->config['init']['searchconfig']['sorting']['alloptions'][$sortfield_name] = $sortfield_direction_array;
            $sorting['init'] = [$sortfield_name => $sortfield_direction_array];
        }
        // Sorting active kann auch in eine bestehende variable eingebaut werden. z.b. $this->config oder so
        $sorting['active'] = $sorting['init'];

        return $sorting;
    }

    /**
     * Ermittelt die Koordinate, auf der sich der User befindet und schreibt diese samt des Radiuses in die Suchsession
     *
     *
     * @param array $search_session Alle Werte der Suchsession als Array
     * @return array aktualisierte Suchsession
     *
     */
    public function getSearchradius($search_session = []): array
    {
        // @all -> Der Radius(distance) ist hardcodiert - der kann perspektivisch aus der Flexform kommen
        if ($this->request->hasArgument('lon') && $this->request->hasArgument('lat')) {
            if ($this->request->getArgument('lon') != '' && $this->request->getArgument('lat') != '') {
                $search_session['content']['searchradius'] = [
                    'lon' => (float) $this->request->getArgument('lon'),
                    'lat' => (float) $this->request->getArgument('lat'),
                    'distance' => 10,
                ];
                // @all -> das Label scheint nicht zu existieren
                $this->flash[] = $this->translate($this->extbase_config['lang_path'] . ':flash.set') . $this->translate($this->extbase_config['lang_path'] . ':flash.searchradius');
            }
        }
        if ($this->request->hasArgument('removesearchradius')) {
            $search_session['content']['searchradius'] = [];
            $this->flash[] = "Sie haben den Umkreisfilter entfernt";
        }
        return $search_session;
    }

    /**
     * Initialisiert den Suchradius in dessen Umkreis Objekte gefunden werden sollen und gibt die Daten in die Konfiguration, die als JavaScript ausgespielt wird
     *
     *
     * @param array $search_session Alle Werte der Suchsession als Array
     * @return void
     *
     */
    public function initSearchradius($search_session = []): void
    {
        if (isset($search_session['content']['searchradius']) && !empty($search_session['content']['searchradius'])) {
            $this->extbase_config['mapconfig']['radiussearch'] = 1;
            $this->extbase_config['mapconfig']['myposlon'] = $search_session['content']['searchradius']['lon'];
            $this->extbase_config['mapconfig']['myposlat'] = $search_session['content']['searchradius']['lat'];
            $this->extbase_config['mapconfig']['distance'] = $search_session['content']['searchradius']['distance'];
        }
    }

    /**
     * Ermittelt und sortiert die norwestlichen und südöstlichen Koordinatem des rechteckigen Suchbereiches
     *
     *
     * @return array Latitude und Longitude zweier Punkte des Suchbereiches
     *
     */
    public function getBounds(): array
    {
        $return = [];
        if ($this->request->hasArgument('lat1') &&
            $this->request->getArgument('lat2') &&
            $this->request->getArgument('lon1') &&
            $this->request->getArgument('lon2')) {
            $lat1 = (float) $this->request->getArgument('lat1');
            $lat2 = (float) $this->request->getArgument('lat2');
            $lon1 = (float) $this->request->getArgument('lon1');
            $lon2 = (float) $this->request->getArgument('lon2');
            // Werte neu bestimmen, da die Box von allen vier Seiten aus aufziehbar ist
            if ($lon2 < $lon1) {
                $help = $lon2;
                $lon2 = $lon1;
                $lon1 = $help;
            }
            if ($lat2 > $lat1) {
                $help = $lat2;
                $lat2 = $lat1;
                $lat1 = $help;
            }
            $return['x1'] = $lon1;
            $return['y1'] = $lat1;
            $return['x2'] = $lon2;
            $return['y2'] = $lat2;
        }
        return $return;
    }

    /**
     * Initialisiert die Boundingbox in deren Dimensionen Objekte gefunden werden sollen und gibt die Daten in die Konfiguration, die als JavaScript ausgespielt wird
     *
     *
     * @param array $bounds Array mit der norwestlichen und südöstlichen Koordinate des rechteckigen Suchbereiches
     * @param array $search_session Alle Werte der Suchsession als Array
     * @return array aktualisierte Suchsession
     *
     */
    public function initBounds($bounds = [], $search_session = []): array
    {
        if (!empty($bounds)) {
            $search_session['content']['Boundingbox'] = $bounds;
            $this->flash[] = "Sie haben einen Bereich auf der Karte ausgewählt.";
        }
        if ($this->request->hasArgument('removeBoundingbox')) {
            $search_session['content']['Boundingbox'] = [];
            $this->flash[] = "Sie haben den Boundingbox-Filter entfernt";
        }

        if (!empty($search_session['content']['Boundingbox'])) {
            $this->extbase_config['mapconfig']['showselection'] = 1;
            $this->extbase_config['mapconfig']['poslon1'] = $search_session['content']['Boundingbox']['x1'];
            $this->extbase_config['mapconfig']['poslon2'] = $search_session['content']['Boundingbox']['x2'];
            $this->extbase_config['mapconfig']['poslat1'] = $search_session['content']['Boundingbox']['y1'];
            $this->extbase_config['mapconfig']['poslat2'] = $search_session['content']['Boundingbox']['y2'];
            $this->extbase_config['mapconfig']['lon1'] = urlencode($this->extbase_config['prefix'] . "[lon1]") . "=";
            $this->extbase_config['mapconfig']['lat1'] = urlencode($this->extbase_config['prefix'] . "[lat1]") . "=";
            $this->extbase_config['mapconfig']['lon2'] = urlencode($this->extbase_config['prefix'] . "[lon2]") . "=";
            $this->extbase_config['mapconfig']['lat2'] = urlencode($this->extbase_config['prefix'] . "[lat2]") . "=";
            // Link zum entfernen der Boundingbox-Filterung erzeugen
            $args_additional = [];
            $action_controller_config = [];
            $args_additional['removeBoundingbox'] = 1;
            $page_type = 0;
            $additional_params = [
                'ceid' => $this->extbase_config['ce_uid'],
                'no_cache' => 1,
            ];
            // Wenn die Seite via AJAX geladen wird, wird der PageType geändert
            if ($this->settings['ajaxload']) {
                $page_type = 200;
            }
            $this->extbase_config['mapconfig']['del'] = $this->joTextUtil->MakeTypoLink($this, $args_additional, null, $action_controller_config, $page_type, $additional_params);
        }
        return $search_session;
    }

    /**
     * Ermittelt die vom Nutzer eingegebenen Begrenzungen für die Timeline
     *
     *
     * @param array $search_session Alle Werte der Suchsession als Array
     * @return array aktualisierte Suchsession
     *
     */
    public function getTimeline($search_session = []): array
    {
        // Filter nach Timeline - Startjahr und Endjahr aus dem Zeitstrahl
        if ($this->request->hasArgument('starttime') && $this->request->hasArgument('endtime')) {
            if ($this->request->getArgument('starttime') != '' && $this->request->getArgument('endtime') != '') {
                $search_session['content']['timeline'] = [
                    'start' => intval($this->request->getArgument('starttime')),
                    'end' => intval($this->request->getArgument('endtime')),
                ];
                $this->flash[] = $this->translate($this->extbase_config['lang_path'] . ':flash.set') . $this->translate($this->extbase_config['lang_path'] . ':flash.zeiten');
                $this->current_selected = 'timeline';
            }
        }
        if ($this->request->hasArgument('jodeletetimefilter')) {
            $search_session['content']['timeline'] = [];
            $this->flash[] = $this->translate($this->extbase_config['lang_path'] . ':flash.remove') . $this->translate($this->extbase_config['lang_path'] . ':flash.zeiten');
        }
        // Wenn der Startzeitpunkt größer als der Endzeitpubkt ist, werden beide Zeitpunkte gleichgesetzt
        if (!empty($search_session['content']['timeline'])) {
            if ($search_session['content']['timeline']['start'] >= $search_session['content']['timeline']['end']) {
                $search_session['content']['timeline']['end'] = $search_session['content']['timeline']['start'];
            }
        }
        return $search_session;
    }

    public function expertSearchFired()
    {
        // Expertensuche inital sperren, damit sie nicht via TS freigegeben werden kann, ohne die Bedingungen zu erfüllen
        $this->config['init']['searchconfig']['expertsearchpermitted'] = false;
        $this->config['init']['searchconfig']['activateexpertsearchmask'] = false;
        // Wenn die Expertensuche in der Flexform aktiviert wurde und im TS Konfiguriert wurde, wird die Darstellung der Suchmaske erlaubt
        if ($this->config['expertsearch']
            && isset($this->config['init']['searchconfig']['expertsearch'])
        ) {
            $this->config['init']['searchconfig']['expertsearchpermitted'] = true;
            if ($this->request->hasArgument('expertensearch')){
                // Suchemaske aufgeklappt, wenn eine Suche abgefeuert wurden
                $this->config['init']['searchconfig']['activateexpertsearchmask'] = true;
            }
        }
    }

    // Erzeugt eine Blätterfunktion für das Browsen zwischen den Objekt-Detailansichten unter berücksichtigung der Suchfilter
    public function buildDetailBrowser(): array
    {
        $return = [];
        if (isset($this->config['init']['searchconfig']['showprevnext'])) {
            if ($this->request->hasArgument('startfrom')) {
                $queryObject = GeneralUtility::makeInstance(\JO\JoMuseo\Utility\Fuzzysearchutils\Makesolrquery::class);
                // Startfrom entspricht immer dem relativen index der Suchtreffer OHNE Berücksichtigng der Pagination (jeder paginierte Abschnitt beginnt mit 0)
                $startvalue = $this->request->getArgument('startfrom');
                if ('empty' == $startvalue) {
                    $relposition = 0;
                } else {
                    $relposition = intval($startvalue);
                }
                $isfirst = true;
                $sublimit = 2;
                // absposition: Absoluten Index kalkulieren relposition + limit * paginationstart
                $absposition = $relposition + $this->joLimitPreset * ($this->jopaginatecenter - 1);
                // Neuer Index beginnt bei ($absposition - 1), insofern vorhanden, da das der Vorgänger ist und endet bei ($absposition + 1), insofern das vorhanden ist; absposition > 0 bedeuten, dass das Objekt nicht das erste in der Liste ist und damit einen Vorgänger hat
                if ($absposition > 0) {
                    $absposition = $absposition - 1;
                    $relposition = $relposition - 1;
                    $isfirst = false;
                    $sublimit = 3;
                }
                // Neuen Query für Vorgänger und Nachfolger bauen
                $prev_next_config = $queryObject->setSolr($this->joSolrCore)
                    ->setLimit($sublimit)
                    ->setQuery($this->basequerydata)
                    ->setFacettes($this->facettesdata)
                    ->setFQuery($this->fqdata)
                    ->setSorting($this->sorting)
                    ->setFieldlist('id')
                    ->setHighlighting(null)
                    ->setStart($absposition)
                    ->generateQuery();
                // Ids von Vorgänger, ausgewähltes Objekt und Nachfolger holen
                $prevnextitems = json_decode($this->solrRepository->contactSolr($prev_next_config));
                $resultsfound = $prevnextitems->response->numFound;
                // Array für die Übergabe ans Template vorbereiten
                if ($resultsfound > 0) {
                    $prevnext_array = [];
                    $prevnext_array['numberresults'] = $resultsfound;
                    if (!$isfirst) {
                        // Vorgänger ist, insofern das Objekt nicht das erste in der Liste ist, der relative Index des docs-Arrays der Solr-Rückgabe
                        $prevnext_array['prev'] = $relposition;
                        $prevnext_array['previd'] = reset($prevnextitems->response->docs)->id;
                        // Der Count des ausgewählten Objektes entspricht dessen ABSOLUTEN Index der Treffermenge, Korrektur um 2 der Index bei 0 beginnt, unsere Zählung jedoch bei 1 und der Current zuvor um -1 für den query angepasst wurde
                        $absposition = $absposition + 2;
                        $current_key = 1;
                        $relposition++;
                    } else {
                        $absposition = $absposition + 1;
                        $current_key = 0;
                    }
                    $prevnext_array['currentid'] = $prevnextitems->response->docs[$current_key]->id;
                    $prevnext_array['current'] = $absposition;
                    // Aktueller Index beginnt bei 0 -> Korrektur -1
                    $prevnext_array['current_index'] = $absposition - 1;
                    if (($absposition) < $resultsfound) {
                        $prevnext_array['nextid'] = end($prevnextitems->response->docs)->id;
                        $prevnext_array['next'] = $relposition + 1;
                    }
                    $return = $prevnext_array;
                }
            }
        }
        return $return;
    }

    // Erzeugt einen Solrquery für die Detailansicht - not ready
    /*
    public function getSingleItem()
    {
    $queryObject = GeneralUtility::makeInstance(\JO\JoMuseo\Utility\Fuzzysearchutils\Makesolrquery::class);
    $singleobjectconfig = $queryObject->setSolr($this->joSolrCore)
    ->setLimit(1)
    ->setQuery($this->basequerydata)
    ->setHighlighting(null)
    ->setStart(1)
    ->generateQuery();
    $return = json_decode($this->solrRepository->contactSolr($singleobjectconfig));
    }
     */

    // Falls auf Basis von Objekttypen unterschiedliche Felder in der Detailansicht geladen werden sollen, wird diese Liste hier generiert
    public function setDetailFieldList($objectType = null)
    {
        if (null != $objectType) {
            if ($this->config['fieldlist']['objecttypefieldconfig']) {
                foreach ($this->config['fieldlist']['objecttypefieldconfig'] as $key => $value) {
                    if ($value['fieldname'] == $objectType) {
                        // Wenn die Konfiguration keine zusätzlichen Strukturmerkmale aufweist, werden nur die Felder berücksichtigt
                        if (isset($value['fields']) && isset($this->config['fieldlist']['detailview']['fields'])) {
                            $this->config['fieldlist']['detailview']['fields'] = $value['fields'];
                        }
                        // Wenn die Konfiguration vollständig strukturiert ist, wird die komplette Struktur übernommen
                        if (isset($value['main_cols']) && isset($this->config['fieldlist']['detailview']['main_cols'])) {
                            $this->config['fieldlist']['detailview']['main_cols'] = $value['main_cols'];
                        }
                    }
                }
            }
        }
    }

    // Detailkonfiguration anpassen, wenn notwendig (Entfernen  nicht vorhandener Konfigurationsstränge, insofern die zugehörigen Felder nicht vorhanden sind und so uch nicht ausgsepielt werden sollen)
    public function postprocessFieldConfig()
    {
        if ($this->config['init']['searchconfig']['detailcleanemptyfields']) {
            if ($this->joSolrResult->response->docs && !empty($this->joSolrResult->response->docs) && isset($this->config['fieldlist']['detailview']['main_cols']['master'])) {
                foreach ($this->config['fieldlist']['detailview']['main_cols']['master'] as $key => $value) {
                    if (isset($value['fields'])) {
                        foreach ($value['fields'] as $skey => $svalue) {
                            // Aggregate prüfen
                            if (isset($svalue['sub'])) {
                                foreach ($svalue['sub'] as $sskey => $ssvalue) {
                                    $isempty = $this->checkEmptyFieldsAndAggregates($ssvalue, $this->joSolrResult->response->docs[0]);
                                    if ($isempty) {
                                        unset($this->config['fieldlist']['detailview']['main_cols']['master'][$key]['fields'][$skey]['sub'][$sskey]);
                                    }
                                }
                                if (empty($this->config['fieldlist']['detailview']['main_cols']['master'][$key]['fields'][$skey]['sub'])) {
                                    // Wenn unterhalb des Aggregates keine Daten vorhanden sind, wird das Aggregat gelöscht
                                    unset($this->config['fieldlist']['detailview']['main_cols']['master'][$key]['fields'][$skey]);
                                }
                                // nicht aggregierte Felder prüfen
                            } else {
                                $isempty = $this->checkEmptyFieldsAndAggregates($svalue, $this->joSolrResult->response->docs[0]);
                                if ($isempty) {
                                    unset($this->config['fieldlist']['detailview']['main_cols']['master'][$key]['fields'][$skey]);
                                }
                            }
                        }
                        /**
                         * Spalten ggf. mit alternativen Feldern oder Aggregaten auffüllen und diese an den anderen Stellen löschen
                         * z.B. wenn die erste Spalte leer ist, können hier Inhalte aus einer anderen Spalte hinzugefügt werden
                         * Aktuell kann nur EIN Feld oder EIN Aggregat hier verschoben werden
                         * Konfiguration darüber passiert in der TS Datei:
                         * ifempty{
                         *      mastercol = 1 -> Referenz auf die Spalte
                         *      fieldnumber = 1 -> Referenz auf die Feldnummer
                         *   }
                         */
                        if (empty($this->config['fieldlist']['detailview']['main_cols']['master'][$key]['fields']) && isset($value['ifempty'])) {
                            // unset($this->config['fieldlist']['detailview']['main_cols']['master'][$key]['fields']);
                            $this->config['fieldlist']['detailview']['main_cols']['master'][$key]['fields'][] = $this->config['fieldlist']['detailview']['main_cols']['master'][$value['ifempty']['mastercol']]['fields'][$value['ifempty']['fieldnumber']];
                            unset($this->config['fieldlist']['detailview']['main_cols']['master'][$value['ifempty']['mastercol']]['fields'][$value['ifempty']['fieldnumber']]);
                        }
                    }
                }
            }
        }
    }

    // Prüfen, ob das Feld leer ist, oder gar nicht existiert
    public function checkEmptyFieldsAndAggregates($current_field = [], $result = [])
    {
        $isempty = false;
        if (!empty($current_field)) {
            if (isset($current_field['name']) && 'passthrough' == $current_field['name']) {
                if (isset($current_field['subtype'])) {
                    switch ($current_field['subtype']) {
                        // Imageviewer
                        case 'imageviewer':
                            /**
                             *  Hier muss geprüft werden, ob ein placeholder oder eine Karte ausgespielt werden soll:
                             *  $this->config['fieldlist']['detailview']['images']['dontshowplaceholder'] = 1
                             */

                            if (!property_exists($result, 'images') &&
                                isset($this->config['fieldlist']['detailview']['images']['dontshowplaceholder'])
                            ) {
                                $isempty = true;
                            }
                            break;
                        // Kartendarstellung
                        case 'map':
                            // ggf. noch weitere geodatenfelder hinzufügen
                            if (!property_exists($result, 'lonlatidFacette') &&
                                !property_exists($result, 'hasGeoLocation')
                            ) {
                                $isempty = true;
                            }
                            break;
                        // Kindobjekte identifizieren
                        case 'childelements':
                            if (!property_exists($result, 'childobjectsInjected')) {
                                $isempty = true;
                            }
                            break;
                    }
                }
            } else {
                if (isset($current_field['name']) && !property_exists($result, $current_field['name'])) {
                    $isempty = true;
                }
            }
        }
        return $isempty;
    }

    public function fixLinefeed($string = ''): string
    {
        $search = ["\r\n", "\n", "\r"];
        $replace = "\\n";
        $string = str_replace($search, $replace, $string);
        return $string;
    }

    // Nachprozessierung und Strukturierung bestimmter Solrfelder
    public function modifyResults($results = [])
    {
        $return = [];
        $hasGeoLocation = false;
        if (!empty($results)) {
            foreach ($results as $items) {
                // modifikation der rückgabewerte - @all - muss dynamisch werden und teilweise müssen auch felder im solr angepasst werden
                if (isset($items->noteBundled)) {
                    foreach ($items->noteBundled as $s) {
                        $s_array = explode('$', $s);
                        if ('language' == $s_array[0] || 'genre' == $s_array[0]) {
                            continue;
                        } else {
                            $items->{$s_array[0]} = $s_array[1];
                        }
                        // @all -> müsste im Index anders abgespeichert werden -> noteBundledJSON z.B.
                        if ('Fundschlüssel' == $s_array[0]) {
                            $items->{$s_array[0]} = array_filter(explode('#', $s_array[1]));
                        }

                        if ('Verortungsstufe' == $s_array[0]) {
                            // @all -> das sollten wir noch dynamisch machen - ist zu fix auf dieses feld
                            $splitted = array_filter(explode('#', $s_array[1]));
                            $items->{$s_array[0]} = '<span class="statusitem statuscolor_' . $splitted[1] . '"></span>' . $splitted[0];
                        }
                    }
                }
                if (isset($items->noteBundledJSON)) {
                    $converted_temp = json_decode($items->noteBundledJSON, true);
                    if (!empty($converted_temp)) {
                        $items->noteBundledJSON = '';
                        foreach ($converted_temp as $keytemp => $temp) {
                            $items->{$keytemp} = $temp;
                        }
                    }
                }
                if (isset($items->classificationtime)) {
                    $all = [];
                    foreach ($items->classificationtime as $s) {
                        $s_array = explode('#', $s);
                        if (isset($s_array[1])) {
                            $all[] = $s_array[1];
                        } else {
                            $all[] = $s_array[0];
                        }
                    }
                    $items->classificationtime = $all;
                }
                // Weitere strukturierte Information zu den Assets
                if (isset($items->assetInformation) && !empty($items->assetInformation)) {
                    $assetInformationArray = [];
                    foreach ($items->assetInformation as $asset) {
                        // Linebreaks harmonisieren
                        $asset = $this->fixLinefeed($asset);
                        $assetInformationArray[] = json_decode($asset, true);
                    }
                    $items->assetInformation = $assetInformationArray;
                }
                if (isset($items->images)) {
                    $all = [];
                    $array_length = 7; // Basislänge des Arrays
                    foreach ($items->images as $s) {
                        $s_array = explode('$', $s);
                        $s_array_empty = array_fill(0, $array_length, '');
                        $s_array = array_replace($s_array_empty, $s_array);
                        if (count($s_array) > $array_length) {
                            $s_array = array_splice($s_array, 0, $array_length);
                        }

                        $v_array = [
                            0 => 'uri',
                            1 => 'viewer',
                            2 => 'caption',
                            3 => 'credits',
                            4 => 'licence',
                            5 => 'licenceholder',
                            6 => 'uiif',
                        ];
                        $images_harmonized = array_combine($v_array, $s_array);
                        $images_harmonized['filetype'] = '';
                        if ('' != $images_harmonized['viewer']) {
                            $ext = pathinfo($images_harmonized['viewer'], PATHINFO_EXTENSION);
                            switch ($ext) {
                                case 'pdf':
                                    $filetype = 'pdf';
                                    break;
                                case 'glb':
                                    $filetype = '3d';
                                    break;
                                default:
                                    $filetype = 'img';
                            }
                            $images_harmonized['filetype'] = $filetype;
                        }
                        // Extended Assetdata dem Asset hinzufügen
                        if (isset($items->assetInformation) &&
                            !empty($items->assetInformation)) {
                            foreach ($items->assetInformation as $ai) {
                                // @all -> da steht normal die caption - den bildnamen brauchen wir auch noch und im JSON muss das auch noch besser gemacht werden
                                if ($ai['Name'] == $s_array[2]) {
                                    $images_harmonized['extended_image_information'] = array_filter($ai);
                                }
                            }
                        }
                        $all[] = $images_harmonized;
                    }
                    // Kompatibilitätsmodus für ältere Image-Blöcke aus dem Solr
                    if(is_array($items->images) && isset($this->config['init']['searchconfig']['imagecompatibilitymode'])){
                        array_unshift($items->images, $items->images[0]);    
                    }
                    $items->allmedia = $all;
                }
                if (isset($items->dateEvents)) {
                    foreach ($items->dateEvents as $s) {
                        $s_array = explode('$', $s);
                        $items->{$s_array[0]} = $items->{$s_array[0]} ? $items->{$s_array[0]} . ', ' . $s_array[1] : $s_array[1];
                    }
                }
                if (isset($items->typology)) {
                    $all = [];
                    foreach ($items->typology as $s) {
                        $all[] = json_decode($s, true);
                    }
                    $items->typology = $all;
                }
                if (isset($items->download)) {
                    $all = [];
                    foreach ($items->download as $s) {
                        $all[] = json_decode($s, true);
                    }
                    $items->download = $all;
                }
                if (isset($items->shelfmark)) {
                    $all = [];
                    foreach ($items->shelfmark as $s) {
                        if($s != "[no indication]"){
                            $all[] = $s;    
                        }
                    }
                    $items->shelfmark = $all;
                }
                if (isset($items->inheritedUnspecified)) {
                    $all = [];
                    foreach ($items->inheritedUnspecified as $s) {
                        $all[] = json_decode($s, true);
                    }
                    $items->inheritedUnspecified = $all;
                }
                if (isset($items->locationQualified) && !empty($items->locationQualified)) {
                    $all = [];
                    // Ist JSON objekt -> das erste Element enthät ALLE orte -> @all kann im solr - schema angepasst werden
                    $items->locationQualified = json_decode($items->locationQualified[0], true);
                    if (!empty($items->locationQualified) && is_array($items->locationQualified)) {
                        rsort($items->locationQualified);
                        foreach ($items->locationQualified as $temp_loc) {
                            // Mapping verschiedener Eventereignisse:
                            if (isset($temp_loc['name']) && '' != $temp_loc['name']) {
                                // minting_place -> wird entfernt und allgemein dem Event Production zugeordnet
                                if ('Productionminting_place' == $temp_loc['role']) {
                                    $temp_loc['role'] = 'Production';
                                }

                                if ('' != $temp_loc['normdata']) {
                                    $temp_loc['normdata'] = explode('#', $temp_loc['normdata']);
                                }

                                // Wenn Geodaten verfügbar sind, wird das Objekt als "verortbar" deklariert
                                if (isset($temp_loc['lonlat']) && '' != $temp_loc['lonlat']) {
                                    $items->hasGeoLocation = true;
                                }
                                $all[$temp_loc['role']][] = array_filter($temp_loc);
                                // Einträge Unique machen - es soll ein Ort nur einmal genannt werden, wenn er mehrfach auftaucht
                                $all[$temp_loc['role']] = array_unique($all[$temp_loc['role']], SORT_REGULAR);
                            }
                        }
                        $items->locationQualified = $all;
                    } else {
                        $items->locationQualified = [];
                    }
                }
                if (isset($items->locationPolygones) && !empty($items->locationPolygones)) {
                    $items->hasGeoLocation = true;
                }
                if (isset($items->entity)) {
                    $entity = [];
                    foreach ($items->entity as $s) {
                        $s_array = explode('$', $s);
                        $name = '';
                        if (isset($s_array[0])) {
                            $name = $s_array[0];
                        }
                        $role = '';
                        if (isset($s_array[1])) {
                            $role = $s_array[1];
                        }
                        $normdata = [];
                        if (isset($s_array[2])) {
                            $normdata_tmp = explode('#', $s_array[2]);
                            if (isset($normdata_tmp[0])) {
                                $normdata['gnd'] = $normdata_tmp[0];
                            }
                            if (isset($normdata_tmp[1])) {
                                $normdata['intern'] = $normdata_tmp[1];
                            }
                        }
                        $tmp_e_array = [
                            'name' => $name,
                            'role' => $role,
                            'normdata' => $normdata,
                        ];
                        $entity[$s_array[1]][] = array_filter($tmp_e_array);
                        $entity[$s_array[1]] = array_unique($entity[$role], SORT_REGULAR);
                    }
                    $items->entity = $entity;
                }

                if (isset($items->literatur)) {
                    $lit = [];
                    foreach ($items->literatur as $s) {
                        $s_array = explode('$', $s);
                        /*
                        bezüge inhalt/nummer:
                        'titel' => 0
                        'subtitle' 1
                        'related' 2
                        'autor' => 3
                        'jahr' =>  4
                        'link' => 5
                        'page' => 6
                         */
                        if (isset($s_array[3])) {
                            $tmp = explode('|', $s_array[3]);
                            foreach ($tmp as $key => $a) {
                                /*
                                bezüge inhalt/nummer:
                                'name, vorname' => 0
                                'normdata/dnb/gnd' 1
                                 */
                                $tmp[$key] = explode('##', $a);
                            }
                            $s_array[3] = $tmp;
                        }
                        $lit[] = $s_array;
                    }
                    $items->literatur = $lit;
                }
                if (isset($items->bibReference[0])) {
                    $items->bibReference[0] = json_decode($items->bibReference[0]);
                    $lit = [];
                    if (!empty($items->bibReference[0])) {
                        foreach ($items->bibReference[0] as $k => $s) {
                            foreach ($s as $sk => $s_array) {
                                /*
                                bezüge inhalt/nummer:
                                'titel' => 0
                                'subtitle' 1
                                'related' 2
                                'autor' => 3
                                'jahr' =>  4
                                'link' => 5
                                'page' => 6
                                 */
                                if (isset($s_array->autor)) {
                                    $tmp = explode('|', $s_array->autor);
                                    foreach ($tmp as $key => $a) {
                                        /*
                                        bezüge inhalt/nummer:
                                        'name, vorname' => 0
                                        'normdata/dnb/gnd' 1
                                         */
                                        $tmp[$key] = explode('##', $a);
                                    }
                                    $s_array->autor = $tmp;
                                }
                                $lit[$k][] = $s_array;
                            }
                        }
                        $items->bibReference[0] = $lit;
                    }
                }
                if (isset($items->location)) {
                    $loc = [];
                    foreach ($items->location as $l) {
                        $loc_temp = explode('$', $l);
                        if (isset($loc_temp[2])) {
                            $loc_temp[2] = explode('#', $loc_temp[2]);
                        }

                        $loc[] = $loc_temp;
                    }
                    $items->location = $loc;
                }
                // $this->request->getAttribute('normalizedParams')->getSiteUrl() - führt bei manchen Proxykonfigurationen zu fehlerhaften Protokollen - http vs. https
                if (isset($this->request->getAttribute('site')->getConfiguration()['base'])
                    && !empty($this->request->getAttribute('site')->getConfiguration()['base'])) {
                    $base_url = $this->request->getAttribute('site')->getConfiguration()['base'];
                } else {
                    $base_url = $this->request->getAttribute('normalizedParams')->getSiteUrl();
                }
                $items->canonical = $base_url . 'item/' . $items->id;
                $return[] = $items;
            }
        }
        return $return;
    }

    /*
     * Geo Utilities
     */
    // GeoJSON Viereck auf Basis von Koordinaten erzeugen
    public function makeSquareFromTwoPoints($item = null)
    {
        if (null != $item) {
            $coords = null;
            $id = null;
            $hasdigitalisat = null;
            if ($item->locationPolygones) {
                $coords = $item->locationPolygones[0];
            }

            if ($item->id) {
                $id = $item->id;
            }

            if ($item->images) {
                $hasdigitalisat = 1;
            }

            if (null != $coords && null != $id) {
                $center_point = explode(',', $coords);
                $point_1_1 = explode(' ', trim($center_point[0]));
                $point_1_2 = explode(' ', trim($center_point[1]));
                $point_2_1 = explode(' ', trim($center_point[2]));
                $point_2_2 = explode(' ', trim($center_point[3]));

                // bei quadrat fangen wir oben links an, wenn der Rechte Punkt als Negativ kommt, müssen wir diesen von 360° abziehen
                if ((float) $point_1_1[0] >= 0 && (float) $point_1_2[0] < 0) {
                    $point_1_2[0] = 360 + (float) $point_1_2[0];
                    $point_2_1[0] = 360 + (float) $point_2_1[0];
                }

                $jsonpolygon = [
                    "type" => "Feature",
                    "properties" => [
                        "id" => $id,
                        "d" => $hasdigitalisat,
                    ],
                    "geometry" => [
                        "type" => "Polygon",
                        "coordinates" => [
                            0 => [
                                0 => [
                                    0 => (float) $point_1_1[0],
                                    1 => (float) $point_1_1[1],
                                ],
                                1 => [
                                    0 => (float) $point_1_2[0],
                                    1 => (float) $point_1_2[1],
                                ],
                                2 => [
                                    0 => (float) $point_2_1[0],
                                    1 => (float) $point_2_1[1],
                                ],
                                3 => [
                                    0 => (float) $point_2_2[0],
                                    1 => (float) $point_2_2[1],
                                ],
                                4 => [
                                    0 => (float) $point_1_1[0],
                                    1 => (float) $point_1_1[1],
                                ],
                            ],
                        ],
                    ],
                ];
                return $jsonpolygon;
            }
        }
    }

    /*
     * Returns the area of a closed path on Earth in sq km
     * Path is an array of arrays
     * Earth radius is fixed at 6371 km
     */
    public function computeAreaKm2($path)
    {
        return abs($this->computeSignedArea($path, 6371));
    }

    /*
     * Returns the area of a closed path on Earth in acres
     * Path is an array of arrays
     * Earth radius is fixed at 6371 km
     */
    public function computeAreaAcres($path)
    {
        return abs($this->computeSignedArea($path, 6371)) * 247.105;
    }

    /*
     * Returns the signed area of a closed path on a sphere of given radius
     * The computed area uses the same units as the radius squared
     */
    public function computeSignedArea($path, $radius)
    {
        $size = count($path);
        if ($size < 3) {return 0;}
        $total = 0;
        $prev = $path[$size - 1];
        $prevTanLat = tan((M_PI / 2 - deg2rad(floatval($prev[1]))) / 2);
        $prevLng = deg2rad(floatval($prev[0]));
        // For each edge, accumulate the signed area of the polar triangle
        foreach ($path as $point) {
            $tanLat = tan((M_PI / 2 - deg2rad(floatval($point[1]))) / 2);
            $lng = deg2rad(floatval($point[0]));
            $total += $this->polarTriangleArea($tanLat, $lng, $prevTanLat, $prevLng);
            $prevTanLat = $tanLat;
            $prevLng = $lng;
        }
        return $total * ($radius * $radius);
    }

    /*
     * Returns the signed area of a triangle which has North Pole as a vertex
     */
    public function polarTriangleArea($tan1, $lng1, $tan2, $lng2)
    {
        $deltaLng = $lng1 - $lng2;
        $t = $tan1 * $tan2;
        return (2 * atan2($t * sin($deltaLng), 1 + $t * cos($deltaLng)));
    }

    public function makeGeoJsonFromLocationPolygones()
    {
        $return = '';
        $geoJSON = [];
        if ($this->joSolrResult->response->numFound > 0) {
            $ordercount = 0;

            $joActionControllerArray = [];
            $joActionControllerArray['pluginname'] = $this->extbase_config['plugin_name'];
            $joActionControllerArray['actionname'] = "ajax";

            foreach ($this->joSolrResult->response->docs as $items) {
                if (!empty($items->locationPolygones) && str_contains($items->locationPolygones[0], 'coordinates')) {
                    $order = $ordercount;

                    if ($items->id) {
                        $id = $items->id;
                    }

                    if ($items->images) {
                        $hasdigitalisat = 1;
                    }

                    $json = json_decode($items->locationPolygones[0], 1);

                    if (str_contains($items->locationPolygones[0], 'FeatureCollection')) {
                        $json = $json['features'][0]['geometry'];
                    }

                    $joArgumentsArrayAdditional = [];
                    $joArgumentsArrayAdditional['joDetailView'] = $id;
                    $lnk = $this->joTextUtil->MakeTypoLink($this, $joArgumentsArrayAdditional, null, $joActionControllerArray, 2328);

                    $jsonpolygon = [
                        "type" => "Feature",
                        "properties" => [
                            "id" => $id,
                            "i" => $id,
                            "l" => $lnk,
                            "d" => $hasdigitalisat,
                        ],
                        "geometry" => $json,
                    ];

                    $pJSON[$order] = $jsonpolygon;
                    $ordercount++;
                } elseif (!empty($items->locationPolygones)) {
                    // Nur das erste Polygon wird berücksichtigt
                    $points_order = explode(',', $items->locationPolygones[0]);
                    // Funktioniert nur mit Vierecken
                    $order = $ordercount;
                    if (!empty($points_order) && count($points_order) == 4) {
                        $points_pair = [];
                        foreach ($points_order as $v) {
                            $points_pair[] = explode(' ', trim($v));
                        }
                        $order = intval($this->computeAreaAcres($points_pair) + $ordercount);
                    }
                    $pJSON[$order] = $this->makeSquareFromTwoPoints($items);
                    $ordercount++;
                }
            }
            // Die Objekte werden so gestapelt, dass die Flächeninhalte nach vorn immer kleiner werden
            if (!empty($pJSON)) {
                krsort($pJSON);
                $pJSON = array_values($pJSON);
                $geoJSONPolygones = [
                    "type" => "FeatureCollection",
                    "features" => $pJSON,
                ];
                $return = 'var polygonsJson=' . json_encode($geoJSONPolygones) . ';';
            }
        }
        return $return;
    }

    public function makeGeoJsonFromLonlatidFacette()
    {
        $return = '';
        $geoJSON = [];
        //@all -> das ist recht provisorisch aufgearbeitet - es werden POIs und polygone gemeinsam auf der Karte dargestellt. sollte man flexibler machen
        if (!empty($this->joSolrResult->facet_counts->facet_fields->lonlatidFacette)) {
            $facette_temp = $this->solrIndexUtil->reorderFacetteArray($this->joSolrResult->facet_counts->facet_fields->lonlatidFacette);
            $geoJSON = $this->makeGeoJSONFacette($facette_temp, '$');
            if (null != $geoJSON) {
                $return = 'var geojson=' . json_encode($geoJSON['pois']) . ';';
            }

            // @all -> das passt noch nicht
            $polygonesFieldValue = $this->joSolrResult->facet_counts->facet_fields->locationPolygonesHash;
            if (!empty($polygonesFieldValue)) {
                $joArgumentsArrayAdditional = [];
                $joActionControllerArray = $this->makeExtbaseActionArray("ajax");
                $i = 0;
                foreach ($polygonesFieldValue as $value) {
                    if ($i % 2 == 0) {
                        $p = [];
                        $tmp_val = explode('$', $value);
                        $joArgumentsArrayAdditional['joDetailViewHash'] = $tmp_val[0];
                        $lnk = $this->joTextUtil->MakeTypoLink($this, $joArgumentsArrayAdditional, null, $joActionControllerArray, 2328);
                        $p[$tmp_val[0]][0] = $tmp_val[1];
                        $p[$tmp_val[0]][1] = $polygonesFieldValue[$i + 1];
                        $p[$tmp_val[0]][2] = $lnk;
                        $pJSON[] = substr(json_encode($p), 1, -1);
                        $tmp_val = null;
                        $value = null;
                    }
                    $i++;
                }
                $geoJSONPolygones = '{' . implode(',', $pJSON) . '}';
                $return .= 'var polygones=' . $geoJSONPolygones . ';';
            }
        }
        return $return;
    }

    /**
     * Methode zur Kumulation von JavaScript-Variablen
     *
     * @param string $varstring Zeichenkette, die die entsprechenden JavaScriptvariablen enthält
     *
     */
    public function addJavaScriptVars(string $varstring = '')
    {
        if ('' != $varstring) {
            $this->javaScriptVars[] = $varstring;
        }
    }

    public function initTimelineConfiguration()
    {
        $config = [];
        $config['color']['fillcg'] = '#ffffff';
        $config['color']['linecg'] = '#000000';
        $config['color']['fillcs'] = '#ffffff';
        $config['color']['linecs'] = '#000000';
        if ($this->settings['fillcg']) {
            $config['color']['fillcg'] = $this->settings['fillcg'];
        }

        if ($this->settings['linecg']) {
            $config['color']['linecg'] = $this->settings['linecg'];
        }

        if ($this->settings['fillcs']) {
            $config['color']['fillcs'] = $this->settings['fillcs'];
        }

        if ($this->settings['linecs']) {
            $config['color']['linecs'] = $this->settings['linecs'];
        }

        return $config;
    }

    public function renderFacettes()
    {
        if (isset($this->settings['facettenselect']) && isset($this->joSolrResult->facet_counts)) {
            $facettes_prepared = $this->solrIndexUtil->prepare_facettes(
                $this->joSolrResult->facet_counts,
                $this->settings,
                $this->joSearchArrayComplete['content'],
                $this->selected_values
            );
            return $facettes_prepared;
        }
    }

    /**
     * Facette-Prefix benutzen, um nicht zuviele Facetten zu ziehen
     * Die Methode hat nur Relevanz bei Ajax-Abfragen der alphabetischen Facetten
     *
     * @return object
     *
     */
    public function generateFacettePrefix(): object
    {
        $this->query_params['facette_prefix'] = null;
        if ($this->request->hasArgument('entityFirstletter')) {
            $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('entityFirstletter'), FILTER_SANITIZE_STRING);
            $this->query_params['f'][] = 'facet.prefix=' . $this->query_params['facette_prefix'];
        }
        if ($this->request->hasArgument('entityAllFirstletter')) {
            $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('entityAllFirstletter'), FILTER_SANITIZE_STRING);
            $this->query_params['f'][] = 'facet.prefix=' . $this->query_params['facette_prefix'];
        }
        if ($this->request->hasArgument('locationFirstletter')) {
            $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('locationFirstletter'), FILTER_SANITIZE_STRING);
            $this->query_params['f'][] = 'facet.prefix=' . $this->query_params['facette_prefix'];
        }
        if ($this->request->hasArgument('locationAllFirstletter')) {
            $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('locationAllFirstletter'), FILTER_SANITIZE_STRING);
            $this->query_params['f'][] = 'facet.prefix=' . $this->query_params['facette_prefix'];
        }
        if ($this->request->hasArgument('classificationtagsFirstletter')) {
            $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('classificationtagsFirstletter'), FILTER_SANITIZE_STRING);
            $this->query_params['f'][] = "facet.field={!key=classificationtags_a+facet.prefix=(" . $this->query_params['facette_prefix'] . "}classificationtags";
            $this->query_params['f'][] = "facet.field={!key=classificationtags_b+facet.prefix=" . $this->query_params['facette_prefix'] . "}classificationtags";
            $this->query_params['f'][] = "facet.field={!key=classificationtags_c+facet.prefix=" . $this->query_params['facette_prefix'] . "}classificationtags";
            $this->query_params['f'][] = "facet.field={!key=classificationtags_d+facet.prefix=(" . $this->query_params['facette_prefix'] . "}classificationtags";
        }
        if ($this->request->hasArgument('classificationtagsAllFirstletter')) {
            $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('classificationtagsAllFirstletter'), FILTER_SANITIZE_STRING);
            $this->query_params['f'][] = 'facet.prefix=' . $this->query_params['facette_prefix'];
        }
        if ($this->request->hasArgument('publisherFirstletter')) {
            $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('publisherFirstletter'), FILTER_SANITIZE_STRING);
            $this->query_params['f'][] = 'facet.prefix=' . $this->query_params['facette_prefix'];
        }
        return $this;
    }

    public function fillTimelineOptions($facettes_prepared = [])
    {
        if (!empty($facettes_prepared) && isset($facettes_prepared["full_index"]["timeline"]["items"])) {
            $facettes_prepared["full_index"]["timeline"] = array_merge($facettes_prepared["full_index"]["timeline"], $this->initTimelineConfiguration());
            $this->extbase_config['starttime'] = urlencode($this->extbase_config['prefix'] . "[starttime]") . "=";
            $this->extbase_config['endtime'] = urlencode($this->extbase_config['prefix'] . "[endtime]") . "=";
            $this->addJavaScriptVars('var timeline=' . json_encode($facettes_prepared["full_index"]["timeline"]) . ';');
            $this->joMuseoUtil->addHeaderFile('chart.min.js', $this->extensionName);
            $this->joMuseoUtil->addHeaderFile('museo_slider.js', $this->extensionName);
            $this->joMuseoUtil->addHeaderFile('museo_init.js', $this->extensionName);
            $this->joMuseoUtil->addHeaderFile('museo_slider.css', $this->extensionName);
        }
        return $facettes_prepared;
    }

    public function loadMapFiles()
    {
        // $asset = GeneralUtility::makeInstance(AssetCollector::class);

        /*
        if ($this->config['init']['map']['newopenlayers'] == 1) {
        $this->joMuseoUtil->addHeaderFile('ol_v6_9.min.js', $this->extensionName);
        } else {
        $this->joMuseoUtil->addHeaderFile('ol.min.js', $this->extensionName);
        }
         */

        $this->joMuseoUtil->addHeaderFile('ol_v6_9.min.js', $this->extensionName);
        //$this->joMuseoUtil->addHeaderFile('ol_v7_0.min.js', $this->extensionName);

        if ($this->settings['alternativPin']) {
            $this->joMuseoUtil->addHeaderFile('ol-ext.min.js', $this->extensionName);
            // $this->response->addAdditionalHeaderData('<script>var alternativePin=1;</script>');
            // $asset->addInlineJavaScript('alt_pin', 'var alternativePin=1;');
            $this->addJavaScriptVars('var alternativePin=1;');
        }
        $this->joMuseoUtil->addHeaderFile('maps.js', $this->extensionName);
        //$this->joMuseoUtil->addHeaderFile('ol.min.css', $this->extensionName);
        $this->joMuseoUtil->addHeaderFile('ol_v6_9.min.css', $this->extensionName);
        //$this->joMuseoUtil->addHeaderFile('ol_v7_0.min.css', $this->extensionName);
        if ($this->settings['alternativPin']) {
            $this->joMuseoUtil->addHeaderFile('ol-ext.min.css', $this->extensionName);
        }
        $this->joMuseoUtil->addHeaderFile('map.css', $this->extensionName);

        if (1 == $this->config['init']['map']['btnOnMapItem']) {
            // $this->response->addAdditionalHeaderData('<script>var btnOnMapItem = 1;</script>');
            // $asset->addInlineJavaScript('btn_onMap', 'var btnOnMapItem = 1;');
            $this->addJavaScriptVars('var btnOnMapItem = 1;');
        }
    }

    /**
     *  Overlays für die Karte via Flexform laden
     *
     **/
    public function loadMapOverlay()
    {
        if ($this->settings['overlay']) {
            $file_references = explode(',', $this->settings['overlay']);
            if (!empty($file_references)) {
                $resourceFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
                $overlay_array = [];
                $cObj = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
                foreach ($file_references as $value) {
                    // es wird nur gepfüft, ob die Filereference existiert  und sie nicht gelöscht oder hidden gesetzt wurde
                    $selectFields = 'uid';
                    $fromTable = 'sys_file_reference';
                    $whereClause = 'uid=' . (int) $value;
                    $limit = '1';
                    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($fromTable);
                    $result = $queryBuilder->select($selectFields)->from($fromTable)->where($whereClause . $cObj->enableFields($fromTable))->setMaxResults($limit)->execute();
                    $recordList = $result->fetchAll();
                    if (!empty($recordList)) {
                        $fileReference = $resourceFactory->getFileReferenceObject($value);
                        $fileArray = $fileReference->getProperties();
                        $overlay_array[] = [
                            't' => $fileArray['title'],
                            'd' => $fileArray['description'],
                            'i' => '/fileadmin' . $fileArray['identifier'],
                        ];
                    }
                }
            }
            //$asset = GeneralUtility::makeInstance(AssetCollector::class);
            // $this->response->addAdditionalHeaderData('<script>map_layer=' . json_encode($overlay_array) . '</script>');
            //$asset->addInlineJavaScript('map_layer', 'var map_layer=' . json_encode($overlay_array) . ';');
            $this->addJavaScriptVars('var map_layer=' . json_encode($overlay_array) . ';');
        }
    }

    public function getGeoDataFromField($geoDataBlock = null, $fieldname = null)
    {
        $return = [];
        if (null != $geoDataBlock && null != $fieldname) {
            switch ($fieldname) {
                case 'lonlat':
                    $temp_posdata = explode('$', $geoDataBlock);
                    $return = [$temp_posdata[0]];
                    break;

                case 'locationQualified':
                    foreach ($geoDataBlock as $value) {
                        if (isset($value['lonlat'])) {
                            $return[] = ['lonlat' => $value['lonlat'], 'name' => $value['name']];
                        }
                    }
                    break;
            }
        }
        return array_filter($return);
    }

    /**
     *        Karten auf der Karte zeigen
     *        GeoJSON Objekt erzeugen
     *          Expects Fields:
     *        lonlat -> (float)lat, (float)lon (Default)
     *        locationQualified -> Stadtname $ Event an diesem Ort $ (float)lat, (float)lon
     */
    public function makeGeoJSON($results, $lonlat_field = null, $coord_type = 'Point')
    {
        $default_field = 'lonlat';
        if (!empty($results) && null != $lonlat_field) {
            $joGeoPosArray = [];
            foreach ($results as $key => $value) {
                // Fallback auf default Feld, wenn das gewünschte Feld leer ist
                if (empty($value->$lonlat_field) && !empty($value->$default_field)) {
                    $lonlat_field = $default_field;
                }

                if (!empty($value->$lonlat_field)) {
                    $geom_array = [];
                    $coordinates = [];

                    foreach ($value->$lonlat_field as $k => $sub) {
                        $role = 'default';
                        if (isset($k)) {
                            $role = $k;
                        }

                        $temp_posdata = $this->getGeoDataFromField($sub, $lonlat_field);
                        if (!empty($temp_posdata)) {
                            foreach ($temp_posdata as $coor) {
                                if (null != $coor) {
                                    $name = 'default';
                                    if (isset($coor['name'])) {
                                        $name = $coor['name'];
                                    }

                                    $temp_coords = explode(',', $coor['lonlat']);
                                    $items = '';
                                    $geom_array["type"] = "Feature";
                                    $geom_array["properties"] = ['t' => $name, 'i' => $value->id, 'n' => $role];
                                    $geom_array["geometry"]["type"] = $coord_type;
                                    $geom_array["geometry"]["coordinates"] = [(float) $temp_coords[0], (float) $temp_coords[1]];
                                    if ($temp_posdata[2]) {
                                        $items = $temp_posdata[2];
                                        if ($geom_array["properties"]['items']) {
                                            array_push($geom_array["properties"]['items'], $items);
                                        } else {
                                            $geom_array["properties"]['items'] = [$items];
                                        }
                                    }
                                    $coordinates[] = $geom_array;
                                }
                            }
                        }
                    }
                }
            }
            $joGeoJSON = [];
            if (!empty($coordinates)) {
                $joGeoJSON = [
                    "type" => "FeatureCollection",
                    "features" => $coordinates,
                ];
            }
            return $joGeoJSON;
        }
    }

    /**
     *    Karten auf der Karte zeigen - von Facette
     *    GeoJSON Objekt erzeugen
     * -> todo -> das muss keine extra Methode sein - kann mit der anderen Methode zuammengeführt werden
     */
    public function makeGeoJSONFacette($results, $delimiter = '$', $coord_type = 'Point')
    {
        // geojsonproperties
        $allowed_properties = [];
        if (array_key_exists('geojsonproperties', $this->config['fieldlist']['mapview']) && is_array($this->config['fieldlist']['mapview']['geojsonproperties'])) {
            $allowed_properties = $this->config['fieldlist']['mapview']['geojsonproperties'];
        }

        $joActionControllerArray = [];
        $joActionControllerArray['pluginname'] = $this->extbase_config['plugin_name'];
        $joActionControllerArray['actionname'] = "ajax";

        if (!empty($results)) {
            $joGeoPosArray = [];
            $n = 0;
            $routing_points = [];
            // $uriBuilder = $this->controllerContext->getUriBuilder();
            // $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId(intval($GLOBALS['TSFE']->id));
            foreach ($results as $key => $value) {
                $lonlatArray = [];
                $temparray = explode($delimiter, $key);
                // @superalex
                $temparray[0] = str_replace("/", "\\/", $temparray[0]);
                if (!empty($temparray[1])) {
                    $active = true;
                    if ('p' == $value) {
                        $active = false;
                    }

                    $joArgumentsArrayAdditional = [];
                    $joArgumentsArrayAdditional['joDetailView'] = $temparray[0];
                    // $joArgumentsArrayAdditional['v'] = 'l';    //@all -> sorgt dafür, das der Lstview auf der Karte geladen wird - könnte man über settings parametrisieren
                    $lonlatArray = explode(',', $temparray[1]);
                    // Objekttyp prüfen und Array danach ordnen -> ist an 4. Stelle id$lonlat$titel$objekttyp
                    $objecttyp = '';
                    $lnk = '';
                    $name = '';
                    if (!empty($allowed_properties)) {
                        if (in_array('link', $allowed_properties)) {
                            $lnk = $this->joTextUtil->MakeTypoLink($this, $joArgumentsArrayAdditional, null, $joActionControllerArray, 2328);
                        }
                        if (in_array('objecttype', $allowed_properties)) {
                            $objecttyp = $temparray[3] ? $temparray[3] : 'default';
                            $objecttyp = strtolower(substr($objecttyp, 0, 2));
                        }
                        if (in_array('name', $allowed_properties)) {
                            $name = $temparray[2];
                        }
                    }
                    $joGeoPosArray[$objecttyp][] = [
                        // $joGeoPosArray[$n] = [
                        "type" => "Feature",
                        "id" => $temparray[0],
                        "properties" => [
                            'l' => $lnk,
                            'p' => $objecttyp,
                            'n' => $name,
                            'a' => $active,
                            'i' => $temparray[0],
                        ],
                        "geometry" => [
                            "type" => $coord_type,
                            "coordinates" => [(float) $lonlatArray[0], (float) $lonlatArray[1]],
                        ],
                    ];
                    if ('p' != $value) {
                        $routing_points[] = (float) $lonlatArray[1] . ',' . (float) $lonlatArray[0];
                    }
                    //$n++;
                }
            }
            $joGeoJSON = [];
            if (!empty($joGeoPosArray)) {
                foreach ($joGeoPosArray as $key => $value) {
                    $joGeoJSON['pois'][$key] = [
                        "type" => "FeatureCollection",
                        "features" => $value,
                    ];
                }
                $joGeoJSON['locations'] = ["locations" => $routing_points];
                /*
            $joGeoJSON['pois'] = [
            "type" => "FeatureCollection",
            "features" => $joGeoPosArray,
            ];
            $joGeoJSON['locations'] = ["locations" => $routing_points];
             */
            }
            return $joGeoJSON;
        }
    }
}
