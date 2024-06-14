<?php
namespace JO\JoMuseo\Controller;

use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Page\AssetCollector;

/**
 * ExhibitionController
 */
class ExhibitionController extends ActionController
{

    public $joSolrCore = ""; // Link zum Solrcore

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
     *    Objekte der Joblist und des Solrservers
     *
     *  @var  \JO\JoMuseo\Domain\Repository\SolrRepository
     */
    protected $solrRepository;

    /**
     *  @var \JO\JoMuseo\Domain\Repository\ExhibitionRepository
     */
    protected $ExhibitionRepository;

    /**
     *  @var \JO\JoMuseo\Domain\Repository\ExhibitRepository
     */
    protected $ExhibitRepository;

    /**
     *  @var \JO\JoMuseo\Domain\Repository\SectionRepository
     */
    protected $SectionRepository;

    /**
     *  @var \JO\JoMuseo\Domain\Repository\EntityRepository
     */
    protected $EntityRepository;

    // Konfiguration
    protected $config = array();


    /**
     * @param \JO\JoMuseo\Domain\Repository\SolrRepository $solrRepository
     */
    public function injectSolrRepository(\JO\JoMuseo\Domain\Repository\SolrRepository $solrRepository)
    {
        $this->solrRepository = $solrRepository;
    }
    /**
     * @param \JO\JoMuseo\Domain\Repository\ExhibitionRepository $ExhibitionRepository
     */
    public function injectExhibitionRepository(\JO\JoMuseo\Domain\Repository\ExhibitionRepository $ExhibitionRepository)
    {
        $this->ExhibitionRepository = $ExhibitionRepository;
    }

    /**
     * @param \JO\JoMuseo\Domain\Repository\SectionRepository $sectionRepository
     */
    public function injectSectionRepository(\JO\JoMuseo\Domain\Repository\SectionRepository $SectionRepository)
    {
        $this->SectionRepository = $SectionRepository;
    }

    /**
     * @param \JO\JoMuseo\Domain\Repository\ExhibitRepository $ExhibitRepository
     */
    public function injectExhibitRepository(\JO\JoMuseo\Domain\Repository\ExhibitRepository $ExhibitRepository)
    {
        $this->ExhibitRepository = $ExhibitRepository;
    }
    /*
    * @param \JO\JoMuseo\Utility\Fuzzysearchutils\Joqueryhandler $joqueryhandler
     */
    public function injectJoqueryhandler(\JO\JoMuseo\Utility\Fuzzysearchutils\Joqueryhandler $joqueryhandler)
    {
        $this->solrQueryhandler = $joqueryhandler;
    }

    /**
     * @param \JO\JoMuseo\Utility\Fuzzysearchutils\Josearchsession $josearchsession
     */
    public function injectJosearchsession(\JO\JoMuseo\Utility\Fuzzysearchutils\Josearchsession $josearchsession)
    {
        $this->joSessionUtil = $josearchsession;
    }

    /**
     * @param \JO\JoMuseo\Domain\Repository\EntityRepository $EntityRepository
     */
    public function injectEntityRepository(\JO\JoMuseo\Domain\Repository\EntityRepository $EntityRepository)
    {
        $this->EntityRepository = $EntityRepository;
    }

    public function initializeView($view) {
        $this->config = $this->settings;
    }

	public function initializeAction() {

	}
	
    /**
     * action showexhibition
     *
     * Lädt eine ausgewählte Ausstellung
     * @return void
     */
    public function showexhibitionAction()
    {    
        if ($this->request->hasArgument('ex')) {
            $assign = array();
            $uid = filter_var($this->request->getArgument('ex'), FILTER_SANITIZE_STRING);
            $items = $this->ExhibitionRepository->findByUid($uid);
            if($items != null){
                $assign['items'] = $items; 
                $pid_data = $items->getConfiguration();
                $this->config = array_merge($this->config, $this->getConfigXML($pid_data));
                $assign['config'] = $this->config;
                $this->addCustomCss($this->config);
                $this->view->assignMultiple($assign); 
            }
        }
    }

