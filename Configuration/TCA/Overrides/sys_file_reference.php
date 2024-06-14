<?php

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function() {
	/**
	 * sys_file_reference erweitern
	 */
	$tempColumns = [
	    'inlinecss' => [
	        'exclude' => 1,
	        'l10n_mode' => 'mergeIfNotBlank',
	        'label' => 'LLL:EXT:jo_museo/Resources/Private/Language/locallang.xlf:inlinecss',
	        'config' => [
	            'default' => '',
	            'eval' => 'null',
	            'mode' => 'useOrOverridePlaceholder',
	            'cols' => 20,
	            'rows' => 5,
	            'type' => 'text',
	        ],
	    ],
	    'description' => [
	        'label' => 'Beschreibung',
	        'config' => [
	            'cols' => 20,
	            'rows' => 5,
	            'type' => 'text',
	            'enableRichtext' => true,
	        ],
	    ],
	    'related' => [
	        'label' => 'Posterimage für Videos oder Audio',
	        'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
	            'imagerelated',
	            [
	                'appearance' => [
	                    'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
	                ],
	                'maxitems' => 1,
	            ],
	            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
	        ),
	    ],
	];
	ExtensionManagementUtility::addTCAcolumns(
		'sys_file_reference', 
		$tempColumns, 
		1
	);
	ExtensionManagementUtility::addFieldsToPalette(
		'sys_file_reference',
		'audioOverlayPalette',
		'related'
	);
	ExtensionManagementUtility::addFieldsToPalette(
		'sys_file_reference',
		'videoOverlayPalette',
		'related'
	);
});
