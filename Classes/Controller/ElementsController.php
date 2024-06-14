<?php
namespace JO\JoMuseo\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class ElementsController extends ActionController
{
    /**
     *    @var \JO\JoMuseo\Utility\Fuzzysearchutils\Jomakeindex
     */
    protected $joIndexUtil;

    /**
     *    @var \JO\JoMuseo\Domain\Repository\InstituteRepository
     */
    protected $institute;

    /**
     *    @var \JO\JoMuseo\Domain\Repository\InstituteclassRepository
     */
    protected $instituteclass;

    /**
     *    Museo Util Utility, verschiedene Funktionen für Museo
     *
     *    @var \JO\JoMuseo\Utility\MuseoUtil
     */
    protected $joMuseoUtil;

	/**
     *    Session Utility
     *
     *    @var \JO\JoMuseo\Utility\Fuzzysearchutils\Josearchsession
     */
    protected $joSessionUtil;

    /**
     * @var \GeorgRinger\News\Domain\Repository\NewsRepository     
     */
    protected $newsRepository;

    /**
     *    Objekte der Joblist und des Solrservers
     *
     *  @var  \JO\JoMuseo\Domain\Repository\SolrRepository
     */
    protected $solrRepository;

    /**
     * TEI File
     *
     */
    protected $xml;

    /**
     * TEI File ID Array
     *
     */
    protected $xmlIdArray;

    /**
     * ISO 639-2
     *
     */
    public $isokeys = [
        'ger' => 'Deutsch',
        'alb' => 'Albanisch',
        'ang' => 'Altenglisch',
        'ara' => 'Arabisch',
        'bul' => 'Bulgarisch',
        'cze' => 'Tschechisch',
        'dan' => 'Dänisch',
        'dut' => 'Niederländisch',
        'eng' => 'Englisch',
        'fin' => 'Finnisch',
        'fra' => 'Französisch',
        'fre' => 'Französisch',
        'fry' => 'Friesisch',
        'hrv' => 'Kroatisch',
        'hun' => 'Ungarisch',
        'ita' => 'Italienisch',
        'lav' => 'Lettisch',
        'nor' => 'Norwegisch',
        'pol' => 'Polnisch',
        'rum' => 'Rumänisch',
        'rus' => 'Russisch',
        'tur' => 'Türkisch'
    ];

    public $joSolrCore = ""; // Link zum Solrcore

    /**
     * AbstractController constructor.
     * @param \JO\JoMuseo\Domain\Repository\SolrRepository $solrRepository
     * @param \JO\JoMuseo\Utility\Fuzzysearchutils\Jomakeindex $joIndexUtil
     * @param \JO\JoMuseo\Utility\Fuzzysearchutils\Josearchsession $joSessionUtil
     * @param \JO\JoMuseo\Utility\Arrayfunc\Joarrayfunctions $joArrayUtil
     * @param \JO\JoMuseo\Utility\MuseoUtil $joMuseoUtil
     * @param \GeorgRinger\News\Domain\Repository\NewsRepository $newsRepository
     * @param \JO\JoMuseo\Domain\Repository\InstituteRepository $institute
     * @param \JO\JoMuseo\Domain\Repository\InstituteclassRepository $instituteclass
     */
    public function __construct(
        \JO\JoMuseo\Domain\Repository\SolrRepository $solrRepository,
        \JO\JoMuseo\Utility\Fuzzysearchutils\Jomakeindex $joIndexUtil,
        \JO\JoMuseo\Utility\Fuzzysearchutils\Josearchsession $joSessionUtil,
        \JO\JoMuseo\Utility\Arrayfunc\Joarrayfunctions $joArrayUtil,
        \JO\JoMuseo\Utility\MuseoUtil $joMuseoUtil,
        \GeorgRinger\News\Domain\Repository\NewsRepository $newsRepository,
        \JO\JoMuseo\Domain\Repository\InstituteRepository $institute,
        \JO\JoMuseo\Domain\Repository\InstituteclassRepository $instituteclass
    )
    {
        $this->solrRepository = $solrRepository;
        $this->joIndexUtil = $joIndexUtil;
        $this->joSessionUtil = $joSessionUtil;
        $this->joArrayUtil = $joArrayUtil;
        $this->joMuseoUtil = $joMuseoUtil;
        $this->newsRepository = $newsRepository;
        $this->institute = $institute;
        $this->instituteclass = $instituteclass;
    }

