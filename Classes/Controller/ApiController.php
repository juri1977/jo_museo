<?php
declare (strict_types = 1);
namespace JO\JoMuseo\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ApiController extends MuseoController
{
    public function apiAction()
    {
        $this->query_params['sort'] = [];
        $this->query_params['start'] = 1;

        $arguments = filter_var_array(GeneralUtility::_GP('tx_jomuseo_pi1009'), FILTER_SANITIZE_STRING);

        if ($arguments['joDetailView']) {
            $this->query_params['q'][0] = 'id:' . $arguments['joDetailView'];

            $this->joSolrResult = json_decode($this->solrRepository->contactSolr($this->query_params));

            $style = filter_var(GeneralUtility::_GP('style'), FILTER_SANITIZE_STRING);

            switch ($style) {
                case 'xml':
                    $output = $this->buildXMLOutput();
                    break;
                default:
                    $output = ['output' => 'Kein Style vorhanden.'];
            }
        } else {
            $output = ['output' => 'Keine ID definiert.'];
        }

        if ('xml' == $style && $arguments['joDetailView']) {
            $this->view->assign('output', $output->saveXML());
        } else {
            $this->view->assign('output', $output);
        }
    }

    /*
     * erstelle dom elemente und füge diese zu einem hinzu
     *
     * @var $dom -> domdocument
     * @var $elements -> array mit elementen welche gebaut werden sollen
     * @var $appendTo -> element zu welchen die neuen elemente hinzugefügt werden
     *
     */
    public function createXMLNodeAppend($dom, $elements, &$appendTo)
    {
        $elementsArr = $this->createXMLNode($dom, $elements);

        foreach ($elementsArr as $key => $value) {
            $appendTo->appendChild($value);
        }
    }

    /*
     *
     * bauvorschrift für array
     *
     * $elements
     * [[
     *  name: '',
     *  attributes: [name: value, name: value],
     *  elements: [
     *   ...
     *  ],
     *  value: ''
     * ]]
     *
     *
     */
    public function createXMLNode($dom, $elements)
    {
        $out = [];

        if ($dom && is_array($elements) && !empty($elements)) {
            foreach ($elements as $key => $value) {
                if ($value['name']) {
                    $tmpElement = $titleInfo = $dom->createElement($value['name']);

                    if ($value['attributes'] && is_array($value['attributes'])) {
                        foreach ($value['attributes'] as $attr_key => $attr_val) {
                            $tmpAttr = $dom->createAttribute($attr_val['name']);
                            $tmpAttr->value = $attr_val['value'];
                            $tmpElement->appendChild($tmpAttr);
                        }
                    }

                    if ($value['value']) {
                        $tmpElement->nodeValue = $value['value'];
                    }

                    if (!$value['value'] && $value['elements'] && is_array($value['elements'])) {
                        $this->createXMLNodeAppend($dom, $value['elements'], $tmpElement);
                    }

                    $out[] = $tmpElement;
                }
            }
        }

        return $out;
    }

    /*
     * search in noteBundled Array
     *
     * @var $noteBundled - array
     * @var $search - to search string
     *
     * return string - value
     */
    public function findInNodebundled($noteBundled, $search)
    {
        if (is_array($noteBundled) && !empty($noteBundled)) {
            foreach ($noteBundled as $key => $value) {
                $arr = explode('$', $value);

                if ($arr[0] == $search) {
                    return $arr[1];
                }
            }
        }

        return '';
    }

    /*
     * search in array for the element
     *
     * @var $elements - elements array
     * @var $search - name to search
     *
     * return int - position
     */
    public function searchElement($elements, $search)
    {
        if (is_array($elements) && !empty($elements)) {
            foreach ($elements as $key => $value) {
                if ($value['name'] == $search) {
                    return $key;
                }
            }
        }

        return -1;
    }

    /*
     * search in array for the element and append children
     *
     * @var $elementsArray - root elements array
     * @var $name - name to search
     * @var $childArray - array to append
     *
     * return int - position
     */
    public function createOrAppendTo(&$elementsArray, $name, $childArr)
    {
        $pos = $this->searchElement($elementsArray, $name);

        if (-1 == $pos) {
            $elementsArray[] = [
                'name' => $name,
                'elements' => $childArr,
            ];
        } else {
            $merged = array_merge($elementsArray[$pos]['elements'], $childArr);

            $elementsArray[$pos]['elements'] = $merged;
        }
    }

