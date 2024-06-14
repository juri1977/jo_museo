<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function() {
	
	$_EXTKEY = "JoMuseo";
	$_ExtensionNameUnderscore = "jo_museo";
	$_ExtensionNameLowercase = strtolower($_EXTKEY);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'JO.' . $_EXTKEY,
	'Pi1009',
	'JO - Museumsplugin'
	);
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$_ExtensionNameLowercase . '_pi1009'] = 'pi_flexform';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_ExtensionNameLowercase . '_pi1009', 'FILE:EXT:' . $_ExtensionNameUnderscore . '/Configuration/Flexform/pi1009.xml');

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'JO.' . $_EXTKEY,
	'Pi1010',
	'JO - Objekte'
	);
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$_ExtensionNameLowercase . '_pi1010'] = 'pi_flexform';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_ExtensionNameLowercase . '_pi1010', 'FILE:EXT:' . $_ExtensionNameUnderscore . '/Configuration/Flexform/pi1010.xml');

	/*

	$TBE_STYLES['skins'][$_EXTKEY]['name'] = $_EXTKEY;
	$TBE_STYLES['skins'][$_EXTKEY]['stylesheetDirectories']['structure'] = 'EXT:' . $_EXTKEY . '/Resources/Public/Css/Skin/';
	*/

	/**
	 *  Plugin für die Darstellung des Objekt des Monats oder des Tages
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	    'JO.' . $_EXTKEY,
	    'Pi1091',
	    'Objekt des Monats oder Tages'
	);
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$_ExtensionNameLowercase . '_pi1091'] = 'pi_flexform';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	    $_ExtensionNameLowercase . '_pi1091',
	    'FILE:EXT:' . $_ExtensionNameUnderscore . '/Configuration/Flexform/pi1091.xml'
	);

	/**
	 *  JO Exhibition
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	    'JO.' . $_EXTKEY,
	    'pi2011',
	    'JO - Exhibition - Digitale Ausstellungen'
	);
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$_ExtensionNameLowercase . '_pi2011'] = 'pi_flexform';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	    $_ExtensionNameLowercase . '_pi2011',
	    'FILE:EXT:' . $_ExtensionNameUnderscore . '/Configuration/Flexform/pi2011.xml'
	);

	/**
	 *  Abbildung einzelner Objekte
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	    'JO.' . $_EXTKEY,
	    'soloshow',
	    'JO - Soloshow - Abbildung einzelner Objekte'
	);
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$_ExtensionNameLowercase . '_soloshow'] = 'pi_flexform';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	    $_ExtensionNameLowercase . '_soloshow',
	    'FILE:EXT:' . $_ExtensionNameUnderscore . '/Configuration/Flexform/Soloshow.xml'
	);
});
