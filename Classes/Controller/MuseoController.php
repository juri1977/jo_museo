<?php declare(strict_types=1);
namespace JO\JoMuseo\Controller;

use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\AssetCollector;
use JO\JoMuseo\Utility\Controller\MuseoControllerUtility;
use JO\JoMuseo\Utility\Controller\SearchUtility;
use JO\JoMuseo\Utility\Controller\QueryUtility;
use Psr\Http\Message\ResponseInterface;


class MuseoController extends ActionController
{
    use MuseoControllerUtility;    // Utilityklassen einbinden -> definition als Trait im Utility Ordner -> JO\JoMuseo\Utility\Controller\MuseoControllerUtility
    use SearchUtility;

    public $joSolrResult; //    Resultat der Solrsuche

    public $joSearchArrayComplete; //    Array der Sucheingaben die in einer Sessn zwischengespeichert werden - kommt aus der FEusersession

    public $selected_values = []; //    Array aus Sucheingaben die nicht in der Session zwischengespeichert sind

    public $current_selected = null; //    letzter/aktueller selektierter Suchfilter

    public $query_parts = []; //    Queryparts für die Solr-Abfragen

    public $javaScriptVars = [];    // Variablen, die als Javascripte in den Header bzw. in das HTML Markup hinterlegt werden

    public $joLimitPreset = 21; //    Standardlimit für die Ausgabe der Datensätze

    public $jopaginatecenter = 1; //    Startpunkt der Pagination

    public $joPaginatePagesShow = 10; //    Maximal anzuzeigende Paginationseiten

    public $query_params = []; //    Konfiguration des Solrqueries 
    
    public $config = [];    // Konfiguration der Museo-Extension

    public $flash = []; // Benutzerdialog via flashmessages

    public $extbase_config = []; // Erfassung und Speicherung der relevanten Extbase Steuerdaten

    public $joSolrCore = null;
    
    public $joCollectorsbox = []; //    Collectorsbox

    public $joSessionvarName = null;

    public $colboxname = null;

    public $placeholderimage = null;
 
    public $projektname = 'museo'; //    Name des Projektes

    public $extensionName = "jo_museo"; // Name der Extension

    protected $basequerydata = []; // Query-Suchparameter

    protected $facettesdata = []; // gewünschte Facetten

    protected $fqdata = '';   // aktive Suchfacetten

    protected $sorting = [];   // aktive Sortierung

    /**
     *    Objekte der Joblist und des Solrservers
     *
     *  @var  \JO\JoMuseo\Domain\Repository\SolrRepository
     */
    protected $solrRepository;

    /**
     *    Annotationen
     *
     *  @var  \JO\JoMuseo\Domain\Repository\AnnotationRepository
     */
    protected $annotationRepository;

    /**
     *    Collectorbox
     *
     *  @var  \JO\JoMuseo\Domain\Repository\CollectorboxRepository
     */
    protected $collectorboxRepository;

    /**
     *    Solr Queryhandler Util
     *
     *    @var \JO\JoMuseo\Utility\Fuzzysearchutils\Joqueryhandler
     */
    protected $solrQueryhandler;

    /**
     *    Solr Index Util
     *
     *    @var \JO\JoMuseo\Utility\Fuzzysearchutils\Jomakeindex
     */
    protected $solrIndexUtil;


    /**
     *    Solr Data Harvesting Util
     *
     *    @var \JO\JoMuseo\Utility\Harvesting\Dataharvesting
     */
    protected $solrHarvester;

    /**
     *    Solr Queryhandler Util
     *
     *    @var \JO\JoMuseo\Utility\Map\Geo
     */
    protected $geoUtil;

    /**
     *    Session Utility
     *
     *    @var \JO\JoMuseo\Utility\Fuzzysearchutils\Josearchsession
     */
    protected $joSessionUtil;

    /**
     *    Solr Array Utility
     *    Modifikation der Arrays
     *
     *    @var \JO\JoMuseo\Utility\Arrayfunc\Joarrayfunctions
     */
    protected $joArrayUtil;

    /**
     *    Solr Text Utility
     *    Modifikation der Arrays
     *
     *    @var \JO\JoMuseo\Utility\Text\Jotextutil
     */
    protected $joTextUtil;


    /**
     *    Field Mapping Util
     *
     *    @var \JO\JoMuseo\Utility\Mapping\Mapping
     */
    protected $mappingUtil;

    /**
     *    Museo Util Utility, verschiedene Funktionen für Museo
     *    @var \JO\JoMuseo\Utility\MuseoUtil
     */
    protected $joMuseoUtil;

    /**
     * AbstractController constructor.
     * @param \JO\JoMuseo\Domain\Repository\SolrRepository $solrRepository
     * @param \JO\JoMuseo\Domain\Repository\AnnotationRepository $annotationRepository
     * @param \JO\JoMuseo\Utility\Fuzzysearchutils\Joqueryhandler $solrQueryhandler
     * @param \JO\JoMuseo\Utility\Fuzzysearchutils\Jomakeindex $solrIndexUtil
     * @param \JO\JoMuseo\Utility\Harvesting\Dataharvesting $solrHarvester
     * @param \JO\JoMuseo\Utility\Map\Geo $geoUtil
     * @param \JO\JoMuseo\Utility\Fuzzysearchutils\Josearchsession $joSessionUtil
     * @param \JO\JoMuseo\Utility\Arrayfunc\Joarrayfunctions $joArrayUtil
     * @param \JO\JoMuseo\Utility\Text\Jotextutil $joTextUtil
     * @param \JO\JoMuseo\Utility\Mapping\Mapping $mappingUtil
     * @param \JO\JoMuseo\Utility\MuseoUtil $joMuseoUtil
     * @param \JO\JoMuseo\Domain\Repository\CollectorboxRepository $collectorboxRepository
     */
    public function __construct(
        \JO\JoMuseo\Domain\Repository\SolrRepository $solrRepository,
        \JO\JoMuseo\Domain\Repository\AnnotationRepository $annotationRepository,
        \JO\JoMuseo\Utility\Fuzzysearchutils\Joqueryhandler $solrQueryhandler,
        \JO\JoMuseo\Utility\Fuzzysearchutils\Jomakeindex $solrIndexUtil,
        \JO\JoMuseo\Utility\Harvesting\Dataharvesting $solrHarvester,
        \JO\JoMuseo\Utility\Map\Geo $geoUtil,
        \JO\JoMuseo\Utility\Fuzzysearchutils\Josearchsession $joSessionUtil,
        \JO\JoMuseo\Utility\Arrayfunc\Joarrayfunctions $joArrayUtil,
        \JO\JoMuseo\Utility\Text\Jotextutil $joTextUtil,
        \JO\JoMuseo\Utility\Mapping\Mapping $mappingUtil,
        \JO\JoMuseo\Utility\MuseoUtil $joMuseoUtil,
        \JO\JoMuseo\Domain\Repository\CollectorboxRepository $collectorboxRepository)
    {
        $this->solrRepository = $solrRepository;
        $this->annotationRepository = $annotationRepository;
        $this->solrQueryhandler = $solrQueryhandler;
        $this->solrIndexUtil = $solrIndexUtil;
        $this->solrHarvester = $solrHarvester;
        $this->geoUtil = $geoUtil;
        $this->joSessionUtil = $joSessionUtil;
        $this->joArrayUtil = $joArrayUtil;
        $this->joTextUtil = $joTextUtil;
        $this->mappingUtil = $mappingUtil;
        $this->joMuseoUtil = $joMuseoUtil;
        $this->collectorboxRepository = $collectorboxRepository;
    }

