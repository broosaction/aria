<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 25 /Mar, 2020 @ 21:11
 * PHP is just a scripting language and thus Security depends on the Coder
 */
declare(strict_types=1);
namespace Core\Tools\CloudValkyrie;


use Exception;
use Tracy\Logger;


class CloudValkyrie extends Utils
{

private static $current_url = '';
/**
 * CloudValkyrie constructor.
 */
public function __construct()
{
    self::$current_url = Utils::get_env('REQUEST_URI');

   // ini_set('session.cookie_lifetime', '0');
   // ini_set('session.cookie_httponly', 'On');
   // ini_set('session.cookie_secure', 'On');
    ini_set('expose_php','off');
    ini_set('display_errors','off');
    ini_set('log_errors', 'on');
    ini_set('error_log',Config::getServerHome() . '/logs/security.log');

    header('Server: Aria Framework Enterprise v0.2.1');
    header('X-Powered-By: Aria Framework');
    header('X-XSS-Protection: 1');
    header_remove('Server');
}

public static function secure()
{

    if (preg_match('#select|update|delete|concat|create|table|union|length|show_table|mysql_list_tables|mysql_list_fields|mysql_list_dbs#i', CloudValkyrie::$current_url)) {
        self::logs('SQL injection attack');
        if (Config::$ATTACK_BLOCK_SCREEN) {

            exit(self::SecurityWarningTemplate()); // Not too good for dev env.

        }
    }


    $securityUlrs_url = Utils::get_env('QUERY_STRING');
    if ($securityUlrs_url !== '' && !preg_match('/^[_a-zA-Z0-9-=&]+$/', $securityUlrs_url)) {

        self::logs('Query String went in');
        if (Config::$ATTACK_BLOCK_SCREEN) {
            if(isset($_REQUEST['ua'],$_REQUEST['w'])){

            }else{
                exit(self::SecurityWarningTemplate());
            }

        }

    }


    //URL protection
    if (Config::$PROTECTION_URL) {
        $ct_rules = array('absolute_path', 'ad_click', 'alert(', 'alert%20', ' and ', 'basepath', 'bash_history', '.bash_history', 'cgi-', 'chmod(', 'chmod%20', '%20chmod', 'chmod=', 'chown%20', 'chgrp%20', 'chown(', '/chown', 'chgrp(', 'chr(', 'chr=', 'chr%20', '%20chr', 'chunked', 'cookie=', 'cmd', 'cmd=', '%20cmd', 'cmd%20', '.conf', 'configdir', 'config.php', 'cp%20', '%20cp', 'cp(', 'diff%20', 'dat?', 'db_mysql.inc', 'document.location', 'document.cookie', 'drop%20', 'echr(', '%20echr', 'echr%20', 'echr=', '}else{', '.eml', 'esystem(', 'esystem%20', '.exe', 'exploit', 'file\://', 'fopen', 'fwrite', '~ftp', 'ftp:', 'ftp.exe', 'getenv', '%20getenv', 'getenv%20', 'getenv(', 'grep%20', '_global', 'global_', 'global[', 'http:', '_globals', 'globals_', 'globals[', 'grep(', 'g\+\+', 'halt%20', '.history', '?hl=', '.htpasswd', 'http_', 'http-equiv', 'http/1.', 'http_php', 'http_user_agent', 'http_host', '&icq', 'if{', 'if%20{', 'img src', 'img%20src', '.inc.php', '.inc', 'insert%20into', 'ISO-8859-1', 'ISO-', 'javascript\://', '.jsp', '.js', 'kill%20', 'kill(', 'killall', '%20like', 'like%20', 'locate%20', 'locate(', 'lsof%20', 'mdir%20', '%20mdir', 'mdir(', 'mcd%20', 'motd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', 'mcd(', 'mrd(', 'mcd=', 'mod_gzip_status', 'modules/', 'mrd=', 'mv%20', 'nc.exe', 'new_password', 'nigga(', '%20nigga', 'nigga%20', '~nobody', 'org.apache', '+outfile+', '%20outfile%20', '*/outfile/*', ' outfile ', 'outfile', 'password=', 'passwd%20', '%20passwd', 'passwd(', 'phpadmin', 'perl%20', '/perl', 'phpbb_root_path', '*/phpbb_root_path/*', 'p0hh', 'ping%20', '.pl', 'powerdown%20', 'rm(', '%20rm', 'rmdir%20', 'mv(', 'rmdir(', 'phpinfo()', '<?php', 'reboot%20', '/robot.txt', '~root', 'root_path', 'rush=', '%20and%20', '%20xorg%20', '%20rush', 'rush%20', 'secure_site, ok', 'select%20', 'select from', 'select%20from', '_server', 'server_', 'server[', 'server-info', 'server-status', 'servlet', 'sql=', '<script', '<script>', '</script', 'script>', '/script', 'switch{', 'switch%20{', '.system', 'system(', 'telnet%20', 'traceroute%20', '.txt', 'union%20', '%20union', 'union(', 'union=', 'vi(', 'vi%20', 'wget', 'wget%20', '%20wget', 'wget(', 'window.open', 'wwwacl', ' xor ', 'xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', '$_request', '$_get', '$request', '$get', '&aim', '/etc/password', '/etc/shadow', '/etc/groups', '/etc/gshadow', '/bin/ps', 'uname\x20-a', '/usr/bin/id', '/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/usr/bin', 'bin/python', 'bin/tclsh', 'bin/nasm', '/usr/x11r6/bin/xterm', '/bin/mail', '/etc/passwd', '/home/ftp', '/home/www', '/servlet/con', '?>', '.txt');
        $check = str_replace($ct_rules, '*', Utils::get_query_string());
        if (Utils::get_query_string() !== $check) {
            self::logs('URL protect');
            if (Config::$ATTACK_BLOCK_SCREEN) {
                if (Config::$INTELLI_PROCESS) {
                    Utils::set_query_string($check);
                } else {
                    if(isset($_REQUEST['ua'],$_REQUEST['w'])){

                    }else{
                        exit(self::SecurityWarningTemplate());
                    }
                }
            }
        }
    }

    //range ip deny
    if (Config::$PROTECTION_RANGE_IP_DENY) {
        $ip_array = array('0', '1', '2', '5', '10', '14', '23', '27', '31', '36', '37', '39', '42', '46', '49', '50', '100', '101', '102', '103', '104', '105', '106', '107', '114', '172', '176', '177', '179', '181', '185', '192', '223', '224');
        $range_ip = explode('.', Utils::get_ip());
        if (in_array($range_ip[0], $ip_array, false)) {

            self::logs('IPs reserved list');
            if (Config::$ATTACK_BLOCK_SCREEN) {
                exit(self::SecurityWarningTemplate());
            }
        }
    }

    //range ip spam
    if (Config::$PROTECTION_RANGE_IP_SPAM) {
        $ip_array = array('24', '186', '189', '190', '200', '201', '202', '209', '212', '213', '217', '222');
        $range_ip = explode('.', Utils::get_ip());
        if (in_array($range_ip[0], $ip_array, true)) {
            self::logs('IPs Spam list');
            if (Config::$ATTACK_BLOCK_SCREEN) {
                exit(self::SecurityWarningTemplate());
            }
        }
    }


    $ct_rules = Array('applet', 'base', 'bgsound', 'blink', 'embed', 'expression', 'frame', 'javascript', 'layer', 'link', 'meta', 'object', 'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', 'script', 'style', 'title', 'vbscript', 'xml');
    if (Config::$PROTECTION_COOKIES) {
        $total_cookies = count($_COOKIE);
        $processed_cookies = 0;
        foreach($_COOKIE as $key => $value) {
            $check = str_replace($ct_rules, '*', $value);
            if ($value !== $check) {
                self::logs('Cookie protect');

                if (Config::$INTELLI_PROCESS) {
                    $_COOKIE[$key] = substr($_COOKIE[$key], 2) . '_';
                } else {
                    setcookie($key,'',1);
                    setcookie($key,false);
                    unset($_COOKIE[$key]);
                }
                $processed_cookies++;
            }
        }
        Utils::set_new_server('cv_total_cookies', $total_cookies);
        Utils::set_new_server('cv_processed_cookies', $processed_cookies);
    }


    if (Config::$PROTECTION_POST) {
        $total_post = 0;
        $processed_post = 0;
        foreach ($_POST as $key => $value) {
            $check = str_replace($ct_rules, '*', $value);
            if ($value !== $check) {
                self::logs('POST protect');
                $_POST[$key] = '';
                unset($_POST[$key], $value);
                $processed_post++;
            }
            $total_post++;
        }
        Utils::set_new_server('cv_total_post', $total_post);
        Utils::set_new_server('cv_processed_post', $processed_post);

    }

    if (Config::$PROTECTION_GET) {
        $total_get = 0;
        $processed_get = 0;
        foreach ($_GET as $key => $value) {
            $check = str_replace($ct_rules, '*', $value);
            if ($value !== $check) {
                self::logs('POST protect');
                $_GET[$key] = '';
                unset($_GET[$key], $value);
                $processed_get++;
            }
            $total_get++;
        }
        Utils::set_new_server('cv_total_get', $total_get);
        Utils::set_new_server('cv_processed_get', $processed_get);

    }


    /** Posting from other servers in not allowed */
    if (Config::$PROTECTION_REQUEST_SERVER && Utils::get_request_method() === 'POST' && isset($_SERVER['HTTP_REFERER']) && !stripos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'], 0)) {
        self::logs('Posting another server');
        if (Config::$ATTACK_BLOCK_SCREEN) {
            exit(self::SecurityWarningTemplate());
        }
    }

    /** protection aganist bots santy */
    if (Config::$PROTECTION_SANTY === true) {
        $ct_rules = array('rush', 'highlight=%', 'perl', 'chr(', 'pillar', 'visualcoder', 'sess_');
        $check = str_replace($ct_rules, '*', strtolower(self::$current_url));
        if (strtolower(self::$current_url) !== $check) {
            self::logs('Santy');
            if (Config::$ATTACK_BLOCK_SCREEN) {
                exit(self::SecurityWarningTemplate());
            }
        }
    }


    // protestion aganist bad bots

    if (Config::$PROTECTION_BOTS) {
        $ct_rules = array('@nonymouse', 'addresses.com', 'ideography.co.uk', 'adsarobot', 'ah-ha', 'aktuelles', 'alexibot', 'almaden', 'amzn_assoc', 'anarchie', 'art-online', 'aspseek', 'assort', 'asterias', 'attach', 'atomz', 'atspider', 'autoemailspider', 'backweb', 'backdoorbot', 'bandit', 'batchftp', 'bdfetch', 'big.brother', 'black.hole', 'blackwidow', 'blowfish', 'bmclient', 'boston project', 'botalot', 'bravobrian', 'buddy', 'bullseye', 'bumblebee ', 'builtbottough', 'bunnyslippers', 'capture', 'cegbfeieh', 'cherrypicker', 'cheesebot', 'chinaclaw', 'cicc', 'civa', 'clipping', 'collage', 'collector', 'copyrightcheck', 'cosmos', 'crescent', 'custo', 'cyberalert', 'deweb', 'diagem', 'digger', 'digimarc', 'diibot', 'directupdate', 'disco', 'dittospyder', 'download accelerator', 'download demon', 'download wonder', 'downloader', 'drip', 'dsurf', 'dts agent', 'dts.agent', 'easydl', 'ecatch', 'echo extense', 'efp@gmx.net', 'eirgrabber', 'elitesys', 'emailsiphon', 'emailwolf', 'envidiosos', 'erocrawler', 'esirover', 'express webpictures', 'extrac', 'eyenetie', 'fastlwspider', 'favorg', 'favorites sweeper', 'fezhead', 'filehound', 'filepack.superbr.org', 'flashget', 'flickbot', 'fluffy', 'frontpage', 'foobot', 'galaxyBot', 'generic', 'getbot ', 'getleft', 'getright', 'getsmart', 'geturl', 'getweb', 'gigabaz', 'girafabot', 'go-ahead-got-it', 'go!zilla', 'gornker', 'grabber', 'grabnet', 'grafula', 'green research', 'harvest', 'havindex', 'hhjhj@yahoo', 'hloader', 'hmview', 'homepagesearch', 'htmlparser', 'hulud', 'http agent', 'httpconnect', 'httpdown', 'http generic', 'httplib', 'httrack', 'humanlinks', 'ia_archiver', 'iaea', 'ibm_planetwide', 'image stripper', 'image sucker', 'imagefetch', 'incywincy', 'indy', 'infonavirobot', 'informant', 'interget', 'internet explore', 'infospiders', 'internet ninja', 'internetlinkagent', 'interneteseer.com', 'ipiumbot', 'iria', 'irvine', 'jbh', 'jeeves', 'jennybot', 'jetcar', 'joc web spider', 'jpeg hunt', 'justview', 'kapere', 'kdd explorer', 'kenjin.spider', 'keyword.density', 'kwebget', 'lachesis', 'larbin', 'laurion(dot)com', 'leechftp', 'lexibot', 'lftp', 'libweb', 'links aromatized', 'linkscan', 'link*sleuth', 'linkwalker', 'libwww', 'lightningdownload', 'likse', 'lwp', 'mac finder', 'mag-net', 'magnet', 'marcopolo', 'mass', 'mata.hari', 'mcspider', 'memoweb', 'microsoft url control', 'microsoft.url', 'midown', 'miixpc', 'minibot', 'mirror', 'missigua', 'mister.pix', 'mmmtocrawl', 'moget', 'mozilla/2', 'mozilla/3.mozilla/2.01', 'mozilla.*newt', 'multithreaddb', 'munky', 'msproxy', 'nationaldirectory', 'naverrobot', 'navroad', 'nearsite', 'netants', 'netcarta', 'netcraft', 'netfactual', 'netmechanic', 'netprospector', 'netresearchserver', 'netspider', 'net vampire', 'newt', 'netzip', 'nicerspro', 'npbot', 'octopus', 'offline.explorer', 'offline explorer', 'offline navigator', 'opaL', 'openfind', 'opentextsitecrawler', 'orangebot', 'packrat', 'papa foto', 'pagegrabber', 'pavuk', 'pbwf', 'pcbrowser', 'personapilot', 'pingalink', 'pockey', 'program shareware', 'propowerbot/2.14', 'prowebwalker', 'proxy', 'psbot', 'psurf', 'puf', 'pushsite', 'pump', 'qrva', 'quepasacreep', 'queryn.metasearch', 'realdownload', 'reaper', 'recorder', 'reget', 'replacer', 'repomonkey', 'rma', 'robozilla', 'rover', 'rpt-httpclient', 'rsync', 'rush=', 'searchexpress', 'searchhippo', 'searchterms.it', 'second street research', 'seeker', 'shai', 'sitecheck', 'sitemapper', 'sitesnagger', 'slysearch', 'smartdownload', 'snagger', 'spacebison', 'spankbot', 'spanner', 'spegla', 'spiderbot', 'spiderengine', 'sqworm', 'ssearcher100', 'star downloader', 'stripper', 'sucker', 'superbot', 'surfwalker', 'superhttp', 'surfbot', 'surveybot', 'suzuran', 'sweeper', 'szukacz/1.4', 'tarspider', 'takeout', 'teleport', 'telesoft', 'templeton', 'the.intraformant', 'thenomad', 'tighttwatbot', 'titan', 'tocrawl/urldispatcher', 'toolpak', 'traffixer', 'true_robot', 'turingos', 'turnitinbot', 'tv33_mercator', 'uiowacrawler', 'urldispatcherlll', 'url_spider_pro', 'urly.warning ', 'utilmind', 'vacuum', 'vagabondo', 'vayala', 'vci', 'visualcoders', 'visibilitygap', 'vobsub', 'voideye', 'vspider', 'w3mir', 'webauto', 'webbandit', 'web.by.mail', 'webcapture', 'webcatcher', 'webclipping', 'webcollage', 'webcopier', 'webcopy', 'webcraft@bea', 'web data extractor', 'webdav', 'webdevil', 'webdownloader', 'webdup', 'webenhancer', 'webfetch', 'webgo', 'webhook', 'web.image.collector', 'web image collector', 'webinator', 'webleacher', 'webmasters', 'webmasterworldforumbot', 'webminer', 'webmirror', 'webmole', 'webreaper', 'websauger', 'websaver', 'website.quester', 'website quester', 'websnake', 'websucker', 'web sucker', 'webster', 'webreaper', 'webstripper', 'webvac', 'webwalk', 'webweasel', 'webzip', 'wget', 'widow', 'wisebot', 'whizbang', 'whostalking', 'wonder', 'wumpus', 'wweb', 'www-collector-e', 'wwwoffle', 'wysigot', 'xaldon', 'xenu', 'xget', 'x-tractor', 'zeus');
        $check = str_replace($ct_rules, '*', strtolower(self::get_user_agent()));
        if (strtolower(self::get_user_agent()) !== $check) {
            self::logs('Bots attack');
            if (Config::$ATTACK_BLOCK_SCREEN) {
                exit(self::SecurityWarningTemplate());
            }
        }
    }

    /** Invalid request method check */
    if (Config::$PROTECTION_REQUEST_METHOD) {
        if (strtolower(self::get_request_method()) !== 'get' && strtolower(self::get_request_method()) !== 'head' && strtolower(self::get_request_method()) !== 'post' && strtolower(self::get_request_method()) !== 'put') {
            self::logs('Invalid request method');
            if (Config::$ATTACK_BLOCK_SCREEN) {
                exit(self::SecurityWarningTemplate());
            }
        }
    }

    if (Config::$PROTECTION_DOS) {
        /** @noinspection NotOptimalIfConditionsInspection */
        if (self::get_user_agent() === '' || self::get_user_agent() === '-') {
            self::logs('DOS attack');
            if (Config::$ATTACK_BLOCK_SCREEN) {
                exit(self::SecurityWarningTemplate());
            }
        }
    }


    /** protection aganist union sql attack */
    if (Config::$PROTECTION_UNION_SQL) {
        $stop = 0;
        $ct_rules = array('*/from/*', '*/insert/*', '+into+', '%20into%20', '*/into/*', ' into ', 'into', '*/limit/*', 'not123exists*', '*/radminsuper/*', '*/select/*', '+select+', '%20select%20', ' select ', '+union+', '%20union%20', '*/union/*', ' union ', '*/update/*', '*/where/*');
        $check = str_replace($ct_rules, '*', self::get_query_string());
        if (self::get_query_string() !== $check) {
            $stop++;
        }
        if (preg_match(self::get_regex_union(), self::get_query_string())) {
            $stop++;
        }
        if (preg_match('/([OdWo5NIbpuU4V2iJT0n]{5}) /', rawurldecode(self::get_query_string()))) {
            $stop++;
        }
        if (false !== strpos(rawurldecode(self::get_query_string()), '*')) {
            $stop++;
        }
        if (!empty($stop)) {
            self:: logs('Union attack');
            if (Config::$ATTACK_BLOCK_SCREEN) {
                exit(self::SecurityWarningTemplate());
            }
        }
    }


    /** protection click attack */
    if (Config::$PROTECTION_CLICK_ATTACK) {
        $ct_rules = array('/*', 'c2nyaxb0', '/*');
        if (self::get_query_string() !== str_replace($ct_rules, '*', self::get_query_string())) {
            self::logs('Click attack');
            if (Config::$ATTACK_BLOCK_SCREEN) {
                exit(self::SecurityWarningTemplate());
            }
        }
    }

    if (Config::$PROTECTION_XSS_ATTACK) {
        $ct_rules = array('eval', 'xmlns', 'xlink:href', 'FScommand', 'style', 'http\:\/\/', 'https\:\/\/', 'cmd=', '&cmd', 'exec', 'concat', './', '../', 'http:', 'h%20ttp:', 'ht%20tp:', 'htt%20p:', 'http%20:', 'https:', 'h%20ttps:', 'ht%20tps:', 'htt%20ps:', 'http%20s:', 'https%20:', 'ftp:', 'f%20tp:', 'ft%20p:', 'ftp%20:', 'ftps:', 'f%20tps:', 'ft%20ps:', 'ftp%20s:', 'ftps%20:', '.php?url=');
        $check = str_replace($ct_rules, '*', self::get_query_string());
        if (self::get_query_string() !== $check) {
            self::logs('XSS attack');
            if (Config::$ATTACK_BLOCK_SCREEN && Config::$INTELLI_PROCESS) {
                self::set_query_string($check);
                if (self::get_query_string() !== $check) {
                    exit(self::SecurityWarningTemplate());
                }
            } elseif (Config::$ATTACK_BLOCK_SCREEN) {
                exit(self::SecurityWarningTemplate());
            } else {
                self::set_query_string($check);
            }
        }
    }

    if (Config::$PROTECTION_IDENTITY_THEFT) {
        if (session_id() === '') {
            session_start();
        }
        if (isset($_SESSION['cv_uip'])) {
            $uip = hash('SHA512', self::get_user_agent() . 'cv_vc', false);
            if ($uip !== $_SESSION['cv_uip']) {
                if (Config::$ATTACK_BLOCK_SCREEN && Config::$INTELLI_PROCESS) {
                    self::logs('Blocked Session hijack');
                    if (!session_regenerate_id()) {
                        exit(self::SecurityWarningTemplate());
                    }
                    self::set_new_server('cv_seschng', 'true');
                } elseif (Config::$ATTACK_BLOCK_SCREEN) {
                    self::logs('Blocked Session hijack');
                    exit(self::SecurityWarningTemplate());
                } else {
                    session_regenerate_id();
                    self::set_new_server('cv_seschng', 'true');
                }
            }
        } else {
            $_SESSION['cv_uip'] = hash('SHA512', self::get_user_agent() . 'cv_vc', false);
        }

    }


}

public static function logs($type)
{

    $log = new Logger(Config::getServerHome() . '/logs');


    $msg = date('j-m-Y H:i:s') . " | $type | IP: " . self::get_ip() . ' ] | DNS: ' . self::gethostbyaddr() . " | Agent: " . self::get_user_agent() . ' | URL: ' . self::$current_url . ' | Referer: ' . self::get_referer() . "\n\n";

    $log->log($msg,$log::CRITICAL);

    if (Config::$ACTIVE_LOG) {

        //   self::push_email('Cloud Valkyrie Security ' . strip_tags($_SERVER['SERVER_NAME']), "Broos Cloud Security logs of " . strip_tags($_SERVER['SERVER_NAME']) . "\n" . str_replace('|', "\n", $msg));
    }
}


private static function SecurityWarningTemplate()
{

$x = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport"
          content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no,minimal-ui">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <meta name="author" content="bruce@Broos action BA"/>
    <title>Cloud Valkyrie</title>
    <style type="text/css">body, p, div, img {margin: 0;padding: 0;}img {border: 0}body {width: 100%;height: 100%;background-color: #EBEBEB;}.main {color: #A9A9A9;width: 100%;height: 100%;}.main .cont {width: 100%;height: 80px;position: absolute;left: 0px;top: 50%;margin-top: -40px;}.cont .as-pic {font-size: 32px;height: 1.5em;width: 1.5em;border-left: 0.16em solid #A9A9A9;border-right: 0.16em solid #A9A9A9;position: relative;margin: 0 auto;}.cont .as-pic span {width: 0.8em;height: 0.8em;position: absolute;z-index: 5;}.cont .as-pic .ball1 {left: -0.48em;top: -0.48em;border-radius: 0.65em;background-color: gold;}.cont .as-pic .ball2 {right: -0.48em;top: -0.48em;border-radius: 0.65em;background-color: green;}.cont .as-pic .ball3 {left: -0.48em;bottom: -0.48em;border-radius: 0.65em;background-color: blue;}.cont .as-pic .ball4 {width: 1.1em;height: 1.1em;right: -0.63em;bottom: -0.63em;border-radius: 0.7em;background-color: red;border: 0.16em solid orange;text-align: center;line-height: 1.1em;font-weight: bold;}.cont .as-pic:after {display: block;content: "";position: absolute;z-index: 4;width: 1.5em;height: 0.16em;left: 0px;top: 0px;background-color: #A9A9A9;transform-origin: left center;transform: rotate(45deg);-webkit-transform-origin: left center;-webkit-transform: rotate(45deg);}.cont #text {text-align: center;margin-top: 30px;font-family: "Microsoft Yahei", Roboto, Tahoma, Arial, "Droid Sans", "Helvetica Neue", "Droid Sans Fallback", "Heiti SC", "Hiragino Sans GB", Simsun, sans-self;font-size: 18px;}html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td {margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: baseline;background: transparent}html {overflow-x: hidden;padding: 0 !important;margin: 0 !important}ol, ul {list-style: none}a {text-decoration: none;-webkit-transition: all .2s linear;-moz-transition: all .2s linear;transition: all .2s linear}a:hover {text-decoration: none}body {font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;font-weight: 300;font-size: 16px;line-height: 28px;color: #666;background: #fff;height: 100%}article, aside, details, dialog, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block}.clearfix:after {content: ".";visibility: hidden;display: block;clear: both;height: 0;font-size: 0}.left-float {float: left}.right-float {float: right}.align-center {text-align: center}strong {font-weight: bold}em {font-style: italic}hr {border: 0;clear: both;margin: 25px 0;height: 1px;border-bottom: 1px solid #d4d4d4}img {max-width: 100%;vertical-align: middle;border: 0;-ms-interpolation-mode: bicubic;opacity: 1}h1, h2, h3, h4, h5, h6 {font-weight: 300}h1 {font-size: 50px;line-height: 50px;font-weight: 500;clear: both;color: #fff;margin-bottom: 5px;text-transform: uppercase}h2 {font-size: 32px;line-height: 32px;font-weight: 400;color: #1fb4da;text-transform: uppercase}h3 {font-size: 20px;line-height: 20px;color: #eee}p {margin-bottom: 20px;color: #656565;font-weight: 300}header .header-inner {clear: both}section {position: relative}.section-inner {position: relative;padding: 40px 0}.row .section-inner:first-child {padding-bottom: 0}.wrapper {max-width: 980px;margin: 0 auto}.wrapper-center {text-align: center}.row {position: relative;padding-left: 70px;padding-right: 70pxpadding-top: 25px;text-align: left;overflow: hidden;background-color: #f9f9f9}.row-darker {background: #f0f0f0;border-bottom: 1px solid #e9e9e9;border-top: 1px solid #e9e9e9}.footer {background: #1a1a1a;padding: 50px 0;text-align: center;left: 0px;bottom: 0px;width: 100%;}footer span.copyright {color: #777;margin-top: 0;margin-bottom: 0;font-size: 12px;text-transform: uppercase;letter-spacing: 2px;line-height: 19px;font-weight: 400}footer span.copyright a {color: #1fb4da;-webkit-transition: all .3s ease;-moz-transition: all .3s ease;transition: all .3s ease}footer span.copyright a:hover {color: #fff}footer .social {display: block;clear: both;cursor: default;line-height: 1;margin-top: 10px;text-align: center}footer .social a {padding: 0 5px}footer .social a i.fa {font-size: 16px;color: #999;-webkit-transition: all .3s ease;-moz-transition: all .3s ease;transition: all .3s ease}footer .social a:hover i.fa {color: #fff}@media (max-width: 767px) {.wrapper {width: 300px}h1 {font-size: 32px;line-height: 32px}h2 {font-size: 24px;line-height: 24px}h3 {font-size: 18px;line-height: 18px}}article {background: #ffffff;border-radius: 3px;box-shadow: 0 1px 5px rgba(0, 0, 0, 0.25);color: #222;top: 20%;width: 75%;left: 0;position: fixed;margin: 0 auto;text-align: left;right: 0;z-index: 2;padding: 30px;}header {position: relative;left: 0;top: 0;width: 100%;padding: 60px 0;background: linear-gradient(-45deg, #EE7752, #E73C7E, #23A6D5, #23D5AB);background-size: 400% 400%;-webkit-animation: Gradient 45s ease infinite;-moz-animation: Gradient 45s ease infinite;animation: Gradient 45s ease infinite;}@-webkit-keyframes Gradient {0% {background-position: 0% 50%}50% {background-position: 100% 50%}100% {background-position: 0% 50%}}@-moz-keyframes Gradient {0% {background-position: 0% 50%}50% {background-position: 100% 50%}100% {background-position: 0% 50%}}@keyframes Gradient {0% {background-position: 0% 50%}50% {background-position: 100% 50%}100% {background-position: 0% 50%}}h1 {margin: 0;}.button {background-color: #4285F5;color: #fff;padding: 8px 16px;border-radius: 2px;text-decoration: none;text-transform: uppercase;}.button:hover {text-decoration: none;}</style>
</head>
<body>
<header>

    <div class="header-inner wrapper wrapper-center clearfix">


        <div class="cont">
            <div class="as-pic">
                <span class="ball1"></span>
                <span class="ball2"></span>
                <span class="ball3"></span>
                <span class="ball4">V</span>
            </div>
        </div>


    </div>
</header>
<section class="row">
    <div class="mainp">
        <article>
            <div class="article">

                <h2 style="text-align: center;"><span
                            style="color: #ff0000;"><strong>Cloud Valkyrie Security System</strong></span></h2>
                <hr>
                <p style="text-align: left;"><span style="color: #800080;"><strong>The HTTP requested is not safe. Our Security System has Detected a possible threat in your request, And authorization to overide such action failed.
        <br/><br/></strong><span style="color: #808080; text-align-all: left;">If you are sure there is no threat in your request or the system miss judged your request or want to find how how our security works, please email us. </span></span>
                </p>
                <a href="javascript:history.go(0);" class="button">Back to main page</a>
                <p style="text-align: center;"><span style="color: #800080;">Your IP : ${IP}


                <p>
                    <small>
                        <!-- Inserting the current date to reassure people. -->
                        <script type="text/javascript">
                            <!--
                            var currentTime = new Date()
                            var month = currentTime.getMonth() + 1
                            var day = currentTime.getDate()
                            var year = currentTime.getFullYear()
                            document.write(month + "/" + day + "/" + year)
                            //-->
                        </script>
                        V. ${VERSION}
                        &nbsp; &nbsp; &nbsp; &nbsp;<b style="text-align:right">Broos Action Inc</b>
                    </small>
                </p>
                </span></p>
            </div>
        </article>
    </div>
</section>
  
</body>';
    return str_replace(array('${VERSION}', '${IP}'), array(Config::getVersion(), self::get_ip()), $x);
}

}