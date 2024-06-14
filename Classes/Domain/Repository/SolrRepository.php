<?php
namespace JO\JoMuseo\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SolrRepository extends Repository
{
    /**
     *    Curlverbindung zum Solr
     */
    public function getSolrData($url = '', $user = '', $pw = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING,  '');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING,  '');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     *    Solr Query
     * @param array $query_params - Array mit den Daten des Solrqueries
     * $query_params['solr'] = {url}:{port}/solr/{core}/ -> link zum solr einschließlich core
     * $query_params['limit'] = 30 -> Anzahl der anzuzeigenden Treffer auf einer Seite
     * $query_params['start'] = 2 -> aktive Paginationseite
     * $query_params['q'] = array -> Mainquery
     * $query_params['f'] = array -> facettes
     * $query_params['sort'] = array(fieldname => direction) -> Sortierung
     *
     */
    public function contactSolr($query_params = [])
    {
        /*
        echo "<pre>";
        print_r($query_params);
        echo "</pre>";
        */
        if (!empty($query_params)) {
            $query_array = [];
            $solr = '';
            if (null != $query_params['solr']) {
                $solr = $query_params['solr'];
            }
            $handler = 'select';
            if (null != $query_params['qt']) {
                $handler = $query_params['qt'];
            }
            $query_array['solr'] = $solr . $handler . '?q=';
            $mainQuery = ['*:*'];
            if (!empty($query_params['q'])) {
                $mainQuery = $query_params['q'];
            }
            $joConcat = " AND ";
            $query_array['q'] = urlencode(implode($joConcat, array_filter($mainQuery))); //    Leere Elemente werden gelöscht
            $joSearchSonderzeichen = ['%22', '%27', '%5C'];
            $joReplaceSonderzeichen = ['"', '\'', '\\'];
            $query_array['q'] = str_replace($joSearchSonderzeichen, $joReplaceSonderzeichen, $query_array['q']); //    Anführungsstriche zurückkonvertieren
            $solrQueryhandler = GeneralUtility::makeInstance('JO\JoMuseo\Utility\Fuzzysearchutils\Joqueryhandler');
            $query_array['f'] = $solrQueryhandler->generateFacetteQuery($query_params);
            $query_array['return'] = '&wt=json&indent=true&omitHeader=true';
            if (null != $query_params['fl']) {
                 $query_array['fl'] = '&fl=' . $query_params['fl'];
            }
            $limit = 50;
            if (null !== $query_params['limit']) {
                $limit = $query_params['limit'];
            }
            $start = 0;
            if (null !== $query_params['start']) {
                $start = $query_params['start'];
                $start = $start * $limit - $limit;
            }
            if (null !== $query_params['startpoint']) {
                $start = $query_params['startpoint'];
            }
            $query_array['rows'] = '&rows=' . $limit;
            $query_array['start'] = '&start=' . $start;
            if (!empty($query_params['sort'])) {
                $query_array['sort'] = $solrQueryhandler->joSetOrdering($query_params['sort']);
            }
            if (!empty($query_params['highlight'])) {
                $query_array['highlight'] = $solrQueryhandler->joSetHighlighter($query_params['highlight']['fields'], $query_params['highlight']['fragsize'], $query_params['highlight']['snippets']);
            }
            if (!empty($query_params['spellcheck.q'])) {
                $query_array['spellcheck.q'] = '&spellcheck.q=' . urlencode($query_params['spellcheck.q']);
            }
            if (!empty($query_params['suggest.q'])) {
                $query_array['suggest.q'] = '&suggest.q=' . urlencode($query_params['suggest.q']);
            }
            if (!empty($query_params['fq'])) {
                $query_array['fq'] = '&fq=' . $query_params['fq'];
            }
            /*
             if (!empty($query_params['cfq'])) {
                $query_array['cfq'] = '&suggest.cfq=' . $query_params['cfq'];
            }
            */
			$joSolrQuery = implode('', $query_array); 
            $joResult = $this->getSolrData($joSolrQuery);

            return $joResult; 
        }
    }
}
