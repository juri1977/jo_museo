<?php
namespace JO\JoMuseo\Utility\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait MuseoControllerUtility
{

    /**
     *  Alle Settings zusammenfassen und in eine eigene Konfigurationsvariable schreiben
     *
     *  @return object this
     */
    public function initTSConfigurationData() : object
    {
        $this->config = $this->settings;
        return $this; 
    }

    /**
     *  Ursprüngliche Action, die von dem Plugin initial angesteuert wurde, bevor diese eventuell überschrieben wurde
     *
     *  @return string Name der initialen Aktion
     */
    public function getInitialActionName() : string
    {
        $original_action = '';
        if (GeneralUtility::_GP('oa')){
            $original_action = filter_var(GeneralUtility::_GP('oa'), FILTER_SANITIZE_STRING);
        }
        return $original_action; 
    }

    /**
     *  Name der Extension in der Form tx_namekleingeschriebenohneunterstriche z.B. tx_jomuseo
     *
     *  @return string Name der Extension
     */
    public function getTxExtensionName() : string
    {
        $tx_extensionname = "tx_" . str_replace("_", "", $this->request->getControllerExtensionKey());
        return $tx_extensionname; 
    }

    /**
     *  Name des Prefixes, der benutzt wird, um die Variablen der Extension auszuzeichnen 
     *
     *  @return string entsprechender Prefix in der Form tx_extensionname_pluginname
     */
    public function getPrefix() : string
    {
        $prefix = $this->getTxExtensionName() . "_" . lcfirst($this->request->getPluginName());
        return $prefix; 
    }

    /**
     *  Pagetype ermitteln. Wenn das Plugin via Ajax geladen wird, wird dieser von 0 auf z.B. 200 gesetzt.
     *
     *  @return int Pagetype
     */
    public function getPagetype() : int
    {
        $pagetype = 0;
        if($this->settings['ajaxload']){
            $pagetype = 200;
        }
        return $pagetype; 
    }

    /**
     *  Pfad zur Language-Datei ermitteln
     *
     *  @return string Languagepfad
     */
    public function getLanguagepath() : string
    {
        $lll = "LLL:";
        // Standardpfad zur Sprachdatei
        $lang_path = $lll . "EXT:" . $this->request->getControllerExtensionKey() . "/Resources/Private/Language/locallang.xlf";
        // Wenn via TypoScript-Settings ein anderer Pfad in der Form EXT:jo_template/Resources/Private/Language/Gothadigital/locallang.xlf hinterlegt wird, wird dieser benutzt
        if($this->settings['langPath']){
            $lang_path = $lll . $this->settings['langPath'];
        }
        return $lang_path; 
    }

    /**
     * Seitenkonfiguration aus der Session laden
     *
     * Wenn keine Konfigurationssession existiert, wird ein leeres Array zurückgegeben
     *
     * @return array Konfiguration als Array
     *             
     */
    public function getPageconfigData() : array 
    {
        $page_config = array();   
        $page_conf_session = 'page_config_' . $GLOBALS['TSFE']->id;
        $page_config_sessionvalues = $this->joSessionUtil->getSessionValues($page_conf_session);
        if(is_array($page_config_sessionvalues) && !empty($page_config_sessionvalues)){
            $page_config = $page_config_sessionvalues;
        }
        return $page_config;
    }

    /**
     *  Placeholderimage ermitteln. Wenn über das Backend ein Placeholderimage hinterlegt wurde, wird es hier ausgelesen
     *
     *  @return obeject this
     */
    public function getPlaceholderImage() : object
    {
        $this->placeholderimage = null;
        if ($this->config['imgplaceholder']) {
            $field = 'image';
            $image_temp = $this->getFileObject($field); 
            if (is_array($image_temp) && !empty($image_temp)) {
                $this->placeholderimage = array_shift($image_temp);
            }
        }
        return $this; 
    }

    public function initExtbaseConfig() : object{
        // Content Objekt ermitteln und aktuelle Page UID ermitteln
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $contentObj = $configurationManager->getContentObject();
        
        $this->extbase_config = [
            'pagetype' => $this->getPagetype(),
            'ce_uid' => $contentObj->data['uid'],
            'ce_pid' => $contentObj->data['pid'],
            'ext_name' => $this->getTxExtensionName(),
            'plugin_name' => $this->request->getPluginName(),
            'controller_name' => $this->request->getControllerName(),
            'action' => $this->request->getControllerActionName(),
            'override_action' => $this->getInitialActionName(),
            'prefix' => $this->getPrefix(),
            'baseurl' => $GLOBALS['TSFE']->baseUrl,
            'currentPageId' => $GLOBALS['TSFE']->id,
            'lang_path' => $this->getLanguagepath()
        ];
        // Zusätzliche Konfigurationsoptionen
        // Annotationen für  TEI Auszeichnungen anzeigen oder ausblenden 
        $this->extbase_config['page_config'] = $this->getPageconfigData();
        return $this;
    }

    /**
     * Gibt einen Teil eines Arrays zurück
     *
     * Wenn der Teil nicht existiert, wird ein leeres Array zurückgegeben
     *
     * @param array $arraypart Arraypart 
     * @return array gewünschter Teil des Arrays
     *             
     */
    public function getArrayPart($arraypart = array()) : array{
        $return = array();
        if(isset($arraypart) && is_array($arraypart)){
            $return = $arraypart;
        }
        return $return;
    }

    protected function makeAnnotation()
    {
        $annotation = [];
        if ($this->request->hasArgument('text') && $this->request->hasArgument('id') && $GLOBALS['TSFE']->fe_user->user) {
            $annotation['comment'] = filter_var($this->request->getArgument('text'), FILTER_SANITIZE_STRING);
            $annotation['entityid'] = filter_var($this->request->getArgument('id'), FILTER_SANITIZE_STRING);
            $annotation['img_name'] = filter_var($this->request->getArgument('img_name'), FILTER_SANITIZE_STRING);
            $annotation['feuser'] = $GLOBALS['TSFE']->fe_user->user['uid'];
            $annotation['public'] = 0;
            $annotation['coords'] = '';
            if ($this->request->hasArgument('coords')) $annotation['coords'] = filter_var($this->request->getArgument('coords'), FILTER_SANITIZE_STRING);
        }
        return $annotation;
    }