    /**
      * Initialize the view
      *
      * @return void
      */
    protected function initializeView($view) : void
    {
        // Suchmodus ermitteln GP:a1 -> 1 Katalog, 2 Indexsuche etc.
        $this->dispatchSearchMode();
        // Name des Arrays, in dem die Suchparameter abgelegt werden, die in die Session übertragen werden
        $joSearcharraycomplete = []; 
        // Array in das die Steuervariablen abgelegt werden, die nicht in die Session geschrieben werden
        $joSelectedValues = []; 
        // Settings und Konfiguration aus TS und Flexform laden
        $this->initTSConfigurationData()
        // Solrcore ermitteln und setzen
        ->setSolrCore()
        // Limit ermitteln und setzen - Daten kommen aus der Flexform des Plugins
        ->setSolrLimit()
        // Pagination konfigurieren
        ->setPaginationData()
        // Projektname ermitteln und setzen
        ->setProjectName()
        // Name der Session, in der die Suchparameter gespeichert werden
        ->setSessionName()
        // Sessionname der Merkliste setzen
        ->setCollectorboxName()
         // Extbase-Basiskonfiguration initialisieren
        ->initExtbaseConfig()
        // Merkliste aus der Session laden
        ->getCollectorbox()
        // Merkliste aus Cookie laden
        ->getCollectorboxCookie()
        // Merkliste bearbeiten - add/remove/deleteall
        ->handleCollectorBoxData()
        // Placeholderimage ziehen, wenn vorhanden
        ->getPlaceholderImage();

        // Vom Nutzer gesetzte Suchfilter laden
        $joSearcharraycomplete = $this->joSessionUtil->getSessionValues($this->joSessionvarName);       
        // neue vom Nutzer eingegebene Suchfilter identifizieren oder vom Nutzer abgewählte Suchfiter aus der Session entfernen
        $joSearcharraycomplete = $this->buildSearchFilters($joSearcharraycomplete);
    
        // Prüfen ob eine Facette kumulativ genutzt werden soll add = true - add -> zu einer eventuell bereits bestehenden Facette kommt eine zweite Facette aus derselben Kategorie hinzu
        $add = (GeneralUtility::_GP('add')) ? true : false; 
    
        /**
         *    Facetten und Suchfilter
         *    Aufbau: fieldname => typ, removefieldname => 1, logischesundoderfieldname => 1
         *    Beispiel: 'classificationtime' => 1, 'removeclassificationtime' => 10, 'lgandorclassificationtime' => 20
         *    Typen:    1 - normales Feld
         *            2 - hierarchisches Feld
         *            3 - Felder auf die via Suchschlitz zugegriffen wird (fulltext, etc)
         *            4 - Feld, das nicht in Session zwischengespeichert werden soll (Alphabetische Felder entityFirstletter, etc.)
         *            10 - remove - Feld löschen
         *            20 - lgandor - Logische Verknüpfung - AND/OR
         */
        $allowed_filters = [
            'oldinventarnummer' => 1, 'removeoldinventarnummer' => 10, 'lgandoroldinventarnummer' => 20,
            'classificationtime' => 1, 'removeclassificationtime' => 10, 'lgandorclassificationtime' => 20,
            'territory' => 1, 'removeterritory' => 10, 'lgandorterritory' => 20,
            'category' => 1, 'removecategory' => 10, 'lgandorcategory' => 20,
            'title' => 1, 'removetitle' => 10, 'lgandortitle' => 20,
            'classProject' => 1, 'removeclassProject' => 10, 'lgandorclassProject' => 20,
            'classCollection' => 1, 'removeclassCollection' => 10, 'lgandorclassCollection' => 20,
            'locationClassification' => 1, 'removelocationClassification' => 10, 'lgandorlocationClassification' => 20,
            'scaleMetric' => 1, 'removescaleMetric' => 10, 'lgandorscaleMetric' => 20,
            'datasource' => 1, 'removedatasource' => 10, 'lgandordatasource' => 20,
            'language' => 1, 'removelanguage' => 10, 'lgandorlanguage' => 20,
            'genre' => 1, 'removegenre' => 10, 'lgandorgenre' => 20,
            'norm' => 1, 'removenorm' => 10, 'lgandornorm' => 20,
            'classificationTopic', 'removeclassificationTopic' => 10, 'lgandorclassificationTopic' => 20,
            'objectsPlants' => 1, 'removeobjectsPlants' => 10, 'lgandorobjectsPlants' => 20,
            'classification' => 1, 'removeclassification' => 10, 'lgandorclassification' => 20,
            'classificationtags' => 1, 'removeclassificationtags' => 10, 'lgandorclassificationtags' => 20,
            'classificationtagsExpert' => 1, 'removeclassificationtagsExpert' => 10, 'lgandorclassificationtagsExpert' => 20,
            'classificationtagsAll' => 1, 'removeclassificationtagsAll' => 10, 'lgandorclassificationtagsAll' => 20,
            'objectType' => 1, 'removeobjectType' => 10, 'lgandorobjectType' => 20,
            'tenant' => 1, 'removetenant' => 10, 'lgandortenant' => 20,
            'material' => 1, 'removematerial' => 10, 'lgandormaterial' => 20,
            'fulltext' => 3, 'removefulltext' => 10, 'lgandorfulltext' => 20,
            'fulltextTei' => 3, 'removefulltextTei' => 10, 'lgandorfulltextTei' => 20,
            'fulltextExpert' => 3, 'removefulltextExpert' => 10, 'lgandorfulltextExpert' => 20,
            'locationExpert' => 3, 'removelocationExpert' => 10, 'lgandorlocationExpert' => 20,
            'locationTokenizedExpert' => 3, 'removelocationTokenizedExpert' => 10, 'lgandorlocationTokenizedExpert' => 20,
            'titleExpert' => 3, 'removetitleExpert' => 10, 'lgandortitleExpert' => 20,
            'entityExpert' => 3, 'removeentityExpert' => 10, 'lgandorentityExpert' => 20,
            'entityTokenizedExpert' => 3, 'removeentityTokenizedExpert' => 10, 'lgandorentityTokenizedExpert' => 20,
            'tenantHierarchy' => 2, 'removetenantHierarchy' => 10, 'lgandortenantHierarchy' => 20,
            'locationHierarchy' => 2, 'removelocationHierarchy' => 10, 'lgandorlocationHierarchy' => 20,
            'classCollectionHierarchy' => 2, 'removeclassCollectionHierarchy' => 10, 'lgandorclassCollectionHierarchy' => 20,
            'classCollectionPrimaryHierarchy' => 2, 'removeclassCollectionPrimaryHierarchy' => 10, 'lgandorclassCollectionPrimaryHierarchy' => 20,
            'classCollectionRelatedHierarchy' => 2, 'removeclassCollectionRelatedHierarchy' => 10, 'lgandorclassCollectionRelatedHierarchy' => 20,
            'objectTypeHierarchy' => 2, 'removeobjectTypeHierarchy' => 10, 'lgandorobjectTypeHierarchy' => 20,
            'classificationTopic' => 2, 'removeclassificationTopic' => 10, 'lgandorclassificationTopic' => 20,
            'parent' => 1, 'removeparent' => 10, 'lgandorparent' => 20,
            'publisher' => 1, 'removepublisher' => 10, 'lgandorpublisher' => 20,
            'pid' => 1, 'removepid' => 10, 'lgandorpid' => 20,
            'bcpId' => 1, 'removebcpId' => 10, 'lgandorbcpId' => 20,
            'childrenReference' => 1, 'removechildrenReference' => 10, 'lgandorchildrenReference' => 20,
            'entityFirstletter' => 4,
            'entityAllFirstletter' => 4,
            'publisherFirstletter' => 4,
            'locationFirstletter' => 4,
            'locationAllFirstletter' => 4,
            'objectsPlantsFirstletter' => 4,
            'classificationtagsFirstletter' => 4, 
            'classificationtagsAllFirstletter' => 4,
            'joDetailView' => 4,
            'jopaginatepage' => 30,
            'entitynorole' => 1, 'removeentitynorole' => 10, 'lgandorentitynorole' => 20,
            'entityAll' => 1, 'removeentityAll' => 10, 'lgandorentityAll' => 20,
            'entityTokenized' => 1, 'removeentityTokenized' => 10, 'lgandorentityTokenized' => 20, 'entityTokenizedwc' => 40,
            'location' => 1, 'removelocation' => 10, 'lgandorlocation' => 20,
            'locationAll' => 1, 'removelocationAll' => 10, 'lgandorlocationAll' => 20
        ];
        $remove_selector_part = "remove"; // um facetten wieder abzuwählen wird der Prefix "remove" vor den Feldnamen gepackt und als Argument verwendet: "removecategory"
        $logical_selector_part = "lgandor"; // lgandor wird als Prefix vor den Feldnamen gestellt und als Argument für die logische Verknüpfung von Suchfacetten verwendet "lgandorcategory"
        $logical_concat = "OR"; // initial werden die innerhalb einer Facette die Suchbegriffe mit "oder" verknüpft
        $string_delimter = "$"; // mehrere Informationen zu einer Facette werden mit "$" aneinandergekettet: info1$info2$info3
        $sort_delimter = "#"; // Sortierreihenfolgen werden mit "#" zusammengebaut: 1#info, 2#info, etc.
        $action_type = "set"; // gibt an ob Daten gesetzt (set) oder gelöscht(remove)


        $this->add_expersearch_settings($allowed_filters);

        $arguments = array_filter($this->request->getArguments());
        if (!empty($arguments)) {
            $selected_allowed_arguments = array_filter(array_intersect_key($arguments, $allowed_filters)); // erlaubte Argumente herausfiltern und leere Variablen entfernen
            if (!empty($selected_allowed_arguments)) {
                foreach ($selected_allowed_arguments as $key => $relevant_items) {
                    // @all -> muss vorher sichergestellt werden, dass es kein Array ist
                    if (is_array($relevant_items)) $relevant_items = $relevant_items[0];
                    $relevant_items = str_replace("<", htmlentities("<"), $relevant_items);
                    $relevant_items = str_replace(">", htmlentities(">"), $relevant_items);
                    $selected_item = filter_var(trim($relevant_items), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); //@all -> aktuell werden <> auch mit esaped - maht Probleme bei Namen und Orten!
                    $selected_item = str_replace(htmlentities("<"), "<", $selected_item);
                    $selected_item = str_replace(htmlentities(">"), ">", $selected_item);
                    $selected_item = str_replace(' | ', " ", $selected_item);
                    $selected_item = str_replace('  ', " ", $selected_item);
                    $selected_item = str_replace('Am ', " ", $selected_item);
                    $selected_item = trim($selected_item);
                    //$selected_item = str_replace('  ', "", $selected_item);
                    // $selected_item = trim(reset($selected_allowed_arguments));
                    $this->current_selected = $key;
                    if ('entitynorole' == $this->current_selected) $logical_concat = "AND"; // @all - das müsste irgendwie in die Field/Facetten Konfiguration
                    if ('classificationtags' == $this->current_selected) $logical_concat = "AND"; // @all - das müsste irgendwie in die Field/Facetten Konfiguration
                    $flash_message_label = $selected_item;
                    if (strpos($selected_item, $string_delimter)) $flash_message_label = explode($string_delimter, $selected_item)[0];
                    if (strpos($selected_item, $sort_delimter)) $flash_message_label = explode($sort_delimter, $selected_item)[1];
                    $field_type = $allowed_filters[$this->current_selected];
                    switch ($field_type) {
                        case 1:
                            if ('bcpId' == $this->current_selected) $joSearcharraycomplete['content'][$this->current_selected] = []; // @all setzt bei der Suche innnerhalb der Hierarchie den Hierarchschen Einstiegspunkt zurück - wir brauchen hier nur einen - das müsste irgendwie in die Field/Facetten Konfiguration
                            $joSearcharraycomplete['content'][$this->current_selected] = $this->joArrayUtil->joAddToArrayAndMakeUnique($joSearcharraycomplete['content'][$this->current_selected], $selected_item);
                            break;
                         case 40:
                            $this->current_selected = str_replace("wc", '', $this->current_selected);
                            $joSearcharraycomplete['content'][$this->current_selected] = [$selected_item];
                            break;
                        case 2:
                            if ($add) {
                                /**
                                 *  Prüfen ob die gewünschte Auswahl in einem über- oder untergeordneten Teil der Hierarchie enthalten ist
                                 *    wenn dem so ist, wird diese vorherige Auswahl korrigiert:
                                 *    - aktuelle Auswahl ist eine Teilmenge eines zuvor ausgewählten Segments - vorheriges Segment wird gelöscht
                                 *    - aktuelle Auswahl ist die übergeordnete Menge der vorherigen Auswahl - vorheriges Segment wird gelöscht
                                 *    -  beide gelöschte Objekte werden später im Code noch als "mittelbar aktiv" gekennzeichnet
                                 *     $add = true -> eine Facette wird gezündet, $add = false -> es wird nur innerhalb der Hierarchie gebrowst
                                 */
                                if (!empty($joSearcharraycomplete['content'][$this->current_selected])) {
                                    /**
                                     *    prüfen ob die neue Eingabe eine übergeordnete Menge irgendeiner zuvor getroffener Auswahl ist
                                     *     umzu verhindern, dass gleiche Zeichenketten INNERHALB einer hierarchischen Ebene selektiert werden, wird noch die Ebene selbst berücksichtigt
                                     *    so verhindern wir, dass
                                     *    Kunsthandwerk und Kunsthandwerk, Glas sich gegenseitig als hierarchischen Vorgänger oder Nachfolger identifizieren - die Ebenen sind identisch und so greift diese Bedingung nicht
                                     */
                                    $itemstoremove = [];
                                    $count_rows_selected_item = substr_count($selected_item, '/');
                                    foreach ($joSearcharraycomplete['content'][$this->current_selected] as $value) {
                                        $count_rows_selected_currentitem = substr_count($value, '/');
                                        if (substr_count($selected_item, $value) > 0 && $count_rows_selected_item != $count_rows_selected_currentitem) {
                                            if ($selected_item != $value) $itemstoremove[] = $value;
                                        }
                                        if (substr_count($value, $selected_item) > 0 && $count_rows_selected_item != $count_rows_selected_currentitem) {
                                            if ($selected_item != $value) $itemstoremove[] = $value;
                                        }
                                    }
                                    $joSearcharraycomplete['content'][$this->current_selected] = array_diff($joSearcharraycomplete['content'][$this->current_selected], $itemstoremove);
                                }
                                $joSearcharraycomplete['content'][$this->current_selected] = $this->joArrayUtil->joAddToArrayAndMakeUnique($joSearcharraycomplete['content'][$this->current_selected], $selected_item);
                            } else if ($this->request->hasArgument('browsestructure')) {
                                $joSearcharraycomplete['content'][$this->current_selected] = [$selected_item];
                            } else {
                                $flash_message_label = null;
                                $logical_concat = null;
                            }

                            $joSelectedValues[$this->current_selected] = [$selected_item];
                            break;
                        case 3:
                            $joSearcharraycomplete['content'][$this->current_selected] = [];
                            $logical_concat = "AND";
                           // echo str_replace("/", "\/", $selected_item);

                            $phrases = $this->solrQueryhandler->splitSearchEntry($selected_item);
                           
                            if (!empty($phrases)) {
                                $joSearcharraycomplete['logical_concat'][$this->current_selected] = $logical_concat;
                                $n = 0;
                                foreach ($phrases as $subvalue) {
                                    if ($n > 4) {
                                        break; // Nur 5 Phrasen können parallel verarbeitet werden
                                    }
                                    $joSearcharraycomplete['content'][$this->current_selected] = $this->joArrayUtil->joAddToArrayAndMakeUnique($joSearcharraycomplete['content'][$this->current_selected], trim($subvalue));
                                    $n++;
                                }
                            }
                            break;
                        case 4:
                            $joSelectedValues[$this->current_selected] = [$selected_item];
                            $flash_message_label = null;
                            $logical_concat = null;
                            break;
                        case 10:
                            $this->current_selected = str_replace($remove_selector_part, '', $this->current_selected);
                            $joSearcharraycomplete['content'][$this->current_selected] = $this->joArrayUtil->joEliminateArrayValueAndKey($joSearcharraycomplete['content'][$this->current_selected], $selected_item);
                            if (is_array($joSearcharraycomplete['content'][$this->current_selected]) && count($joSearcharraycomplete['content'][$this->current_selected]) === 0) unset($joSearcharraycomplete['logical_concat'][$this->current_selected]);

                            $action_type = "remove";
                            $logical_concat = null;
                            break;
                        case 20:
                            ((int) $selected_item == 1) ? $logical_concat = "AND" : $logical_concat = "OR";
                            $this->current_selected = str_replace($logical_selector_part, '', $this->current_selected);
                            $joSearcharraycomplete['logical_concat'][$this->current_selected] = $logical_concat;
                            $logical_concat = null;
                            //@all -> die flashmessage hier passt noch nicht - der text für das den value steht noch auf 1 und 2 - muss UND oder ODER heissen
                            break;
                        case 30:
                            if ($selected_item > 0) $this->jopaginatecenter = $selected_item;
                            $flash_message_label = null;
                            $logical_concat = null;
                    }
                    if (!$joSearcharraycomplete['logical_concat'][$this->current_selected] && null != $logical_concat) $joSearcharraycomplete['logical_concat'][$this->current_selected] = $logical_concat;
                    if (null != $flash_message_label)  $this->flash[] = $this->translate($this->extbase_config['lang_path'] . ':flash.' . $action_type) . $this->translate($this->extbase_config['lang_path'] . ':flash.' . $this->current_selected) . ' - ' . $flash_message_label;
                }
            }

        }

        // Initiale Sortierung der Ergebnisse vorbereiten
        $joSearcharraycomplete = $this->getSorting($joSearcharraycomplete);
        $this->sorting = $this->initSorting($joSearcharraycomplete, $this->config['init']['searchconfig']['sorting']['init']);

        // Umkreissuche initialisieren
        $joSearcharraycomplete = $this->getSearchradius($joSearcharraycomplete);
        $this->initSearchradius($joSearcharraycomplete);

        // Boundingboxsuche initialisieren
        $bounds = $this->getBounds();
        $joSearcharraycomplete = $this->initBounds($bounds, $joSearcharraycomplete);

        // Suche innerhalb vom Nutzer definierter Zeiten
        $joSearcharraycomplete = $this->getTimeline($joSearcharraycomplete);

        if (isset($joSearcharraycomplete['content']) && is_array($joSearcharraycomplete['content'])) $joSearcharraycomplete['content'] = array_filter($joSearcharraycomplete['content']);
        if (isset($joSearcharraycomplete['logical_concat']) && is_array($joSearcharraycomplete['logical_concat'])) $joSearcharraycomplete['logical_concat'] = array_filter($joSearcharraycomplete['logical_concat']);
        if (is_array($joSearcharraycomplete)) $this->joSearchArrayComplete = array_filter($joSearcharraycomplete); //     Sessionwerte
        
        if (is_array($joSelectedValues)) $this->selected_values = array_filter($joSelectedValues); //    Auswahl die nicht in die Session gespeichert wird (xyzFirstletter, DetailId oder die Hierachieebenen der hierarchischen Facetten)

        // Wenn man innerhalb von Parent/Child Strukturen browst und erstmalig in einen Detailview springt, wird der Paginator zurückgesetzt
        if ($this->request->hasArgument('h') && $this->request->hasArgument('joDetailView')) {
            $this->jopaginatecenter = 1;
            if ($this->request->hasArgument('jopaginatepage')) $this->jopaginatecenter = intval($this->request->getArgument('jopaginatepage'));
        }
        // Suchkonfiguration bereinigen und Klassenvariable und Session scheiben, Paginatorvariable schreiben
        $this->extbase_config['paginate'] = $this->jopaginatecenter;

        // Sobald eine Flashmessage erzeugt wurde, ist davon auszugehenm dass die Suchsession komplett aktualisiert werden muss
        $searchfilterset = null;
        if (!empty($this->flash)) {
            $this->joSessionUtil->replaceAllValues($this->joSessionvarName, $this->joSearchArrayComplete);
            $searchfilterset = 1;
        }

        // Suchparameter in die Session schreiben @all -> aktuell differenziert die condtion nicht zwischen Merklistenaktionen und suchaktionen
        // Suchfacetten erzeugen auf Basis der Flexform-Settings
        $joSolrFacettes = [];
        if (isset($this->config['facettenselect'])) {
            $this->config['allowed_facettes'] = [];
            $facettes_togenerate = explode("," , $this->config['facettenselect']);
            foreach ($facettes_togenerate as $value) {
                $facettes_config = explode("$", $value);
                $this->config['allowed_facettes'][$facettes_config[0]] = 1;
                $field_name = $facettes_config[0];
                $facette_type = $facettes_config[1];
                $facette_tag_name = $facettes_config[2];
                $facette_structured = $facettes_config[3];
                $facette_selected_value = $joSearcharraycomplete['content'][$field_name];
                if (is_array($joSearcharraycomplete['logical_concat']) && array_key_exists($field_name, $joSearcharraycomplete['logical_concat'])) $logical_concat = $joSearcharraycomplete['logical_concat'][$field_name];

                // Wenn kein Wert in der Session des betreffenden Datenfeldes enthalten ist oder die Facette via Ajax aufgerufen wird, wird geschaut on im temporären Speicher etwas aufgerufen wird (entityFirstletter, publisherFirstletter, etc)
                if (empty($facette_selected_value) || GeneralUtility::_GP('ret') == "h") {
                    //@all -> das greift nur bei hierarchischen facetten und führt aber zu sinnlosen dopplungen beim query, weil der folgenden query immer ausgeführt wird
                    $joSolrFacettes[] = $this->joMakeFacette($field_name, $facette_type, $facette_selected_value, $facette_tag_name, $logical_concat); // Sessionauswahl der Facette beachten
                    $facette_selected_value = $joSelectedValues[$field_name];
                }
                /**
                 *    Facettenytp überschreiben wenn es nötig ist
                 *     bei hierarchischen Facetten muss eine Unterscheidung vorgenommen werden:
                 *    Wenn $joSelectedValues['hierarchiefeld'] gesetzt ist (Auswahl eines Elements oder Sprung zur nächsten Ebene) muss ein Prefix mit verwendet werden
                 *    Wird benötigt um bei einem Ajax Request die nicht relevanten Facetten auszublenden -> solr Prefix
                 */
                // @all -> müsste man noch prüfen ob alles passt
                if (!empty($joSelectedValues[$field_name]) && GeneralUtility::_GP('ret') == "h" && "hierarchical" == $facette_structured) $facette_type = $facette_type . 'Prefix';
                    // Prototyp nur für BPI -> muss umgebaut werden
                    if ($field_name == 'classCollectionHierarchy' && $this->request->hasArgument('browsestructure')) {
                        if (!empty($facette_selected_value)) {
                            $facette_selected_value[0] = str_replace('(', '\(', $facette_selected_value[0]);
                            $facette_selected_value[0] = str_replace(')', '\)', $facette_selected_value[0]);
                            $facette_selected_value[0] = str_replace(':', '\:', $facette_selected_value[0]);
                            $joFacetteValues = '(' . urlencode($facette_selected_value[0] . ' NOT ' . $facette_selected_value[0]) .'/*)';
                            $joSolrFacettes[] = 'fq={!tag=' . $facette_tag_name . '}' . $field_name . ':' . $joFacetteValues . '&facet=on&facet.field={!ex=' . $facette_tag_name . '}' . $field_name;
                        } 
                        // $joSolrFacettes[] =  "fq={!tag=tes}tenantHierarchy:%22Berater%22&facet=on&facet.field={!ex=tes}tenantHierarchy";
                    } else {
                        $joSolrFacettes[] = $this->joMakeFacette($field_name, $facette_type, $facette_selected_value, $facette_tag_name, $logical_concat);
                    }
                    
            }
            if (!empty($joSelectedValues['entityFirstletter']) || !empty($joSearcharraycomplete['content']['entitynorole'])) {
                // @all -> das greift aktuell nur bei der Personenfacette - muss generell für alle Facetten gemacht werden -> aktuell greift es gar nicht - ist nicht fertig ;)
                $facet_limit = 150;
                $facet_offset = 0;
                if (GeneralUtility::_GP('start')) $facet_offset = (int) GeneralUtility::_GP('start');
                $joFieldname = 'entitynorole';
                $joFacetteType = "simple";
                $joTagname = [
                    "name" => "ent",
                    "exclude" => "ent,efl"
                ];
                $joSolrFacettes[] = $this->joMakeFacette($joFieldname, $joFacetteType, $joSearcharraycomplete['content']['entitynorole'], $joTagname, $logical_concat);
                //$joSolrFacettes[] = $this->solrQueryhandler->makeSimpleFacet($joFieldname, 0, 150, 'index');
            }
            if (!empty($joSelectedValues['entityAllFirstletter']) || !empty($joSearcharraycomplete['content']['entityAll'])) {
                // @all -> das greift aktuell nur bei der Personenfacette - muss generell für alle Facetten gemacht werden -> aktuell greift es gar nicht - ist nicht fertig ;)
                $facet_limit = 150;
                $facet_offset = 0;
                if (GeneralUtility::_GP('start')) $facet_offset = (int) GeneralUtility::_GP('start');
                $joFieldname = 'entityAll';
                $joFacetteType = "simple";
                $joTagname = [
                    "name" => "enta",
                    "exclude" => "enta,efla"
                ];
                $joSolrFacettes[] = $this->joMakeFacette($joFieldname, $joFacetteType, $joSearcharraycomplete['content']['entityAll'], $joTagname, $logical_concat);
                //$joSolrFacettes[] = $this->solrQueryhandler->makeSimpleFacet($joFieldname, 0, 150, 'index');
            }
            if (!empty($joSelectedValues['locationFirstletter']) || !empty($joSearcharraycomplete['content']['location'])) {
                $joFieldname = 'location';
                $joFacetteType = "extended";
                $joTagname = [
                    "name" => "loc",
                    "exclude" => "loc,lfl"
                ];
                $joSolrFacettes[] = $this->joMakeFacette($joFieldname, $joFacetteType, $joSearcharraycomplete['content']['location'], $joTagname, $logical_concat);
            }
            if (!empty($joSelectedValues['publisherFirstletter']) || !empty($joSearcharraycomplete['content']['publisher'])) {
                $joFieldname = 'publisher';
                $joFacetteType = "extended";
                $joTagname = [
                    "name" => "pub",
                    "exclude" => "pub,pfl"
                ];
                $joSolrFacettes[] = $this->joMakeFacette($joFieldname, $joFacetteType, $joSearcharraycomplete['content']['publisher'], $joTagname, $logical_concat);
            }

            if (!empty($joSelectedValues['objectsPlantsFirstletter']) || !empty($joSearcharraycomplete['content']['objectsPlants'])) {
                $joFieldname = 'objectsPlants';
                $joFacetteType = "extended";
                $joTagname = [
                    "name" => "opl",
                    "exclude" => "opl,opfl"
                ];
                $joSolrFacettes[] = $this->joMakeFacette($joFieldname, $joFacetteType, $joSearcharraycomplete['content']['objectsPlants'], $joTagname, $logical_concat);
            }
            if (!empty($joSelectedValues['classificationtagsFirstletter']) || !empty($joSearcharraycomplete['content']['classificationtags'])) {
                $joFieldname = 'classificationtags';
                $joFacetteType = "extended";
                $joTagname = [
                    "name" => "cft",
                    "exclude" => "cft,ctfl"
                ];
                $joSolrFacettes[] = $this->joMakeFacette($joFieldname, $joFacetteType, $joSearcharraycomplete['content']['classificationtags'], $joTagname, $logical_concat);
            }
            if (!empty($joSelectedValues['classificationtagsAllFirstletter']) || !empty($joSearcharraycomplete['content']['classificationtagsAll'])) {
                $joFieldname = 'classificationtagsAll';
                $joFacetteType = "extended";
                $joTagname = [
                    "name" => "cfta",
                    "exclude" => "cfta,ctfla"
                ];
                $joSolrFacettes[] = $this->joMakeFacette($joFieldname, $joFacetteType, $joSearcharraycomplete['content']['classificationtagsAll'], $joTagname, $logical_concat);
            }
            if (!empty($joSelectedValues['locationAllFirstletter']) || !empty($joSearcharraycomplete['content']['locationAll'])) {
                $joFieldname = 'locationAll';
                $joFacetteType = "extended";
                $joTagname = [
                    "name" => "loca",
                    "exclude" => "loca,lfla"
                ];
                $joSolrFacettes[] = $this->joMakeFacette($joFieldname, $joFacetteType, $joSearcharraycomplete['content']['locationAll'], $joTagname, $logical_concat);
            }
            // Massstab ermitteln 
            if (!empty($joSearcharraycomplete['content']['scaleMetric'])) {
                $joFieldname = 'scaleMetric';
                $joFacetteType = "range";
                $joTagname = [
                    "name" => "scm",
                    "exclude" => "scm"
                ];
                $joSolrFacettes[] = $this->joMakeFacette($joFieldname, $joFacetteType, $joSearcharraycomplete['content']['scaleMetric'], $joTagname, $logical_concat);
            }
        }

        // Queryparts erzeugen
        $fq = null;
        // Neuen Querybuilder starten -> @all es müssen die anderen Queries noch nachgezogen werden
        $query = GeneralUtility::makeInstance(QueryUtility::class);
        $all_query = $query
            ->setRequestObject($this->request)
            ->setConfig($this->config)
            ->setSearchsession($joSearcharraycomplete)
            ->setSelectedvalues($this->selected_values)
            ->buildQuery();

        // @all -> $all_query sollte perspektivisch ALLE Queryparts enthalten - aktuell sind nur der Part beim Browsen durch die Hierarchien und ein paar andere Exoten enthalten
        $joQuery = $all_query;
        
        // Boundingbox berücksichtigen - korrespondiert mit Solrfield geoLocation -> solr.BBoxField
        // Ist noch nicht optimal umgesetzt
        if ($GLOBALS['TSFE']->id == 339) {
            //@all -> das ist vom Kartenspeicher - muss geprüft werden, wie wir das via Parameter einbauen können
            if (!empty($joSearcharraycomplete['content']['Boundingbox'])){ 
                $fq = '{!field+f=georpt}Within(ENVELOPE(' . $this->joSearchArrayComplete['content']['Boundingbox']['x1'] . ',' . $this->joSearchArrayComplete['content']['Boundingbox']['x2'] . ',' . $this->joSearchArrayComplete['content']['Boundingbox']['y1'] . ',' . $this->joSearchArrayComplete['content']['Boundingbox']['y2'] . '))';
                // print_r($joSearcharraycomplete['content']['Boundingbox']);
            } else {
                // @all -> für NK umgesetzt und auskommentiert - schauen, ob das einfluss auf andere Projekte hat
                // $joQuery[] = 'locationPolygones:*';
            }
        } else {
            if (!empty($joSearcharraycomplete['content']['Boundingbox'])) $joSolrFacettes[] = $this->solrQueryhandler->makeRangeQuery('locationGeo', $joSearcharraycomplete['content']['Boundingbox']);
        }

        if (isset($joSearcharraycomplete['content']['searchradius']['lon']) && isset($joSearcharraycomplete['content']['searchradius']['lat'])) {
          $fq = '{!geofilt+sfield=georpt}&pt=' . $joSearcharraycomplete['content']['searchradius']['lat'] . ',' . $joSearcharraycomplete['content']['searchradius']['lon'] . '&d=' . ($joSearcharraycomplete['content']['searchradius']['distance']);
        }
        
        // Rangequery -> funktioniert mit dem Feld scaleMetrics z.B.
        $this->solrQueryhandler->josearchmode = "fuzzy";
        $this->solrQueryhandler->joReturnValue = "name";
        //if($this->extbase_config['action'] != 'detailobject' && $this->extbase_config['action'] != 'ajax'){
        //@all -> die condition hab ich auskommentiert - bei den ältren solrversionen hat das zu problemen geführt - jetzt scheint es zu gehen
        //if ($this->extbase_config['action'] != 'detailobject') {
            $fieldprefix = '';
            $joFieldname = 'fulltext';
            if ($this->extbase_config['action'] == 'ajax') $fieldprefix = 'fulltext:';
            if (!empty($joSearcharraycomplete['content'][$joFieldname])) $joQuery[] = $fieldprefix . $this->solrQueryhandler->joMakeQuerypartBetter($joSearcharraycomplete['content'][$joFieldname], " " . $joSearcharraycomplete['logical_concat'][$joFieldname] . " ", $joFieldname); 
           
                /*
                if(GeneralUtility::_GP('te') == 1){
                    echo "<pre>";
                    print_r($joQuery);
                    echo "</pre>";
                }  
                */
        //}

        // @all -> das hier besser umsetzen
        if (isset($this->settings['datatype'])) {
            // Wenn eine Anfrage nach Relationen gezündet wurde, wird der in der Flexform hinterlegte Datentyp überschrieben
            if (!empty($joSearcharraycomplete['content']['childrenReference']) && !empty($this->config['init']['baserequest']['objecttypes'])) {
                $ot = $this->config['init']['baserequest']['objecttypes'];
                if (!empty($joSearcharraycomplete['content']['objectType'])) $ot = $joSearcharraycomplete['content']['objectType'];
                $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($ot, " OR ", "objectType");
            } else {
                if (isset($this->settings['datatype']) && $this->settings['datatype']!= null) {
                    $querieparts_togenerate = explode(",", $this->settings['datatype']);
                    foreach ($querieparts_togenerate as $value) {
                        $query_config = explode("$", $value);
                        $query_items = explode('#', $query_config[0]);
                        $query_concat = " " . $query_config[2] . " ";
                        $query_fieldname = $query_config[1];
                        $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($query_items, $query_concat, $query_fieldname);
                    }
                }
            }
        }

        if (isset($this->config['classificationTopic']) && $this->config['classificationTopic'] != null) {
            $querieparts_togenerate = explode("," , $this->config['classificationTopic']);
            if (!empty($querieparts_togenerate)) {
                $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($querieparts_togenerate, ' OR ', 'classificationTopic');
            }
        }

        //@all -> möglicherweise obsolet -> checken
        if (!empty($joSearcharraycomplete['content']['pid'])) {
            $joQuery = [];
            $joSolrFacettes = [];
            $joFieldname = 'pid';
            $joConcat = " OR ";
            $joItems = $joSearcharraycomplete['content']['pid'];
            $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);
        }

