<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_section',
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
        'translationSource' => 'l10n_source',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,subtitle,kontextinfo,links,tags,location,entity,exhibit',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('jo_museo') . 'Resources/Public/Icons/Extension.svg',
    ],
    'interface' => [
        
    ],
    'types' => [
        '1' => ['showitem' => 'title, subtitle, startdate, view, configuration, teaser, description, nextsectiontext,
            --div--;Abbildungen, derivate, 
            --div--;Zugehörige Audiodateien, audio, 
            --div--;Zugehörige Objekte/Exponate, exhibit, 
            --div--;Sonstiges, literature, kontextinfo, links, tags, location, entity, 
            --div--;Sprache und Zeitsteuerung, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, starttime, endtime,'
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
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
                'foreign_table' => 'tx_jomuseo_domain_model_section',
                'foreign_table_where' => 'AND tx_jomuseo_domain_model_section.pid=###CURRENT_PID### AND tx_jomuseo_domain_model_section.sys_language_uid IN (-1,0)',
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
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_section.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'subtitle' => [
            'label' => 'Ein oder mehrere Untertitel der Sektion',
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
        'startdate' => [
            'label' => 'Startdatum',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'view' => [
            'label' => 'Darstellung der Sektionsübersicht',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'maxitems' => 1,
                'items' => [
                    ['Ein oder mehrere Bilder vollflächig ggf. als Slideshow (stable)',1, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v1.jpg'],
                    ['Abbildung links neben Headline, Teaser oder Bildcollage von bis zu 4 Abbildungen (stable)',2, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v2.jpg'],
                    ['Abbildung rechts neben Headline, Teaser oder Bildcollage von bis zu 4 Abbildungen (stable)',3, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg'],
                    ['2 oder 4 Bilder im Landscapeformat, je zwei nebeneinander (stable)',4, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v4.jpg'],
                    ['Infotext zwischen zwei Bildern oder rechts neben einer Abbildung (stable)',5, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v5.jpg'],
                    ['Beliebige Anzahl von quadratischen Bildern in Dreierreihen (stable)',7, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v7.jpg'],
                    ['Beliebige Anzahl von Bildern nebeneinander (stable)',8, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v8.jpg']
                ],
                'fieldWizard' => [
                    'selectIcons' => [
                        'disabled' => false,
                    ],
                ],
            ],
        ],
        'configuration' => [
            'label' => 'Konfiguration der Darstellung der Sektion',
            'description' => 'Konfigurieren Sie hier die Darstellung Ihrer Sektionen. Die hier hinterlegten Daten überschreiben die allgemeinen Deklarationen, die Sie in der Ausstellungsübersicht für die Sektionen festgelegt haben.',
            'config' => [
                'type' => 'flex',
                'ds' => [
                    'default' => 'FILE:EXT:jo_museo/Configuration/Flexform/Section.xml',
                ],
            ],
        ],
        'padding' => [
            'label' => 'Abstand der Sektionsübersichten nach links und rechts (Desktop)',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'maxitems' => 1,
                'items' => [
                    ['Kein Abstand (Default)',0],
                    ['Abstand 1 (klein)',1]
                ],
            ],
        ],
        'textpos' => [
            'label' => 'Position der Textinformationen der Sektionen',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'maxitems' => 1,
                'items' => [
                    ['Overlay - Headline und Teaser vor dem Bildensemble (Default)',0],
                    ['Datum, Headline, Subheadline und Teaser Über dem Bildensemble',1],
                    ['Datum, Headline, Subheadline im ersten Bildcontainer, Teaser unter dem Bildensemble',2]
                ],
            ],
        ],
        'teaser' => [
            'label' => 'Teasertext zu der Sektion/Kapitel',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'description' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_section.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'nextsectiontext' => [
            'label' => 'Überleitung zu dieser Sektion',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'literature' => [
            'label' => 'Literatur/Fachbeiträge',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'exhibit' => [
            'label' => 'Zugeordnete Exponate/Objekte in der entsprechenden Reihenfolge',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_jomuseo_domain_model_exhibit',
                'MM' => 'tx_jomuseo_domain_model_section_exhibit_mm',
                'foreign_table_where' => ' AND tx_jomuseo_domain_model_exhibit.pid=###CURRENT_PID###',
                'minitems' => 0,
                'maxitems' => 100,
                'size' => 30,
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
            'label' => 'Key Visual, das diese Sektion repräsentiert',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('derivate', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.derivate.add',
                ],
            ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],
        'publikation' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibit.publikation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'audio' => [
            'label' => 'Audioinformationen zur dieser Sektion',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('audio', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.audio.add',
                ],
                'foreign_types' => [
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
            ], 'mp3,wav,mp4'),
        ],
        'kontextinfo' => [
            'label' => 'Kontextinformation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
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
    ],
];