    protected function setAnnotationValues($object = null, $annotation = [])
    {
        if ($object != null && !empty($annotation)) {
            $object->setCoordinates($annotation['coords']);
            $object->setComment($annotation['comment']);
            $object->setFeuser($annotation['feuser']);
            $object->setEntityid($annotation['entityid']);
            $object->setAsset($annotation['img_name']);
            $object->setPublic($annotation['public']);
            return $object;
        }
    }

    protected function setTemplateForOverview(): object
    {
        // Erlaubte Templates identifizieren
        $allowed_templates = $this->getArrayPart($this->config['init']['searchconfig']['allowed_templates']['listview']);
        // Prüfen welche Ansicht ausgespielt werden soll Liste/Tabelle/Grid/Listfolders/Gallery

        $list_template = $this->extbase_config['action']; // Initiales Template - entspricht dem Actionnamen und ist in jedem Fall erlaubt
        // Initiale Darstellung der Listenansicht wenn es eine Konfiguration in der Flexform gibt -> config.listview
        if (isset($this->config['listview'])){
            $listview_array = explode(',', $this->config['listview']);
            if (!empty($listview_array) && isset($listview_array[0]) && in_array($listview_array[0], $allowed_templates)) {
                $this->extbase_config['differentviews'] = $listview_array;
                $list_template = $listview_array[0];
            }
        }

        // Steuerung der Templates durch den Benutzer -> Umschaltung der Views im FE -> überschreibt die Flexformauswahl
        if (GeneralUtility::_GP('v')) {
            $list_template_selected = filter_var(GeneralUtility::_GP('v'), FILTER_SANITIZE_STRING);
            if (in_array($list_template_selected, $allowed_templates)) {
                $list_template = $list_template_selected;
            }
        }  
        $list_template = ucfirst($list_template);

        // Wenn das ausgewählte Template erlaubt ist, wird es ausgespielt
        $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'typo3conf/ext/' . $this->request->getControllerExtensionKey() . '/Resources/Private/Templates/Museo/' . $list_template . '.html'
        ));

        // gewähltes Template global definieren
        $this->extbase_config['listview'] = $list_template;

        return $this;
    }

    protected function processAnnotations()
    {
        $note_id = 0;
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager"); 
        $annotation = $this->makeAnnotation();
        $dbaction = false;
        if ($this->request->hasArgument('note_id') && intval($this->request->getArgument('note_id')) > 0) {
            $note_id = intval($this->request->getArgument('note_id'));
            $annotationobjects = $this->annotationRepository->findByUid($note_id);
            if ($annotationobjects) {
                // Annotation löschen 
                if ($this->request->hasArgument('delete') && intval($this->request->getArgument('delete')) == 1) {
                    $this->annotationRepository->remove($annotationobjects);
                    $dbaction = true;
                } else {
                    // Bestehende Annotation ändern
                    if (!empty($annotation)) {
                        $annotationobjects = $this->setAnnotationValues($annotationobjects, $annotation);
                        $this->annotationRepository->update($annotationobjects);
                        $dbaction = true;
                    }
                }
            }
        } else {
            // Neue Annotation hinzufügen
            if (!empty($annotation)) {
                $annotationobjects = new \JO\JoMuseo\Domain\Model\Annotation;
                $annotationobjects = $this->setAnnotationValues($annotationobjects, $annotation);
                $this->annotationRepository->add($annotationobjects);
                $dbaction = true;
           } 
        }
        if ($dbaction == true) {
            $persistenceManager->persistAll();
            if ($annotationobjects) echo $current_uid = $annotationobjects->getUid();
            exit();
        }
    }

    protected function checkForAnnotations()
    {
        $return = [];
        if ($this->request->hasArgument('joDetailView')) {
            $objectid_clean = filter_var($this->request->getArgument('joDetailView'), FILTER_SANITIZE_STRING);
            $demand = [
                'data' => [
                    'entityid' => $objectid_clean,
                    'feuser' => $GLOBALS['TSFE']->fe_user->user['uid']
                ]
            ];
            $annotationobjects = $this->annotationRepository->findDemanded($demand);
            $arr_obj = [];
            foreach ($annotationobjects as $key => $value) {
                $arr_obj[] = [
                    'id' => $value->getUid(),
                    'asset' => $value->getAsset(),
                    'coords' => $value->getCoordinates(),
                    'note' => $value->getComment(),
                ];
            }
            $return = $arr_obj;
        } 
        return $return;
    }
    // toDo - diese Methode ist nicht fertig
    public function clearAllDecisionTree($joSearcharraycomplete = array()){
        $return = false;
        if(!empty($joSearcharraycomplete)){
            $args = $this->request->getArguments();
            // die folgenden Parameter sorgen dafür, dass die suchsession nicht gelöscht wird
            $blacklist = array('entitynorole');
            $somethingonblacklist = array();
            if(is_array($args) && is_array($blacklist)){
                $somethingonblacklist = array_intersect(array_flip($args), $blacklist);
            }
            // Grundlegend wird immer gelöscht wenn der delete Button gedrückt wird
            if($this->request->hasArgument('joDel')){
                $return = true;
            }
            // Wenn das joDel-Signal gesetzt wird und aber ein anderes Argument gezündet wird, das 
        }

        return $return;
    }

    public function setSolrCore(){
        if (isset($this->config['init']['authdata']['server'])) {
            $this->joSolrCore = trim($this->config['init']['authdata']['server']);
        } else {
            exit('no repository defined');
        } 
         return $this; 
    }

    public function setSolrLimit(){
        // Initial wird der Wert der Klassenvariable benutzt
        // Wenn im Baserequest der Konfiguration ein Wert gesetzt ist, wird dieser benutzt
        if (isset($this->config['init']['baserequest']['limit']) && is_numeric($this->config['init']['baserequest']['limit'])) {
            $this->joLimitPreset = intval($this->config['init']['baserequest']['limit']);
        }
        // Wenn in der Flexform ein wert konkretisiert wurde, wird dieser benutzt
        if (isset($this->config['limit']) && is_numeric($this->config['limit'])) {
            $this->joLimitPreset = intval($this->config['limit']);
        }
        return $this;
    }

    public function setPaginationData(){
        // Initial wird der Wert der Klassenvariable benutzt
        // Wenn im Baserequest der Konfiguration ein Wert gesetzt ist, wird dieser benutzt
        if (isset($this->config['init']['baserequest']['paginatepagecount']) && is_numeric($this->config['init']['baserequest']['paginatepagecount'])) {
            $this->joPaginatePagesShow = intval($this->config['init']['baserequest']['paginatepagecount']);
        }
        return $this;
    }

    public function setProjectName(){
         if (isset($this->config['init']['specific']['projectname'])) {
            $this->projektname = $this->config['init']['specific']['projectname'];
         }
        return $this;  
    }

    public function setMaskmenu(){
         if (isset($this->config['maskmenu'])) {
            $pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
            $pages = explode(',', $this->config['maskmenu']);
            $menu = [];
            foreach ($pages as $k => $pageId) {
                $page = $pageRepository->getPage($pageId);
                $menu[$pageId] = $page['title'];
            }
            $this->config['maskmenu'] = $menu;
        }
         return $this;
    }

    public function setSessionName(){
         // Content Objekt ermitteln und aktuelle Page UID ermitteln
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $contentObj = $configurationManager->getContentObject();
        $content_element_uid = $contentObj->data['uid'];
        $this->joSessionvarName = "museosearch_" . $GLOBALS['TSFE']->id . "_" . $content_element_uid;
        return $this;
    }

    public function setCollectorboxName(){
        $this->colboxname = "collectorsbox-" . $this->projektname;
        return $this;
    }

    public function getCollectorbox() {
        if ($this->joSessionUtil->getSessionValues($this->colboxname)) {
            $this->joCollectorsbox = $this->joSessionUtil->getSessionValues($this->colboxname);
        }
        return $this;      
    }

    public function getCollectorboxCookie() {
        if ($this->joSessionUtil->getCookieValues($this->colboxname)) {
            $this->joCollectorsbox = $this->joSessionUtil->getCookieValues($this->colboxname);
        }
        return $this;      
    }

    /**
     * Merkliste verwalten
     *
     * Objekte auf die temporäre oder persistente Liste setzen
     *
     * @return object
     *             
     */
    public function handleCollectorBoxData() : object
    {
        $flashmessages = null;
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager"); 
        if ($this->request->hasArgument('add_to_box')) {
            $add_to_box = filter_var($this->request->getArgument('add_to_box'), FILTER_SANITIZE_STRING);
            $this->joCollectorsbox = $this->joArrayUtil->joAddToArrayAndMakeUnique($this->joCollectorsbox, $add_to_box);
            $flashmessages = $this->translate($this->extbase_config['lang_path'] . ':flash.add.merkliste');
        }
        if ($this->request->hasArgument('remove_from_box')) {
            $remove_from_box = filter_var($this->request->getArgument('remove_from_box'), FILTER_SANITIZE_STRING);
            $this->joCollectorsbox = $this->joArrayUtil->joEliminateArrayValueAndKey($this->joCollectorsbox, $remove_from_box);
            $flashmessages = $this->translate($this->extbase_config['lang_path'] . ':flash.remove.merkliste');
        }
        if ($this->request->hasArgument('delete_box')) {
            $this->joCollectorsbox = [];
            $flashmessages = 'Sie haben Ihre Merkliste gelöscht.';
        }
        $this->joCollectorsbox = array_filter($this->joCollectorsbox);
        // Dialoge vorbereiten
        if ($flashmessages != null) {
            $this->joSessionUtil->replaceAllValues($this->colboxname, $this->joCollectorsbox);
            $this->joSessionUtil->replaceAllCookieValues($this->colboxname, $this->joCollectorsbox);
            // Wenn der Warenkorb persistent gespeichert werden soll, der Nutzer eingeloggt ist und ein Speicherort für den Warenkorb hinterlegt ist wird der Warenkorb in die TYPO3 Datenbank geschrieben
            if(isset($this->config['warenkorb']['persistence']) && 
                !empty($GLOBALS['TSFE']->fe_user->user) &&
                isset($this->config['warenkorb']['persistenceid']) &&
                isset($this->config['warenkorb']['projectname'])){
                    $user_id = $GLOBALS['TSFE']->fe_user->user['uid'];
                    // Generischen Typo3 User aussperren
                    if($user_id < 1000000){
                        // Prüfen, ob noch eine Merkliste vorhanden ist
                        $demand = array();
                        $demand['data']['feuserid'] = $user_id;
                        $demand['data']['project'] = $this->config['warenkorb']['projectname'];  
                        $demand['data']['pid'] = $this->config['warenkorb']['persistenceid'];
                        $inbox = $this->collectorboxRepository->findDemanded($demand);
                        $new = true;
                        if(is_object($inbox) && $inbox->count() > 0){
                            $new = false;
                            $collectorboxobjects = $inbox[0];
                        }else{
                            $collectorboxobjects = new \JO\JoMuseo\Domain\Model\Collectorbox;
                            $collectorboxobjects->setProject($this->config['warenkorb']['projectname']);
                            $collectorboxobjects->setPid($this->config['warenkorb']['persistenceid']);
                            $collectorboxobjects->setFeuserid($user_id);
                        }
                        $collectorboxobjects->setBoxdata(json_encode($this->joCollectorsbox));
                        if($new){
                            $this->collectorboxRepository->add($collectorboxobjects);
                        }else{
                            $this->collectorboxRepository->update($collectorboxobjects);
                        }
                        $persistenceManager->persistAll();
                    }
            }
            $this->flash[] = $flashmessages;
        }else{
            // Wenn man die Merkliste einfach ansteuert, ohne ein Objekt hinzufügen oder zu löschen und man ist eingeloggt, wird die Merkliste aus der Datenbank übernommen
            if(isset($this->config['warenkorb']['persistence']) && 
                !empty($GLOBALS['TSFE']->fe_user->user) &&
                isset($this->config['warenkorb']['persistenceid']) &&
                isset($this->config['warenkorb']['projectname'])){
                    $user_id = $GLOBALS['TSFE']->fe_user->user['uid'];
                    if($user_id < 1000000){
                        $demand = array();
                        $demand['data']['feuserid'] = $user_id;
                        $demand['data']['project'] = $this->config['warenkorb']['projectname'];  
                        $demand['data']['pid'] = $this->config['warenkorb']['persistenceid'];
                        $inbox = $this->collectorboxRepository->findDemanded($demand);
                        if(is_object($inbox) && $inbox->count() > 0){
                            $boxdata = $inbox[0]->getBoxdata();
                            if($boxdata){
                               $this->joCollectorsbox = json_decode($boxdata);
                               $this->joSessionUtil->replaceAllValues($this->colboxname, $this->joCollectorsbox);
                               $this->joSessionUtil->replaceAllCookieValues($this->colboxname, $this->joCollectorsbox);
                            }
                        }
                    }
            }
        }
        return $this;
    }

    public function buildSearchFilters($joSearcharraycomplete = array()){
        $joSearcharraycomplete = $this->deleteSearcharray($joSearcharraycomplete);
        $joSearcharraycomplete = $this->activateOrDeactivateSubsearch($joSearcharraycomplete);
        $joSearcharraycomplete = $this->showObjectsWithImagesOnly($joSearcharraycomplete);
        return $joSearcharraycomplete;
    }

    public function activateOrDeactivateSubsearch($joSearcharraycomplete = array()){
        // Wenn innerhalb einer Parent/Child verbindung gesucht wird, wird die ursprüngliche Suche zwischengespeichert
        if ($this->request->hasArgument('setsubsearch')) {
            if (!isset($joSearcharraycomplete['history']) && !isset($joSearcharraycomplete['content']['childrenReference'])) {
                $searchhistory = $joSearcharraycomplete;
                if (!empty($joSearcharraycomplete)) {
                    $this->joSessionUtil->emptySession($this->joSessionvarName);
                    $joSearcharraycomplete = [];
                }
                $joSearcharraycomplete['history'] = $searchhistory;
            }
        }
        if ($this->request->hasArgument('backtooriginalsearch') || $this->request->hasArgument('removechildrenReference')) {
            if (!isset($joSearcharraycomplete['history'])) $joSearcharraycomplete['history'] = [];
            if (isset($joSearcharraycomplete['history'])) {
                $searchhistory = $joSearcharraycomplete['history'];
                $this->joSessionUtil->emptySession($this->joSessionvarName);
                $joSearcharraycomplete = [];
                $joSearcharraycomplete = $searchhistory;
                $this->flash[] =  "Zurück zur ursprünglichen Suche.";
            }
        }
        return $joSearcharraycomplete;
    }

    public function deleteSearcharray($joSearcharraycomplete = array()){
        // $this->clearAllDecisionTree($joSearcharraycomplete);
        // @all > rs wird der neue Standard - joDel wird perspektivisch remigiert
        if ($this->request->hasArgument('joDel') || GeneralUtility::_GP('rs')) {
            $joSearcharraycomplete = [];
            $this->joSessionUtil->emptySession($this->joSessionvarName);
            $this->flash[] = $this->translate($this->extbase_config['lang_path'] . ':flash.remove.suchfilter');
        } 
        return $joSearcharraycomplete;
    }

    public function showObjectsWithImagesOnly($joSearcharraycomplete = array()){
        if ($this->request->hasArgument('imageonly')) {
            $joSearcharraycomplete['content']['imageonly'] = array('aktiviert');
            $this->flash[] = "zeige nur Objekte mit Abbildungen";
        } 
        if ($this->request->hasArgument('removeimageonly')) {
            unset($joSearcharraycomplete['content']['imageonly']);
            $this->flash[] = "zeige Objekte mit und ohne Abbildungen";
        } 
        return $joSearcharraycomplete;
    }

    /**
     *  Funktion zur Formulierung exkludierender Facetten
     *  z.B. facet.field={!ex=det,dat}fieldname -> das Feld 'fieldname' berücksichtigt bei dessen Facettierung nicht die Felder mit den Tags 'det' und 'dat'
     *  So wird z.B. eine Mehrgfachauswahl eines Feldes möglich, weil neben der ausgewählten Option die weiherhin möglichen Optionen mit ausgespielt werden
     *  Das Suchergebnis der Objekte enhält hingeben NUR die Ergebnisse, die dem Filter entsprechen
     *
     *  @param string $facet_fieldname - Feldname, auf den die Tags angewendet werden sollen
     *  @param array $tags - Tags mit denen die Felder markiert wurden, die exkludiert werden sollen
     *  @return string - nicht url-encoded String mit den exkludierten Tags
     */
    public function excludeFacetteFilter(array $tags = [], string $facet_fieldname = '') : string
    {
        $return = '';
        if(!empty($tags) && $facet_fieldname != ''){
            $tag_string = implode(',', $tags);
            $return = 'facet.field={!ex=' . $tag_string . '}' . $facet_fieldname;
        }
        return $return;
    }

    // Noch nicht optimiert
    /**
     *  Funktion zur Erstellung von Facetten
     *
     *  @param string $joFacetteType - Facettentyp - simple, 
     *  @param string $joFieldname - Feldname der facettiert werden soll
     *  @param array $joSelectedValues - Ausgewählte Facetten für die Extended Version des Facettenqueries oder Pivotfacetten
     *  @param string $joTagname - Tagname für den extended Query
     *  @param string $logical_concat - Verknüpfung der Suchanfragen - logisches UND oder logisches ODER
     *  @param array $joRangesAndParams - Spezielle Werte, die für die Facetten benötigt werden
     *  @return string  - nicht url-encoded Querystring
     */
    public function joMakeFacette($joFieldname = 'id', $joFacetteType = "simple", $joSelectedValues = [], $joTagname = "dt", $logical_concat = "OR", $joRangesAndParams = [])
    {
   
        $logical_concat = " " . $logical_concat . " ";  // Leerzeichen für die String-Zusammenführung
        // Tagname array in String konvertieren - @all -> funktioniert noch nicht richtig
        if (is_array($joTagname)) {
            $joTagname = implode(',', $joTagname);
        }
        $joQuery = '';
        $joFacetteField = "facet.field";
        // @all -> array Map verwenden und insgesamt besser umsetzen
        
        if (!empty($joSelectedValues) && $joFacetteType != "pivot" && $joFacetteType != "geo" && $joFacetteType != "pivot_ex" && "extendedPrefix" != $joFacetteType) {
            $temp = [];
            foreach ($joSelectedValues as $value) {
                $value = $this->cleanSearchWords($value);   //@all -> das müsten wir auch für die queries machen - dort haben wir die methode nicht drin sondern nur die eine zeile für das escapen der sonderzeichen
                $temp[] = '"'.$value.'"';
                // $temp[] = $value;
            }
            $joSelectedValues = $temp;
        }
        if (!empty($joFieldname)) {
            switch ($joFacetteType) {
                case "simple":      /*
                                    @all -> hier kann man noch optimieren und die Abrfagen nicht im controller via q= senden sondern via fq=
                                    if(!empty($joSelectedValues)){
                                        $joQueryConcat = " OR ";
                                        if(count($joSelectedValues) > 1){
                                            $joFacetteValues = '('.urlencode(implode($joQueryConcat, $joSelectedValues)).')';
                                        }else{
                                            $joFacetteValues = urlencode(implode($joQueryConcat, $joSelectedValues));
                                        }
                                        $joQuery = 'fq={!tag='.$joTagname.'}'.$joFieldname.':'.$joFacetteValues.'&facet=on';
                                    }else{
                                        $joQuery = $joFacetteField."=".$joFieldname;
                                    }
                                    */
                                    $joQuery = $joFacetteField . "=" . $joFieldname;
                                    break;
                case "extended":    if (!empty($joSelectedValues)) {
                                        if (count($joSelectedValues) > 1) {
                                            $joFacetteValues = '(' . urlencode(implode($logical_concat, $joSelectedValues)) . ')';
                                        } else {
                                            $joFacetteValues = urlencode(implode($logical_concat, $joSelectedValues));
                                        }
                                        // $joFacetteValues = '(' . urlencode($joSelectedValues[0] . ' NOT ' . $joSelectedValues[0]) .'/*)';
                                        $joQuery = 'fq={!tag=' . $joTagname . '}' . $joFieldname . ':' . $joFacetteValues;
                                        $joQuery .= '&facet=on';
                                        $joQuery .= '&' . $this->excludeFacetteFilter(array($joTagname), $joFieldname);
                                    } else {
                                        $joQuery = $joFacetteField . "=" . $joFieldname;
                                    }
                                    break;
                                    
                case "extendedPrefix":
                                    //@all -> kann verbessert werden - orientiert sich sehr stark an extended - berücksichtig aber nur EINE Auswahl!
                                 // print_r($joSelectedValues);
                                  if (!empty($joSelectedValues)) {
                                        $prefix = '';
                                        if (count($joSelectedValues) == 1) {
                                            $prefix = "&f." . $joFieldname . ".facet.prefix=" . urlencode($joSelectedValues[0]) . "/";
                                            $joFacetteValues = urlencode(implode($logical_concat, $joSelectedValues));
                                        }
                                        $joQuery = 'fq={!tag=' . $joTagname . '}' . $joFieldname . ':' . '"' . $joFacetteValues . '"' . '*&facet=on&facet.field={!ex=' . $joTagname . '}' . $joFieldname . $prefix;
                                    } else {
                                        $joQuery = 'facet.field={!ex=' . $joTagname . '}' . $joFieldname . '&facet=on';
                                    }
                                    break;
                /*
                case "geofilt":     if (!empty($joRangesAndParams)) {
                                        $joQuery = "sfield=" . $joFieldname . "&pt=" . $joRangesAndParams['mittelpunkt']['lat'] . "," . $joRangesAndParams['mittelpunkt']['lon'];
                                        $this->joReturnValue = 'geofilt';
                                        $joCallback = [$this, 'joArrayCallback'];
                                        $joRangesAndParams['range'] = array_map($joCallback, $joRangesAndParams['range']);  
                                        $joQuery .= implode("", $joRangesAndParams['range']);
                                    }
                                    break;
                    */
                case "geo":         if (!empty($joRangesAndParams)) {
                                        $joQuery = "sfield=".$joFieldname."&pt=".$joRangesAndParams['mittelpunkt']['lat'].",".$joRangesAndParams['mittelpunkt']['lon'];
                                        if (!empty($joRangesAndParams['range'])) {
                                            $i = 0;
                                            $joTempArray = [];
                                            foreach ($joRangesAndParams['range'] as $value) {
                                                $joStartSequenz = 0;
                                                if ($joRangesAndParams['range'][($i + 1)]) {
                                                    $joStartSequenz = $joRangesAndParams['range'][($i + 1)];
                                                }
                                                $joTempArray['range'][] = "&facet.query=" . urlencode("{!frange l='" . $joStartSequenz . "' u='" . ($value) . "' key='" . ($value * 1000) . "-" . ($joStartSequenz * 1000) . "'}geodist()");    //  Geofacette für Suche nach Lon/Lat Values - der Key wird von KM in Meter umgerechnet - Key muss ein string sein, da chrome das JSON array sonst sortiert wenn es numerisch ist
                                                $i++;
                                            }
                                        }
                                        if (!empty($joSelectedValues)) {
                                            foreach ($joSelectedValues as $value) {
                                                $joSelectedValuesArray = explode("-", $value);
                                                $joQuery .= '&fq=' . urlencode('{!frange l=' . ($joSelectedValuesArray[1] / 1000) . ' u=' . ($joSelectedValuesArray[0] / 1000) . '}geodist()');
                                            }
                                        }
                                        $joQuery .= "&facet=true" . implode("", $joTempArray['range']);
                                    }
                                    
                                    break;
                                    
                case "pivot":       $joQueryConcat = ',';
                                    $joQuery = 'facet.pivot=';
                                    if ($joTagname != 'dt') {
                                        $joQuery.= '{!ex=' . $joTagname . '}';
                                    }
                                    $joQuery.= implode($joQueryConcat, $joSelectedValues);
                                    break;
                case "pivot_ex":    if (!empty($joSelectedValues)) {
                                        $joQueryConcat = ',';
                                        $joQuery = 'facet=true';
                                        $ex_array = [];
                                        $ex_str = ''; 
                                        foreach ($joSelectedValues as $value) {
                                            if (!empty($value["values"])) {
                                                $joQuery.= '&fq={!tag=' . $value["tagname"] . '}' . $value["fieldname"] . ':"' . urlencode(implode($joQueryConcat, $value["values"])) . '"';
                                                if ($value["tagname"]) {
                                                    $ex_array[] = $value["tagname"];
                                                }
                                            }
                                        }
                                        if (!empty($ex_array)) {
                                            $ex_str = '{!ex=' . implode($joQueryConcat, $ex_array).'}';
                                        }
                                        $joQuery.= '&facet.pivot=';
                                        $joQuery.= $ex_str;
                                        $joQuery.= $joTagname;
                                    }
                                    // echo $joQuery;
                                    break;
                                    
                case "range":       
                                    /* @all -> das muss noch ordentlich dynamisch gemacht werden!!!! */ 

                                    $config_range = array(
                                        'start' => 0,
                                        'end' => 5000000,
                                        'gap' => 500000
                                    );
                                    $query = array();
                                    $query[] = 'facet.range.start=' . $config_range['start'];
                                    $query[] = 'facet.range.end=' . $config_range['end'];
                                    $query[] = 'facet.range.gap=' . $config_range['gap'];
                                    $query[] = 'facet.range.other=all';
                                    $query[] = 'facet.range=' . $joFieldname;
                                    if (!empty($joSelectedValues)) {
                                        $range_request = array();
                                        foreach($joSelectedValues as $ranges){
                                            $ranges = str_replace('"', '', $ranges);
                                            $fromto = explode(' - ', $ranges);
                                            if(isset($fromto[1])){
                                                if($fromto[1] == '*') {
                                                    $fromto[1] = '*';
                                                }else{
                                                    $fromto[1] = intval($fromto[1] - 1);
                                                }
                                                $range_request[] = $joFieldname . ':' . '[' . intval($fromto[0]) . ' TO ' . $fromto[1] . ']';
                                            }else{
                                                $range_request[] = "NOT ". $joFieldname . ':*';
                                            }   
                                        }
                                        if (count($range_request) > 1) {
                                            $joFacetteValues = '(' . urlencode(implode($logical_concat, $range_request)) . ')';
                                        } else {
                                            $joFacetteValues = urlencode(implode($logical_concat, $range_request));
                                        }
                                        $query[] = '&fq={!tag=' . $joTagname . '}' . $joFacetteValues;
                                        $query[] = 'facet=true';
                                        $query[] = 'facet.range={!ex=' . $joTagname . '}' . $joFieldname;
                                    } 
                                    $joQuery = implode('&', $query);
                                    break;
                
            }
        }
        return $joQuery;
    }

    public function cleanSearchWords($phrase = NULL)
    {
        if ($phrase != NULL) {
            $search = ['"'];
            $replace = ['\"'];
            $phrase = str_replace($search, $replace, $phrase);  //Quotes schützen
        }
        return $phrase;
    }

    public function makeExtbaseActionArray($action = null)
    {
        $linkActionArray = [];
        if (null != $action) {
            $linkActionArray['pluginname'] = $this->extbase_config['plugin_name'];
            $linkActionArray['actionname'] = $action;
        }
        return $linkActionArray;
    }

    public function getMailConfiguration()
    {
        $mailconfig = [
            'emailSubject' => 'Anmerkungen zu einem Objekt des Portalframeworks JUSTORANGE - MUSEO',
            'emailHostName' => 'JUSTORANGE - Agentur für Informationsästhetik',
            'emailHostAddress' => 'info@justorange.de',
            'emailSendToAddress' => 'info@justorange.de',
            // 'objectManager' => $this->objectManager,
            'extensionName' => $this->request->getControllerExtensionName()
            // 'configurationManager' => $this->configurationManager
        ];

        // Wenn in der globalen Projektbeschreibung Daten hintelegt sind, werden die benutzt
        if (isset($this->config['init']['searchconfig']['objectcomment']['active'])) {
            if (isset($this->config['init']['searchconfig']['objectcomment']['email'])) {
                $mailconfig['emailHostAddress'] = $this->config['init']['searchconfig']['objectcomment']['email'];
            }
            if (isset($this->config['init']['searchconfig']['objectcomment']['emailSendTo'])) {
                $mailconfig['emailSendToAddress'] = $this->config['init']['searchconfig']['objectcomment']['emailSendTo'];
            }
            if (isset($this->config['init']['searchconfig']['objectcomment']['host'])) {
                $mailconfig['emailHostName'] = $this->config['init']['searchconfig']['objectcomment']['host'];
            }
            if (isset($this->config['init']['searchconfig']['objectcomment']['subject'])) {
                $mailconfig['emailSubject'] = $this->config['init']['searchconfig']['objectcomment']['subject'];
            }
        }
        // Wenn in den Plugins Daten hinterlegt wurden, werden die benutzt und konkretisieren die Projektdaten
        if (isset($this->settings)) {
            if (isset($this->settings['email'])) {
                $mailconfig['emailHostAddress'] = $this->settings['email'];
            }
            if (isset($this->settings['emailSendTo'])) {
                $mailconfig['emailSendToAddress'] = $this->settings['emailSendTo'];
            }
            if (isset($this->settings['emailhost'])) {
                $mailconfig['emailHostName'] = $this->settings['emailhost'];
            }
            if (isset($this->settings['emailsubject'])) {
                $mailconfig['emailSubject'] = $this->settings['emailsubject'];
            }
        }
        return $mailconfig;
    }

    
    /**
     * @param string $name
     * @return array
     */
    public function getFileObject($name)
    {
        $cObj = $this->configurationManager->getContentObject();
        $language = $this->request->getAttribute('language');
        $language_uid = $language->getLanguageId();
        $uid = (int) $cObj->data['uid'];
        if($language_uid > 0){
             $uid = (int) $cObj->data['_LOCALIZED_UID'];
        }
        $castName = $name;
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $fileObjects = $fileRepository->findByRelation('tt_content', $castName, $uid);

        return $fileObjects;
    }


    public function findRelations($id = null)
    {
        $return = [];
        if ($id != null) {
            $child_query[] = "(childrenReference:" . $id . ")";
            if ($this->settings['hidefromsearch']) {
                $joItems = ['0'];
                $child_query[] = $this->solrQueryhandler->joMakeQuerypart($joItems, " AND ", "hiddenFromSearch");
            }
            if (!empty($this->settings["portal"])) {
                $query_items = explode("," ,  $this->settings["portal"]);
                $child_query[] = $this->solrQueryhandler->joMakeQuerypart($query_items, " OR ", "classPortal");
            }
            $this->query_params['q'] = $child_query;
            $this->query_params['start'] = $this->jopaginatecenter;
            $this->query_params['f'] = [];
            $this->query_params['f'][] = $this->joMakeFacette('objectType', 'simple'); 
            $child_elements_facette = json_decode($this->solrRepository->contactSolr($this->query_params));
            if (!empty($child_elements_facette->facet_counts->facet_fields->objectType)) {
                $i = 0;
                foreach ($child_elements_facette->facet_counts->facet_fields->objectType as $c) {
                    if ($i % 2 == 0) {
                        $return[] = [
                            'name' => $c,
                            'number' => $child_elements_facette->facet_counts->facet_fields->objectType[($i + 1)],
                            'id' => $id
                        ];
                    }
                    $i++;
                }
                $return = $this->solrIndexUtil->reorderFacetteArray($child_elements_facette->facet_counts->facet_fields->objectType);
            }
            /*
            if (GeneralUtility::_GP('test') == 1) {
                echo "<pre>";
                print_r($return);
                echo "</pre>";
            }
            */
        }
        return $return;
    }

    public function initExcelExportFields() : array
    {
        // Basiskonfiguration - Sprache muss noch lokalisiert werden
        $xlsconfig = array(
            'A' => array(
                'name' => 'Zitierlink',
                'fieldname' => 'canonical',
                'fieldtype' => 'link'
            ),
            'B' => array(
                'name' => 'Objektkategorie',
                'fieldname' => 'objectType'
            ),
            'C' => array(
                'name' => 'Titel',
                'width' => 200,
                'textwrap' => true,
                'fieldname' => 'title'
            ),
            'D' => array(
                'name' => 'Personen/Institutionen [jeweilige Rollen]',
                'fieldname' => 'entity',
                'textwrap' => true,
                'fieldtype' => 'rolebasedentity'
            ),
            'E' => array(
                'name' => 'Ort/Herkunft',
                'fieldname' => 'locationQualified',
                'textwrap' => true,
                'fieldtype' => 'rolebasedentity'
            ),
            'F' => array(
                'name' => 'Entstehungszeit',
                'fieldname' => 'showtime'
            ),
            'G' => array(
                'name' => 'Umfang, Illustration, Format',
                'fieldname' => 'umfang'
            ),
            'H' => array(
                'name' => 'Abmessungen',
                'fieldname' => 'objectdimension'
            ),
            'I' => array(
                'name' => 'Maßstab',
                'fieldname' => 'scale'
            ),
            'J' => array(
                'name' => 'Koordinaten',
                'fieldname' => 'locationPolygones'
            ),
            'K' => array(
                'name' => 'Sprache',
                'fieldname' => 'language'
            ),
            'L' => array(
                'name' => 'Material',
                'fieldname' => 'material'
            ),
            'M' => array(
                'name' => 'Technik',
                'fieldname' => 'technik'
            ),
            'N' => array(
                'name' => 'Enthalten in',
                'fieldname' => 'parent',
                'delimiter' => '$',
                'relevantpart' => 1 
            ),
            'O' => array(
                'name' => 'Beschreibung',
                'fieldname' => 'note',
                'width' => 200,
                'textwrap' => true
            ),
            'P' => array(
                'name' => 'Schlagworte',
                'fieldname' => 'classificationtags'
            ),
            'Q' => array(
                'name' => 'Provenienz',
                'fieldname' => 'provenienz'
            ),
            'R' => array(
                'name' => 'Bereitstellende Institution',
                'width' => 200,
                'fieldname' => 'tenant',
                'delimiter' => '$',
                'relevantpart' => 0 
            ),
            'S' => array(
                'name' => 'Standort',
                'fieldname' => 'located'
            ),
            'T' => array(
                'name' => 'Signatur/Inventarnummer',
                'fieldname' => 'inventarnummer,shelfmark'
            ),
            'U' => array(
                'name' => 'Weitere Objektinformationen',
                'fieldname' => 'oldinventarnummer,history,textkurz,contents',
                'width' => 300,
                'textwrap' => true
            ),
            'V' => array(
                'name' => 'Bibliographie',
                'fieldname' => 'literaturUnstructured',
                'width' => 200,
                'textwrap' => true
            ),
            'W' => array(
                'name' => 'geliefert über',
                'fieldname' => 'contextorig',
                'fieldtype' => 'link'
            ),
            'X' => array(
                'name' => 'Exportlinks',
                'fieldtype' => 'download',
                'fieldname' => 'download',
                'textwrap' => true
            )
        );
        // Wenn im TypoScript nähere Konfigurationen enthalten sind, werden diese benuztzt -> todo!
        return $xlsconfig;
    }

    // Die Methode kann verbessert werden. Momentan ist sie sehr starr
    public function writeExcel($items = array()){

        $xlsconfig = $this->initExcelExportFields();

        if(!empty($items)){
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getDefaultStyle()
             ->getFont()
             ->setName('Arial')
             ->setSize(15);

            $spreadsheet->getProperties()
                ->setTitle('Gotha Digital')
                ->setSubject('Meine Merkliste')
                ->setDescription('Export')
                ->setCreator('JO Researchportal')
                ->setLastModifiedBy('JO Researchportal');
            // Excel Headline definieren
            if(!empty($xlsconfig)){
                foreach ($xlsconfig as $key => $value) {

                    // Name setzen
                    $cellname = $value['name'] . ":";
                    if( $this->translate($this->extbase_config['lang_path'] . ':jo_bkr_base.' . $value['fieldname'])){
                       $cellname =  $this->translate($this->extbase_config['lang_path'] . ':jo_bkr_base.' . $value['fieldname']);
                    }
                    $spreadsheet->setActiveSheetIndex(0)
                     ->setCellValue($key . '1', $cellname);

                    // Spezifische oder Standardweite der Zelle setzen
                    if(isset($value['width'])){
                        $spreadsheet->getActiveSheet()
                         ->getColumnDimension($key)
                         ->setWidth(intval($value['width']), 'pt');
                    } else {
                        $spreadsheet->getActiveSheet()
                         ->getColumnDimension($key)
                          ->setAutoSize(true);
                    }

                    // Zellausrichtung definieren
                    $spreadsheet->getActiveSheet()
                     ->getStyle($key)
                     ->getAlignment()
                     ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                    // Textwrap, wenn in die Zellen der betreffenden Spalte längere Texte geschrieben werden sollen, werden die umgebrochen
                    if(isset($value['textwrap'])){
                        $spreadsheet->getActiveSheet()
                         ->getStyle($key)
                         ->getAlignment()
                         ->setWrapText(true);
                    }

                    // Hintergrundfarbe definieren für die Headline
                    $spreadsheet->getActiveSheet()
                    ->getStyle($key . '1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('cecece');

                    // Schriftschnitt definieren für die Headline
                    $spreadsheet->getActiveSheet()
                     ->getStyle( $key . '1')
                     ->getFont()
                     ->setBold( true );
                }
            }

            $count = count($items);
            for ($i = 0; $i < $count; $i++) {

                $excelline = $i + 2;
                $image = 'Kein Digitalisat vorhanden';
                if (!empty($items[$i]->images)) {
                    // Achtung - es wird nur das erste Digitalisat berücksichtigt
                    $images = explode('$', $items[$i]->images[0]);
                    if (isset($images[0])) {
                        $image = 'Digitalisat herunterladen';
                        $spreadsheet->getActiveSheet()->getCell('F' . $excelline)->getHyperlink()->setUrl($images[0]);
                    }
                }
                foreach ($xlsconfig as $key => $value) {
                    if(isset($value['fieldname'])){
                        $field_value_processed = '';
                        $field_name_array = explode(',', $value['fieldname']);
                        foreach ($field_name_array as $fnvalue) {
                            if($items[$i]->{$fnvalue}){
                                $fieldvalue_found = $items[$i]->{$fnvalue};
                                $fieldvalue_raw = array();
                                if(!is_array($fieldvalue_found)){
                                    $fieldvalue_raw[] = $fieldvalue_found;
                                } else {
                                    $fieldvalue_raw = $fieldvalue_found;
                                }
                                
                                switch ($value['fieldtype']) {
                                    case 'rolebasedentity':
                                       
                                        foreach ($fieldvalue_raw as $skey => $svalue) {
                                            $label = $skey . ':';
                                            if( $this->translate($this->extbase_config['lang_path'] . ':jo_bkr_base.' . $skey)){
                                               $label =  $this->translate($this->extbase_config['lang_path'] . ':jo_bkr_base.' . $skey);
                                            }
                                            $field_value_processed .= $label . PHP_EOL;
                                            if(is_array($svalue)){
                                                foreach ($svalue as $ssvalue) {
                                                    $field_value_processed .=  $ssvalue['name'] . PHP_EOL;
                                                }
                                            }
                                        }
                                        break;
                                    case 'download':
                                        $field_value_processed = '';
                                        foreach ($fieldvalue_raw as $svalue) {
                                            if(isset($svalue['zip']['file'])){
                                                $field_value_processed .= $svalue['zip']['file'] . PHP_EOL;
                                            }
                                        }
                                        break;
                                    default:
                                        foreach ($fieldvalue_raw as $skey => $svalue) {
                                           if(isset($value['delimiter'])){
                                                $relevantpart = 0;
                                                if(isset($value['relevantpart'])){
                                                    $relevantpart = $value['relevantpart'];
                                                }
                                                $field_value_array = explode($value['delimiter'], $svalue);
                                                if(isset($field_value_array[$relevantpart])){
                                                    $fieldvalue_raw[$skey] = $field_value_array[$relevantpart];
                                                }
                                            }
                                        }
                                        $field_value_processed .= implode(', ', $fieldvalue_raw) . PHP_EOL . PHP_EOL;
                                }
                            }
                            $spreadsheet->getActiveSheet()->setCellValue($key . $excelline, $field_value_processed);
                            if($value['fieldtype'] == 'link'){
                                $spreadsheet->getActiveSheet()->getCell($key . $excelline)->getHyperlink()->setUrl($field_value_processed);
                            }
                        }
                    }
                }
            }

            $date = date('d-m-y-'.substr((string)microtime(), 1, 8));
            $date = str_replace(".", "", $date);
            $filename = "export_" . $date . ".xlsx";
            $filePath = 'fileadmin/' . $filename;

            try {
                $writer = new Xlsx($spreadsheet);
                $writer->save($filePath);
                $content = file_get_contents($filePath);
            } catch(Exception $e) {
                exit($e->getMessage());
            }
            ob_end_clean();
            header("Content-Disposition: attachment; filename=" . $filename);
            unlink($filePath);
            exit($content);                
        }       
    }

    /**
     * Wrapper call to LocalizationUtility
     *
     * @param string $id Translation Key
     * @param string $extensionName UpperCamelCased extension key (for example BlogExample)
     * @param array $arguments Arguments to be replaced in the resulting string
     * @param string $languageKey Language key to use for this translation
     * @param string[] $alternativeLanguageKeys Alternative language keys if no translation does exist
     *
     * @return string|null
     */
    public function translate($id, $extensionName = null, $arguments = [], $languageKey = null, $alternativeLanguageKeys = [])
    {
        if ($id === null) {
            return null;
        }
        
        $value = LocalizationUtility::translate($id, $extensionName, $arguments, $languageKey, $alternativeLanguageKeys);
        
        if ($value === null) {
            if (str_starts_with($id, 'LLL:')) {
                $keyParts = explode(':', $id);
                unset($keyParts[0]);
                $id = array_pop($keyParts);
                // $languageFilePath = implode(':', $keyParts);
            }

            $languageFilePath = 'LLL:EXT:jo_museo/Resources/Private/Language/locallang.xlf:';
            
            $value = LocalizationUtility::translate($languageFilePath . $id, $extensionName, $arguments, $languageKey, $alternativeLanguageKeys);
        }

        return $value;
    }
}
