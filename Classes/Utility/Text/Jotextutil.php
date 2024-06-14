<?php
namespace JO\JoMuseo\Utility\Text;

use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class Jotextutil extends ActionController
{
    public function MakeTypoLink(
        $_this,
        $ArgumentsArrayAdditional = [],
        $pageUid = 0,
        $ActionControllerArray = [],
        $page_type = null,
        $additional_params = []
    ) {
        $uri = '';
        if (!empty($_this)) {
            $PluginName = $_this->request->getPluginName();
            $ActionName = $_this->request->getControllerActionName();
            $ControllerName = $_this->request->getControllerName();
            $Extensionkey = strtolower($_this->request->getControllerExtensionName());
            $pageUid = intval($GLOBALS['TSFE']->id);
            if ($pageUid > 0) {
                $pageUid = intval($pageUid);
            }

            if (!empty($ActionControllerArray)) {
                if ($ActionControllerArray['pluginname']) {
                    $PluginName = $ActionControllerArray['pluginname'];
                }
                if ($ActionControllerArray['actionname']) {
                    $ActionName = $ActionControllerArray['actionname'];
                }
                if ($ActionControllerArray['controllername']) {
                    $ControllerName = $ActionControllerArray['controllername'];
                }
                if ($ActionControllerArray['extensionkey']) {
                    $Extensionkey = $ActionControllerArray['extensionkey'];
                }
            }
            $Typolinkprefix = 'tx_' . $Extensionkey . '_' . lcfirst($PluginName);
            $arguments = [
                $Typolinkprefix . '[action]' => $ActionName,
                $Typolinkprefix . '[controller]' => $ControllerName,
            ];
            if (!empty($ArgumentsArrayAdditional)) {
                foreach ($ArgumentsArrayAdditional as $key => $value) {
                    $arguments[$Typolinkprefix . '[' . $key . ']'] = $value;
                }
            }
            if (!empty($additional_params)) {
                foreach ($additional_params as $key => $value) {
                    $arguments[$key] = $value;
                }
            }
            if (null != $page_type) {
                $arguments['type'] = $page_type;
            }
            $additionalParams = HttpUtility::buildQueryString($arguments, '&');
            $linkConf = [
                'parameter' => $pageUid,
                'additionalParams' => $additionalParams,
            ];
            $uri = $_this->request->getBaseUri() . 'index.php?id=' . $pageUid . $additionalParams;
        }
        return $uri;
    }
}
