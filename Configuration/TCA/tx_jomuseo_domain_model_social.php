<?php
return [
    'ctrl' => [
        'title' => 'Social Links',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'sortby' => 'sorting',
        'hideTable' => true,
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
        'searchFields' => 'title,description',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('jo_museo') . 'Resources/Public/Icons/Extension.svg',
    ],
    'interface' => [
        
    ],
    'types' => [
        0 => [
            'showitem' => '--palette--;;paletteTypeTitleUri, --palette--;;paletteDescription',
        ],
    ],
    'palettes' => [
        'paletteCore' => [
            'showitem' => 'hidden,sys_language_uid,l10n_parent, l10n_diffsource,',
        ],
        'paletteTypeTitleUri' => [
            'showitem' => 'type,title,uri,',
        ],
        'paletteDescription' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
				'default' => 0,
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0],
                ],
            ],
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
                'foreign_table' => 'tx_jomuseo_domain_model_social',
                'foreign_table_where' => 'AND tx_jomuseo_domain_model_social.pid=###CURRENT_PID### AND tx_jomuseo_domain_model_social.sys_language_uid IN (-1,0)',
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
        'type' => [
            'label' => 'Type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['Bitte wählen','default'],
                    /*
                    ['Facebook','fc'],
                    ['Twitter','tw'],
                    ['Instagram','in'],
                    ['Whatsapp','wh'],
                    ['Snapchat','sn'],
                    ['Tiktok','tk'],
                    ['LinkedIn','li'],
                    ['XING','xi'],
                    ['Pinterest','pi'],
                    ['Reddit','re'],
                    ['Twitch','twi'],
                    ['Tumblr','tu'],
                    ['YouTube','yo'],
                    ['Vimeo','vi'],
                    ['Webseite','wb'],
                    ['Andere','ot']
                    */
                    ['Webseite','wb'],
                    ['Blog','bl'],
                    ['Facebook','fc'],
                    ['Twitter','tw'],
                    ['Instagram','in'],
                    ['YouTube','yo'],
                    ['Vimeo','vi'],
                    ['Andere','ot']
                ],
            ],
        ],
        'title' => [
            'label' => 'Titel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'uri' => [
            'label' => 'URL',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
            ],
        ],
        'parentid' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'parenttable' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
