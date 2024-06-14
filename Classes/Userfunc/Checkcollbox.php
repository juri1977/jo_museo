<?php
namespace JO\JoMuseo\Userfunc;

use TYPO3\CMS\Core\Utility\GeneralUtility;

//@all -> das muss noch optimiert werden - language Datei muss angeschlossen werden
class Checkcollbox
{
    public function main($content, $conf)
    {
        /* Nur wenn das folgende TS auf dem Mountpoint gesetzt ist, wird ein Merklistenlink erzeugt
         *    plugin.tx_jomuseo {
         *      settings {
         *        merkliste = 119
         *      }
         *    }
         */
        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jomuseo.']['settings.']['merkliste'])) {
            $target_uid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jomuseo.']['settings.']['merkliste'];
            $label = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jomuseo.']['settings.']['merklistelabel'];
            $label = $label ? $label : 'Meine Merkliste';

            $projectname = 'museo'; // Defaultname der Collectorbox
            if(isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jomuseo.']['settings.']['init.']['specific.']['projectname'])) $projectname = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jomuseo.']['settings.']['init.']['specific.']['projectname'];
            $colboxname = "collectorsbox-" . $projectname; 
            // Getvars checken - wenn {merkliste} vor {mainContent} im Template eingebunden ist, zeigt die Merkliste den zuletzt hinzugefügten Datensatz erst beim nchsten Seitenreload an
            
            $sess_array = [];
            $last_added = null;
            $last_removed = null;
            $remove_all = null;
            if (!empty(GeneralUtility::_GET())) {
                $get_array = GeneralUtility::_GET();
                if ($get_array['tx_jomuseo_pi1009']['add_to_box']) $last_added = filter_var($get_array['tx_jomuseo_pi1009']['add_to_box'], FILTER_SANITIZE_STRING);
                if ($get_array['tx_jomuseo_pi1009']['remove_from_box']) $last_removed = filter_var($get_array['tx_jomuseo_pi1009']['remove_from_box'], FILTER_SANITIZE_STRING);
                if ($get_array['tx_jomuseo_pi1009']['delete_box']) $remove_all = 1;
            }
            if (1 == $remove_all) {
                $sess_array = [];
            } else {
                $joSessionUtil = GeneralUtility::makeInstance('JO\\JoMuseo\\Utility\\Fuzzysearchutils\\Josearchsession');
                if ($joSessionUtil->getSessionValues($colboxname)) $sess_array = $joSessionUtil->getSessionValues($colboxname);
                if ($joSessionUtil->getCookieValues($colboxname)) $sess_array = $joSessionUtil->getCookieValues($colboxname);
                if (null != $last_added && !in_array($last_added, $sess_array)) array_push($sess_array, $last_added);
                if (null != $last_removed && in_array($last_removed, $sess_array)) $sess_array = array_diff($sess_array, [$last_removed]);
            }
            $number_items = count($sess_array);
            if ($number_items > 0) {
                $linkConf = [
                    'parameter' => $target_uid,
                    'returnLast' => 'url'
                ];
                $link = $GLOBALS['TSFE']->cObj->typoLink('', $linkConf);
                $active = $GLOBALS['TSFE']->id == $target_uid ? ' active' : '';
                $markup = '<div class="joDownload-container' . $active . '">
						<a href="' . $link . '">
							<div class="joDownload-button">
								<div class="ml_number" data-num="' . $number_items . '">' . $number_items . '</div>
							</div>
							<span>' . $label . '</span>
						</a>
					</div>';
                $return_values = [
                    'anzahl' => $number_items,
                    'target_uid' => $target_uid,
                    'target_link' => $link,
                    'full_markup' => $markup
                ];
                return $return_values['full_markup'];
            }
        }
    }
}
