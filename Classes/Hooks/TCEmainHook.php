<?php
namespace JO\JoMuseo\Hooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class TCEmainHook
{

    public $url = '';

    public $solrCore = null;

    public $typodelimiter = ',';

    public $language = 'de';

    public $prefix = 'default_';

    public function getPluginSettings($pid = null)
    {
        $return = [];
        if (null != $pid) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

            $rootline = BackendUtility::BEgetRootLine($pid);
            $ts = $objectManager->get(TemplateService::class);
            $ts->rootLine = $rootline;
            $ts->runThroughTemplates($rootline, 0);
            $ts->generateConfig();

            $return = $ts->setup;
        }
        return $return;
    }

    protected function addToSolr($joSolrArray = [], $solrCore = null)
    {
        $response = '';
        if (!empty($joSolrArray) && null != $solrCore) {
            $ch = curl_init($solrCore . "update?wt=json");
            $data = [
                "add" => [
                    "doc" => $joSolrArray,
                    "commitWithin" => 1000,
                ],
            ];
            $data_string = json_encode($data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            $response = curl_exec($ch);
        }
        return $response;
    }

    public function getAssetData($cid = null, $table = null, $castname = null)
    {
        $return = [];
        if (null != $cid && null != $table && null != $castname) {
            $uid = (int) $cid;
            $castName = $name;
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $fileObjects = $fileRepository->findByRelation($table, $castname, $uid);
            if (!is_object($fileObjects)) {
                foreach ($fileObjects as $value) {
                    $basepath = $value->getStorage()->getStoragerecord()['name'];
                    $image_array = [
                        'uri' => $this->url . $basepath . $value->getProperties()['identifier'],
                        'viewer' => $this->url . $basepath . $value->getProperties()['identifier'],
                        'caption' => $value->getProperties()['title'],
                        'credits' => '',
                        'licence' => '',
                        'licenceholder' => '',
                    ];
                    $return[] = implode("$", $image_array);
                }
            }
        }
        return $return;
    }

    public function processDatamap_afterDatabaseOperations($status, $table, $id, array $fieldArray, DataHandler &$pObj)
    {
        $pid = $pObj->checkValue_currentRecord['pid'];

        // beim kopieren und einfügen ist die pid negativ, in diesem fall nicht speichern
        if ($table && $pid && $pid > 0) {
            $config = $this->getPluginSettings($pid);
            $config = $config['plugin.']['tx_jomuseo.']['settings.'];

            $allowed_tables = $config['init.']['typo3tosolr.']['tables.'];

            // Funktionen werden nur ausgeführt, wenn die Tabelle für die Übernahme in den Solr freigegeben ist
            if (is_array($allowed_tables) && in_array($table, $allowed_tables)) {
                $solr_data = [];

                // Solrcore definieren - Abbruch, wenn keine derartige Konfiguration vorliegt
                if ($config['init.']['authdata.']['server']) {
                    $this->solrCore = $config['init.']['authdata.']['server'];
                } else {
                    exit('Kein Core definiert - Settings überprüfen');
                }

                $datasource = 'museo_extension_typo3';
                if ($config['init.']['typo3tosolr.']['datasource']) {
                    $datasource = $config['init.']['typo3tosolr.']['datasource'];
                }

                $classProject = '';
                if ($config['init.']['typo3tosolr.']['classProject']) {
                    $classProject = $config['init.']['typo3tosolr.']['classProject'];
                }

                // Prefix für die IDs der Objekte
                $this->prefix = md5($table) . '_';
                switch ($table) {
                    case 'tx_jomuseo_domain_model_entity':
                        $baseobject = $pObj->datamap[$table][$id];

                        if (strpos($id, "NEW") !== false && $pObj->substNEWwithIDs && !empty($pObj->substNEWwithIDs) && isset($pObj->substNEWwithIDs[$id])) {
                            $id = $pObj->substNEWwithIDs[$id];
                        }

                        if (is_numeric($id)) {
                            if ($baseobject) {
                                $solr_data['id'] = $this->prefix . $id;
                                $solr_data['classProject'] = $classProject;
                                $solr_data['datasource'] = $datasource;
                                $solr_data['objectType'] = 'undefined';
                                $solr_data['hiddenFromSearch'] = 0;
                                $solr_data['title'] = 'unnamed';

                                if (isset($pObj->checkValue_currentRecord['sorting']) && $pObj->checkValue_currentRecord['sorting']) {
                                    $solr_data['sorting'] = $pObj->checkValue_currentRecord['sorting'];
                                }

                                if (isset($baseobject['objecttype']) && !empty($baseobject['objecttype'])) {
                                    $solr_data['objectType'] = array_pop($baseobject['objecttype']);
                                }
                                if (isset($baseobject['title'])) {
                                    $solr_data['title'] = $baseobject['title'];
                                }
                                if (isset($baseobject['bodytext'])) {
                                    $solr_data['note'] = str_replace(array("\r", "\n"), "", $baseobject['bodytext']);
                                }
                                if (isset($baseobject['geoplace'])) {
                                    $combine_coordinates = $baseobject['geoplace'];

                                    $gedreht = explode(',', $combine_coordinates);
                                    $gedreht = $gedreht[1] . ',' . $gedreht[0];

                                    $solr_data['lonlat'] = $combine_coordinates;
                                    $solr_data['locationGeo'] = str_replace(",", " ", $gedreht);
                                    $solr_data['georpt'] = "POINT (" . $solr_data['locationGeo'] . ")";
                                    $solr_data['lonlatidFacette'] = $solr_data['id'] . "$" . $gedreht . "$" . $solr_data['title'] . "$" . $solr_data['objectType'];
                                }
                                $solr_data['bcp'][] = $solr_data['id'] . "$" . $solr_data['title'];
                                $solr_data['bcpId'][] = $solr_data['id'];
                                // Assets aus dem Feld "Video" ermittelb
                                $solr_data['images'] = $this->getAssetData($id, $table, 'video');

                                if (!empty($solr_data) && isset($solr_data['id'])) {
                                    $this->addToSolr($solr_data, $this->solrCore);
                                }
                            }
                        }

                        break;
                }
            }
        }
    }
}
