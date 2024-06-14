<?php
namespace JO\JoMuseo\Controller;

use JO\JoMuseo\Utility\Controller\MuseoControllerUtility;
use JO\JoMuseo\Utility\Controller\QueryUtility;
use JO\JoMuseo\Utility\Controller\SearchUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ContentController extends ActionController
{
    // Utilityklassen einbinden -> definition als Trait im Utility Ordner -> JO\JoMuseo\Utility\Controller\MuseoControllerUtility
    use MuseoControllerUtility;
    use SearchUtility;
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

    public $joSolrCore = null; // Solrcore

    public $limit = 1; //    Standardlimit für die Ausgabe der Datensätze

    public $query_params = []; //    Konfiguration des Solrqueries

    // Konfiguration
    protected $config = [];

    /**
     * @param \JO\JoMuseo\Domain\Repository\SolrRepository $solrRepository
     */
    public function injectSolrRepository(\JO\JoMuseo\Domain\Repository\SolrRepository $solrRepository)
    {
        $this->solrRepository = $solrRepository;
    }

    public function initializeView($view)
    {
        $this->config = $this->settings;
    }

    public function initializeAction() {}

    /**
     * Objekt des Monats, Tages oder zufälliges Objekt
     *
     *
     * @return void
     */
    public function selectobjectfrompoolAction(): void
    {
        // Anzahl der auszugebenden Objekte - die Limitierung ist bei 100 festgesetzt
        if ($this->config['limit']) {
            $limit = intval($this->config['limit']);
            if (is_int($limit) && $limit > 0 && $limit < 100) {
                $this->limit = $limit;
            }
        }

        if ($this->config['externaldatasource']) {
            switch ($this->config['externaldatasource']) {
                // DigiCult Lastitems
                case 1:
                    $url = "/metadata_repository_index/0.0.1/get_last_items.php";
                    $data = file_get_contents($url);
                    if ($data) {
                        $data_array = json_decode($data, 1);
                        if (is_array($data_array) && isset($data_array['item'])) {
                            $result = array_slice($data_array['item'], 0, $this->limit);
                        }
                    }
                    break;
            }
        } else {
            $this->setSolrCore();

            $query = GeneralUtility::makeInstance(QueryUtility::class);

            $joSearcharraycomplete['content']['imageonly'] = 1;

            $all_query = $query
                ->setRequestObject($this->request)
                ->setConfig($this->config)
                ->setSearchsession($joSearcharraycomplete)
                ->buildQuery();

            // "criteria" kommt aus der Flexform des Plugins und enthält fortlaufende Nummern, die nachfolgend interpretiert werden
            if ($this->config['criteria']) {
                switch ($this->config['criteria']) {
                    // Suche nach Objekt(en) von "heute vor x Jahren"
                    case 1:
                        $current_time_transformed = date("m-d", time());
                        $query_part_flexform = 'showtime:*-' . $current_time_transformed . ' AND NOT timeline:[1933 TO 1945]';
                        break;
                    // Suche nach Objekt(en) aus dem aktuellen Monat vor x Jahren an beliebigen Tagen
                    case 2:
                        $current_time_transformed = date("m", time());
                        $query_part_flexform = 'showtime:*-' . $current_time_transformed . '-*' . ' AND NOT timeline:[1933 TO 1945]';
                        break;
                }
                $all_query[] = $query_part_flexform;
            }

            if ($this->config['fulltext']) {
                $all_query[] = 'fulltext:' . $this->config['fulltext'];
            }

            $this->query_params = [
                'solr' => $this->joSolrCore,
                'limit' => $this->limit,
                'start' => 1,
                'fl' => null,
                'fq' => null,
                'q' => $all_query,
                'f' => null,
            ];

            $result = $this->solrRepository->contactSolr($this->query_params);

            if ($result) {
                $result = json_decode($result);
                $result->response->docs = $this->modifyResults($result->response->docs);
            } else {
                exit("solr connection failed");
            }
        }
        $this->view->assign('result', $result);
    }
}
