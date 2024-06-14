<?php
return [
    'ctrl' => [
        'title' => 'Angebote',
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
        'searchFields' => 'title, description, image, videolink, video, audiolink, audio',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('jo_museo') . 'Resources/Public/Icons/Extension.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, description, image, videolink, video, audiolink, audio, otherlinktext, otherlink',
    ],
    'types' => [
        '1' => ['showitem' => 'view, title, description, image, videolink, video, audiolink, audio, otherlinktext, otherlink,
            --div--;Zugriff, starttime, endtime'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
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
        'view' => [
            'label' => 'Darstellung des Datensatzes',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'maxitems' => 1,
                'items' => [
                    ['Ansicht 1 (Default)',1, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v1.jpg'],
                    ['Ansicht 2',2, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v2.jpg'],
                    ['Ansicht 3',3, 'EXT:jo_museo/Resources/Public/Images/Backend/sektionsuebersicht_v3.jpg']
                ],
                'fieldWizard' => [
                    'selectIcons' => [
                        'disabled' => false,
                    ],
                ],
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
        'description' => [
            'label' => 'Kurzbeschreibung',
            'description' => 'An dieser Stelle können Sie eine kurze Beschreibung des Datensatzes hinterlegen. Er wird als Teasertext an den entsprechenden Stellen ausgegeben.',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
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
        'audiolink' => [
            'label' => 'Audio Link',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
            ],
        ],
        'audio' => [
            'label' => 'Audio',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('audio', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.audio.add',
                ],
                'maxitems' => 1,
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
        'videolink' => [
            'label' => 'Video Link',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
            ],
        ],
        'video' => [
            'label' => 'Video',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('video', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_exhibition.audio.add',
                ],
                'maxitems' => 1,
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
        'otherlinktext' => [
            'label' => 'Zusätzliche Verlinkung Titel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'otherlink' => [
            'label' => 'Zusätzliche Verlinkung Link',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
            ],
        ],
    ],
];
