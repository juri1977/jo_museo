<?php
namespace JO\JoMuseo\Utility\Mapping;

class Mapping
{

    protected $field_mapping = [
        'solr_ris' => [
            'entity#aut' => 'A1',
            'showtime' => 'DA',
            'entity#aut' => 'PB',
            'title' => 'TI',
            'urn' => 'UR',
            'id' => 'ID',
            'contextorig' => 'UR',
            'note' => 'N1',
            'abstract' => 'AB',
            'titleAlt' => 'T2',
            'entity#author' => 'AU',
            'language' => 'LA',
        ],
        'ris_ty_classification' => [
            'Karte' => 'MAP',
            'journal' => 'JFULL',
            'Drucke' => 'CLSWK',
        ],
    ];

    public function __construct() {}

    /**
     *    R�ckgabe eines RIS-Keys aus dem Solrfield
     *
     *    @var string $field_name SolrField zu dem die RIS Entsprechung gesucht wird
     *    @var string $convert_from Richtung in die die Mappings gefunden werden sollen
     *    @var array $latlon_array_pois Array aus Geokordinaten der POIs in der Form:  [0] => 50.6964,11.59595 [1] => 50.6973,11.59785
     *    @return String
     */
    public function getMappedField($field_name = null, $convert_from = 'solr_to_ris')
    {
        if (null != $field_name) {
            if (array_key_exists($field_name, $this->field_mapping['solr_ris'])) {
                return $this->field_mapping['solr_ris'][$field_name];
            }
        }
        return null;
    }
}
