<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_annotation',
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
        'searchFields' => 'title',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('jo_museo') . 'Resources/Public/Icons/Extension.svg',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime'
        ]
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title'
    ],
    'types' => [
        '0' => [
            'showitem' => 'title, entityid, asset, coordinates, comment, public, feuser'
        ]
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
                ]
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255
            ]
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_institute.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'comment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_annotation.comment',
            'config' => [
                'type' => 'text'
            ]
        ],
        'feuser' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_annotation.feuser',
            'config' => [
                'type' => 'input',
                'eval' => 'int'
            ]
        ],
        'coordinates' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_annotation.coordinates',
            'config' => [
                'type' => 'text'
            ]
        ],
        'entityid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_annotation.entityid',
            'config' => [
                'type' => 'text'
            ]
        ],
        'asset' => [
            'exclude' => 1,
            'label' => 'Betreffendes Asset',
            'config' => [
                'type' => 'text'
            ]
        ],
        'public' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang_db.xlf:tx_jomuseo_domain_model_annotation.public',
            'config' => [
                'type' => 'check',
                'size' => 30,
                'eval' => 'trim'
            ]
        ]
    ]
];