    /**
     * indexAction
     * Index-Action basierend auf tx_jomuseo_domain_model_institute
     *
     * @param string $uid
     */
    public function indexAction($uid = '')
    {
        // Sprache prüfen -> nur für Sammlungsportal
        // @all -> das muss dynamisch gemacht werden
        // DE -> 0, Leichte Sprache -> 2
        if ($GLOBALS['TSFE']->id == 531) {
            if ($this->request->hasArgument('uid')) $institute_id = intval($this->request->getArgument('uid'));
            $lang = $GLOBALS['TSFE']->sys_language_uid;
            if ($lang === 0) {
                $lang_array = [
                    'switch' => 2,
                    'wording' => 'In einfacher Sprache lesen',
                    'uid' => $institute_id
                ];
            $this->view->assign('lang_array', $lang_array);
            }
            if ($lang == 2) {
                $lang_array = [
                    'switch' => 0,
                    'wording' => 'In normaler Sprache lesen',
                    'uid' => $institute_id
                ];
            $this->view->assign('lang_array', $lang_array);
            }
        }


        $tmpList = explode(',', $this->settings['ansichten']);
        // Template auswählen
        //  'kacheln_with_text' => ['id' => 'kacheln_with_text', 'label' => 'Kacheln mit Text'],
        $allowed_templates = [
            'list' => [
                'id' => 'list',
                'label' => 'Liste'
            ], 
            'kacheln' => [
                'id' => 'kacheln',
                'label' => 'Kacheln'
            ],
            'map' => [
                'id' => 'map',
                'label' => 'Karteneinstieg'
            ],
            'newstyle' => [
                'id' => 'newstyle',
                'label' => 'newstyle'
            ],
            'editpro_kacheln' => [
                'id' => 'editpro_kacheln',
                'label' => 'Editionsportal Kacheln'
            ]
        ];
        $template_list = [];
        foreach ($tmpList as $key => $value) {
            if (array_key_exists($value, $allowed_templates)) $template_list[$value] = $allowed_templates[$value];
        }

        $t_count = count($template_list);
        $list_template = $t_count ? array_pop(array_reverse($template_list)) : $allowed_templates['list']; // default
        if ($this->settings['initialview']) $list_template = $allowed_templates[filter_var($this->settings['initialview'], FILTER_SANITIZE_STRING)];

        if (GeneralUtility::_GET('type')) {
            $tmp = filter_var(GeneralUtility::_GET('type'), FILTER_SANITIZE_STRING);
            if (array_key_exists($tmp, $allowed_templates)) $list_template = $allowed_templates[$tmp];
        }
        //@all -> in die settings villeicht, zeigt mit welchem option gestartet wird / 1 - Alle anzeigen, 2 - Thüringer Institutionen, 3 - Partner Institutionen
        $localstatus = $this->settings['showinstdist'] ? 2 : 1;
        if ($this->request->hasArgument('localstatus')) $localstatus = intval($this->request->getArgument('localstatus'));

        // Klassifikation als Untermenü erzeugen - nur wenn es tatsächlich mehrere Klassifikationen gibt
        $class = null;
        if ($this->request->hasArgument('class')) $class = intval($this->request->getArgument('class'));

        $classification['items'] = $this->instituteclass->findAll();
        if ($classification['items']->count()) {
            $classification['active'] = $class;
            $this->view->assign('mainnavi', $classification);
        }
        // Elemente entsprechend der Klasifikation auslesen auslesen
        if (null != $class) {
            $subnavi = $this->institute->findCategories([$class])->toArray();
        } else {
            if ($this->settings['sortbydate']) $this->institute->setDefaultOrderings(['day' => QueryInterface::ORDER_DESCENDING]);
            $subnavi = $this->institute->findAll()->toArray();
        }

        $boolStat = $localstatus - 2;
        if (0 == $boolStat || 1 == $boolStat) {
            $tmp = [];
            foreach ($subnavi as $key => $value) {
                if ($value->getNotlocalstatus() == $boolStat) $tmp[] = $value;
            }
            $subnavi = $tmp;
        }
        $searchtype = ['tenantHierarchy', 'entityTokenizedwc', 'classProject', 'location', 'classProject', 'detail', 'detail'];

        if (count($subnavi)) {
            if ('' != $uid) {
                $obj = $this->institute->findByUid($uid);
                $d_storage = $obj->getDatastorage();
                if ($d_storage && $d_storage != '') {
                    $pidList = GeneralUtility::intExplode(',', $d_storage, true);
                    $querySettings = $this->newsRepository->createQuery()->getQuerySettings();
                    $querySettings->setStoragePageIds($pidList);
                    $this->newsRepository->setDefaultQuerySettings($querySettings);
                    $p_items = $this->newsRepository->findAll();
                    $this->view->assign('p_items', $p_items);
                }
                $type = $obj->getDatatype();
                if (GeneralUtility::_GP('test')) $this->view->assign('joTest', 'true');

                if ($obj->getClassfication()->count()) {
                    $classiTitle = $obj->getClassfication()->toArray()[0]->getTitle();

                    $classUID = 0;
                    foreach ($classification['items'] as $key => $value) {
                        if ($classiTitle == $value->getTitle()) {
                            $classUID = $value->getUid();
                            break;
                        }
                    }

                    $link2 = $this->controllerContext->getUriBuilder()->setCreateAbsoluteUri(true)->uriFor('index', ['class' => $classUID], 'Elements', $this->extensionName, 'pi1010');
                    $link3 = $this->controllerContext->getUriBuilder()->setCreateAbsoluteUri(true)->uriFor('index', ['uid' => $uid], 'Elements', $this->extensionName, 'pi1010');
                    $arr = [$classiTitle => $link2, $obj->getTitle() => $link3];
                    $this->view->assign('breadcrumb', $arr);
                }

                if ('map' == $list_template['id'] || $this->settings['listeandmap']) {
                    $this->loadMapFiles();

                    $joGeoPosArray = [];
                    $geodata = $obj->getGeodata();
                    if ($geodata) {
                        $lonlatArray = explode(',', $geodata);
                        if ($this->settings['lng_lat_tausch']) {
                            $lonlatArray_tmp = $lonlatArray;
                            $lonlatArray[0] = $lonlatArray_tmp[1];
                            $lonlatArray[1] = $lonlatArray_tmp[0];
                        }
                        $allgClassi = '';
                        if ($this->settings['allg_classi']) {
                            $allgClassi = $this->settings['allg_classi'];
                        }

                        if ($obj->getClassfication()->count() || $allgClassi != '') {
                            $classiTitle = $obj->getClassfication()->count() ? $obj->getClassfication()->toArray()[0]->getTitle() : $allgClassi;
                            $link = $this->controllerContext->getUriBuilder()->setArguments(['uid' => $obj->getUid(), 'type' => '2328'])->uriFor('ajaxinstitute', [], 'Elements', $this->extensionName, 'pi1010');
                            if ($classiTitle && count($lonlatArray) == 2) {
                                $joGeoPosArray[$classiTitle][] = [
                                    "type" => "Feature",
                                    "id" => $obj->getUid(),
                                    "properties" => [
                                        'l' => $link,
                                        'n' => $obj->getTitle(),
                                        'a' => true,
                                        'i' => $obj->getUid()
                                    ],
                                    "geometry" => [
                                        "type" => 'Point',
                                        "coordinates" => [(float) trim($lonlatArray[0]), (float) trim($lonlatArray[1])]
                                    ]
                                ];
                            }
                        }
                    }
                    $joGeoJSON = [];
                    if (!empty($joGeoPosArray)) {
                        foreach ($joGeoPosArray as $key => $value) {
                            $joGeoJSON['pois'] = [
                                "type" => "FeatureCollection",
                                "features" => $value,
                            ];
                        }
                        $this->response->addAdditionalHeaderData('<script>geojson=' . json_encode($joGeoJSON['pois']) . '</script>');
                        $this->loadMapOverlay();
                    }
                }
                $this->view->assignMultiple(
                    [
                        'searchtype' => $searchtype[-1 == $type ? 0 : $type - 1],
                        'object' => $obj
                    ]
                );
            } else if (count($subnavi) == 1) {
                $obj = $subnavi[0];
                $type = $obj->getDatatype();
                $this->view->assignMultiple(
                    [
                        'searchtype' => $searchtype[-1 == $type ? 0 : $type - 1],
                        'object' => $obj
                    ]
                );
            } else {
                $subnaviOriginal = $subnavi;
                if ('list' == $list_template['id']) {
                    $tmp = [];
                    // @all -> die schleife kann man sich gewiss sparen - könne in die columsplit mit rein!
                    foreach ($subnavi as $key => $value) {
                      if ($this->settings['sortgeo']) {
                          $tmp[strtoupper($value->getGeotext()[0])][] = $value;
                      } else {
                        $tmp[strtoupper($value->getTitle()[0])][] = $value;
                      }
                    }
                    $this->joIndexUtil->joSpalten = 3;
                    if ($this->settings['listspalten']) {
                        $this->joIndexUtil->joSpalten = $this->settings['listspalten'];
                        $this->view->assign('list_width', 12 / $this->settings['listspalten']);
                    }
                    $this->joIndexUtil->joSimpleSplit = true;
                    $subnavi = $this->joIndexUtil->joColumSplit($tmp);
                }
                $this->view->assign('template', $list_template);
                if ('map' == $list_template['id'] || $this->settings['listeandmap']) {
                    $this->loadMapFiles();

                    $joGeoPosArray = [];
                    foreach ($subnaviOriginal as $key => $value) {
                        $geodata = $value->getGeodata();
                        if ($geodata) {
                            $lonlatArray = explode(',', $geodata);
                            if ($this->settings['lng_lat_tausch']) {
                                $lonlatArray_tmp = $lonlatArray;
                                $lonlatArray[0] = $lonlatArray_tmp[1];
                                $lonlatArray[1] = $lonlatArray_tmp[0];
                            }
                            $allgClassi = '';
                            if ($this->settings['allg_classi']) {
                                $allgClassi = $this->settings['allg_classi'];
                            }
                            if ($value->getClassfication()->count() || $allgClassi != '') {
                                $classiTitle = $value->getClassfication()->count() ? $value->getClassfication()->toArray()[0]->getTitle() : $allgClassi;
                                $link = $this->controllerContext->getUriBuilder()->setArguments(['uid' => $value->getUid(), 'type' => '2328'])->uriFor('ajaxinstitute', [], 'Elements', $this->extensionName, 'pi1010');
                                if ($classiTitle && count($lonlatArray) == 2) {
                                    $joGeoPosArray[$classiTitle][] = [
                                        "type" => "Feature",
                                        "id" => $value->getUid(),
                                        "properties" => [
                                            'l' => $link,
                                            'n' => $value->getTitle(),
                                            'a' => true,
                                            'i' => $value->getUid()
                                        ],
                                        "geometry" => [
                                            "type" => 'Point',
                                            "coordinates" => [(float) trim($lonlatArray[0]), (float) trim($lonlatArray[1])]
                                        ]
                                    ];
                                }
                            }
                        }
                    }
                    $joGeoJSON = [];
                    if (!empty($joGeoPosArray)) {
                        foreach ($joGeoPosArray as $key => $value) {
                            $joGeoJSON['pois'][$key] = [
                                "type" => "FeatureCollection",
                                "features" => $value
                            ];
                        }
                        $this->response->addAdditionalHeaderData('<script>geojson=' . json_encode($joGeoJSON['pois']) . '</script>');
                        $this->loadMapOverlay();
                    }
                }
                if ('newstyle' == $list_template['id']) {
                    $newNavi = [];
                    $newNavi[0] = [];
                    $newNavi[1] = [];
                    $newNavi[2] = [];
                    $newNavi[3] = [];
                    $newNavi[4] = [];
                    foreach ($subnavi as $key => $value) {
                        $num = $key % 5;
                        $newNavi[$num][] = $value;
                    }
                    $subnavi = $newNavi;
                }
            }
        } else {
            $this->view->assign('nothingFound', '1');
        }
        $this->view->assignMultiple(
            [
                'localstatus' => $localstatus,
                'subnavi' => $subnavi
            ]
        );
        if ($t_count) $this->view->assign('allowed_templates', $template_list);
        $page_conf_session = 'page_config_' . $GLOBALS['TSFE']->id;
        $page_config_sessionvalues = $this->joSessionUtil->getSessionValues($page_conf_session);
        if (is_array($page_config_sessionvalues) && !empty($page_config_sessionvalues) && $page_config_sessionvalues['vorschau']) $this->view->assign('hideVorschau', 1);
    }

