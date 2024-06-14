<?php
return [
    'ctrl' => [
        'title' => 'Allgemeine Objektentitäten (Objekt, Person oder Geografika)',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => true,
        'iconfile' => 'EXT:jo_museo/Resources/Public/Icons/Extension.svg',
        'sortby' => 'sorting',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'default_sortby' => 'ORDER BY sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
    ],
    'sorting' => [
        'label'   => 'sorting',
        'config'  => [
            'type' => 'input',
            'size' => 11,
            'default' => 0,
            'eval' => 'num'
        ]
    ],
    'columns' => [
        'title' => [
            'label' => 'Titel des Objektes',
            'config' => [
                'type' => 'input',
                'size' => '255'
            ]
        ],
        'stipendiat' => [
            'label' => 'Stipendiat',
            'config' => [
                'type' => 'input',
                'size' => '255'
            ]
        ],
        'objecttype' => [
            'label' => 'Objektentität',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'maxitems' => 1,
                'items' => [
                    ['Person', 'Person'],
                    ['Geografika', 'Ort'],
                    ['Objekt', 'Objekt'],
                ],
            ],
        ],
        'hidden' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
            ],
        ],
        'shorttext' => [
            'label' => 'Teasertext',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'bodytext' => [
            'label' => 'Beschreibender Text',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'datierungstart' => [
            'label' => 'Datierung (Beginn)',
            'config' => [
                'type' => 'input',
                'size' => '255',
                'placeholder' => 'YYYY-MM-TT oder YYYY-MM oder YYYY'
            ]
        ],
        'datierungend' => [
            'label' => 'Datierung (Ende)',
            'config' => [
                'type' => 'input',
                'size' => '255',
                'placeholder' => 'YYYY-MM-TT oder YYYY-MM oder YYYY'
            ]
        ],
        'linktosite' => [
            'label' => 'Link zur Seite',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink'
            ]
        ],
        'linkvideo' => [
            'label' => 'Link zum Video',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink'
            ]
        ],
        'geoplace' => [
            'label' => 'Geo Standort',
            'config' => [
                'type' => 'input',
                'size' => '255',
                'placeholder' => 'Jena oder https://www.geonames.org/2895044'
            ]
        ],
        'geoplacegeojson' => [
            'label' => 'Koordinaten des Objektes als geoJSON',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'roommodel' => [
            'label' => 'Room Model',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink'
            ]
        ],
        'audio' => [
            'label' => 'Verknüpfte Audiodatei',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('audio', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.audio.add',
                ],
                'overrideChildTca' => [
                    'types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
                                            --palette--;;audioOverlayPalette,
                                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
                                            --palette--;;videoOverlayPalette,
                                            --palette--;;filePalette',
                        ],
                    ]
                ],
            ], 'mp3,wav,mp4,m4a'),
        ],
        'video' => [
            'label' => 'Videoinformationen zum Objekt',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('video', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'Videodatei hinzufügen',
                ],
                'overrideChildTca' => [
                    'types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
                                --palette--;;audioOverlayPalette,
                                --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
                                --palette--;;videoOverlayPalette,
                                --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                    ]
                ],
            ], 'mp4,m4a,youtube,vimeo,jpg,jpeg'),
        ],
        'jsonstring' => [
            'label' => 'JSON String',
            'config' => [
                'type' => 'input',
                'size' => '255'
            ]
        ],
        'exhibitcta' => [
            'label' => 'Einführender Text für die Überleitung auf die verknüpften Exponate',
            'config' => [
                'type' => 'input',
                'size' => '255'
            ]
        ],
        'additionalproperties' => [
            'label' => 'Erweitere Eigenschaften',
            'config' => [
                'type' => 'flex',
                'ds' => [
                    'default' => 'FILE:EXT:jo_museo/Configuration/Flexform/Locations.xml',
                ],
            ],
        ],
        'objectstorage' => [
            'label' => 'Storage, in der sich die Exponate befinden',
            'config' => [
              'type' => 'group',
              'internal_type' => 'db',
              'allowed' => 'pages',
              'maxitems' => 1,
              'minitems' => 0,
              'size' => 1,
              'default' => 0,
              'suggestOptions' => [
                 'default' => [
                    'additionalSearchFields' => 'nav_title, alias, url',
                    'addWhere' => 'AND pages.doktype = 1'
                 ]
              ]
           ]
        ],
        'exhibit' => [
            'label' => 'Zugeordnete Exponate/Objekte in der entsprechenden Reihenfolge',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_jomuseo_domain_model_exhibit',
                'MM' => 'tx_jomuseo_domain_model_section_exhibit_mm',
                'foreign_table_where' => ' AND tx_jomuseo_domain_model_exhibit.pid=###REC_FIELD_objectstorage###',
                'minitems' => 0,
                'maxitems' => 100,
                'size' => 30,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'title, objecttype, stipendiat, shorttext, bodytext , datierungstart, datierungend, jsonstring, --div--;Interne und externe Referenzen, linktosite, linkvideo, roommodel, audio, video, objectstorage, exhibitcta, exhibit, --div--;Geografische Verortung, geoplace, --div--;Zusätzliche Eigenschaften des Objekts, additionalproperties, --div--;Sprache und Zeitsteuerung, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, starttime, endtime'
        ]
    ]
];