    /*
     * XML Output
     *
     */
    public function buildXMLOutput()
    {
        $results = $this->joSolrResult->response->docs[0];

        $dom = new \DOMDocument('1.0', 'UTF-8');

        // comment
        $commentString = '
Die ist eine API Info. CC0 http://creativecommons.org/publicdomain/zero/1.0/
';
        $commentNode = $dom->createComment($commentString);
        $dom->appendChild($commentNode);

        // namespace
        $root = $dom->createElementNS('http://www.loc.gov/mods/v3', 'mods:mods');

        // id
        $idAttr = $dom->createAttribute('id');
        $idAttr->value = $results->id;
        $root->appendChild($idAttr);

        $elementsArray = [];

        // inventarnummer
        if ($results->inventarnummer) {
            $elementsArray[] = [
                'name' => 'mods:location',
                'elements' => [
                    [
                        'name' => 'mods:shelfLocator',
                        'value' => $results->inventarnummer,
                    ],
                ],
            ];
        }

        // title
        if ($results->title) {
            $elementsArray[] = [
                'name' => 'mods:titleInfo',
                'attributes' => [
                    [
                        'name' => 'usage',
                        'value' => 'primary',
                    ],
                ],
                'elements' => [
                    [
                        'name' => 'mods:title',
                        'value' => $results->title,
                    ],
                ],
            ];
        }

        // alt title
        if ($results->titleAlt && !empty($results->titleAlt)) {
            $altTitleArr = [
                'name' => 'mods:titleInfo',
                'attributes' => [
                    [
                        'name' => 'usage',
                        'value' => 'alternative',
                    ],
                ],
                'elements' => [],
            ];

            foreach ($results->titleAlt as $key => $value) {
                $altTitleArr['elements'][] = ['name' => 'mods:title', 'value' => $value];
            }

            $elementsArray[] = $altTitleArr;
        }

        // contextorig -> url zu home
        if ($results->contextorig) {
            $elementsArray[] = [
                'name' => 'mods:identifier',
                'attributes' => [
                    [
                        'name' => 'type',
                        'value' => 'uri',
                    ],
                ],
                'value' => $results->contextorig,
            ];
        }

        // scale oder locations coords
        if ($results->scale || $results->locationPolygones) {
            $locPolyArr = [
                'name' => 'mods:subject',
                'elements' => [
                    [
                        'name' => 'mods:cartographics',
                        'elements' => [],
                    ],
                ],
            ];

            if ($results->scale) {
                $locPolyArr['elements'][0]['elements'][] = ['name' => 'mods:scale', 'value' => $results->scale];
            }

            if ($results->locationPolygones && !empty($results->locationPolygones)) {
                $locPolyArr['elements'][0]['elements'][] = ['name' => 'mods:coordinates', 'value' => $results->locationPolygones[0]];
            }

            $elementsArray[] = $locPolyArr;
        }

        // personen
        if ($results->entity && !empty($results->entity)) {
            foreach ($results->entity as $key => $value) {
                $nameArr = explode('$', $value);

                $namePart = explode(',', $nameArr[0]);
                if (is_array($namePart) && !empty($namePart)) {
                    $namePart = array_map('trim', $namePart);
                }

                $rolle = $nameArr[1];

                $entityArr = [
                    'name' => 'mods:name',
                    'attributes' => [
                        [
                            'name' => 'type',
                            'value' => 'personal',
                        ],
                    ],
                    'elements' => [],
                ];

                foreach ($namePart as $kk => $vv) {
                    $entityArr['elements'][] = ['name' => 'mods:namePart', 'value' => $vv];
                }

                if ($rolle && '' != $rolle) {
                    $rolleArr = [
                        'name' => 'mods:roleTerm',
                        'attributes' => [
                            [
                                'name' => 'authority',
                                'value' => 'marcrelator',
                            ],
                            [
                                'name' => 'type',
                                'value' => 'code',
                            ],
                        ],
                        'value' => $rolle,
                    ];

                    $entityArr['elements'][] = ['name' => 'mods:role', 'elements' => [$rolleArr]];
                }

                $elementsArray[] = $entityArr;
            }
        }

        // material
        if ($results->material && !empty($results->material)) {
            $materialArr = [];

            foreach ($results->material as $key => $value) {
                $materialArr[] = [
                    'name' => 'mods:note',
                    'attributes' => [
                        [
                            'name' => 'type',
                            'value' => 'content',
                        ],
                    ],
                    'value' => $value,
                ];
            }

            $this->createOrAppendTo($elementsArray, 'mods:physicalDescription', $materialArr);
        }

        // format
        if ($results->format && !empty($results->format)) {
            $format = [
                'name' => 'mods:note',
                'attributes' => [
                    [
                        'name' => 'type',
                        'value' => 'source_dimensions',
                    ],
                ],
                'value' => $results->format,
            ];

            $this->createOrAppendTo($elementsArray, 'mods:physicalDescription', [$format]);
        }

        if ($results->noteBundled && !empty($results->noteBundled)) {
            $umfang = $this->findInNodebundled($results->noteBundled, 'Umfang');

            if ('' != $umfang) {
                $umfang_el = [
                    'name' => 'mods:extent',
                    'value' => $umfang,
                ];

                $this->createOrAppendTo($elementsArray, 'mods:physicalDescription', [$umfang_el]);
            }

            $bsz = $this->findInNodebundled($results->noteBundled, 'Besitznachweis');

            if ('' != $bsz) {
                $bsz_el = [
                    'name' => 'mods:physicalLocation',
                    'attributes' => [
                        [
                            'name' => 'type',
                            'value' => 'current',
                        ],
                    ],
                    'value' => $bsz,
                ];

                $this->createOrAppendTo($elementsArray, 'mods:location', [$bsz_el]);
            }
        }

        if ($results->classificationtags && is_array($results->classificationtags) && !empty($results->classificationtags)) {
            $cls_el = [];

            foreach ($results->classificationtags as $key => $value) {
                $cls_el[] = [
                    'name' => 'mods:topic',
                    'value' => $value,
                ];
            }

            $this->createOrAppendTo($elementsArray, 'mods:subject', $cls_el);
        }

        $this->createXMLNodeAppend($dom, $elementsArray, $root);
        $dom->appendChild($root);

        return $dom;
    }
}