    /**
     * action showsection
     *
     * Lädt die Übersichtsseite der Sektionen/Kapitel
     *
     * @return void
     */
    public function showsectionAction()
    {
        $assign = array();
        $assign['pagetype'] = 0;
        // Checken ob irgendwer im Backend angemeldet ist
        if(isset($GLOBALS['BE_USER']) && !empty($GLOBALS['BE_USER']) && ($GLOBALS['BE_USER']->user['admin'] === 1 || $GLOBALS['BE_USER']->user['uid'] === 7)) $assign['beuser'] = true;
        // Wenn ein Aufruf einer URl mit dem Parameter "nc" erfolgt, wird das Objekt ohne den Ausstellungskontext geladen
        if (GeneralUtility::_GP("nc")) {
            $current = intval(GeneralUtility::_GP("nc")); 
            $obj = $this->SectionRepository->findByUid($current);
            $assign['sektionen'][$current]['orig'] = $obj;
            $assign['nc'] = 1;
            $assign['pagetype'] = 124;
        }
        if($this->request->hasArgument('parent')){
            $uidparent = filter_var($this->request->getArgument('parent'), FILTER_SANITIZE_STRING);
            // Übergeordnete Ausstellung laden und Konfiguration holen
            $parent = $this->ExhibitionRepository->findByUid($uidparent);
            if($parent != null){
                // Konfigurationen der Ausstellung holen und in die zentrale konfiguration übertragen
                if($parent->getConfiguration() && !empty($parent->getConfiguration())){
                    $pid_data = $parent->getConfiguration();
                    $this->config = array_merge($this->config, $this->getConfigXML($pid_data));
                }
                $this->addCustomCss($this->config);
                $assign['parent'] = $parent;
            }
        } 
        if($this->request->hasArgument('pid')){
            $pid = filter_var($this->request->getArgument('pid'), FILTER_SANITIZE_STRING);
            $result = $this->getObjectsFromStorage($this->SectionRepository, $pid);
            if(is_object($result) && $result->count() > 0){
                $sektionen = $this->getArrayNeighbours($result);
                $assign['sektionen'] = $sektionen;
            }
        }
        $assign['config'] = $this->config;
        $this->view->assignMultiple($assign); 
    }

    