    public function loadMapFiles()
    {
        $this->joMuseoUtil->addHeaderFile('ol.min.js', $this->extensionName);
        $this->joMuseoUtil->addHeaderFile('maps.js', $this->extensionName);
        $this->joMuseoUtil->addHeaderFile('ol.min.css', $this->extensionName);
        $this->joMuseoUtil->addHeaderFile('map.css', $this->extensionName);
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
                            'i' => '/fileadmin' . $fileArray['identifier']
                        ];
                    }
                }
            }
            $this->response->addAdditionalHeaderData('<script>map_layer=' . json_encode($overlay_array) . '</script>');
        }
    }

    public function getXMLFile($url)
    {
        $xml = new \SimpleXMLElement($url, null, true);
        if (!empty($xml)) {
            $namespaces = $xml->getNamespaces(true);
            if (!empty($namespaces)) {
                foreach ($namespaces as $key => $value) {
                    $xml->registerXPathNamespace($key, $value);
                }
            }
            return $xml;
        }
    }

    private function teiIdMap(&$node)
    {
        foreach ($node as $key => $value) {
            if (count($value->children())) $this->teiIdMap($value);
            if ('anchor' == $key && isset($value['ana'])) {
                $id = (string) $value['ana'];
                if (array_key_exists($id, $this->xmlIdArray) && $this->xmlIdArray[$id] % 2 == 1) {
                    $el = $this->xml->xpath('//item[@xml:id="' . $id . '"]');
                    //$newTitle = htmlentities($el[0]->__toString());
                    $newTitle = (string) $el[0]->asXML();
					$newTitle = trim(str_ireplace('Zu lange Liste:', '', $newTitle));

                    if ($el[0]->list && $el[0]->list->item->link['target']) {
                        $link = $el[0]->list->item->link['target']->__toString();
                        if (isset($link)) {
                            $link = explode('//', $link);
                            if (sizeof($link) == 2) {
                                $eltmp = $this->xml->xpath('//item[@xml:id="doclink' . $link[1] . '"]');
                                if ($eltmp) {
                                    $linkN = (string) $eltmp[0]->link['n'];
                                    $linkT = $link[1];
                                	$arr = explode(','.PHP_EOL, $eltmp[0]->bibl->__toString());
                                	$size = sizeof($arr);
                                	if ($size) {
                                		$newTitle = '';
                                    	$obj = [];
                                    	// packe alle sachen in das object array
                                        for ($i = 0; $i < $size; $i++) {
                                            if (strpos($arr[$i], '={') !== false) {
                                                $tmp = explode('={', trim($arr[$i]));
                                                $key = $tmp[0];
                                                $val = str_replace('}', '', $tmp[1]);
                                                $obj[$key] = $val;
                                            }
                                        }

                                        if (array_key_exists('keyword_in_index', $obj)) {
                                            // {Indexeintrag} ({Sprache ausgeschrieben, wenn ger dann Klammer weglassen}): {übersetzung}. {Kommentar}. Letzte Änderung: {tt.mm.jjjj}.
                                            // {keyword_in_index} ({keyword_lang}): {keyword_translation}, {comment}. Letzte Änderung: {timelastchg}.
                                            $newTitle .= '<div class="additional stichwort">';
                                            if (array_key_exists('keyword_in_index', $obj)) $newTitle .= str_ireplace(' XX', '', $obj['keyword_in_index']);
                                            if (array_key_exists('keyword_lang', $obj)) {
                                            	$l = $obj['keyword_lang'];
                                            	if ('ger' == $l) {
                                        			$newTitle .= ': ';
                                            	} else {
                                            		$lang = array_key_exists($l, $this->isokeys) ? $this->isokeys[$l] : $l;
                                                	$newTitle .= ' (' . $lang . '): ';
                                            	}
                                            }
                                            if (array_key_exists('keyword_translation', $obj)) $newTitle .= strpos($arr[$i], 'comment={') !== false ? $obj['keyword_translation'] . ', ' : $obj['keyword_translation'] . '. ';
                                            if (array_key_exists('comment', $obj)) {
                                            	if (trim($obj['comment']) !== '') $newTitle .= $obj['comment'] . '. ';
                                            }
                                            if (array_key_exists('timelastchg', $obj)) {
                                                $timeArr = explode(' ', $obj['timelastchg']);
                                            	$time = strtotime($timeArr[0]);
                                            	$time = $time == false ? $timeArr[0] : date('d.m.Y', $time);
                                                $newTitle .= 'Letzte Änderung: ' . $time . '.';
                                            }
                                            $newTitle .= '</div>';
                                        } else if (array_key_exists('plangofdata', $obj)) {
                                        	// {Vorname} {Familienname} {titledesign} ({Von}–{Bis}), {Projektklasse}, {Typ Literatur: Seite // GND (URN hinterlegt)}. {Kommentar}. Letzte Änderung: {tt.mm.jjjj}.
                                        	$newTitle .= '<div class="additional person">';

                                        	if (array_key_exists('pnamefirst', $obj)) $newTitle .= $obj['pnamefirst'];
                                            if (array_key_exists('pnamefamily', $obj)) $newTitle .= ' ' . $obj['pnamefamily'];
                                            if (array_key_exists('ptitledesign', $obj)) $newTitle .= ' ' . $obj['ptitledesign'];
                                            if (array_key_exists('pusedfrom', $obj)) {
                                            	$time = strtotime($obj['pusedfrom']);
                                            	$time = $time == false ? $obj['pusedfrom'] : date('d.m.Y', $time);
                                            	if (array_key_exists('pusedto', $obj)) {
                                                	$newTitle .= ' (' . $time;
                                            	} else {
                                            		$newTitle .= ' (' . $time . '), ';
                                            	}
                                            }
                                            if (array_key_exists('pusedto', $obj)) {
                                            	$time = strtotime($obj['pusedto']);
                                            	$time = $time == false ? $obj['pusedto'] : date('d.m.Y', $time);
                                            	if (array_key_exists('pusedfrom', $obj)) {
                                                	$newTitle .= ' – ' . $time . ')';
                                            	} else {
                                            		$newTitle .= ' (gest. ' . $time . ')';
                                            	}
                                                $newTitle .= array_key_exists('pprojectclass', $obj) ? ', ' : '';
                                            }
                                            if (array_key_exists('pprojectclass', $obj)) {
                                                if (!array_key_exists('pusedfrom', $obj) && !array_key_exists('pusedto', $obj)) $newTitle .= ', ';
                                                $newTitle .= str_ireplace('#', ' ', str_ireplace('\\', ',', $obj['pprojectclass']));
                                                if (array_key_exists('pauthorithyurn', $obj)) $newTitle .= ', ';
                                            }
                                            if (array_key_exists('pauthorithyurn', $obj)) $newTitle .= '<a href="' . $obj['pauthorithyurn'] . '" target="_blank">GND</a>';
                                            if (array_key_exists('pauthority_lit', $obj)) {
                                            	if (array_key_exists('pauthority_pages', $obj)) {
                                                	$newTitle .= '. ' . $obj['pauthority_lit'] . ': ';
                                            	} else {
                                            		$newTitle .= '. ';
                                            	}
                                            }
                                            if (array_key_exists('pauthority_pages', $obj)) $newTitle .= $obj['pauthority_pages'] . '. ';

                                            //Kommentar hohlen
                                            $tmpid = 'docTypID_' . $linkN . '.docID_' . $linkT;
                                            $tmp = $this->xml->xpath('//list[@n="Kommentar"]/item[contains(@xml:id, "' . $tmpid . '") and @n="pcomments"]');
                                            if (count($tmp) > 0) {
                                                $tmpComment = (string) $tmp[0]->asXML();
                                                $tmpComment = preg_replace('/<label\>[1-9a-zA-z:;\- ]+<\/label>/', '', $tmpComment);
                                                $newTitle .= $tmpComment . ' ';
                                            } else if (array_key_exists('pcomments', $obj)) {
                                                if (trim($obj['pcomments']) !== '') $newTitle .= $obj['pcomments'] . '. ';
                                            }
                                            if (array_key_exists('timelastchg', $obj)) {
                                            	$timeArr = explode(' ', $obj['timelastchg']);
                                            	$time = strtotime($timeArr[0]);
                                            	$time = $time == false ? $timeArr[0] : date('d.m.Y', $time);
                                                $newTitle .= 'Letzte Änderung: ' . $time . '.';
                                            }
                                            $newTitle .= '</div>';
                                        } else if (array_key_exists('plant_index', $obj)) {
                                        	$newTitle .= '<div class="additional plant">';
                                    		// {actual_accept_name} ({link_plantlist}, {link_ipni}, {zot_link_FO [F.O. aufgelöst zu Flora Orientalis]: {page_FO} ({remarks_FO}), Herbarbeleg: {herbar_exist}, {lokale Namen [das sind evtl mehrere, Sprache mit angeben)}. {Kommentar}. Letzte Änderung: {tt.mm.jjjj}.
                                        	if (array_key_exists('actual_accept_name', $obj)) $newTitle .= $obj['actual_accept_name'];
                                        	if (array_key_exists('link_plantlist', $obj) && array_key_exists('link_ipni', $obj)) {
                                        		$newTitle .= ' (<a href="' . $obj['link_plantlist'] . '" target="_blank">PlantList</a><a href="' . $obj['link_ipni'] . '" target="_blank">IPIN</a>), ';
                                        	} else if (array_key_exists('link_plantlist', $obj)) {
                                        		$newTitle .= ' (<a href="' . $obj['link_plantlist'] . '" target="_blank">PlantList</a>), ';
                                        	} else if (array_key_exists('link_ipni', $obj)) {
                                        		$newTitle .= ' (<a href="' . $obj['link_ipni'] . '" target="_blank">IPIN</a>), ';
                                        	} else if(!array_key_exists('zot_link_FO', $obj) && !array_key_exists('page_FO', $obj) && !array_key_exists('remarks_FO', $obj) && !array_key_exists('herbar_exist', $obj)) {
                                        		$newTitle .= '. ';
                                        	} else {
                                        		$newTitle .= ', ';
                                        	}
                                        	if (array_key_exists('zot_link_FO', $obj)) $newTitle .= 'Flora Orientalis ' . explode(' ', $obj['zot_link_FO'])[1] . ': ';
                                        	if (array_key_exists('page_FO', $obj)) $newTitle .= $obj['page_FO'];
                                        	if (array_key_exists('remarks_FO', $obj)) $newTitle .= ' (' . $obj['remarks_FO'] . '), ';
                                        	if (array_key_exists('herbar_exist', $obj)) {
                                        		if (!array_key_exists('remarks_FO', $obj)) $newTitle .= ', ';
                                        		$newTitle .= 'Herbarbeleg: ' . $obj['herbar_exist'] . '. ';
                                        	}
                                        	if (array_key_exists('remarks', $obj)) $newTitle .= $obj['remarks'] . '. ';
                                    		if (array_key_exists('timelastchg', $obj)) {
                                            	$timeArr = explode(' ', $obj['timelastchg']);
                                            	$time = strtotime($timeArr[0]);
                                            	$time = $time == false ? $timeArr[0] : date('d.m.Y', $time);
                                                $newTitle .= 'Letzte Änderung: ' . $time . '.';
                                            }
                                        	$newTitle .= '</div>';
                                        } else if (array_key_exists('plc_index', $obj)) {
                                        	//{Ort in Index}, {Projektklassifikation}, {Beziehungen}, {Namensart [zB Geonames, wenn was in URN steht, dann soll das direkt mit der Namensart verlinkt sein. Auch wenn Namensart Literatur]}. {Kommentar}. Letzte Änderung: {tt.mm.jjjj}.
                                        	$newTitle .= '<div class="additional location">';
                                        	if (array_key_exists('plc_index', $obj)) $newTitle .= str_ireplace(' XX', '', $obj['plc_index']) . ', ';
                                            if (array_key_exists('plc_prjclass', $obj)) $newTitle .= $obj['plc_prjclass'];
                                            if (array_key_exists('plc_auth_url', $obj)) {
                                                $newTitle .= '. <a href="' . $obj['plc_auth_url'] . '" target="_blank">GND</a>.';
                                            } else {
                                            	$newTitle .= ', ';
                                            }
                                            if (array_key_exists('plc_generalnotes', $obj)) $newTitle .= $obj['plc_generalnotes'] . '. ';
                                            if (array_key_exists('timelastchg', $obj)) {
                                            	$timeArr = explode(' ', $obj['timelastchg']);
                                            	$time = strtotime($timeArr[0]);
                                            	$time = $time == false ? $timeArr[0] : date('d.m.Y', $time);
                                                $newTitle .= 'Letzte Änderung: ' . $time . '.';
                                            }
                                        	$newTitle .= '</div>';
                                        } else {
                                            $newTitle .= '<div class="additional row no-gutters">';
                                            for ($i = 0; $i < $size; $i++) {
                                                if (strpos($arr[$i], '={') !== false) {
                                                    $tmp = explode('={', trim($arr[$i]));
                                                    $key = LocalizationUtility::translate('LLL:EXT:jo_museo/Resources/Private/Language/locallang.xlf:jo_bkr_base.tei.' . $tmp[0]);
                                                    $key = isset($key) ? $key : $tmp[0] . ': ';
                                                    $val = str_replace('}', '', $tmp[1]);
                                                    if ('pauthorithyurn' == $tmp[0] || strpos($val, 'http://') !== false || strpos($val, 'https://') !== false) $val = '<a href="' . $val . '" target="_blank">' . $val . '</a>';
                                                    $newTitle .= '<div class="col-12 col-md-3">' . $key . '</div><div class="col-12 col-md-9">' . $val . '</div>';
                                                }
                                            }
                                            $newTitle .= '</div>';
                                        }
                                	}
                                }
                            }
                        }
                    }

                    $parent = $this->xml->xpath('//item[@xml:id="' . $id . '"]/..');

                    if (strpos((string)$parent[0]['n'], '_SK') !== false) $newTitle .= '<span class="tei_sk"> (SK)</span>';
                    if ($newTitle && strpos($newTitle, 'Gestrichen') == false && strpos($newTitle, 'Unterstrichen') == false) {
                        $value->addAttribute('data-toggle', 'joTeiPopover');
                        $value->addAttribute('data-container', 'body');
                        $value->addAttribute('data-placement', 'top');
                        $value->addAttribute('data-content', $newTitle);
                    }
                    if (strpos($newTitle, 'Gestrichen') !== false) {
                        $value->addAttribute('data-type', 'joGestrichen');
                    } else if (strpos($newTitle, 'Unterstrichen') !== false) {
                        $value->addAttribute('data-type', 'joUnterstrichen');
                    }
                    $this->xmlIdArray[$id] = array_key_exists($id, $this->xmlIdArray) ? $this->xmlIdArray[$id] + 1 : 1;
                } else {
                    //$this->xmlIdArray[] = $id;
                    $this->xmlIdArray[$id] = array_key_exists($id, $this->xmlIdArray) ? $this->xmlIdArray[$id] + 1 : 1;
                }
            }
        }
    }

    public function teiAction()
    {
        $teiurl = filter_var(GeneralUtility::_GET('teiurl'), FILTER_SANITIZE_STRING);
        $this->xmlIdArray = [];
        if ($teiurl) {
            $tei_stream = file_get_contents($teiurl);
            $tei_stream = str_replace('xmlns=', 'ns=', $tei_stream);
            if ($tei_stream) {
                $tei_stream = str_replace('<lb/>', '<br/>', $tei_stream);
				$tei_stream = str_replace('underline:1', 'text-decoration:underline;', $tei_stream);
				$tei_stream = str_replace('overstrike:1', 'text-decoration: line-through;', $tei_stream);

                $tei_stream = str_replace('<ref target=', '<a href=', $tei_stream);
                $tei_stream = str_replace('/ref>', '/a>', $tei_stream);
                if (strpos($teiurl, 'HisBest_derivate_00020836')) {
                    //$tei_stream = str_replace('rend=', 'style=', $tei_stream);
                    /* $tei_stream = str_replace('<hi rend="background:aqua;overstrike:1">', '', $tei_stream);
                    $tei_stream = str_replace('<hi rend="overstrike:1">', '', $tei_stream);
                    $tei_stream = str_replace('<hi rend="background:aqua">', '', $tei_stream); */
                    $tei_stream = preg_replace('/<hi rend="[1-9a-zA-z:;\- ]+">/', '', $tei_stream);
                    $tei_stream = str_replace('<hi>', '', $tei_stream);
                    $tei_stream = str_replace('</hi>', '', $tei_stream);
                    $tei_stream = str_replace('(für Frau Drost)', '', $tei_stream);
                    $this->xml = simplexml_load_string($tei_stream);

                    foreach ($this->xml->getDocNamespaces() as $prefix => $namespace) {
                        $this->xml->registerXPathNamespace($prefix, $namespace);
                    }

                    //$this->teiIdMap($this->xml->TEI->text->body);

                    $worktitle = $this->xml->xpath('//list/item[@n="worktitle"]')[0]->__toString();

                    if (isset($worktitle)) echo "<h3>" . $worktitle . "</h3><br/>";

                    foreach ($this->xml->xpath('//list/item[@n="main"]') as $key => $value) {
                        if (count($value->children())) {
                            $this->teiIdMap($value);
                            echo $value->asXML();
                        }
                    }

                    //echo $this->xml->TEI->text->body->asXML();
                } else {
                    $tei_stream = str_replace('rend=', 'class=', $tei_stream);
                    //$tei_stream = str_replace('<hi', '<h3', $tei_stream);
                    //$tei_stream = str_replace('/hi>', '/h3>', $tei_stream);
                    $xml = simplexml_load_string($tei_stream);
                    echo $xml->text->body->asXML();
                }
            } else {
                echo 'oO Something gone wrong. Could not load TEI File. Oo';
            }
        }
    }

	public function pageconfigAction()
    {
		/* Seitenkonfiguration lesen und schreiben */
		// @all -> page_config muss von zentraler stelle aus den ettings kommen - ist gedoppelt gerade im museocontroller
		if (GeneralUtility::_GP('id')) {
			$page_config = [
				'ev' => 1
			];
			$page_conf_session = 'page_config_' . intval(GeneralUtility::_GP('id'));
			$pageconfig_change_flag = FALSE;
			$page_config_sessionvalues = $this->joSessionUtil->getSessionValues($page_conf_session);
			if (is_array($page_config_sessionvalues) && !empty($page_config_sessionvalues)) $page_config = $page_config_sessionvalues;
			if (GeneralUtility::_GP('ev')) {
				if (intval(GeneralUtility::_GP('ev')) != $page_config['ev']) {
					$page_config['ev'] = intval(GeneralUtility::_GP('ev')); // Ansicht der Editionsoberfläche -> 1 Annotationen sichtbar, 2 -> Leseansicht ohne Annotationen
					$pageconfig_change_flag = TRUE;
				}
			}
            if (GeneralUtility::_GP('vorschau')) {
                if (intval(GeneralUtility::_GP('vorschau')) != $page_config['vorschau']) {
                    $page_config['vorschau'] = intval(GeneralUtility::_GP('vorschau')); // Ansicht der Editionsoberfläche -> 1 Annotationen sichtbar, 2 -> Leseansicht ohne Annotationen
                    $pageconfig_change_flag = TRUE;
                }
            }
			if ($pageconfig_change_flag) $this->joSessionUtil->replaceAllValues($page_conf_session, $page_config);
		}
	}

    public function getSolrDataForFlexform(&$fConfig, $fObj)
    {

        $field = 'classProject';
        if ($fConfig["row"]["settings.facettename"][0]) $field = $fConfig["row"]["settings.facettename"][0];
		if ($fConfig["row"]["datatype"] == 8) {
           
        }
        $field = "classProject";
        $url = $this->joSolrCore . "select?q=*%3A*&rows=0&wt=json&indent=true&facet=true&facet.limit=-1&facet.sort=index&facet.field=" . $field;
        $solr_result = json_decode($this->solrRepository->getSolrData($url));
        $facettes = $solr_result->facet_counts->facet_fields->$field;
        if (!empty($facettes)) {
            $i = 0;
            foreach ($facettes as $value) {
                if ($i % 2 == 0) {
                    array_push($fConfig['items'], [
                        $value,
                        $value
                    ]);
                }
                $i++;
            }
        }
    }

    public function ajaxinstituteAction()
    {
        if (GeneralUtility::_GP('uid')) $this->view->assign('object', $this->institute->findByUid(intval(GeneralUtility::_GP('uid'))));
    }
}
