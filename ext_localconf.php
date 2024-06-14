<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
call_user_func(
    function () {

        $_EXTKEY = "jo_museo";

        /**
         * Rechercheportal
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JO.' . $_EXTKEY,
            'Pi1009',
            [\JO\JoMuseo\Controller\MuseoController::class => 'list, index, listitems, detailobject, collbox, facette, map, ajax, suggest, entrymask, canonical, entityfacts, downloadimage'],
            [\JO\JoMuseo\Controller\MuseoController::class => 'list, index, listitems, detailobject, collbox, facette, map, ajax, suggest, entrymask, canonical, entityfacts, downloadimage']
        );

        /**
         * Digitale Ausstellungen
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JO.' . $_EXTKEY,
            'pi2011',
            [\JO\JoMuseo\Controller\ExhibitionController::class => 'showexhibition, showsection, showobject, showfullindex, import, ajax, showteaser, showall, loadbook, showdata'],
            [\JO\JoMuseo\Controller\ExhibitionController::class => 'showexhibition, showsection, showobject, showfullindex, import, ajax, showteaser, showall, loadbook, showdata']
        );

        /**
         * Plugin für die Darstellung des Objekt des Monats oder des Tages oder eines zufälligen Objektes
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JO.' . $_EXTKEY,
            'Pi1091',
            [\JO\JoMuseo\Controller\ContentController::class => 'selectobjectfrompool'],
            [\JO\JoMuseo\Controller\ContentController::class => 'selectobjectfrompool']
        );


        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JO.' . $_EXTKEY,
            'Pi1010',
            [\JO\JoMuseo\Controller\ElementsController::class => 'index, ajaxinstitute, teiurl, pageconfig'],
            [\JO\JoMuseo\Controller\ElementsController::class => '']
        );

        /**
         * Images CE
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JO.' . $_EXTKEY,
            'pi2010',
            [\JO\JoMuseo\Controller\ContentController::class => 'showexhibition, imagerotate, imagezoom'],
            [\JO\JoMuseo\Controller\ContentController::class => 'showexhibition, imagerotate, imagezoom']
        );

        /**
         *  API
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JO.' . $_EXTKEY,
            'api',
            [\JO\JoMuseo\Controller\ApiController::class => 'api'],
            [\JO\JoMuseo\Controller\ApiController::class => 'api']
        );

        /**
         *  Abbildung einzelner Objekte
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'JO.' . $_EXTKEY,
            'soloshow',
            [\JO\JoMuseo\Controller\MuseoController::class => 'soloshow'],
            [\JO\JoMuseo\Controller\MuseoController::class => 'soloshow']
        );

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
        ->registerImplementation(
            \TYPO3\CMS\Extbase\Domain\Model\FileReference::class,
            \JO\JoMuseo\Domain\Model\FileReference::class
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig("@import 'EXT:jo_museo/Configuration/TSconfig/plugin.tsconfig'");

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'jo_museo-extension',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:jo_museo/Resources/Public/Icons/Extension.svg']
        );
        
        // \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', 'EXT:jo_museo/Configuration/TypoScript/Museo/');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            $_EXTKEY,
            'setup',
            "@import 'EXT:jo_museo/Configuration/TypoScript/Museo/setup.typoscript'"
        );

        // Solr Hook - speichert ausgewählte Datensätze in einen entfernten Solr
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['addtosolr'] =
            \JO\JoMuseo\Hooks\TCEmainHook::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['addtosolr'] =
            \JO\JoMuseo\Hooks\TCEmainHook::class;

        // EID Scripte
        // Annotationen für digitale Editionen aus- und einschalten
        $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['hideandshowannotations'] =
             \JO\JoMuseo\Eid\Hideandshowannotations::class;

        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['fullWithImg'] = 'EXT:jo_museo/Configuration/RTE/FullWithImg.yaml';
    }
);