    /**
     * action showobject
     *
     * Lädt die Detailansicht der Exponate
     *
     * @return void
     */
    public function showobjectAction()
    {
        $geodata = array();
        $assign = array();

        // Checken ob irgendwer im Backend angemeldet ist
        if(isset($GLOBALS['BE_USER']) && !empty($GLOBALS['BE_USER']) && ($GLOBALS['BE_USER']->user['admin'] === 1 || $GLOBALS['BE_USER']->user['uid'] === 7)) $assign['beuser'] = true;

        if (GeneralUtility::_GP('soloview')) {
            $assign['soloview'] = true;
        }

        // Übergeordnete Ausstellung laden und Konfiguration holen
        if ($this->request->hasArgument('parent')) {
            $uidparent = filter_var($this->request->getArgument('parent'), FILTER_SANITIZE_STRING);
            $parent = $this->ExhibitionRepository->findByUid($uidparent);
            if ($parent != null) {
                // Konfigurationen der Ausstellung holen und in die zentrale konfiguration übertragen
                if ($parent->getConfiguration() && !empty($parent->getConfiguration())) {
                    $pid_data = $parent->getConfiguration();
                    $this->config = array_merge($this->config, $this->getConfigXML($pid_data));
                }
                $assign['parent'] = $parent;
            }
        }
        if (GeneralUtility::_GP("nc")){
            $assign['nc'] = 1;
        }
        // Aufruf der Detailansicht mit Ausstellungskontext
        if ($this->request->hasArgument('uid') && $this->request->hasArgument('pid')) {
            // Wenn nc und uid und pid gesetzt sind, kommt der Nutzer aus der Sektionseinzelansicht
            if (GeneralUtility::_GP("nc")){
                $assign['showobjectsfromsinglesection'] = 1;
            }
            // Sektions-ID, die geladen werden soll
            $uid = filter_var($this->request->getArgument('uid'), FILTER_SANITIZE_STRING);
            // Ordner-ID, in der die Ausstellungsdaten zu finden sind
            $pid = filter_var($this->request->getArgument('pid'), FILTER_SANITIZE_STRING);
            // Alle zugehörigen Sektionen ermitteln und ausgeben
            $result = $this->getObjectsFromStorage($this->SectionRepository, $pid);
            if (is_object($result) && $result->count() > 0) {
                $sektionen = $this->getArrayNeighbours($result);
                if (!empty($sektionen) && isset($sektionen[$uid])) {
                    $item = $sektionen[$uid];
                    if (!empty($item['orig'])) {
                        $exhibits = $item['orig']->getExhibit();
                        if (is_object($exhibits) && $exhibits->count() > 0) {
                            foreach ($exhibits as $value) {
                                // Wenn eine Geo-Datenquelle hinterlegt ist, wird diese geladen
                                $current = $value->getUid();
                                $geodata[$current] = $this->acquireGeoData($value);
                                // Spezifische Konfiguration der Exponate
                                if ($value->getConfiguration() && !empty($value->getConfiguration())) {
                                    $uid_exhibit_configdata = $value->getConfiguration();
                                    $this->config['items'][$value->getUid()] = $this->getConfigXML($uid_exhibit_configdata);
                                }
                            }
                        }
                        $assign['sektion'] = $item;
                    }       
                }
            }
        }
        // Wenn ein Aufruf einer URl mit dem Parameter "nc" erfolgt, wird das Objekt ohne den Ausstellungskontext geladen
        if (GeneralUtility::_GP("nc") && !($this->request->hasArgument('uid'))) {
            // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(GeneralUtility::_GP("nc"));
            $current = intval(GeneralUtility::_GP("nc")); 
            $obj = $this->ExhibitRepository->findByUid($current);
            if($obj != null){
                $geodata[$current] = $this->acquireGeoData($obj);
                $assign['obj'] = $obj;
            }
        }
        // Wenn die Inhalte ohne Kontext geladen wurden und dennoch Sektionsvorgänger und Nachfolger enthalten sind, werden diese gekickt
        if (GeneralUtility::_GP("nc") && isset($assign['sektion']['orig'])){
            $current_section = $assign['sektion']['orig'];
            $assign['sektion'] = array();
            $assign['sektion']['orig'] =  $current_section;    
        }
        if (!empty($geodata)) {
            $geodata = array_filter($geodata);
            if (!empty($geodata)) {
                $jsvar .= 'var geojson = [];';
                foreach ($geodata as $k => $g) {
                    $jsvar .= 'geojson[' . $k . '] = [' . json_encode($g) . '];';
                }
                $asset = GeneralUtility::makeInstance(AssetCollector::class);
                $asset->addInlineJavaScript('map_geodata', $jsvar);
            }   
        }
        
        $this->addCustomCss($this->config);
        $assign['config'] = $this->config;
        $this->view->assignMultiple($assign);
    }

          
        
       
    /*
         //   \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($assign);
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($assign);
        $AssetCollector = GeneralUtility::makeInstance(AssetCollector::class);
        $AssetCollector->addStyleSheet('jo.ce20.css', 'EXT:jo_content/Resources/Public/Css/jo.ce20.css');
          //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($colors);
    */

