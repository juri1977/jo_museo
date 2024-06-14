<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'sortby' => 'sorting',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'datatype, title, subtitle, description, contact, image, classfication, keywords, geodata, geotext, gnddata, isilnummer, tenantreference, idreference, website, externlink, outerlink, externalstock, localstatus',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('jo_museo') . 'Resources/Public/Icons/Extension.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, notlocalstatus, datatype, title, subtitle, shorttitle, description, contact, image, bannerimage, classfication, keywords, geodata, geodtext, gnddata, isilnummer, tenantreference, idreference, website, externlink, outerlink, externalstock, barrierfree, morelinklabel, morelinkimg, datastorage',
    ],
    'types' => [
        '1' => ['showitem' => 'datatype, active, --palette--;;paletteTitleAndShort, subtitle, description, descriptionlong, social, contact, subobjectstitle, relateditems, relatedsingleitemsobjectstorage, relatedsingleitemstitle, relatedsingleitems, morelinklabel, morelinkimg, datastorage, tenantreference,
            --div--;Abbildungen - Banner und Slideshows, image, bannerimage, moreimagestitle, moreimages, 
            --div--;Filter/Klassifikationen, topicdb, keywords, times, classfication, 
            --div--;Digitale Angebote, metas, 
            --div--;Sonstiges - nicht relevant/nicht benutzt, notlocalstatus, geodata, geotext, gnddata, isilnummer, idreference, website, externlink, externalstock, barrierfree,
            --div--;Alternative, outerlink,
            --div--;Zugriff, starttime, endtime'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
        'paletteTitleAndShort' => [
            'showitem' => 'title,shorttitle,',
        ],
    ],
    'columns' => [
        /* 'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0],
                ],
            ],
        ], */
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_jomuseo_domain_model_institute',
                'foreign_table_where' => 'AND tx_jomuseo_domain_model_institute.pid=###CURRENT_PID### AND tx_jomuseo_domain_model_institute.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'active' => [
            'label' => 'Projekt/Sammlung ist aktiv',
            'config' => [
                'type' => 'check',
            ],
        ],
        'hidden' => [
            'exclude' => 1,
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
        'notlocalstatus' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.notlocalstatus',
            'config' => [
                'type' => 'check',
            ],
        ],
        'datatype' => [
            'label' => 'Datentyp',
            'onChange' => 'reload',
            'config' => [
                'type' => 'radio',
                'items' => [
                    ['Sammlung', 'Sammlung'],
                    ['Projekt', 'Projekt'],
                    ['Person', 'Person']
                ],
            ],
        ],
        'topicdb' => [
            'label' => 'Objekttyp DB',
            'description' => 'Wählen Sie den Objekttyp für das Exponat aus der in der Suche berücksichtigt wird.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_jomuseo_domain_model_topic',
                'MM' => 'tx_jomuseo_domain_model_topic_mm',
                'minitems' => 0,
                'maxitems' => 100,
                'size' => 30,
            ],
        ],
        'times' => [
            'label' => 'Zeitraum',
            'description' => 'Wählen Sie den Zeitraum für das Exponat aus was in der Suche berücksichtigt wird.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_jomuseo_domain_model_times',
                'MM' => 'tx_jomuseo_domain_model_times_mm',
                'minitems' => 0,
                'maxitems' => 100,
                'size' => 30,
            ],
        ],
        'title' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'shorttitle' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.shorttitle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'subtitle' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.subtitle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'externlink' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.externlink',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'eval' => 'trim',
            ],
        ],
        'outerlink' => [
            'label' => 'Externer Link - Alternative',
            'description' => 'Achtung! Die Verlinkung zum Detail wird durch diesen Link ersetzt.',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'eval' => 'trim',
            ],
        ],
        'description' => [
            'label' => 'Teaser (Übersichtsseite)',
            'description' => 'An dieser Stelle können Sie eine kurze Beschreibung des Datensatzes hinterlegen. Er wird als Teasertext an den entsprechenden Stellen ausgegeben.',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'descriptionlong' => [
            'label' => 'Langbeschreibung',
            'description' => 'Hier kann die Langbeschreibung des Datensatzes abgelegt werden.',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'subobjectstitle' => [
            'label' => 'Titel zu zugehöringe Objekten',
            'config' => [
                'type' => 'input',
                'default' => 'Untersammlung',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'relateditems' => [
            'label' => 'Verknüpfung mit einem Ordner, der zugehörige Objekte enthält',
            'description' => 'In dem Ordner können sowohl Kindelemente, wie zum Beispiel untergeordnete Sammlungen oder Projekte, als auch unspezifisch verknüpfte Objekte liegen.',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => '5',
                'maxitems' => '5',
                'minitems' => '0',
                'show_thumbs' => '1',
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                    ],
                ],
            ],
        ],
        'relatedsingleitemsobjectstorage' => [
            'onChange' => 'reload',
            'label' => 'Storage, in der sich einzelne, zugeordnete Exponate/Objekte befinden',
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
        'relatedsingleitemstitle' => [
            'label' => 'Titel zu Zugeordnete Exponate/Objekte',
            'config' => [
                'type' => 'input',
                'default' => 'Einzelne zugehörige Sammlungen/Projekte',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'relatedsingleitems' => [
            'label' => 'Zugeordnete Exponate/Objekte in der entsprechenden Reihenfolge',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_jomuseo_domain_model_institute',
                'foreign_table_where' => ' AND tx_jomuseo_domain_model_institute.pid=###REC_FIELD_relatedsingleitemsobjectstorage### AND tx_jomuseo_domain_model_institute.hidden = 0',
                'minitems' => 0,
                'maxitems' => 100,
                'size' => 30,
            ],
        ],
        'contact' => [
            'label' => 'Infobox',
            'label' => 'Hier kann die Infobox mit Inhalt befüllt werden.',			
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'geodata' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.geodata',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'geotext' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.geotext',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'tenantreference' => [
            'displayCond' => [
                'OR' => [
                    'FIELD:datatype:=:Sammlung',
                    'FIELD:datatype:=:Projekt'
                ],
            ],
            'label' => 'Verknüpfung mit den Projekten oder Beständen',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'selectedListStyle' => 'width: 1000px;',
                'itemListStyle' => 'width: 1000px;',
                "size" => 10,
                "minitems" => 0,
                "maxitems" => 1,
                "itemsProcFunc" => "JO\JoMuseo\Controller\ElementsController->getSolrDataForFlexform",
            ],
        ],
        'idreference' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.idref',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'gnddata' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.gnddata',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'isilnummer' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.isilnummer',
            'config' => [
                'type' => 'input',
                'size' => 150,
                'eval' => 'trim',
            ],
        ],
        'website' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.website',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'externalstock' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.externalstock',
            'config' => [
                'type' => 'input',
                'size' => '255',
            ],
        ],
        'classfication' => [
            'label' => 'Institution',
            'description' => 'Wählen Sie die Institution für das Exponat aus die in der Suche berücksichtigt werden.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_jomuseo_domain_model_instituteclass',
                'MM' => 'tx_jomuseo_domain_model_instituteclass_mm',
                'minitems' => 0,
                'maxitems' => 100,
                'size' => 30,
            ],
        ],
        'keywords' => [
            'label' => 'Schlagwörter',
            'description' => 'Wählen Sie die Schlagwörter für das Exponat aus die in der Suche berücksichtigt werden.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_jomuseo_domain_model_keywords',
                'MM' => 'tx_jomuseo_domain_model_keywords_mm',
                'minitems' => 0,
                'maxitems' => 100,
                'size' => 30,
            ],
        ],
        'bannerimg' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.bannerimg',
            'config' => [
                'type' => 'check',
            ],
        ],
        'image' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('image', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.image.add',
                ],
                'maxitems' => 1,
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                    ],
                ],
            ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],
        'bannerimage' => [
            'label' => 'Bannerbilder für die Detailansicht',
            'description' => 'Diese Abbildungen werden für die Bildbanner in der Detailansicht benutzt',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('bannerimage', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.image.add',
                ],
                'maxitems' => 1,
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                    ],
                ],
            ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],
        'moreimagestitle' => [
            'label' => 'Titel zu weiteren Abbildungen',
            'config' => [
                'type' => 'input',
                'default' => 'Highlights der Sammlung/des Projektes',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'moreimages' => [
            'label' => 'Weitere Abbildungen',
            'description' => 'Diese Abbildungen können als Slideshow oder als Galerie dargestellt werden.',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('moreimages', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.image.add',
                ],
                'maxitems' => 20,
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                    ],
                ],
            ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],
        'barrierfree' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.barrierfree',
            'config' => [
                'type' => 'check',
            ],
        ],
        'datastorage' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.datastorage',
            'description' => 'Wählen Sie weiter/zugehörige Exponate, Projekte oder Sammlungen aus',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => '5',
                'maxitems' => '5',
                'minitems' => '0',
                'show_thumbs' => '1',
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                    ],
                ],
            ],
        ],
        'morelinklabel' => [
            'label' => 'Titel - weiter Infos',
            'description' => 'Überschrift für die Teaserboxen',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'morelinkimg' => [
            'label' => 'Abbildungen - weiter Infos',
            'description' => 'Diese Abbildungen werden als Teaserboxen mit Verlinkung dargestellt',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('morelinkimages', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.image.add',
                ],
                'maxitems' => 2,
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                    ],
                ],
            ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],
        'metas' => [
            'exclude' => 1,
            'label' => 'Digitale Angebote',
            'description' => 'Stellen Sie hier weiter Informationen in Form von Bild, Text und weiteren Verlinkungen ein.',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_jomuseo_domain_model_meta',
                'foreign_field' => 'parentid',
                'foreign_table_field' => 'parenttable',
                'foreign_sortby' => 'sorting',
                'maxitems' => 20,
                'appearance' => [
                    'collapseAll' => 1,
                ],
            ],
        ],
        'social' => [
            'exclude' => 1,
            'label' => 'Sozial Links',
            'description' => 'Verlinkungen zu Facebook, Twitter etc.',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_jomuseo_domain_model_social',
                'foreign_field' => 'parentid',
                'foreign_table_field' => 'parenttable',
                'maxitems' => 20,
                'appearance' => [
                    'collapseAll' => 1,
                ],
            ],
        ],
    ],
];
