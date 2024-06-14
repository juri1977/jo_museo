<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function() {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('jo_museo', 'Configuration/TypoScript/Exhibition', 'Zusatzkonfiguration für digitale Ausstellungen (exhibition)');

	// \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Extension zur Darstellung musealer Inhalte');
});