     public function addCustomCss($colors = array()){

        $customCss = array();
        // Ausstellungsübersicht
        $maincolor = null;
        $maincolorfont = null;
        $maincolorlinkfont = null;
        $maincolorborder = null;
        $fontcolor = null;
        // Sektionsansicht
        $secfontcolor = null;
        $secfontcolortext = null;
        $secbggradient_1 = null;
        $secbggradient_2 = null;
        // Objektansicht
        $objfontcolor = null;
        $fontcolor_detail_1 = null;
        $bggradient_1 = null;
        $bggradient_2 = null;
       
        if(!empty($colors)){
            if($colors['maincolor']) $maincolor = $colors['maincolor'];
            if($colors['maincolorfont']) $maincolorfont = $colors['maincolorfont'];
            if($colors['maincolorlinkfont']) $maincolorlinkfont = $colors['maincolorlinkfont'];
            if($colors['maincolorborder']) $maincolorborder = $colors['maincolorborder'];
            if($colors['fontcolor']) $fontcolor = $colors['fontcolor'];

            if($colors['secbggradient_1']) $secbggradient_1 = $colors['secbggradient_1'];
            if($colors['secbggradient_2']) $secbggradient_2 = $colors['secbggradient_2'];
            if($colors['secfontcolor']) $secfontcolor = $colors['secfontcolor'];
            if($colors['secfontcolortext']) $secfontcolortext = $colors['secfontcolortext'];
            

            if($colors['bggradient_1']) $bggradient_1 = $colors['bggradient_1'];
            if($colors['bggradient_2']) $bggradient_2 = $colors['bggradient_2'];
            if($colors['objfontcolor']) $objfontcolor = $colors['objfontcolor'];
            if($colors['fontcolor_detail_1']) $fontcolor_detail_1 = $colors['fontcolor_detail_1'];
        }
        if($maincolor){
            $customCss[] = '.joExhibition_content .sectionInfo-c-btn, html .progress .progress-bar, html .JoMuseo_content .sectionInfo-c-btn, html .sectionInfo-c-btn-container::after,html .sectionInfo-c-btn-container::before, html body .maincolor{background-color:' . $maincolor . ';}';
            $customCss[] = 'html .load_section::after, html .load_section::before {border-color:' . $maincolor . ';}';
        }
        if($maincolorfont) {
            $customCss[] = 'html .right-side .link_external,html .left-side a,html .right-side .more-links a,html .content_wrap a.load_section:hover,html .tosectiondesc, html .exhibition .item h3, html .sektion-title, html .exhibition.showindex h3, html body .maincolorfont, html body .view2-overlay-text a, body .aboutproject a, body .zitiervorschlag a, body .showexhibition .location a {color:' . $maincolorfont . '!important;}';
        }
        if ($maincolorlinkfont) {
            $customCss[] = 'html .right-side .link_external,html .left-side a,html .right-side .more-links a {color:' . $maincolorlinkfont . ';}';
        } elseif ($maincolorfont) {
            $customCss[] = 'html .right-side .link_external,html .left-side a,html .right-side .more-links a {color:' . $maincolorfont . ';}';
        }
        if($maincolorborder){
            $customCss[] = 'html .exhibition.showall .more_link::after, html .exhibition.showall .more_link::before, html .nextsection .text-wrap,html .summaryContent, html .joObject.desc-con > .content-wrapper > div, html .exhibition .summaryContent, html .sektion-title, html .joSitemap, html body .maincolorborder{border-color:' . $maincolorborder. ';}';
        }
        if($fontcolor){
            $customCss[] = 'html body .fontcolor{color:' . $fontcolor. ';}';
        }
        // Sektion
        if($secfontcolor){
            $customCss[] = 'html body .sec .sectionTitle-container h3 {color:' . $secfontcolor . ';}';
        }
        if($secfontcolortext){
            $customCss[] = 'body .sec .info_wrap, body .sec .startdate, body .sec .sectionSubTitle, body .sec .sectionInfo-container, body .sec .more a {color:' . $secfontcolortext . ';}';
            $customCss[] = 'body .sec .more a {border: ' . $secfontcolortext . ' 1px solid;}';
        }
        if($secbggradient_1 && $secbggradient_2 ){
            $customCss[] = 'body .joExhibition_content .section {background-color:' . $secbggradient_1 . '; background:linear-gradient(180deg, ' . $secbggradient_1 . ' 0%, ' . $secbggradient_2 . ' 100%);}';
        }
        if($secbggradient_1 && !$secbggradient_2 ){
            $customCss[] = 'body .joExhibition_content .section {background-color:' . $secbggradient_1 . ';}';
        }
        if(!$secbggradient_1 && $secbggradient_2 ){
            $customCss[] = 'body .joExhibition_content .section {background-color:' . $secbggradient_2 . ';}';
        }
        // Objekte generell auf Basis der Eingaben auf Ebene der Ausstellungen
        if($objfontcolor){
            $customCss[] = 'body .joObject .text-title h3, body .joObject .text-title h2 {color:' . $objfontcolor . ';}';
        }
        if($fontcolor_detail_1){
            $customCss[] = 'html body .joObject .text_wrap,html body .joObject .section_img_info-cc,html body .joObject .text-wrap, html body .joObject .view_8_alt .text-title > * {color:' . $fontcolor_detail_1 . ';}';
            $customCss[] = 'html body .joObject .learn_more .btn, html body .joObject button.i-icon {color:' . $fontcolor_detail_1 . '; border-color:' . $fontcolor_detail_1 . ';}';
            $customCss[] = 'html body .joObject .text-title h3:after {background-color:' . $fontcolor_detail_1 . ';}';
            $customCss[] = 'html body .joObject svg {fill:' . $fontcolor_detail_1 . ';}';
        }
        if($bggradient_1 && $bggradient_2 ){
            $customCss[] = 'body .joObject {background-color: ' . $bggradient_1. '; background-image: radial-gradient(circle 100vh at 30% 100%, ' . $bggradient_2. ', ' . $bggradient_1. ');} body .joObject.even {
                background-image: radial-gradient(circle 100vh at 70% 100%, ' . $bggradient_2. ', ' . $bggradient_1. ');}';
        }
        if($bggradient_1 && !$bggradient_2 ){
            $customCss[] = 'body .joObject {background-color: ' . $bggradient_1. ';}';
        }
        if(!$bggradient_1 && $bggradient_2){
            $customCss[] = 'body .joObject {background-color: ' . $bggradient_2. ';}';
        }
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($customCss);
        // Objekte auf Basis der Konfigurationen der Objekte
        
