<?php
namespace JO\JoMuseo\Utility\Fuzzysearchutils;

class Makesolrquery
{
    protected $query_params = [
		        'solr' => null,
		        'limit' => 50,
		        'start' => 1,
		        'fl' => 'id',
		        'fq' => null,
		        'q' => null,
		        'f' => null,
		        'sort' => null,
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

    public function __construct()
    {
    }

    public function setLimit($limit = 50){
    	if(is_int($limit) && $limit < 5000){
    		$this->query_params['limit'] = $limit;
    	}
    	return $this; 
    }

    public function setStart($start = 1){
    	if(is_int($start) && $start > 0){
    		$this->query_params['startpoint'] = $start;
    	}
  		return $this; 
    }

    public function setSolr($solr = ''){
    	$this->query_params['solr'] = $solr;
    	return $this; 
    }

    public function setFieldlist($fieldlist = ''){
    	$this->query_params['fl'] = $fieldlist;
    	return $this; 
    }

    public function setQuery($query = array()){
    	if(is_array($query) && !empty($query)){
    		$this->query_params['q'] = $query;
    	}
    	return $this; 
    }

    public function setHighlighting($highlight = array()){
    	$this->query_params['highlight'] = $highlight;
    	return $this; 
    }

    public function setFQuery($fquery = ''){
    	$this->query_params['fq'] = $fquery;
    	return $this; 
    }

    public function setFacettes($facettes = array()){
    	if(is_array($facettes) && !empty($facettes)){
    		$this->query_params['f'] = $facettes;
    	}
    	return $this; 
    }

    public function setSorting($sort = array()){
    	if(is_array($sort) && !empty($sort)){
    		$this->query_params['sort'] = $sort;
    	}
    	return $this; 
    }

    public function generateQuery()
    {
    	if($this->query_params['solr'] != null && 
    		!empty($this->query_params['q'])
    	){
    		return $this->query_params;
    	} else {
    		return 'Wrong configuration -  query or solr missing' ;
    	}
    }
}
