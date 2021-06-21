<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 18:32
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Security\Utils\ValkyrieUtils;
use Core\Tools\Timer;

class PostProtect extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_POST) {

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

            $total_posts = count($_POST);
            $processed_post = 0;
            foreach ($_POST as $key => $value) {
                $check = str_replace($ct_rules, '*', $value);
                if ($value !== $check) {

                    $this->results->setIsThreat(true);
                    $message = 'POST protect';
                    $this->fireEventLog($message);
                    $this->defaultEventHandler->log($message);

                    if ($this->intlligentProcess) {

                        $_POST[$key] = '';
                        $this->results->setSeverityLevel(1);
                        $this->results->setAction(DefaultActions::RATE_LIMIT);

                    } else {

                        $this->results->setSeverityLevel(3);
                        $this->results->setAction(DefaultActions::BLOCK);
                        unset($_POST[$key], $value);
                    }

                    $processed_post++;

                }
                $this->results->setBadParameters([$value]);
                $this->results->setParameters([$check]);
            }
            ValkyrieUtils::set_new_server('cv_total_post', $total_posts);
            ValkyrieUtils::set_new_server('cv_processed_post', $processed_post);

            $runtime->finish();

            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);
        }
        return $this->results;
    }
}