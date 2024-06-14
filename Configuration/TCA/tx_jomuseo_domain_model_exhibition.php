<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'versioning_followPages' => true,
        'sortby' => 'sorting',
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'transOrigPointerField' => 'l10n_parent',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,subtitle,section, links, tags, location, entity, kontextinfo',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('jo_museo') . 'Resources/Public/Icons/Extension.svg',
    ],
    'interface' => [
        
    ],
    'types' => [
        '1' => ['showitem' => 'title, subtitle, derivate, --palette--;;maincolordefinition, 
            --div--;Introtext und Audio, summary, audio, 
            --div--;Einstiegsvideo, intro,
            --div--;Einstieg in die digitale Ausstellung, tosectiondesc, tosectionbtntext, tosectionimg, 
            --div--;Über die Ausstellung, aboutproject, aboutprojectcolor, 
            --div--;Zitierempfehlung, zitiervorschlag, zitiervorschlagcolor, 
            --div--;Literaturangaben, infotextetitle, infotexte, infotextecolor, 
            --div--;Kontaktdaten, place, placecolor,
            --div--;Konfiguration der Ausstellung, configuration,
            --div--;Informationen ungeordnet, period, video, openinghours, section, flyer, links, kontextinfo, tags, location, entity, jsonfile, vorschauseite, maincolorlinkfont, fontcolor,
            --div--;Sprache und Zeitsteuerung, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, starttime, endtime
        ']
    ],
    'palettes' => [
        'maincolordefinition' => [
          'label' => 'Grundlegende Farbgebung der Ausstellung',
          'showitem' => 'maincolor, maincolorfont, maincolorborder',
       ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_jomuseo_domain_model_exhibition',
                'foreign_table_where' => 'AND tx_jomuseo_domain_model_exhibition.pid=###CURRENT_PID### AND tx_jomuseo_domain_model_exhibition.sys_language_uid IN (-1,0)',
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
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 16,
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'endtime' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 16,
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'title' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'configuration' => [
            'label' => 'Konfiguration der Darstellung der Ausstellung, der Sektionen und der Exponate',
            'config' => [
                'type' => 'flex',
                'ds' => [
                    'default' => 'FILE:EXT:jo_museo/Configuration/Flexform/Exhibition.xml',
                ],
            ],
        ],
        'subtitle' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.subtitle',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_jomuseo_domain_model_data',
                'foreign_field' => 'parentid',
                'foreign_table_field' => 'parenttable',
                'foreign_match_fields' => [
                    'fieldname' => 'subtitle',
                ],
                'maxitems' => 99,
                'appearance' => [
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ],
            ],
        ],
        'period' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.period',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'placeholder' => '10.12.2022 - 15.12.2022'
            ],
        ],
        'place' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.place',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'openinghours' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.openinghours',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'summary' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.summary',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'aboutproject' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.aboutproject',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'zitiervorschlag' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.zitiervorschlag',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'infotextetitle' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.infotextetitle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'infotextecolor' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.infotextecolor',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker'
            ],
        ],
        'infotexte' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.infotexte',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_jomuseo_domain_model_data',
                'foreign_field' => 'parentid',
                'foreign_table_field' => 'parenttable',
                'foreign_match_fields' => [
                    'fieldname' => 'infotext',
                ],
                'maxitems' => 99,
                'appearance' => [
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ],
            ],
        ],
		'video' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.video',
            'config' => [
                'type' => 'text',
            ],
        ],
        'section' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.section',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_jomuseo_domain_model_section',
                'MM' => 'tx_jomuseo_domain_model_exhibition_section_mm',
                'foreign_table_where' => ' AND tx_jomuseo_domain_model_section.pid=###CURRENT_PID###',
                'minitems' => 0,
                'maxitems' => 30,
                'size' => 30,
            ],
        ],
        'intro' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.intro',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('intro', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.intro.add',
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
                    ],
                ],
            ], 'mp3,wav,mp4'),
        ],
        'audio' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.audio',
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
                    ],
                ],
            ], 'mp3,wav,mp4'),
        ],
        'flyer' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.flyer',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('flyer', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.flyer.add',
                ],
            ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],
        'tosectionimg' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.tosectionimg',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('tosectionimg', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.tosectionimg.add',
                ],
            ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],
        'tosectiondesc' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.tosectiondesc',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'tosectionbtntext' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.tosectionbtntext',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'placeholder' => 'Klicken Sie hier, um zur Ausstellung zu gelangen'
            ],
        ],
        'links' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibit.links',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'kontextinfo' => [
            'label' => 'Kontextinformationen',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'tags' => [
            'label' => 'Tags',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'location' => [
            'label' => 'Orte',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'entity' => [
            'label' => 'Personen',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'placecolor' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.placecolor',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker'
            ],
        ],
        'aboutprojectcolor' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.aboutprojectcolor',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker'
            ],
        ],
        'zitiervorschlagcolor' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.zitiervorschlagcolor',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonfile' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.jsonfile',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('jsonfile', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.jsonfile.add',
                ],
            ], 'json'),
        ],
        'derivate' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.derivate',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('derivate', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.derivate.add',
                ],
            ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],

        'maincolor' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.maincolor',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker',
            ],
        ],
        'maincolorborder' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.maincolorborder',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker',
            ],
        ],
        'maincolorfont' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.maincolorfont',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker',
            ],
        ],
        'maincolorlinkfont' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.maincolorlinkfont',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker',
            ],
        ],
        'fontcolor' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.fontcolor',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker',
            ],
        ],
        'bggradient1' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.bggradient1',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker',
            ],
        ],
        'bggradient2' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.bggradient2',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker',
            ],
        ],
        'fontcolordetail1' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.fontcolordetail1',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'renderType' => 'colorpicker',
            ],
        ],
        'vorschauseite' => [
            'label' => 'Bei der Vorschau Text ausrichtung rechts',
            'config' => [
                'type' => 'check',
            ],
        ],
    ],
];