        if(isset($colors['items'])){
            foreach ($colors['items'] as $key => $value) {
                if(isset($value['bgcolor']) && $value['bgcolor'] != ''){
                    $customCss[] = 'body .joObject#obj' . $key . '{background-image: unset; background-color:' . $value['bgcolor'] . ';}';
                }
                if(isset($value['fontcolor']) && $value['fontcolor'] != ''){
                    $customCss[] = 'body .joObject#obj' . $key . ' .text-wrap {color:' . $value['fontcolor'] . ';}';
                }
            }
        }
        
        if(!empty($customCss)){
            $asset = GeneralUtility::makeInstance(AssetCollector::class);
            $asset->addInlineStyleSheet('customCss', implode('', $customCss));
        }
    }
     /**
     * @param string $fileName
     * @param string $publicPath
     * @return void
     */
    public function addHeaderFile($fileName, $publicPath = 'Resources/Public/', $showing = false)
    {
       // if (!$showing) return '';
        // all -> der Pfad stimmt nicht . die Dateien werden nicht gefunden
        /*
         *    Ausfruf erfolgt hierbei im controlleraction
         *    $this->addHeaderFile('jo.ce001.css');
         *    $this->addHeaderFile('jo.ce001.js');
         */
        $filepath = '';

        // Pagerenderer instanziieren
        $pageRender = GeneralUtility::makeInstance(PageRenderer::class);
        // extkey und extpath ermitteln
        $extkey = GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionKey());

        $extPath = GeneralUtility::getFileAbsFileName('EXT:' . $extkey . '/' . $publicPath);
        $extPath = PathUtility::getAbsoluteWebPath($extPath);

        // Dateipfad je nach Endung setzen
        $pi = pathinfo($fileName);
        switch (true) {
            case (strtolower($pi['extension']) == 'css'):
                $filepath = ('Resources/Public/' != $publicPath) ?  $extPath . $fileName : $extPath . 'Css/' . $fileName;
                //if (is_file($filepath)) {
                $pageRender->addCssFile($filepath, 'stylesheet', 'all', '', false, false, '', true);
                //}

                break;
            case (strtolower($pi['extension']) == 'js'):

                $filepath = ('Resources/Public/' != $publicPath) ? $extPath . $fileName : $extPath . 'JavaScript/' . $fileName;
                //if (is_file($filepath)) {
                $pageRender->addJsFooterFile($filepath, '', false, false, '', true);
                //}

                break;
        }
    }


    public function buildGeojsonFromPid($pid)
    {
        if (!$pid) return null;
        $geoJson = array();
        $geoObjects = $this->EntityRepository->findAllByPid($pid);
        if($geoObjects) {
            $objects = array();
            foreach ($geoObjects as $g) {
                $js = json_decode($g->getGeoplacegeojson());
                if(!empty($js)){
                    // Die Pinvergabe ist sehr starr - das müsste noch flexibel gemacht werden
                    $pin = 'joPin.svg';
                    if($g->getObjecttype() == 'Person'){
                        $pin = 'pin_person.svg';
                        if(is_object($g->getAudio()) && $g->getAudio()->count() > 0){
                            $pin = 'pin_person_speaker.svg';
                        }
                        if(is_object($g->getVideo()) && $g->getVideo()->count() > 0){
                            $pin = 'pin_person_speaker.svg';
                        }
                    }
                    if($g->getObjecttype() == 'Objekt'){
                        if(is_object($g->getExhibit()) && $g->getExhibit()->count() > 0){
                            $pin = 'pin_books.svg';
                        }
                    }
                    $uri = $this->uriBuilder->setArguments(['type' => '2328'])->uriFor('showdata', ['uid' => $g->getUid()], 'Exhibition', $this->extensionName, 'pi2011');
                    $js->properties->link = $uri;
                    $js->properties->pin = $pin;
                    $objects[] = $js;
                }
            }
           //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($objects);
            if(!empty($objects)){
                 $geoJson = [
                    "type" => "FeatureCollection",
                    "features" => $objects,
                ];
            }
            
        }
       return $geoJson;
    }
    
    public function getConfigXML($xml = ''){
        $config = array();
        if($xml != ''){
            foreach($xml as $key => $value){
                if(isset($value['lDEF'])){
                    foreach($value['lDEF'] as $k => $v){
                        $config[$k] = $v['vDEF'];
                    }
                }else if (isset($value['vDEF'])){
                     $config[$key] = $value['vDEF'];
                }
            }
        }
        return $config;
    }

    public function acquireGeoData($object = null){
        $data = array();
        if($object != null){
             // Wenn eine Datenquelle hinterlegt ist, wird diese geladen - Feld datapage hat Priorität
            if ($object->getDatapage()) {
                $pid = intval($object->getDatapage());
                $data = $this->buildGeojsonFromPid($pid);
            // alternativ wird eine eventuell bestehende JSON Datei geladen 
            } elseif ($object->getJsonfile() && count($object->getJsonfile())) {
                // Aktuell wird nur eine JSON Datei berücksichtigt
                foreach ($object->getJsonfile() as $f) {
                    $pub_url = $f->getOriginalResource()->getOriginalFile()->getPublicUrl();
                    $data = GeneralUtility::getURL($pub_url);
                }
            }
        }
        return $data;
    }

    public function getGeoLocationName($geoplace_url) {
        $name = '';

        if ($geoplace_url != '') {
            $xml_data = $this->getRDF($geoplace_url . '/about.rdf');

            $xml_data = $this->makeXMLContent($xml_data);
            
            if ($xml_data->xpath('//gn:alternateName[@xml:lang="de"]/text()')) {
                $name = $this->returnStringFromNodevalue($xml_data->xpath('//gn:alternateName[@xml:lang="de"]/text()'));
            } else if ($xml_data->xpath('//gn:name/text()')) {
                $name = $this->returnStringFromNodevalue($xml_data->xpath('//gn:name/text()'));
            }
        }

        return $name;
    }

    public function showdataAction()
    {
        if ($this->request->hasArgument('uid')) {
            $uid = intval($this->request->getArgument('uid'));
            $item = $this->EntityRepository->findByUid($uid);
            $item_array = $item->_getProperties();
            $item_array['additionalproperties'] = $item->getAdditionalproperties();

            /* @all - bücher mussen es nicht laden, machen es aber, vllt umbauen */
            if ($item->getGeoplace()) {
                $url = $item->getGeoplace();

                $item_array['geo_ort_name'] = $this->getGeoLocationName($url);
            } else if ($item->getGeoplacegeojson()) {
                $json = json_decode($item->getGeoplacegeojson());

                if ($json->properties->uri) {
                    $url = $json->properties->uri;

                    $item_array['geo_ort_name'] = $this->getGeoLocationName($url);
                }
            }

            $this->view->assign('item', $item_array);
        }
    }

    public function loadbookAction()
    {
        if ($this->request->hasArgument('path')) {
            $path = filter_var($this->request->getArgument('path'), FILTER_SANITIZE_STRING);
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
            $folder = $resourceFactory->getFolderObjectFromCombinedIdentifier($path);
            // $files = $folder->getFiles(0, 20);
            $files = $folder->getFiles();
            if (count($files) > 0) $this->view->assign('files', $files);
        }
    }

	// Übersicht ALLER Ausstellungen anzeigen
	public function showallAction(){
		$items = $this->ExhibitionRepository->findAll();
		$this->view->assign('items', $items);

        if ($items && count($items) > 0) {
            $color_config = [];

            foreach($items as $item) {
                $tmpconfig = [];
                $pid_data = $item->getConfiguration();
                $tmpconfig = array_merge($this->config, $this->getConfigXML($pid_data));
                $color_config[] = $tmpconfig;
            }

            $this->view->assign('color_config', $color_config);
        }
	}

	// Übersicht einiger Ausstellungen als Teaser anzeigen
	public function showteaserAction(){
	 
	}

    public function getObjectsFromStorage($repository = null, $storagePid = null){
        $return = array();
        if($repository != null && is_object($repository) && $storagePid != null){
            $querySettings = $repository->createQuery()->getQuerySettings();
            $querySettings->setStoragePageIds([$storagePid]);
            $repository->setDefaultQuerySettings($querySettings);
            $return = $repository->findAll();
        }
        return $return;
    }
    

    public function getArrayNeighbours($result = null){
        $nav_array = array();
        if(is_object($result) && $result->count() > 0){
            $i = 0;
            foreach($result as $r){
                $array_key = $i;
                if(is_object($r) && $r->getUid()) $array_key = $r->getUid();
                $nav_array[$array_key] = array(
                    'orig' => $r
                );
                if(isset($result[($i + 1)])) $nav_array[$array_key]['next'] = $result[($i + 1)];
                if(isset($result[($i - 1)])) $nav_array[$array_key]['prev'] = $result[($i - 1)];
                $i++;
            }
        }
        return $nav_array;
    }

    /**
     * action showsection
     *
     * @return void
     */
    public function showoverviewAction()
    {

    }



    public function ajaxAction()
    {

    }

    /**
     * action showfullindex
     *
     * lädt die Gesamtübersicht der Ausstellung als Inhaltsverzeichnis
     *
     * @return void
     */
    public function showfullindexAction(){
        if ($this->request->hasArgument('ex')) {
            $exhibition = array();
            $uid = filter_var($this->request->getArgument('ex'), FILTER_SANITIZE_STRING);
            $items = $this->ExhibitionRepository->findByUid($uid);
            if($items != null) {
                $exhibition['exhibition'] = $items;
                $pid = $items->getPid();
                if($pid){
                    $sections = $this->SectionRepository->findByPid($pid);
                    $exhibition['sections'] = $sections;
                }
                $colors = array(
                    'maincolor' => $items->getMaincolor(),
                    'maincolorborder' => $items->getMaincolorborder(),
                    'maincolorfont' => $items->getMaincolorfont(),
                    'maincolorlinkfont' => $items->getMaincolorlinkfont(),
                    'fontcolor' => $items->getFontcolor(),
                    'bggradient_1' => $items->getBggradient1(),
                    'bggradient_2' => $items->getBggradient2(),
                    'fontcolor_detail_1' => $items->getFontcolordetail1(),
                );
                $this->addCustomCss($colors);
            }
            $this->view->assign('exhibition', $exhibition);
        }
    }

    //@all -> das muss noch in s Backend und besser struktuiert werden
    public function getFormatedMedia($media_object, $fields = [], $delimiter = '$')
    {
        $formated_data = [];
        if (!empty($media_object) && !empty($fields)) {
            foreach ($media_object as $subvalue) {
                $temp = [
                    '0' => '', // IMG Link
                    '1' => '', // Link zum Viewer wenn vorhanden
                    '2' => '', // Bildunterschrift
                    '3' => '', // FAL IMG ID
                ];
                foreach ($fields as $subsubvalue) {
                    if ('getOriginalResource' == $subsubvalue) {
                        if ($subvalue->getOriginalResource()->getOriginalFile()->getIdentifier()) {
                            $temp[0] = $subvalue->getOriginalResource()->getOriginalFile()->getPublicUrl();
                            $temp[3] = $subvalue->getUid();
                        }
                    }
                    // Description als Bildcaption
                    if ('description' == $subsubvalue) {
                        if ($subvalue->getOriginalResource()->getDescription()) {
                            $temp[2] = $subvalue->getOriginalResource()->getDescription();
                        }
                    }
                }
                $formated_data[] = implode($delimiter, $temp);
            }
        }
        return $formated_data;
    }

    public function transformObjectsToArray($object, $field = null)
    {
        $obj_array = [];
        if (!empty($object)) {
            foreach ($object as $value) {
                if (null == $field) {
                    $obj_array[] = $value;
                } else {
                    $method = 'get' . ucfirst($field);
                    $obj_array[] = $value->$method();
                }
            }
        }
        return $obj_array;
    }

    /**
     *    Curl Content Negotiation -> Rückgabe eines RDF Files
     */
    public function getRDF($url = null)
    {
        if (null != $url) {
            $ch = curl_init();
            $headers = [
                'Accept: application/rdf+xml',
                'Content-type: application/rdf+xml; charset=UTF-8',
            ];
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
     *    Erzeugen eines XML Strings aus einem String
     */
    public function makeXMLContent($string = null)
    {
        if (null != $string) {
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

    public function transformDate($date = null)
    {
        if (null != $date) {
            $temp = explode('-', $date);
            if (count($temp) == 3) {
                $return = $temp[2] . "." . $temp[1] . "." . $temp[0];
            }
            if (count($temp) == 2) {
                $return = $temp[1] . "." . $temp[0];
            }
            if (count($temp) == 1) {
                $return = $temp[0];
            }
            return $return;
        }
    }

    public function getPerson($url)
    {
        $xml_data = $this->getRDF($url);
        $xml_data = $this->makeXMLContent($xml_data);
        $joReturn = '';
        $delimiter = "$";
        if (is_object($xml_data)) {
            $person_array = [
                'name' => '',
                'bracketstart' => '',
                'gebtagsymbol' => '',
                'gebtag' => '',
                'geb_tod_concat' => '',
                'todestagsymbol' => '',
                'todestag' => '',
                'bracketend' => '',
            ];
            if ($xml_data->xpath('//gndo:preferredNameForThePerson/text()')) {
                $person_array['name'] = $this->returnStringFromNodevalue($xml_data->xpath('//gndo:preferredNameForThePerson/text()')); // Hier wird der Titel des Bildes genommen und nicht der Titel der Kategorie - Herr Schlapke weiß Beschied
            }
            if ($xml_data->xpath('//gndo:dateOfBirth/text()')) {
                $person_array['gebtag'] = $this->transformDate($this->returnStringFromNodevalue($xml_data->xpath('//gndo:dateOfBirth/text()'))); // Hier wird der Titel des Bildes genommen und nicht der Titel der Kategorie - Herr Schlapke weiß Beschied
                $person_array['gebtagsymbol'] = '* ';
            }
            if ($xml_data->xpath('//gndo:dateOfDeath/text()')) {
                $person_array['todestag'] = $this->transformDate($this->returnStringFromNodevalue($xml_data->xpath('//gndo:dateOfDeath/text()'))); // Hier wird der Titel des Bildes genommen und nicht der Titel der Kategorie - Herr Schlapke weiß Beschied
                $person_array['todestagsymbol'] = '† ';
            }
            if ('' != $person_array['gebtagsymbol'] || '' != $person_array['todestagsymbol']) {
                $person_array['bracketstart'] = ' (';
                $person_array['bracketend'] = ')';
            }
            if ('' != $person_array['gebtagsymbol'] && '' != $person_array['todestagsymbol']) {
                $person_array['geb_tod_concat'] = ' - ';
            }
            $joReturn = implode('', $person_array);

            /*
        if($name){
        $name_string = $name[0]->__toString();
        }
        $return_data = array(
        'name' => $name_string,
        'url' => $url_string,
        'filename' => array_pop(explode('/',$url_string))
        );
        if(!empty($return_data)){
        $joReturn = implode($delimiter, $return_data);
        }
         */
        } else {
            $joReturn = 'Personennormdaten nicht ermittelbar - GND Nummer fehlerhaft';
        }
        return $joReturn;
    }

    public function returnStringFromNodevalue($node_value)
    {
        $return = null;
        if (!empty($node_value)) {
            $return = json_decode(json_encode($node_value), true);
            $return = call_user_func_array('array_merge', $return);
        }
        return $return[0];
    }

}
