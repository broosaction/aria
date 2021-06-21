<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 18:12
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Security\Utils\ValkyrieUtils;
use Core\Tools\Timer;

class CookieProtect extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {

        if (ValkyrieConfig::$PROTECTION_COOKIES) {

            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            $ct_rules = ['applet', 'base', 'bgsound', 'blink', 'embed', 'expression', 'frame', 'javascript', 'layer',
                'link', 'meta', 'object', 'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate',
                'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste',
                'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange',
                'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged',
                'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave',
                'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish',
                'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
                'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout',
                'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste',
                'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart',
                'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange',
                'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', 'script', 'style', 'title',
                'vbscript', 'xml'];

            $total_cookies = count($_COOKIE);
            $processed_cookies = 0;
            foreach ($_COOKIE as $key => $value) {
                $check = str_replace($ct_rules, '*', $value);
                if ($value !== $check) {

                    $this->results->setIsThreat(true);
                    $message = 'Cookie protect';
                    $this->fireEventLog($message);
                    $this->defaultEventHandler->log($message);

                    if ($this->intlligentProcess) {

                        $_COOKIE[$key] = substr($_COOKIE[$key], 2) . '_';
                        $this->results->setSeverityLevel(1);
                        $this->results->setAction(DefaultActions::RATE_LIMIT);

                    } else {
                        setcookie($key, '', 1, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), (bool)ini_get('session.cookie_secure'), (bool)ini_get('session.cookie_httponly'));
                        $this->results->setSeverityLevel(3);
                        $this->results->setAction(DefaultActions::RATE_LIMIT);
                        unset($_COOKIE[$key]);
                    }
                    $processed_cookies++;
                }
                $this->results->setBadParameters([$value]);
                $this->results->setParameters([$check]);
            }
            ValkyrieUtils::set_new_server('cv_total_cookies', $total_cookies);
            ValkyrieUtils::set_new_server('cv_processed_cookies', $processed_cookies);
            $runtime->finish();

            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);
        }
        return $this->results;
    }
}