<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibit',
        'label' => 'objektnummer',
        'label_alt' => 'datasubtype, objektnummer, title',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'default_sortby' => 'ORDER BY uid DESC',
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'transOrigPointerField' => 'l10n_parent',
        'translationSource' => 'l10n_source',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title, subtitle, shorttext, bodytext, publikation, kontextinformation, links, tags, location, entity',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('jo_museo') . 'Resources/Public/Icons/Extension.svg',
    ],
    'interface' => [
        
    ],
    'types' => [
        '1' => ['showitem' => 'datasubtype, title, subtitle, shorttext, ctatext, transkript, derivate, audiotitel, audiotext, audio, video, videotitel, videotext, booksites, bookfolder, 
            --div--;Mehr wissen, bodytext,publikation, links, tags, location, locationprocessed, entity, entityprocessed,
            --div--;Mehr hören und sehen, morederivate, moreaudio, morevideo, 
            --div--;Bezug zu physischen Objekten, objektnummer, koordinaten, 
            --div--;Geodaten zum Objekt, jsonfile, datapage, 
            --div--;Konfiguration der Objektdarstellung, configuration,
            --div--;Sprache und Zeitsteuerung, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, starttime, endtime,
        '],
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
                'foreign_table' => 'tx_jomuseo_domain_model_exhibit',
                'foreign_table_where' => 'AND tx_jomuseo_domain_model_exhibit.pid=###CURRENT_PID### AND tx_jomuseo_domain_model_exhibit.sys_language_uid IN (-1,0)',
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
        'datasubtype' => [
            'label' => 'Darstellung der Datentypen in der Ausstellung',
            'description' => 'Definieren Sie hier die Art Ihres Objektes. Die Abbildungen unter der Selectbox dokumentieren das spätere Aussehen.',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'maxitems' => 1,
                'items' => [
                    ['Darstellung der Informationsobjekte', '--div--'],
                    ['Standardansicht', 1, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v1.jpg'],
                    ['Alternative Ansicht 1 - to migrate - dont use!', 2, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v2.jpg'],
                    ['Alternative Ansicht 2 - to migrate - dont use!', 3, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg'],
                    ['Alternative Ansicht 3 - Asset links/Text rechts (stable)', 4, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg'],
                    ['Alternative Ansicht 4 - Asset rechts/Text links (stable)', 5, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg'],
                    ['Alternative Ansicht 5 - Asset mittig über oder unter Text (stable)', 6, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg'],
                    ['Alternative Ansicht 6 - 4 Assets/Text darüber oder darunter (in Arbeit)', 7, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg'],
                    ['Alternative Ansicht 7 - Galerie links/Text rechts (stable)', 8, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg'],
                    ['Alternative Ansicht 8 - Galerie rechts/Text rechts (stable)', 9, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg'],
                    ['Alternative Ansicht 9 - Galerie mittig über oder unter Text (stable)', 10, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg'],
                    ['Kartenansicht',100],
                    ['Audio ausspielen', 50],
                    ['Blätterbuch/Einzelbilder', 60],
                    ['Blätterbuch/Ordner - Ansicht 1', 61],
                    ['Blätterbuch/Ordner -  Ansicht 2', 62],
                    ['Blätterbuch/Ordner -  Ansicht 3', 63],
                    ['Darstellung der Zusatzobjekte', '--div--'],
                    ['Überleitung/Zitat/Fragestellung/etc. - Text unter Abbildung (stable)', 20],
                    ['not configured - Überleitung Text - Bild links', 21],
                    ['not configured - Überleitung Text - Bild rechts', 22],
                    ['Überleitung/Zitat/Fragestellung/etc. - Text über Abbildung (stable)', 23],
                    ['Überleitung Hintergrundbild/Video', 24],
                    ['Überleitung Slideshow Format 1', 30],
                    ['not configured - Überleitung Slideshow Format 2', 31],
                    ['not configured - Fragestellung Format 1', 40],
                    ['not configured - Fragestellung Format 2', 41],
                ],
                'fieldWizard' => [
                    'selectIcons' => [
                        'disabled' => false,
                    ],
                ],
            ],
        ],
        'title' => [
            'label' => 'Titel des Exponats',
            'config' => [
                'type' => 'text',
            ],
        ],
        'configuration' => [
            'label' => 'Konfiguration der Darstellung des Exponats',
            'description' => 'Konfigurieren Sie hier die Farbgebung und die Darstellung Ihres Objektes. Die hier hinterlegten Daten überschreiben die allgemeinen Deklarationen, die Sie in der Ausstellungsübersicht für die Objekte festgelegt haben.',
            'config' => [
                'type' => 'flex',
                'ds' => [
                    'default' => 'FILE:EXT:jo_museo/Configuration/Flexform/Exhibit.xml',
                ],
            ],
        ],
        'subtitle' => [
            'label' => 'Ein oder mehrere Untertitel des Exponats',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_jomuseo_domain_model_data',
                'foreign_field' => 'parentid',
                'foreign_table_field' => 'parenttable',
                'maxitems' => 99,
                'appearance' => [
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ],
            ],
        ],
        'shorttext' => [
            'label' => 'Kurzer Text als Teaser für das Exponat',
            'description' => 'Dieser Text erscheint auf der ersten Seite der Objektansicht. Bitte halten Sie diesen Text recht kurz. Unter dem Reiter MEHR WISSEN können Sie eine ausführliche Beschreibung hinterlegen.',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'bodytext' => [
            'label' => 'Ausführliche Beschreibung des Objektes',
            'description' => 'Dieser Text erscheint in der Detailansicht des Objektes entweder als Overlay or entsprechend unter der ersten Objektübersicht.',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'transkript' => [
            'label' => 'Transkription',
            'displayCond' => [
                'OR' => [
                    'FIELD:datasubtype:=:1',
                    'FIELD:datasubtype:=:2',
                    'FIELD:datasubtype:=:3'
                ],
            ],
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'ctatext' => [
            'label' => 'Buttontext - call to action',
            'description' => 'Sie können hier den Text, der in dem Button auf der Übersichtsseite geschrieben wird, selbst hinterlegen. Der Button erscheint automatisch, wenn Sie Informationen in den Reitern MEHR WISSEN etc. oder ein Blätterbuch hinterlegt haben. Bitte formulieren Sie die Worte so, dass der Nutzer sich animiert fühlt, darauf zu klicken. Sie können beispielsweise eine objektspezifische Aufforderung oder auch eine Fragestellung hinterlegen. Wenn Sie diese Feld leer lassen, wird der Standardtext in den Button geschrieben.',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'publikation' => [
            'label' => 'Publikationen',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'kontextinformation' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibit.kontextinformation',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'links' => [
            'label' => 'Links und Kontextinformationen',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'derivate' => [
            'label' => 'Ein oder mehrere Abbildungen des Objektes',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('derivate', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'Abbildung hinzufügen',
                ],
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
        'morederivate' => [
            'label' => 'Zusätzliche Bilddaten',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('morederivate', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'Bilddaten hinzufügen',
                ],
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
        'booksites' => [
            'label' => 'Book',
            'displayCond' => [
                'OR' => [
                    'FIELD:datasubtype:=:60'
                ],
            ],
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('booksites', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.derivate.add',
                ],
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
        'bookfolder' => [
           'label' => 'Order, in dem die Bilder für das Blätterbuch liegen',
           'description' => 'Verknüpfen Sie hier den Ordner, in dem Sie die Bilder für das Blätterbuch hintlegt haben. Achten Sie darauf, dass Sie die Bilder in einem darstellbaren Format und als Einzelseiten in der korrekten Reihenfolge hochgeladen haben.',
           'displayCond' => [
                'OR' => [
                    'FIELD:datasubtype:=:61',
                    'FIELD:datasubtype:=:62',
                    'FIELD:datasubtype:=:63'
                ],
            ],
           'config' => [
              'type' => 'group',
              'internal_type' => 'folder',
              'maxitems' => 1,
              'minitems' => 0,
              'size' => 1,
              'default' => 0
           ]
        ],
        'tags' => [
            'label' => 'Tags und Schlagworte',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'location' => [
            'label' => 'Relevante und verbundene Orte',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'locationprocessed' => [
            'label' => 'Relevante und verbundene Orte (normdata)',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'entity' => [
            'label' => 'Relevante und verbundene Personen',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'entityprocessed' => [
            'label' => 'Relevante und verbundene Personen (normdata)',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'objektnummer' => [
            'label' => 'Objektnummer/Inventarnummer in der physischen Ausstellung',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'koordinaten' => [
            'label' => 'Koordinaten',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'jsonfile' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.jsonfile',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('jsonfile', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.jsonfile.add',
                ],
            ], 'json'),
        ],
        'audiotitel' => [
            'label' => 'Allgemeiner Titel der Audioinformationen',
            'displayCond' => [
                'OR' => [
                    'FIELD:datasubtype:=:1',
                    'FIELD:datasubtype:=:2',
                    'FIELD:datasubtype:=:3',
                    'FIELD:datasubtype:=:50',
                    'FIELD:datasubtype:=:4',
                    'FIELD:datasubtype:=:5',
                    'FIELD:datasubtype:=:6',
                    'FIELD:datasubtype:=:7',
                    'FIELD:datasubtype:=:8',
                    'FIELD:datasubtype:=:9',
                    'FIELD:datasubtype:=:10'
                ],
            ],
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'audiotext' => [
            'label' => 'Allgemeiner Text für die Audio Dateien',
            'displayCond' => [
                'OR' => [
                    'FIELD:datasubtype:=:1',
                    'FIELD:datasubtype:=:2',
                    'FIELD:datasubtype:=:3',
                    'FIELD:datasubtype:=:50',
                    'FIELD:datasubtype:=:4',
                    'FIELD:datasubtype:=:5',
                    'FIELD:datasubtype:=:6',
                    'FIELD:datasubtype:=:7',
                    'FIELD:datasubtype:=:8',
                    'FIELD:datasubtype:=:9',
                    'FIELD:datasubtype:=:10'
                ],
            ],
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'videotitel' => [
            'label' => 'Allgemeiner Titel der Videoinformationen',
            'displayCond' => [
                'OR' => [
                    'FIELD:datasubtype:=:1',
                    'FIELD:datasubtype:=:2',
                    'FIELD:datasubtype:=:3',
                    'FIELD:datasubtype:=:50',
                    'FIELD:datasubtype:=:4',
                    'FIELD:datasubtype:=:5',
                    'FIELD:datasubtype:=:6',
                    'FIELD:datasubtype:=:7',
                    'FIELD:datasubtype:=:8',
                    'FIELD:datasubtype:=:9',
                    'FIELD:datasubtype:=:10'
                ],
            ],
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'videotext' => [
            'label' => 'Allgemeiner Text für die Video Dateien',
            'displayCond' => [
                'OR' => [
                    'FIELD:datasubtype:=:1',
                    'FIELD:datasubtype:=:2',
                    'FIELD:datasubtype:=:3',
                    'FIELD:datasubtype:=:50',
                    'FIELD:datasubtype:=:4',
                    'FIELD:datasubtype:=:5',
                    'FIELD:datasubtype:=:6',
                    'FIELD:datasubtype:=:7',
                    'FIELD:datasubtype:=:8',
                    'FIELD:datasubtype:=:9',
                    'FIELD:datasubtype:=:10'
                ],
            ],
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'audio' => [
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.audio',
            'displayCond' => [
                'OR' => [
                    'FIELD:datasubtype:=:1',
                    'FIELD:datasubtype:=:2',
                    'FIELD:datasubtype:=:3',
                    'FIELD:datasubtype:=:50',
                    'FIELD:datasubtype:=:4',
                    'FIELD:datasubtype:=:5',
                    'FIELD:datasubtype:=:6',
                    'FIELD:datasubtype:=:7',
                    'FIELD:datasubtype:=:8',
                    'FIELD:datasubtype:=:9',
                    'FIELD:datasubtype:=:10'
                ],
            ],
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
        'moreaudio' => [
            'label' => 'Zusätzliche Audioinformationen',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('moreaudio', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'Audiodatei hinzufügen',
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
            'displayCond' => [
                'OR' => [
                    'FIELD:datasubtype:=:1',
                    'FIELD:datasubtype:=:2',
                    'FIELD:datasubtype:=:3',
                    'FIELD:datasubtype:=:50',
                    'FIELD:datasubtype:=:24',
                    'FIELD:datasubtype:=:4',
                    'FIELD:datasubtype:=:5',
                    'FIELD:datasubtype:=:6',
                    'FIELD:datasubtype:=:7',
                    'FIELD:datasubtype:=:8',
                    'FIELD:datasubtype:=:9',
                    'FIELD:datasubtype:=:10'
                ],
            ],
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
                        ]
                    ]
                ],
            ], 'mp3,wav,mp4,m4a,youtube,vimeo'),
        ],
        'morevideo' => [
            'label' => 'Zusätzliche Videoinformationen',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('morevideo', [
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
                    ]
                ],
            ], 'mp3,wav,mp4,m4a'),
        ],
        'datapage' => [
            'label' => 'Data Ordner (Objekt, Person oder Geografika)',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'maxitems' => 1,
                'minitems' => 0
            ]
        ]
    ],
];
