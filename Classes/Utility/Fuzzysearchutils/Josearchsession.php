<?php
namespace JO\JoMuseo\Utility\Fuzzysearchutils;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Josearchsession
{

    protected $arrayUtil;

    public function __construct()
    {
        $this->arrayUtil = GeneralUtility::makeInstance('JO\JoMuseo\Utility\Arrayfunc\Joarrayfunctions');
    }

    public function addValue($session_name = null, $session_value = null)
    {
        $session_items = [];
        if (null != $session_name && null != $session_value) {
            $session_items = $this->getSessionValues($session_name);
            $session_items = array_filter($this->arrayUtil->joAddToArrayAndMakeUnique($session_items, $session_value));
            $_SESSION[$session_name] = $session_items;
        }
        return $session_items;
    }

    public function removeValue($session_name = null, $session_value = null)
    {
        $session_items = [];
        if (null != $session_name && null != $session_value) {
            $session_items = $this->getSessionValues($session_name);
            $session_items = array_filter($this->arrayUtil->joEliminateArrayValueAndKey($session_items, $session_value));
            if (count($session_items) > 0) {
                $_SESSION[$session_name] = $session_items;
            } else {
                $this->emptySession($session_name);
            }
        }
        return $session_items;
    }

    public function getSessionValues($session_name = null)
    {
        $session_items = [];
        if (null != $session_name) {
            if (!session_id()) {
                @session_start();
            }

            if ($_SESSION[$session_name]) {
                $session_items = $_SESSION[$session_name];
            }
        }
        return $session_items;
    }

    public function getCookieValues($session_name = null)
    {
        $session_items = [];
        if (null != $session_name && isset($_COOKIE[$session_name])) {
            $session_items = json_decode($_COOKIE[$session_name], true);
        }
        return $session_items;
    }

    public function emptySession($session_name = null)
    {
        if (null != $session_name) {
            if (!session_id()) {
                @session_start();
            }

            unset($_SESSION[$session_name]);
        }
    }

    public function replaceAllValues($session_name = null, $session_value = null)
    {
        if (null != $session_name) {
            if (!session_id()) {
                @session_start();
            }

            $_SESSION[$session_name] = $session_value;
        }
    }

    public function replaceAllCookieValues($session_name = null, $session_value = null)
    {
        if (null != $session_name) {
            if (is_array($session_value) && empty($session_value)) {
                $session_value = '';
            } else {
                $session_value = json_encode($session_value);
            }

            setcookie($session_name, $session_value, time() + (86400 * 365), "/"); // 86400 Sekunden = 1 day // 365 = 365 days
        }
    }
}