        //@all -> korrespondiert mit der entitynorole Facette -> kann man sicher auch noch optimieren indem man eine Abfrage in den facettenrequest einbaut
        //@all -> das muss auch noch vereinheitlicht werden - nicht gut gelöst
        if (!empty($joSearcharraycomplete['content']['entitynorole'])) {
            $joFieldname = 'entitynorole';
            $joConcat = " AND ";
            if ($joSearcharraycomplete['logical_concat']['entitynorole']) $joConcat = " " . $joSearcharraycomplete['logical_concat']['entitynorole'] . " ";
            $joItems = $joSearcharraycomplete['content']['entitynorole'];
           $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);
        }

        if (!empty($joSearcharraycomplete['content']['entityAll'])) {
            $joFieldname = 'entityAll';
            $joConcat = " AND ";
            if ($joSearcharraycomplete['logical_concat']['entityAll']) $joConcat = " " . $joSearcharraycomplete['logical_concat']['entityAll'] . " ";
            $joItems = $joSearcharraycomplete['content']['entityAll'];
           $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);
        }

        if (!empty($joSearcharraycomplete['content']['classificationtags'])) {
            $joFieldname = 'classificationtags';
            $joConcat = " AND ";
            if ($joSearcharraycomplete['logical_concat']['classificationtags']) $joConcat = " " . $joSearcharraycomplete['logical_concat']['classificationtags'] . " ";
            $joItems = $joSearcharraycomplete['content']['classificationtags'];
            $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);
        }

        if (!empty($joSearcharraycomplete['content']['oldinventarnummer'])) {
            $joFieldname = 'oldinventarnummer';
            $joConcat = " AND ";
            if ($joSearcharraycomplete['logical_concat']['oldinventarnummer']) $joConcat = " " . $joSearcharraycomplete['logical_concat']['oldinventarnummer'] . " ";
            $joItems = $joSearcharraycomplete['content']['oldinventarnummer'];
            $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);
        }

        // Ausstellung gotha ausblenden @all -> das muss wieder raus
        //  $joQuery[] = 'NOT tenant:"Ausstellungen der FB Gotha"';

        $joQuery = array_filter($joQuery);
        if (empty($joQuery)) {
            $joItems = ['*'];
            $joConcat = " AND ";
            $joFieldname = '*';
            $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);
        }
        // Hierarchische Suche zulassen
        if (!empty($joSearcharraycomplete['content']['bcpId'])) {
            //$this->solrQueryhandler->josearchmode = "sharp";
            //$this->solrQueryhandler->joReturnValue = "name";
            $joFieldname = 'bcpId';
            $joConcat = " OR ";
            $joItems = $joSearcharraycomplete['content']['bcpId'];
            $joQuery[] = $this->solrQueryhandler->joMakeQuerypartBetter($joItems, $joConcat, $joFieldname);
        }
        // childrenReference Suche zulassen
        if (!empty($joSearcharraycomplete['content']['childrenReference'])) {
            $joFieldname = 'childrenReference';
            $joConcat = " OR ";
            $joItems = $joSearcharraycomplete['content']['childrenReference'];
            $joQuery[] = $this->solrQueryhandler->joMakeQuerypartBetter($joItems, $joConcat, $joFieldname);
        }
        if (!empty($joSearcharraycomplete['content']['entityTokenized'])) {
            // $joQuery = [];
            //$joSolrFacettes = [];
            $joFieldname = 'entityTokenized';
            $joConcat = " OR ";
            $joItems = $joSearcharraycomplete['content']['entityTokenized'];
            $joQuery[] = $this->solrQueryhandler->makeQueryWC($joItems, $joConcat, $joFieldname);
        }
        // Wenn die Items auf einer Karte dargestellt weden sollen, werden nur Datensätze mit ermittelten Geodaten ausgelesen
        if ($GLOBALS['TSFE']->id != 339 && $GLOBALS['TSFE']->id != 98) {
            if ($this->extbase_config['action'] == 'map' || $this->extbase_config['override_action'] == 'map') {
                $joItems = ['*'];
                $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, ' AND ', 'locationPolygones');
            }
        }

        // Eva Schiffmann fix, auf der karte wird irgendie nur nach locationPolygones gesucht und nicht lonlatidFacette
        if ($GLOBALS['TSFE']->id == 98) {
            $joItems = ['*'];
            $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, ' AND ', 'lonlatidFacette');
        }
        
        $this->convert_expersearch_settings($joQuery, $joSearcharraycomplete);
         //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($joQuery);
        /*
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('test')) {
            if (is_array($this->config['init']['searchconfig']['expertsearchfields'])) {
                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($allowed_filters);
            }
        }
        */

        // Das passiert nachgelagert noch einmal - kann später weg, wenn die Makesolrqueryklasse vollständig eingebaut wurde
        if (!empty($joSolrFacettes)) {
            $joSolrFacettes = array_unique($joSolrFacettes);
        }
        $joQuery = array_filter($joQuery);

        // Basale Suchparameter in Klassenvariablen schreiben
        $this->basequerydata = $joQuery; 
        $this->facettesdata = $joSolrFacettes; 
         
  
        /**
         *    Bei Aufruf der Detailaktion wird der bisherige Query und Facettenquery überschrieben
         *    Das Filterarray wird nicht geleert und steht somit noch zur Verfügung
         */
        $baseid = NULL;
       // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($joQuery);
        if (!empty($joSelectedValues['joDetailView'])) {
            // Wenn der Detailview direkt nach der Suche gezündet wird, werden die Suchparameter mit übergeben, ansonsten werden die Suchfilter ignoriert
            if (!$this->request->hasArgument('h')) {
                $joQuery = [];
                $joSolrFacettes = [];
            }
            // @all -> das muss aktuell immer gemacht werden, da sonst detailviews nur in kombination mit sucheingaben funktionieren - das einfügen einer detail url bei bestehenden (anderen) suchfiltern führte sonst zu leeren detailseiten 
            $joQuery = [];
           // $joSolrFacettes = [];
            //@all -> muss mit der zuvor stehenden Condition verbunden werden - das wird nötig, wenn man den kompletten suchquery für den Detailview benötigt um das highlightung zu realisieren...
            if ($this->request->hasArgument('browse')) {
                $joQuery = [];
                $joSolrFacettes = [];
            }
            $baseid = $joSelectedValues['joDetailView'][0];
            $joItems = $joSelectedValues['joDetailView'];
            
            $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, " OR ", 'id');
            // $joQuery[] = $this->solrQueryhandler->joMakeQuerypart(array("Kunstkammer Gotha"), " OR ", 'classificationtags');

            // $fq = "{!tag=t_id}id:" . $joSelectedValues['joDetailView'][0] . "&facet.field={!ex=t_id}id&facet.limit=10&facet.sort=index&facet=true&facet=on&facet";
            /*
            $joQuery[] = "id:*";
            $joSolrFacettes[] = $this->joMakeFacette('id', 'extended', $joSelectedValues['joDetailView'], 'det', ' OR '); // Sessionauswahl der Facette beachten
            $joSolrFacettes[] = "f.id.facet.limit=0";
            */
        }

        $this->fqdata = $fq;  
        
        $joQuery = array_filter($joQuery);
        /*
         if (GeneralUtility::_GP('test')) {
             $joQuery = array();
           
            $query_items = array("Editionsportal");
            $query_concat = " OR ";
            $query_fieldname = "classPortal";
            $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($query_items, $query_concat, $query_fieldname);
            if (!empty($joSelectedValues['joDetailView'])) {
                // Wenn der Detailview direkt nach der Suche gezündet wird, werden die Suchparameter mit übergeben, ansonsten werden die Suchfilter ignoriert
                if (!$this->request->hasArgument('h')) {
                    $joQuery = [];
                    $joSolrFacettes = [];
                }
                //@all -> muss mit der zuvor stehenden Condition verbunden werden - das wird nötig, wenn man den kompletten suchquery für den Detailview benötigt um das highlightung zu realisieren...
                if ($this->request->hasArgument('browse')) {
                    $joQuery = [];
                    $joSolrFacettes = [];
                }
                $baseid = $joSelectedValues['joDetailView'][0];
                $joItems = $joSelectedValues['joDetailView'];
                $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, " OR ", 'id');
            }
            
         }
         */
        // facettenarray bereinigen - es sind hier Dubletten drin @all -> die könnte man auch grundlegend verhindern zu einem frührere Zeitpunkt
        if (!empty($joSolrFacettes)) {
            $joSolrFacettes = array_unique($joSolrFacettes);
        }
        // Main-Query vorbereiten
        $this->query_params = [
            'solr' => $this->joSolrCore,
            'limit' => $this->joLimitPreset,
            'start' => $this->jopaginatecenter,
            'fl' => null,
            'fq' => $fq,
            'q' => $joQuery,
            'f' => $joSolrFacettes,
            'sort' => $this->sorting,
            'highlight' => [
                'fields' => [
                    '0' => 'title',
                    '1' => 'titleAlt',
                    '2' => 'bemerkung',
                    '3' => 'classification',
                    '4' => 'fulltextClean'
                ],
                'fragsize' => 120,
                'snippets' => 5
            ]
        ];

        // Collectorbox Berücksichtigen
        // @all -> ich denke, das brauchen wir nicht mehr - prüfen und ggf. löschen
        /*
        if (GeneralUtility::_GP('v')) {
           $special_view = filter_var(GeneralUtility::_GP('v'), FILTER_SANITIZE_STRING);
           switch ($special_view) {
                case 'colbox':  $joQuery = [];
                                $joSolrFacettes = [];
                                $joItems = $this->joCollectorsbox;
                                $joConcat = " OR ";
                                $joFieldname = 'id';
                                $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);

                                $this->query_params['solr'] = $this->joSolrCore;
                                $this->query_params['fl'] = 'id,images,tenant,title,titleAlt,classification,location,classificationtime,showtime,category,bemerkung,sorting,entity,entitynorole,objectTypeHierarchy,dateEvents,noteBundled'; // Es werden im Listview nur diese Datenfelder von Solr an das Frontend übertragen - @all -> könnte in die Settings übertragen werden
                                $this->query_params['sort'] = [];
                                $this->query_params['start'] = 1; //@all -> das passt noch nicht wenn die merkliste sehr lang ist - pagination muss noch rein
                                $this->query_params['q'] = $joQuery;
                                break;

           }
        }
        */

        // Ausgewählte Facetten aufbereiten
        if($this->config['facettenselect'] && $this->config['facettenselect'] != ''){
            $all_data = explode(',', $this->config['facettenselect']);
            if(!empty($all_data)){
                $all_facettes = [];
                $u = 0;
                foreach($all_data as $fac){
                    $tmp = explode('$', $fac);
                    $facette_literal = $tmp[0];
                    $all_facettes[] = $facette_literal;
                    if ($u < 1) $all_facettes['first_facette'] = $facette_literal;
                    $u++;
                }
                $all_facettes['summary'] = array_flip($all_facettes);
            }
        }

        // Wenn die erste Facette in der Facettenübersicht initial aufgeklappt sein soll, wird hier der Flag gesetzt
        $openfirstfacetteonload = null;
        if (!$this->current_selected && $this->config['openfirstfacette']) {
            $openfirstfacetteonload = $all_facettes['first_facette'];
        }

        if (!empty($this->joCollectorsbox)) {
            $this->view->assign('collbox_content', $this->joCollectorsbox);
        }
        
        $this->view->assignMultiple(
            [
                'openfirstfacetteonload' => $openfirstfacetteonload,
                'baseid' => $baseid,
                'config' => $this->config,
                'searchfilterset' => $searchfilterset,
                'userselected_facettes' => $all_facettes['summary'],
                'placeholerimage' => $this->placeholderimage,
                'contentelement' => $this->configurationManager->getContentObject()->data,
                'sorting' => $this->sorting,
                'langPath' =>  $this->extbase_config['lang_path'],
                'joSearcharraycomplete' => $this->joSearchArrayComplete,
                'settings' => $this->settings,
                'current_selected' => $this->current_selected
            ]
        );
    }

    /**
     * Download an external image regardless of whether jpeg, jpg or png
     * 
     * @return void
     */
    public function downloadimageAction() :void
    {
        if ($this->request->hasArgument('url')) {

            $image_url = filter_var($this->request->getArgument('url'), FILTER_SANITIZE_STRING);
            $image_arr = array_values(array_filter(explode('/', $image_url)));
            $image_size = str_replace(
                ['!', ','],
                ['', 'x'],
                $image_arr[8]
            );

            $image_name = strstr(end(explode('%2F', $image_arr[6])), '.', true);
            $dot_image_format = strstr(end($image_arr), '.');
            $nodot_image_format = str_replace('.', '', $dot_image_format);

            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Expires: 0");
            header("Content-Transfer-Encoding: binary");
            header("Content-Description: File Transfer");
            header("Content-Disposition:attachment;filename=" . $image_name . '__' . $image_size . $dot_image_format);
            header("Content-Type: image/" . $nodot_image_format);
            header("Pragma: public");

            $url = $image_url;
            $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_VERBOSE => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => $agent,
                CURLOPT_URL => $url
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
            echo $result;

            // readfile($image_url);
            die();
        }
    }

    public function add_expersearch_settings(&$allowed_filters)
    {
        //if (GeneralUtility::_GP('test')) {
            if (is_array($this->config['init']['searchconfig']['expertsearchfields'])) {
                foreach ($this->config['init']['searchconfig']['expertsearchfields'] as $key => $value) {
                    $allowed_filters[$value] = 3;
                    $allowed_filters['remove' . $value] = 10;
                    $allowed_filters['lgandor' . $value] = 20;
                }
            }
        //}
    }

    public function convert_expersearch_settings(&$joQuery, $joSearcharraycomplete)
    {
        //if (GeneralUtility::_GP('test')) {
            if (is_array($this->config['init']['searchconfig']['expertsearchfields'])) {
                foreach ($this->config['init']['searchconfig']['expertsearchfields'] as $key => $value) {
                    if (isset($joSearcharraycomplete['content'][$value])) {
                        // Wert splitten, wenn ein ___ darin auftaucht. So wird in Sammelfeldern differenziert. noteBundled___fundort
                        $field_array = explode('___', $value);
                        $joFieldname = $field_array[0];
                        $joConcat = " OR ";
                        $joItems = $joSearcharraycomplete['content'][$value];
                        $joQuery[] = $this->solrQueryhandler->joMakeQuerypartBetter($joItems, $joConcat, $joFieldname);
                    }

                }
            }
        //}
    }
    

    //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($facettes_prepared);
    public function listAction()
    {
        // Relevantes Template zur Darstellung der Objektübersicht identifizieren
        $this->setTemplateForOverview();

        // Daten aus dem Solr holen
        $this->joSolrResult = $this->solrRepository->contactSolr($this->query_params);
     
        if ($this->joSolrResult) {

            $this->joSolrResult = json_decode($this->joSolrResult);

            // Suchtreffer Highlighten
            if (!empty($this->joSearchArrayComplete['content'])) $this->joSolrResult = $this->solrQueryhandler->returnHighlightString($this->joSolrResult);
            
            // Suchtreffer aufbereiten
            $this->joSolrResult->response->docs = $this->modifyResults($this->joSolrResult->response->docs);     
            // Paginator berechnen @all -> das kann optimiert werden - zuviele Variablen schwirren hier umher
            $joPaginateDataArray = $this->solrIndexUtil->joMakePagination($this->joSolrResult->response, $this->query_params['limit'], $this->joPaginatePagesShow, $this->extbase_config['action'], $this->jopaginatecenter);
            // Facetten rendern
            $facettes_prepared = $this->renderFacettes();
            $facettes_prepared = $this->fillTimelineOptions($facettes_prepared);

            // @all -> ist noch prototypisch umgesetzt - muss verbessert werden
            if($this->extbase_config['listview'] == 'Listfolders') {
                $browse_facettes = $facettes_prepared['hierarchical_index']['tenantHierarchy'];
                if (isset($this->joSearchArrayComplete['content']['tenantHierarchy'])) {
                    $joSelectedIndex = $this->joSearchArrayComplete['content']['tenantHierarchy'][0];
                    $facette_name = "tenantHierarchy";
                    $index_facette = $this->joSolrResult->facet_counts->facet_fields->$facette_name;
                    $rows_to_show = substr_count($joSelectedIndex, '/') + 2;
                    $browse_facettes = $this->solrIndexUtil->getFacetteStructure($index_facette, $facette_name, $rows_to_show, $this->selected_values, $this->joSearchArrayComplete['content']);
                
                }
                $this->view->assign('browse_facettes', $browse_facettes);
            }
        } else {
            // exit ("solr connection failed");
        }

        /*
        // @all -> das ist für das Bayrische Münzprojekt prototypisch integriert worden - das muss verbessert werden, wenn dieses Projekt migriert wird
        if (isset($this->joSolrResult->facet_counts->facet_fields)) {
            if ($this->joSolrResult->facet_counts->facet_fields->material) {
                $material = [];
                $i = 0;
                $material[] = 'Material - Bitte auswählen' ;
                foreach ($this->joSolrResult->facet_counts->facet_fields->material as $k => $c) {
                    $i++;  
                    if ($i % 2 != 0) $material[$c] = $c;
                }
                $this->view->assign('material', $material);
            }
            $this->joSolrResult->facet_counts->facet_fields = [];
        }
        // @all -> expertsearch ist für NK konzipiert - noch nicht in aktuellen Projekten benutzt - muss verbessert werden
        if ($this->request->hasArgument('expertensearch')) $this->view->assign('expertensearch', true);
        */

        // Prüfen, ob eine Expertensiche gezündet wurde
        $this->expertSearchFired();

        // Flashmessages kumilieren und dem Template zugänglich machen
        if (!empty($this->flash))  {
            $this->addFlashMessage(implode(' ', $this->flash));
        }

        // Konfigurationsdaten als JavaScriptvariablen transformieren
        $this->addJavaScriptVars('var extbase_config=' . json_encode($this->extbase_config) . ';');

        // Javascriptvariablen in den Header schreiben insofern die Seite NICHT via AJAX geladen wird
        if (!$this->settings['ajaxload']) {
            if (!empty($this->javaScriptVars)) {
                $asset = GeneralUtility::makeInstance(AssetCollector::class);
                $this->javaScriptVars = array_filter($this->javaScriptVars);
                $asset->addInlineJavaScript('javaScriptVars', implode('', $this->javaScriptVars));
            }
        }
        
        // Prüfen, ob ein Bild geladen wurde, das als Hintergrundbild für die Suchmaske dient
        $img_obj = null;
        if ($this->settings['maskImages']) {
            $field = 'maskimages';
            $img_obj =  $this->getFileObject($field); 
        }
        
        // Variablen an das Template geben
        $this->view->assignMultiple(
            [
                'config' => $this->config,
                'maskImages' => $img_obj,
                'joSolrObjects' => $this->joSolrResult,
                'extbase_config' => $this->extbase_config,
                'paginationdata' => $joPaginateDataArray,
                'facettes_prepared' => $facettes_prepared,
                'javascriptvar' => implode(' ', $this->javaScriptVars)
            ]
        );
    }

    public function loadIIIFOverlay(): void
    {
        if ($this->config['iiifoverlay'] && $this->config['iiifoverlaycoord']) {
            $this->addJavaScriptVars('var iiif_url = "' . $this->config['iiifoverlay'] . '";');
            $this->addJavaScriptVars('var iiifoverlaycoord = ' . json_encode(array_map('str_getcsv',explode(' ', $this->config['iiifoverlaycoord']))) . ';');
        }
    }

    public function mapAction(): void
    {
        // Kartendarstellung initial rendern
        $this->loadMapFiles();
        // GeoJSON Overlay über die Karte legen -> aktuell in Flexform nicht auswählbar - auskommentiert
        $this->loadMapOverlay();
        // Wenn ein Objekt via IIIF über die OSM Karte gelegt werden soll, wird hier das JS gerendert
        $this->loadIIIFOverlay();   

        $search = $this->solrRepository->contactSolr($this->query_params);
        if ($search != '') {
            $this->joSolrResult = json_decode($search);
            if ($this->joSolrResult->response->numFound > 0) {
                $joPaginateDataArray = $this->solrIndexUtil->joMakePagination(
                    $this->joSolrResult->response, 
                    $this->query_params['limit'], 
                    $this->joPaginatePagesShow, 
                    $this->extbase_config['action'], 
                    $this->jopaginatecenter
                );
                $this->joSolrResult->response->docs = $this->modifyResults($this->joSolrResult->response->docs);
            }
        } 
        // Facetten rendern
        $facettes_prepared = $this->renderFacettes();
        $facettes_prepared = $this->fillTimelineOptions($facettes_prepared);

        // Geofelder Identifizieren
        $geodata = null;
        switch($this->config['geodatasource']){
            case 'locationPolygones':   $geodata = $this->makeGeoJsonFromLocationPolygones();
                                        break;

            default:                    $geodata = $this->makeGeoJsonFromLonlatidFacette();
                    
        }

        // JoMuseo Framework-Hooks hinzufügen - aktuell prototypisch - kann in Methode überführt werden, wenn wir weitere Anwendungsfälle haben
        if (isset($this->config['fieldlist']['mapview']['hook'][0]['class'])) {
           $classname = stripslashes($this->config['fieldlist']['mapview']['hook'][0]['class']);  
            if (class_exists($classname)) {
                $hook = GeneralUtility::makeInstance($classname);
                $hookdata = $hook
                    ->setRequestObject($this->request)
                    ->setSearchsession($this->joSearchArrayComplete)
                    ->setConfig($this->config)
                    ->init();
               // $geodata = $hookdata;
            }
        }

        // Javascript Variablen vorbereiten 
        // Boundingboxsuche zuschalten wenn der Parameter in der Flexform gesetzt wurde
        if ($this->config['showBoundingBox']) $this->addJavaScriptVars('var showBoundingBox = true;');

        // Geolocation zur Identifikation der eigenen Position zuschalten wenn der Parameter in der Flexform gesetzt wurde
        if ($this->config['geolocation']) $this->addJavaScriptVars('var geolocation = true;');

        // Georeferenzierte Objekte hinzufügen
        if ($geodata != null) $this->addJavaScriptVars($geodata);

        // Basiskonfiguration als Javascript JSON Variable hinzufügen
        $this->addJavaScriptVars('var extbase_config=' . json_encode($this->extbase_config) . ';');
        // Javascriptvariablen in den Header schreiben insofern die Seite NICHT via AJAX geladen wird
        if (!$this->config['ajaxload']) {
            if (!empty($this->javaScriptVars)) {
                $asset = GeneralUtility::makeInstance(AssetCollector::class);
                $this->javaScriptVars = array_filter($this->javaScriptVars);
                $asset->addInlineJavaScript('javaScriptVars', implode('', $this->javaScriptVars));
            }
        }

        // Flashmessages hinzufügen
        if (!empty($this->flash)) $this->addFlashMessage(implode(' ', $this->flash));
       
        // Alle Daten an das Template geben
        $this->view->assignMultiple(
            [
                'extbase_config' => $this->extbase_config,
                'paginationdata' => $joPaginateDataArray,
                'joSolrObjects' => $this->joSolrResult,
                'facettes_prepared' => $facettes_prepared,
                'javascriptvar' => implode(' ', $this->javaScriptVars)
            ]
        );     
    }

    public function detailobjectAction()
    {
        $assign = [];
        $showmap = false;   // Als Default wird keine Karte in der Detailviewansicht gezeichnet
        // todo - annotation checken in methode auslagern
        if ($GLOBALS['TSFE']->fe_user->user) {
            // Nutzerdaten zur Basiskonfiguration hinzufügen
            $this->extbase_config['registereduser'] = $GLOBALS['TSFE']->fe_user;
            // Annotationen verarbeiten
            $this->processAnnotations();
            // Prüfen, ob es eine Anotationen vom User bereits gibt
            $annotations = $this->checkForAnnotations();
            if (!empty($annotations)) $assign['usernote_clean'] = $annotations;
        }

        // todo - in methode auslagern - Einbindung einer Kommentarfunktion für das Userfeedback zu den Objekten
        if ($this->request->hasArgument('emaildata') && isset($this->config['init']['searchconfig']['objectcomment']['active'])) {
            $mailconfig = $this->getMailConfiguration();
            $send = $this->joMuseoUtil->sendMapServiceMail(filter_var_array($this->request->getArgument('emaildata'), FILTER_SANITIZE_STRING), $mailconfig);
            $this->flash[] = $send ? $this->translate($this->extbase_config['lang_path'] . ':email.feedback.send') : $this->translate($this->extbase_config['lang_path'] . ':email.error');
        }
        // Wenn man durch die Detailansichten browst, wird der Einstiegspunkt gesetzt und dann nur das nächste Objekt bzw. das vorherige gezogen
        $assign['prevnext'] = $this->buildDetailBrowser(); 
       

        $this->query_params['sort'] = [];
        $this->query_params['start'] = 1;
       
        // Daten aus dem Solr holen
        $solrdata = $this->solrRepository->contactSolr($this->query_params);    
        if ($solrdata) {
            $this->joSolrResult = json_decode($solrdata);
            $this->joSolrResult->response->docs = $this->modifyResults($this->joSolrResult->response->docs);
           // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->joSolrResult);
            // Facetten modifizieren
            $joSolrFacettes = $this->joSolrResult->facet_counts->facet_fields;
            if (!empty($joSolrFacettes)) {
                foreach ($joSolrFacettes as $facet_field_name => $fvalue) {
                    $reorderedfacette = $this->solrIndexUtil->reorderFacetteArray($joSolrFacettes->$facet_field_name);
                    $this->joSolrResult->facet_counts->facet_fields->$facet_field_name = $reorderedfacette;
                    // das dynamisch machen
                    unset($this->joSolrResult->facet_counts->facet_fields->entityFirstletter);
                    unset($this->joSolrResult->facet_counts->facet_fields->locationFirstletter);
                    unset($this->joSolrResult->facet_counts->facet_fields->classificationtagsFirstletter);
                    unset($this->joSolrResult->facet_counts->facet_fields->timeline);
                    unset($this->joSolrResult->facet_counts->facet_fields->tenant);
                }
                $assign['related_facettes'] = (array) $this->joSolrResult->facet_counts->facet_fields;
                // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($assign['related_facettes']);
            }
           

            // Falls es in der Konfiguration eine spezifische Darstellungskonfiguration für den jeweiligen Objekttyp gibt, wird dieser herangezogen für die Datenaggregation
            if ($this->joSolrResult->response->docs[0]->objectType) {
                $this->setDetailFieldList($this->joSolrResult->response->docs[0]->objectType);
            }

            // Verknüpfte Objekte ausgeben
            if (isset($this->config['init']['searchconfig']['showrelatedobjects']['connectingfield'])) {
                $relatedfieldname = $this->config['init']['searchconfig']['showrelatedobjects']['connectingfield'];
                // Wenn dieses Feld bespielt ist, wird eine Suche nach verknüpften Objekten gezündet
                if ($this->joSolrResult->response->docs[0]->$relatedfieldname) {
                    $fieldvalues = $this->joSolrResult->response->docs[0]->$relatedfieldname;
                    // Muss noch flexibler werden - aktuell wird nicht auf Vorhandensein der Variablen
                    // Das gesamte Paket der "related objects" muss flexibler gestaltet werden
                    $sorting_related = $this->initSorting([], $this->config['init']['searchconfig']['showrelatedobjects']['sorting']['init']);
                    if (is_array($fieldvalues) && !empty($fieldvalues)) {
                        $queryObject = GeneralUtility::makeInstance(\JO\JoMuseo\Utility\Fuzzysearchutils\Makesolrquery::class);
                        $relatedObjecsLimit = 48;
                        $related_objects_query = [];
                        if (isset($this->settings['hidefromsearch']) && $this->settings['hidefromsearch'] > 0) {
                            $joItems = ['0'];
                            $joConcat = " AND ";
                            $joFieldname = 'hiddenFromSearch';
                            $related_objects_query[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);
                        }

                        $joItems = $fieldvalues;
                        $joConcat = " OR ";
                        $joFieldname = $relatedfieldname;
                        $related_objects_query[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);
                        
                        // Neuen Query für Vorgänger und Nachfolger bauen
                        $related_objects_query = $queryObject->setSolr($this->joSolrCore)
                                                    ->setLimit($relatedObjecsLimit)
                                                    ->setQuery($related_objects_query)
                                                    ->setSorting($sorting_related)
                                                    ->setFieldlist('*')
                                                    ->generateQuery();
                        $related_objects = json_decode($this->solrRepository->contactSolr($related_objects_query));
                        $related_objects->response->docs = $this->modifyResults($related_objects->response->docs);
                        $assign['related_objects'] = $related_objects;
                    }
                }
            }


            // Seitentitel ermitteln und ändern
            $title = $GLOBALS['TSFE']->page['title'];
            $url = $this->request->getRequestUri();
            $image = null;
            $description = null;
            if ($this->joSolrResult->response->docs[0]->title && $this->joSolrResult->response->docs[0]->title != '') {
                $titleProvider = GeneralUtility::makeInstance(\JO\JoMuseo\PageTitle\RecordTitleProvider::class);
                $title = $this->joSolrResult->response->docs[0]->title;
                $titleProvider->setTitle($title);
                $url = $this->joSolrResult->response->docs[0]->canonical;
            }
            if ($this->joSolrResult->response->docs[0]->images) {
                $img_array = explode('$', $this->joSolrResult->response->docs[0]->images[0]);
                $image = $img_array[0];
            }
            if ($this->joSolrResult->response->docs[0]->note) {
                $description = $this->joSolrResult->response->docs[0]->note;
            }
            $assign['currentobject']['title'] = $title;
            $assign['currentobject']['url'] = $url;
            $assign['relation_pid'] = $this->joSolrResult->response->docs[0]->id;

            // Metatags anpassen für Facebook/Twitter und Shared Content
            // URL
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('og:url')->addProperty('og:url', $url);
            // Titel
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('og:title')->addProperty('og:title', $title);
            // Beschreibung
            if ($description != null) {
                $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('og:description')->addProperty('og:description', $description);
                $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('twitter:description')->addProperty('twitter:description', $description);
            }
            // Abbildung
            if ($image != null) {
                $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('og:image')->addProperty('og:image', $image);
                $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('twitter:image')->addProperty('twitter:image', $image);
            }
            // Twitter
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('twitter:title')->addProperty('twitter:title', $title);
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('twitter:domain')->addProperty('twitter:domain', $url);
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('twitter:card')->removeProperty('twitter:card'); 
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('twitter:card')->addProperty('twitter:card', 'summary_large_image');

            // @todo 
            // Wenn es unterobjekte gibt dann werden die an dieser Stelle ermittelt -> immer das vorletzte Element im Array des Parent-Fields @all -> das muss besser umgesetzt werden  das parentfeld muss im solr angepasst werden
            //@all -> aktuell wird IMMER eine Solr abfrage nach Kindobjekten gezündet - das müssen wir mal prüfen ob das die beste Lösung ist
            if (!$this->config['fieldlist']['childobjects']['dontshow']) {
                // Parent Element ermitteln
                $parent_relation = $this->joSolrResult->response->docs[0]->id;
                if ($parent_relation != null) {
                    $assign['parent_relation'] = $parent_relation;
                    // Offset ermitteln/setzen - ist initial 0
                    $chl_start = 0;
                    // Limit überschreiben, wenn es kleiner als 100 und in der Konfiguration gesetzt ist
                    $chl_limit = $this->getLimit($this->config['fieldlist']['childobjects']['limit']);
                    $related_objects = $this->getChildobjects($parent_relation, $chl_start, $chl_limit);
                    if (!empty($related_objects) && !empty($related_objects->response->docs)) {
                        // Markierung, dass es Kindelemente gibt an postprocessFieldConfig senden - damit wird das Kindelement_feld in der Ansicht nur dann rausgenommen, wenn es leer ist 
                        $this->joSolrResult->response->docs[0]->childobjectsInjected = 1;
                        $assign['child_elements'] = $related_objects;
                        // Offset für die nächsten auszulesenden Objekte ermitteln
                        $assign['calculate_offset'] = $this->getOffset($chl_start, $chl_limit, $related_objects->response->numFound);
                    }
                }
               
                // Detailkonfiguration anpassen, wenn notwendig (Entfernen  nicht vorhandener Konfigurationsstränge, insofern die zugehörigen Felder nicht vorhanden sind und so uch nicht ausgsepielt werden sollen)
                // Leere Spalten ermitteln und ggf. aus der Konfiguration entfernen
                // @all -> ist noch nicht feertig und kann sein, dass wir das ganz anders realisieren
                // Muss als Methode umgesetzt werdn und kann mit setDetailFieldList() verbunden werden
                $this->postprocessFieldConfig();
            }
            $lonlatfield = null;
            $zoomoff = 0;
          //  $this->joSolrResult->response->docs = $this->modifyResults($this->joSolrResult->response->docs);
            
            $asset = GeneralUtility::makeInstance(AssetCollector::class);
            
            $geojsonempty = true;

            if ($this->joSolrResult->response->docs[0]->locationQualified) $lonlatfield = "locationQualified";
            if (null != $lonlatfield && !$this->settings['hideMapDetail']) {
                $geoJSON = $this->makeGeoJSON($this->joSolrResult->response->docs, $lonlatfield);
                if (!empty($geoJSON)) $showmap = true;
                if (isset($geoJSON['features']) && count($geoJSON['features']) > 1) $zoomoff = 1;
                $geojsonempty = false;
                $asset->addInlineJavaScript('map_lonlat', 'var zoomoff=' . $zoomoff . '; var geojson=' . json_encode($geoJSON) . ';');
            }
            // Wenn es ein Polygon neben der einfachen Geoinformation gibt, wird dieses hier als GeoJSON Objekt in den Header geschrieben
            // @all -> das ist noch nicht flexibel -> berücksichtigt nur das erste polygon
            
            if ($this->joSolrResult->response->docs[0]->locationPolygones){
                if (is_object(json_decode($this->joSolrResult->response->docs[0]->locationPolygones[0]))) {
                    $asset->addInlineJavaScript('map_polygons', 'var polygones=' . $this->joSolrResult->response->docs[0]->locationPolygones[0] . ';');

                    if ($geojsonempty) $asset->addInlineJavaScript('map_lonlat', 'var geojson=[];');
                    
                    $showmap = true;
                } 
            } 
            if ($showmap == true) $this->loadMapFiles();

            // Resultate modifizieren
            if ($this->config['fieldlist']['detailview']['images']['useopenlayers'] == 1) {
                $this->joMuseoUtil->addHeaderFile('ol_v6_9.min.js', $this->extensionName);
                $this->joMuseoUtil->addHeaderFile('ol_v6_9.min.css', $this->extensionName);
               // $this->joSolrResult->response->docs = $this->modifyResults($this->joSolrResult->response->docs);
            }



        }
        $assign['config'] = $this->config;
        
        // Zoomlevel für die Detailkarte
        if (isset($this->config['fieldlist']['detailview']['config']['zoomlevelondetailmap'])) {
            $this->addJavaScriptVars('var zoomlevelondetailmap = ' . intval($this->config['fieldlist']['detailview']['config']['zoomlevelondetailmap']) .';');
        }

        if (isset($this->config['fieldlist']['detailview']['config']['showOnlyPolygone'])) {
            $this->addJavaScriptVars('var showOnlyPolygone=1;');
        }

        // Javascriptvariablen in den Header schreiben insofern die Seite NICHT via AJAX geladen wird
        if (!$this->settings['ajaxload']) {
            if (!empty($this->javaScriptVars)) {
                $asset = GeneralUtility::makeInstance(AssetCollector::class);
                $this->javaScriptVars = array_filter($this->javaScriptVars);
                $asset->addInlineJavaScript('javaScriptVars', implode('', $this->javaScriptVars));
            }
        }
        // Kartenanwendung laden
        $this->loadMapOverlay();
        // Flashmessages
        if (!empty($this->flash))  $this->addFlashMessage(implode(' ', $this->flash));

    
        // Variablen an Template geben
        $assign['showmap'] = $showmap;
        $assign['extbase_config'] = $this->extbase_config;
        $assign['joSolrObjects'] = $this->joSolrResult;
        $assign['javascriptvar'] = implode(' ', $this->javaScriptVars);
        $this->view->assignMultiple($assign);
    }

     public function facetteAction()
    {
        $this->query_params['sort'] = [];
        $this->query_params['limit'] = 0;
        $this->joSolrResult = json_decode($this->solrRepository->contactSolr($this->query_params));
        $joSolrFacettes = $this->joSolrResult->facet_counts->facet_fields;
        // erste Facette ermitteln - alle anderen Facetten werden ignoriert
        if (count($joSolrFacettes) === 1 && key($joSolrFacettes)) {
            $field_name = key($joSolrFacettes);
            $facettetype = 'standard';
            if(strpos($field_name, 'Firstletter'))  $facettetype = 'alphabetic';
            switch ($facettetype) {
                case 'alphabetic':  $reorderedfacette = $this->solrIndexUtil->makeIndexAlphabet($joSolrFacettes->$field_name);
                                    break;
                default:            $reorderedfacette = $this->solrIndexUtil->reorderFacetteArray($joSolrFacettes->$field_name);

            }
            $joSolrFacettes->$field_name = $reorderedfacette;
        } else {
            exit("Bitte wählen Sie mindestens/höchstens eine Facette aus");
        }
        $this->view->assignMultiple(
            [
                'config' => $this->config,
                'joSolrFacettes' => $joSolrFacettes,
                'extbase_config' => $this->extbase_config
            ]
        );
    }


    public function entrymaskAction()
    {
        // Menü für die Einstiegsmaske rendern - optional - source: Flexform
        $assign = [];
        $this->setMaskmenu();
        if (!$this->config['hideResults'] && $this->config['init']['searchconfig']['expertsearch'] != 0) {
            $allowed_templates = ['List', 'Listgrid'];  // Erlaubte Templates -> @all -> kann in TS Datei
            // Prüfen welche Ansicht ausgespielt werden soll Liste/Tabelle/Grid
            $list_template = $this->extbase_config['action']; // Initiales Template - entspricht dem Actionnamen
            // Initiale Darstellung der Listenansicht wenn es eine Konfiguration in der TS Datei gibt
            if ($this->config['templates']['initlist'] && $this->settings['allowlistandtable']) $list_template = $this->config['templates']['initlist'];
            // Steuerung der Templates durch den Benutzer -> Umschaltung der Views im FE
            if (GeneralUtility::_GP('v') && $this->settings['allowlistandtable']) $list_template = filter_var(GeneralUtility::_GP('v'), FILTER_SANITIZE_STRING);
            $list_template = ucfirst($list_template);

            // Wenn das ausgewählte Template erlaubt ist, wird es ausgespielt
            /*
            if (in_array($list_template, $allowed_templates)) {
                $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
                    'typo3conf/ext/' . $this->request->getControllerExtensionKey() . '/Resources/Private/Templates/Museo/' . $list_template . '.html'
                ));
            }
            */
            // gewähltes Template global definieren
            $this->extbase_config['listview'] = $list_template;
            // Daten aus dem Solr holen
            $this->joSolrResult = $this->solrRepository->contactSolr($this->query_params);

            if (!$this->joSolrResult) exit ("solr connection failed");

            $this->joSolrResult = json_decode($this->joSolrResult);
            // Suchtreffer Highlighten
            $this->joSolrResult = $this->solrQueryhandler->returnHighlightString($this->joSolrResult);
            // Suchtreffer aufbereiten
            $this->joSolrResult->response->docs = $this->modifyResults($this->joSolrResult->response->docs);
            // Paginator berechnen @all -> das kann optimiert werden - zuviele Variablen schwirren hier umher
            $joPaginateDataArray = $this->solrIndexUtil->joMakePagination($this->joSolrResult->response, $this->query_params['limit'], $this->joPaginatePagesShow, $this->extbase_config['action'], $this->jopaginatecenter);
            // Facetten rendern
            $facettes_prepared = $this->renderFacettes();
            $facettes_prepared = $this->fillTimelineOptions($facettes_prepared);

            $assign['paginationdata'] = $joPaginateDataArray;
            $assign['facettes_prepared'] = $facettes_prepared;

            if ($this->joSolrResult->facet_counts->facet_fields) {
                if ($this->joSolrResult->facet_counts->facet_fields->material) {
                    $material = [];
                    $i = 0;
                    $material[] = 'Material - Bitte auswählen' ;
                    foreach ($this->joSolrResult->facet_counts->facet_fields->material as $k => $c) {
                        $i++;  
                        if ($i % 2 != 0) $material[$c] = $c;
                    }
                    $assign['material'] = $material;
                }
            }
            // @all -> keine Ahnung wofr das gut ist
            if ($this->joSolrResult->facet_counts->facet_fields) $this->joSolrResult->facet_counts->facet_fields = [];
            $assign['joSolrObjects'] = $this->joSolrResult;

        }
        // Wenn Hintergrundbilder benötigt werden, werden die hier erzeugt
        $img_obj = [];
        if ($this->settings['maskImages']) {
            $field = 'maskimages';
            $img_obj =  $this->getFileObject($field); 
            $assign['maskImages'] = $img_obj;
        }

        if ($this->settings['maskLogo']) {
            $field = 'masklogo';
            $img_logo =  $this->getFileObject($field); 
            $assign['masklogo'] = $img_logo;
        }

        $assign['javascriptvar'] = implode(' ', $this->javaScriptVars); 
        $assign['config'] = $this->config;
        $assign['extbase_config'] = $this->extbase_config;

        // Variablen an das Template geben
        $this->view->assignMultiple($assign);
    }

    public function collboxAction()
    {
        $joQuery = [];
        $joSolrFacettes = [];
        $joItems = $this->joCollectorsbox;
        $joConcat = " OR ";
        $joFieldname = 'id';
        $joQuery[] = $this->solrQueryhandler->joMakeQuerypart($joItems, $joConcat, $joFieldname);
        $this->query_params['solr'] = $this->joSolrCore;
        // $this->query_params['fl'] = 'id,images,tenant,title,titleAlt,classification,location,classificationtime,showtime,category,bemerkung,sorting,entity,entitynorole,objectTypeHierarchy,dateEvents,noteBundled'; // Es werden im Listview nur diese Datenfelder von Solr an das Frontend übertragen - @all -> könnte in die Settings übertragen werden
        $this->query_params['sort'] = [];
        $this->query_params['start'] = 1; //@all -> das passt noch nicht wenn die merkliste sehr lang ist - pagination muss noch rein
        $this->query_params['q'] = $joQuery;
        $this->joSolrResult = json_decode($this->solrRepository->contactSolr($this->query_params));
        if (!empty($this->joSolrResult->response->docs))  {
            $this->joSolrResult->response->docs = $this->modifyResults($this->joSolrResult->response->docs);
             // Export der Merkliste
            if ($this->request->hasArgument('export_box')) $this->writeExcel($this->joSolrResult->response->docs);
        }
        $this->view->assignMultiple(
            [
                'joSolrObjects' => $this->joSolrResult,
                'extbase_config' => $this->extbase_config
            ]
        );
    }

    public function entityfactsAction()
    {
        $gnd = filter_var(GeneralUtility::_GET('gnd'), FILTER_SANITIZE_STRING);
        if ($gnd) {
            $fact_stream = file_get_contents('https://hub.culturegraph.org/entityfacts/' . $gnd);

            if ($fact_stream) {
                $fact_stream = str_replace('@', '', $fact_stream);
                $fact_stream = \Normalizer::normalize($fact_stream, \Normalizer::FORM_C);
                $fact_stream = json_decode($fact_stream, true);
                
                $this->view->assign('fact_stream', $fact_stream);
            } else {
                $xml_stream = $this->solrHarvester->getRDFData('https://d-nb.info/gnd/' . $gnd . '/about/rdf');
                $xml_data = $this->solrHarvester->makeXMLContent($xml_stream);

                $output = [];

                if ($xml_data) {
                    if ($xml_data->xpath('/rdf:RDF/rdf:Description/gndo:gndIdentifier')) {
                        $output['gndIdentifier'] = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:gndIdentifier')[0]->__toString();
                    }

                    if ($xml_data->xpath('/rdf:RDF/rdf:Description/gndo:oldAuthorityNumber')) {
                        $output['oldAuthorityNumber'] = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:oldAuthorityNumber')[0]->__toString();
                    }

                    if ($xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheCorporateBody')) {
                        $output['preferredNameForTheCorporateBody'] = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheCorporateBody')[0]->__toString();
                    }

                    if ($xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheFamily')) {
                        $output['preferredNameForTheFamily'] = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheFamily')[0]->__toString();
                    }

                    if ($xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForThePlaceOrGeographicName')) {
                        $output['preferredNameForThePlaceOrGeographicName'] = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForThePlaceOrGeographicName')[0]->__toString();
                    }

                    if ($xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForThePerson')) {
                        $output['preferredNameForThePerson'] = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForThePerson')[0]->__toString();
                    }

                    if ($xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheConferenceOrEvent')) {
                        $output['preferredNameForTheConferenceOrEvent'] = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheConferenceOrEvent')[0]->__toString();
                    }

                    if ($xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheWork')) {
                        $output['preferredNameForTheWork'] = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheWork')[0]->__toString();
                    }

                    if ($xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheSubjectHeading')) {
                        $output['preferredNameForTheSubjectHeading'] = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:preferredNameForTheSubjectHeading')[0]->__toString();
                    }

                    $itemSet = $xml_data->xpath('/rdf:RDF/rdf:Description/gndo:variantNameForTheSubjectHeading');
                }

                if ($itemSet) {
                    $i = 0;
                    $len = count($itemSet);

                    foreach ($itemSet as $sub) {
                        $output['variantNameForTheSubjectHeading'] .= $sub[0]->__toString();
                        if ($i !== $len - 1) {
                            $output['variantNameForTheSubjectHeading'] .= '<br/>';
                        }
                        $i++;
                    }
                }

                $this->view->assign('gnd_stream', $output);
            }
            
        }
    }


    public function ajaxAction()
    {
        $assign = [];
        //@all -> das muss aus der konfig kommen
        $this->query_params['limit'] = 100;

        if ($this->request->hasArgument('joDetailViewHash')) {
            //$this->query_params['q'] = ['locationHash:"' . $this->request->getArgument('joDetailViewHash') . '"'];
            //@all -> geht noch nicht richtig - zu viele einschränkungen
            $this->query_params['q'][] = 'locationHash:"' . filter_var($this->request->getArgument('joDetailViewHash'), FILTER_SANITIZE_STRING) . '"';
        }
        //    Wenn der Parameter "ret == fac" übergeben wird, werden nur die alphabetischen Facetten zurückgespielt und keine Objekte ausgegeben
        if (GeneralUtility::_GP('ret') == 'fac') {
            $this->query_params['limit'] = 0;
            $this->query_params['sort'] = [];
            $this->query_params['highlight'] = [];
            /*  Es werden nur die ersten 151 facetten gezogen 
             *  150 werden angezeigt und 1 dient als Indikator, dass da noch weitere kommen - unter dieser Bedingung wird ein Slider aktiviert, mit dem weitere Facetten nachgeladen werden können 
             * Diese Zahl könnte theoretsch auch in die TS - später Yaml Konfiguration übernommen werden
             */
            $this->query_params['facette_limit'] = 151;
            $this->query_params['facette_offset'] = 0;
            if (GeneralUtility::_GP('start')) $this->query_params['facette_offset'] = intval(GeneralUtility::_GP('start'));
            // Prefix benutzen, um nicht zuviele Facetten zu ziehen
            $this->generateFacettePrefix();
            /*
            $this->query_params['facette_prefix'] = null;
            if ($this->request->hasArgument('entityFirstletter')) {
                $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('entityFirstletter'), FILTER_SANITIZE_STRING);
            }
            if ($this->request->hasArgument('entityAllFirstletter')) {
                $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('entityAllFirstletter'), FILTER_SANITIZE_STRING);
            }
            if ($this->request->hasArgument('locationFirstletter')) {
                $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('locationFirstletter'), FILTER_SANITIZE_STRING);
            }
            if ($this->request->hasArgument('locationAllFirstletter')) {
                $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('locationAllFirstletter'), FILTER_SANITIZE_STRING);
            }
            if ($this->request->hasArgument('classificationtagsFirstletter')) {
                $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('classificationtagsFirstletter'), FILTER_SANITIZE_STRING);
            }
            if ($this->request->hasArgument('classificationtagsAllFirstletter')) {
                $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('classificationtagsAllFirstletter'), FILTER_SANITIZE_STRING);
            }
            if ($this->request->hasArgument('publisherFirstletter')) {
                $this->query_params['facette_prefix'] = filter_var($this->request->getArgument('publisherFirstletter'), FILTER_SANITIZE_STRING);
            }
            // Muss dynamisch alle firstletter variablen berücksichtigen -> alphafield mapping
            // Dann kann alles was in dem ret swicthcase kommt noch optimiert werden
            */
        }
        // Wenn die Hierarchische Facette gezündet wird, werden ebenfalls keine Objekte ermittelt und ausgegeben
        if (GeneralUtility::_GP('ret') == 'h') {
            $this->query_params['limit'] = 0;
            $this->query_params['sort'] = [];
            $this->query_params['highlight'] = [];
        }
        //@all -> das kann weg nach prüfung
        $this->query_params['sort'] = [];

        /*
        echo "<pre>";
        print_r($this->query_params);
        echo "</pre>";
        */
         $this->joSolrResult = json_decode($this->solrRepository->contactSolr($this->query_params));
        /*
         echo "<pre>";
        print_r($this->joSolrResult);
        echo "</pre>";
        */
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->joSolrResult);
        switch (GeneralUtility::_GP('ret')) {
            // Childelements und andere Bezüge nachladen
            case 'chl':
                // Parent Element ermitteln
                $parent_relation = $this->getVariable('prl', 'extbase', 'string');
                if ($parent_relation != null) {
                    $assign['parent_relation'] = $parent_relation;
                    // Aktuellen Offset aus der Variable ermitteln
                    $chl_start = $this->getVariable('chs', 'extbase', 'int');
                    // Limit überschreiben, wenn es kleiner als 100 und in der Konfiguration gesetzt ist
                    $chl_limit = $this->getLimit($this->config['fieldlist']['childobjects']['limit']);
                    $related_objects = $this->getChildobjects($parent_relation, $chl_start, $chl_limit);
                    if (!empty($related_objects) && !empty($related_objects->response->docs)) {
                        $assign['child_elements'] = $related_objects;
                        // Offset für die nächsten auszulesenden Objekte ermitteln
                        $assign['calculate_offset'] = $this->getOffset($chl_start, $chl_limit, $related_objects->response->numFound);
                    }
                }
                // Templatepfad umbiegen
                $pathToTemplate = 'typo3conf/ext/' . $this->request->getControllerExtensionKey() . '/Resources/Private/Templates/Museo/Ajaxchildren.html';
                $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($pathToTemplate));
                break;

            // Rückgabe einer hierarchischen Facette
            case 'h':
                // Hierachischer Objekttyp
                //@all -> das muss noch dynamisch gemacht werden, damit es für alle hierarchischen facetten zutrifft - prüfen ob der Case mit dem fac-case zuammengelegt werden kann
                if ($this->request->hasArgument('objectTypeHierarchy')) {
                    $facette_name = "objectTypeHierarchy";
                    $joSelectedIndex = filter_var($this->request->getArgument('objectTypeHierarchy'), FILTER_SANITIZE_STRING);
                }
                if ($this->request->hasArgument('tenantHierarchy')) {
                    $facette_name = "tenantHierarchy";
                    $joSelectedIndex = filter_var($this->request->getArgument('tenantHierarchy'), FILTER_SANITIZE_STRING);
                }
                if ($this->request->hasArgument('classCollectionHierarchy')) {
                    $facette_name = "classCollectionHierarchy";
                    $joSelectedIndex = filter_var($this->request->getArgument('classCollectionHierarchy'), FILTER_SANITIZE_STRING);
                }
                if ($this->request->hasArgument('locationHierarchy')) {
                    $facette_name = "locationHierarchy";
                    $joSelectedIndex = filter_var($this->request->getArgument('locationHierarchy'), FILTER_SANITIZE_STRING);
                }
                if ($joSelectedIndex) {
                    $pathToTemplate = 'typo3conf/ext/' . $this->request->getControllerExtensionKey() . '/Resources/Private/Templates/Museo/HierarchieFacette.html';
                    $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($pathToTemplate));
                    $index_facette = $this->joSolrResult->facet_counts->facet_fields->$facette_name;
                    $rows_to_show = substr_count($joSelectedIndex, '/') + 2;
                    $facettes_prepared['hierarchical_index'][$facette_name] = $this->solrIndexUtil->getFacetteStructure($index_facette, $facette_name, $rows_to_show, $this->selected_values, $this->joSearchArrayComplete['content']);
                    $this->view->assign('facette_results', $facettes_prepared['hierarchical_index'][$facette_name]);
                }

                break;
            case 'fac':
                $joSelectedIndex = false;
                $joSelectedVarArray = [];
                /* Verbundene Personen */
                // @all -> da noch optimieren und mit den alpha_mappings abstimmen
                if ($this->request->hasArgument('entityFirstletter')) {
                    $joSelectedVarArray["varname"] = "entitynorole";
                    $joSelectedVarArray["letter"] = filter_var($this->request->getArgument('entityFirstletter'), FILTER_SANITIZE_STRING);
                    $joSelectedIndex = "entitynorole";
                }
                if ($this->request->hasArgument('entityAllFirstletter')) {
                    $joSelectedVarArray["varname"] = "entityAll";
                    $joSelectedVarArray["letter"] = filter_var($this->request->getArgument('entityAllFirstletter'), FILTER_SANITIZE_STRING);
                    $joSelectedIndex = "entityAll";
                }
                if ($this->request->hasArgument('locationFirstletter')) {
                    $joSelectedVarArray["varname"] = "location";
                    $joSelectedVarArray["letter"] = filter_var($this->request->getArgument('locationFirstletter'), FILTER_SANITIZE_STRING);
                    $joSelectedIndex = "location";
                }
                if ($this->request->hasArgument('publisherFirstletter')) {
                    $joSelectedVarArray["varname"] = "publisher";
                    $joSelectedVarArray["letter"] = filter_var($this->request->getArgument('publisherFirstletter'), FILTER_SANITIZE_STRING);
                    $joSelectedIndex = "publisher";
                }
                if ($this->request->hasArgument('locationAllFirstletter')) {
                    $joSelectedVarArray["varname"] = "locationAll";
                    $joSelectedVarArray["letter"] = filter_var($this->request->getArgument('locationAllFirstletter'), FILTER_SANITIZE_STRING);
                    $joSelectedIndex = "locationAll";
                }
                if ($this->request->hasArgument('objectsPlantsFirstletter')) {
                    $joSelectedVarArray["varname"] = "objectsPlants";
                    $joSelectedVarArray["letter"] = filter_var($this->request->getArgument('objectsPlantsFirstletter'), FILTER_SANITIZE_STRING);
                    $joSelectedIndex = "objectsPlants";
                }
                if ($this->request->hasArgument('classificationtagsFirstletter')) {
                    $joSelectedVarArray["varname"] = "classificationtags";
                    $joSelectedVarArray["letter"] = filter_var($this->request->getArgument('classificationtagsFirstletter'), FILTER_SANITIZE_STRING);
                    $joSelectedIndex = "classificationtags";
                }
                if ($this->request->hasArgument('classificationtagsAllFirstletter')) {
                    $joSelectedVarArray["varname"] = "classificationtagsAll";
                    $joSelectedVarArray["letter"] = filter_var($this->request->getArgument('classificationtagsAllFirstletter'), FILTER_SANITIZE_STRING);
                    $joSelectedIndex = "classificationtagsAll";
                }
                if ($joSelectedIndex) {
                    $joSolrResults = $this->joSolrResult->facet_counts->facet_fields->$joSelectedIndex; // enthaltene Facetten

                    $this->solrIndexUtil->joSpecifiedLetterOnly = $joSelectedVarArray["letter"]; // Multivaluefields bereinigen - nur Werte mit ausgewählte Anfangsbuchstaben werden berücksichtigt
                    $joSolrResults = $this->solrIndexUtil->reorderFacetteArray($joSolrResults);
                    // Wenn mehr als 150 Entitäten ermittelt wurden, wird das Ergebnis geteilt und als Zwischenstufe an das Template übergeben
                    // @all -> das muss noch flexibel gestaltet werden
                    if ($joSolrResults) {
                        $count = count($joSolrResults);
                        if ($count > 150) {
                            $start = 0;
                            if (GeneralUtility::_GP('start')) $start = intval(GeneralUtility::_GP('start'));
                            $newArray = array_slice($joSolrResults, $start, 150);

                            $this->solrIndexUtil->joSpalten = 3;
                            $this->solrIndexUtil->joDelimiter = "$";
                            $this->solrIndexUtil->joGetKeyOrValue = "key";
                            $newArray = $this->solrIndexUtil->joColumSplitNew($newArray);
                            $this->view->assignMultiple(
                                [
                                    'joSolrResults' => $newArray,
                                    'joSolrResultsPrev' => $start > 0 ? true : false,
                                    'joSolrResultsStart' => $start + 150,
                                    'nextExist' => $count > ($start + 150) ? true : false
                                ]
                            );
                        } else {
                            $this->solrIndexUtil->joSpalten = 3;
                            $this->solrIndexUtil->joDelimiter = "$";
                            $this->solrIndexUtil->joGetKeyOrValue = "key";
                            $joSolrResults = $this->solrIndexUtil->joColumSplitNew($joSolrResults);
                            $this->view->assign('joSolrResults', $joSolrResults);
                        }
                    }
                }
                // Template umbiegen - es wird die Facettenübersicht geladen
                $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
                    'typo3conf/ext/' .
                    $this->request->getControllerExtensionKey() .
                    '/Resources/Private/Templates/Museo/Ajaxfacette.html'
                ));  
                $this->view->assign('joSelectedVarArray', $joSelectedVarArray);

                break;
            case 'json':
                // Aktive Pins ermitteln
                $session_name = 'hidden_items' . $GLOBALS['TSFE']->id;
                $hidden_items = $this->joSessionUtil->getSessionValues($session_name);
                if ($this->request->hasArgument('toggle_id')) {
                    $item_to_toggle = filter_var($this->request->getArgument('toggle_id'), FILTER_SANITIZE_STRING);
                    if ('all' == $item_to_toggle) {
                        $hidden_items = $this->joSessionUtil->emptySession($session_name);
                    } else {
                        $hidden_items = in_array($item_to_toggle, $hidden_items) ? $this->joSessionUtil->removeValue($session_name, $item_to_toggle) : $this->joSessionUtil->addValue($session_name, $item_to_toggle);
                    }
                }
                if (!empty($this->joSolrResult->facet_counts->facet_fields->lonlatidFacette)) {
                    $facette_temp = $this->solrIndexUtil->reorderFacetteArray($this->joSolrResult->facet_counts->facet_fields->lonlatidFacette);
                    $geoJSON = $this->makeGeoJSONFacette($facette_temp, '$');
                } else {
                    if (!empty($this->joSolrResult->response->docs)) {
                        $results_modified = [];
                        foreach ($this->joSolrResult->response->docs as $value) {
                            //@all -> hier wird nur der erste Wert der Geokoordinaten berücksichtigt - man müsste ggf ds Datenmodel ändern  und die ergebnisse in Mittelpubktkoorinate und Polygone trennen
                            $pin_active = 'a';
                            if (!empty($hidden_items) && in_array($value->id, $hidden_items)) $pin_active = 'p';
                            $results_modified[$value->id . '$' . $value->lonlat[0] . '$#' . $value->sorting] = $pin_active;
                        }
                        $geoJSON = $this->makeGeoJSONFacette($results_modified, '$');
                    }
                }
                if (null != $geoJSON) {
                    // Routing auf der Karte ausgeben zwischen den Punkten
                    // @all -> das ist noch sehr undynamisch
                    if ($this->settings['showRoute']) {
                        $point_array = $this->geoUtil->getRouteShape($this->settings['routeUrl'], $this->settings['routeKey'], 'pedestrian', $geoJSON['locations']);
                        $geoJSONFull = [
                            'geojson' => $geoJSON['pois'],
                            'geojsonRoute' => [],
                            'routeInfo' => $point_array['routeinfo_details']
                        ];
                        if (count($point_array['waypoints']) > 1) {
                            $geoJSONFull['geojsonRoute'] = [
                                'title' => 'Industrieroute',
                                'type' => 'LineString',
                                'coordinates' => $point_array['waypoints']
                            ];
                        }
                        ob_flush();
                        header("Content-Type: application/json");
                        echo json_encode($geoJSONFull);
                        exit();
                    }
                }
                break;
            // Full Detail -> Detail geladen via AJAX
            case 'fd':
                $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
                    'typo3conf/ext/' .
                    $this->request->getControllerExtensionKey() .
                    '/Resources/Private/Templates/Museo/Ajaxdetail.html'
                ));
                $this->view->assign('joSolrObjects', $this->joSolrResult);
                break;
            default:
                $this->joSolrResult = $this->solrQueryhandler->returnHighlightString($this->joSolrResult);
                $this->joSolrResult->response->docs = $this->modifyResults($this->joSolrResult->response->docs);
                $this->view->assign('joSolrObjects', $this->joSolrResult);
                $session_name = 'hidden_items' . $GLOBALS['TSFE']->id;
                $hidden_items = $this->joSessionUtil->getSessionValues($session_name);
                $this->view->assign('hidden_items', $hidden_items);
        }

        $assign['extbase_config'] = $this->extbase_config;
        $assign['isajax'] = true;
        $this->view->assignMultiple($assign);
    }

    public function soloshowAction()
    {
        $assign = [];
        $tmp_query_params = $this->query_params;
        $tmp_query_params['solr'] = $this->joSolrCore;
        $tmp_query_params['sort'] = [];
        $tmp_query_params['limit'] = 10;
        $tmp_query_params['q'] = [];
        $tmp_query_params['q'][] = 'objectType:Bühnenbild';

        if ($this->request->hasArgument('detail')) {
            $selectedID = $this->request->getArgument('detail');
            $tmp_query_params['q'][] = 'id:' . $selectedID;            
            $assign['detail'] = 1;
        }
        $joSolrResult = $this->solrRepository->contactSolr($tmp_query_params);
        if ($joSolrResult) {
            $joSolrResult = json_decode($joSolrResult);
            $joSolrResult->response->docs = $this->modifyResults($joSolrResult->response->docs);
            $assign['results'] = $joSolrResult;
        }
        $this->view->assignMultiple($assign);
        
    }

    public function suggestAction()
    {
        $query = filter_var(GeneralUtility::_POST('q'), FILTER_SANITIZE_STRING);
        $handler = filter_var(GeneralUtility::_POST('handler'), FILTER_SANITIZE_STRING);
        $id = filter_var(GeneralUtility::_POST('id'), FILTER_VALIDATE_INT);
        if ($query && $handler) {
            $this->query_params['qt'] = $handler;
            if (str_contains($handler, 'suggest')) {
                $this->query_params['suggest.q'] = $query;
            } else {
                $this->query_params['spellcheck.q'] = $query;
            }

            
            /*
            echo "<pre>";
        print_r($this->query_params);
         echo "</pre>";
        die("ss");
        */
            $joSolrResult = json_decode($this->solrRepository->contactSolr($this->query_params));

            if (str_contains($handler, 'suggest')) {
                // @all -> man kann dies zusammenfassen, den suggester so nennen wie die handler und so mit variable zugreifne oder so
                
                if ($joSolrResult->suggest->joSuggester->$query) {
                    $output = [];
                    foreach ($joSolrResult->suggest->joSuggester->$query->suggestions as $key => $value) {
                        $link = $this->controllerContext->getUriBuilder()->setTargetPageUid($id)->setNoCache(true)->setCreateAbsoluteUri(true)->uriFor(null, ['entitynorole' => $value->term], 'Museo', null, 'pi1009');
                        array_push($output, [
                            'label' => explode('$', $value->term)[0],
                            'link' => $link
                        ]);
                    }
                    echo json_encode($output);
                }
                if ($joSolrResult->suggest->joSuggesterClassi->$query) {
                    $output = [];
                    foreach ($joSolrResult->suggest->joSuggesterClassi->$query->suggestions as $key => $value) {
                        $link = $this->controllerContext->getUriBuilder()->setTargetPageUid($id)->setNoCache(true)->setCreateAbsoluteUri(true)->uriFor(null, ['classificationtags' => $value->term], 'Museo', null, 'pi1009');
                        array_push($output, [
                            'label' => explode('$', $value->term)[0],
                            'link' => $link
                        ]);
                    }
                    echo json_encode($output);
                }
                if ($joSolrResult->suggest->joSuggesterLoc->$query) {
                    $output = [];
                    foreach ($joSolrResult->suggest->joSuggesterLoc->$query->suggestions as $key => $value) {
                        $link = $this->controllerContext->getUriBuilder()->setTargetPageUid($id)->setNoCache(true)->setCreateAbsoluteUri(true)->uriFor(null, ['location' => $value->term], 'Museo', null, 'pi1009');
                        array_push($output, [
                            'label' => explode('$', $value->term)[0],
                            'link' => $link
                        ]);
                    }
                    echo json_encode($output);
                }
            } else {
                if ($joSolrResult->spellcheck->suggestions) {
                    $output = [];
                    //$num = count($joSolrResult->spellcheck->suggestions) - 3;
                    $num = 1;
                    foreach ($joSolrResult->spellcheck->suggestions[$num]->suggestion as $key => $value) {
                        $link = $this->controllerContext->getUriBuilder()->setTargetPageUid($id)->setNoCache(true)->setCreateAbsoluteUri(true)->uriFor(null, ['entitynorole' => $value], 'Museo', null, 'pi1009');
                        array_push($output, [
                            'label' => explode('$', $value)[0],
                            'link' => $link
                        ]);
                    }
                    echo json_encode($output);
                }
            }
        }
    }
}