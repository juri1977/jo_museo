<?php
namespace JO\JoMuseo\Eid;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use JO\JoMuseo\Utility\Fuzzysearchutils\Josearchsession;

class Hideandshowannotations
{
	
    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
    	$return = $this->setPageconfigAction();
        return GeneralUtility::makeInstance(JsonResponse::class, $return);
    }

    public function setPageconfigAction() {
    	$session_util = GeneralUtility::makeInstance(Josearchsession::class);
    	$return = array();
		if(GeneralUtility::_GP('pid')){		
			$page_conf_session = 'page_config_' . intval(GeneralUtility::_GP('pid'));
			$pageconfig_change_flag = FALSE;
			$page_config_sessionvalues = $session_util->getSessionValues($page_conf_session);
			if(is_array($page_config_sessionvalues)){
				if( isset($page_config_sessionvalues['showteiannotion'])){
					// @all -> im Moment wird die ganze Pagekonfig gelöscht - ist kein Problem weil nur dieser eine Parameter dort abgelegt wird
					$page_config = array();
				}else{
					$page_config['showteiannotion'] = true;
				}
				$session_util->replaceAllValues($page_conf_session, $page_config);
				$return = $page_config;
			}
		}
		return $return;
	}
}
