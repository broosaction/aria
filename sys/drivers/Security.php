<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/11/2019
 * Time: 22:11
 */

namespace Core\drivers;


use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**

 */
Class Security
{
    private static $inurl;
    public static $admin_mail = 'broosaction@gmail.com';
    public static $apiKey = '';
    public static $send_email = false;
    public static $charset = 'UTF-8';
    public static $neutral = false;           //Automatically changes
    public static $server_home;

    public function __construct($run=false)
    {

        if($run) {
            self::secure();                       // recreate the Security maze wall
            //self::findRuners();                   // Identify the maze runners
        }
    }

   public static function secure()
    {


        define('PHP_FIREWALL_PROTECTION_RANGE_IP_DENY', false);
        define('PHP_FIREWALL_PROTECTION_RANGE_IP_SPAM', false);
        define('PHP_FIREWALL_PROTECTION_URL', true );
        define('PHP_FIREWALL_PROTECTION_REQUEST_SERVER', true );
        define('PHP_FIREWALL_PROTECTION_SANTY', true );
        define('PHP_FIREWALL_PROTECTION_BOTS', true );
        define('PHP_FIREWALL_PROTECTION_REQUEST_METHOD', true );
        define('PHP_FIREWALL_PROTECTION_DOS', true );
        define('PHP_FIREWALL_PROTECTION_UNION_SQL', true );
        define('PHP_FIREWALL_PROTECTION_CLICK_ATTACK', true );
        define('PHP_FIREWALL_PROTECTION_XSS_ATTACK', true );
        define('PHP_FIREWALL_PROTECTION_COOKIES', true );
        define('PHP_FIREWALL_PROTECTION_POST', false );
        define('PHP_FIREWALL_PROTECTION_GET', true );
        define('PHP_FIREWALL_PROTECTION_SERVER_OVH', true );
        define('PHP_FIREWALL_PROTECTION_SERVER_KIMSUFI', true );
        define('PHP_FIREWALL_PROTECTION_SERVER_DEDIBOX', true );
        define('PHP_FIREWALL_PROTECTION_SERVER_DIGICUBE', true );
        define('PHP_FIREWALL_PROTECTION_SERVER_OVH_BY_IP', true );
        define('PHP_FIREWALL_PROTECTION_SERVER_KIMSUFI_BY_IP', true );
        define('PHP_FIREWALL_PROTECTION_SERVER_DEDIBOX_BY_IP', true );
        define('PHP_FIREWALL_PROTECTION_SERVER_DIGICUBE_BY_IP', true );

        // get the current url
        $inurl = self::get_env('REQUEST_URI');
        self::$inurl = $inurl;

        $request_method = self::get_request_method();


        //Anti - SQL injection
        if (preg_match('#select|update|delete|concat|create|table|union|length|show_table|mysql_list_tables|mysql_list_fields|mysql_list_dbs#i', $inurl))
        {
            self::logs( 'SQL injection attack' );
            exit(self::SecurityWarningTemplate()); // Not too good for dev env.
        }

        $securityUlrs_url = $_SERVER['QUERY_STRING'];
        if ($securityUlrs_url !== '' && !preg_match('/^[_a-zA-Z0-9-=&]+$/', $securityUlrs_url))
        {
            if(isset($_REQUEST['ua'],$_REQUEST['key'])) {
                self::$neutral = true;
            }else{
                self::logs( 'Query String went in');
               // exit(self::SecurityWarningTemplate());
            }

        }

        //URL protection
        $ct_rules = array( 'absolute_path', 'ad_click', 'alert(', 'alert%20', ' and ', 'basepath', 'bash_history', '.bash_history', 'cgi-', 'chmod(', 'chmod%20', '%20chmod', 'chmod=', 'chown%20', 'chgrp%20', 'chown(', '/chown', 'chgrp(', 'chr(', 'chr=', 'chr%20', '%20chr', 'chunked', 'cookie=', 'cmd', 'cmd=', '%20cmd', 'cmd%20', '.conf', 'configdir', 'config.php', 'cp%20', '%20cp', 'cp(', 'diff%20', 'dat?', 'db_mysql.inc', 'document.location', 'document.cookie', 'drop%20', 'echr(', '%20echr', 'echr%20', 'echr=', '}else{', '.eml', 'esystem(', 'esystem%20', '.exe',  'exploit', 'file\://', 'fopen', 'fwrite', '~ftp', 'ftp:', 'ftp.exe', 'getenv', '%20getenv', 'getenv%20', 'getenv(', 'grep%20', '_global', 'global_', 'global[', 'http:', '_globals', 'globals_', 'globals[', 'grep(', 'g\+\+', 'halt%20', '.history', '?hl=', '.htpasswd', 'http_', 'http-equiv', 'http/1.', 'http_php', 'http_user_agent', 'http_host', '&icq', 'if{', 'if%20{', 'img src', 'img%20src', '.inc.php', '.inc', 'insert%20into', 'ISO-8859-1', 'ISO-', 'javascript\://', '.jsp', '.js', 'kill%20', 'kill(', 'killall', '%20like', 'like%20', 'locate%20', 'locate(', 'lsof%20', 'mdir%20', '%20mdir', 'mdir(', 'mcd%20', 'motd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', 'mcd(', 'mrd(', 'mcd=', 'mod_gzip_status', 'modules/', 'mrd=', 'mv%20', 'nc.exe', 'new_password', 'nigga(', '%20nigga', 'nigga%20', '~nobody', 'org.apache', '+outfile+', '%20outfile%20', '*/outfile/*',' outfile ','outfile', 'password=', 'passwd%20', '%20passwd', 'passwd(', 'phpadmin', 'perl%20', '/perl', 'phpbb_root_path','*/phpbb_root_path/*','p0hh', 'ping%20', '.pl', 'powerdown%20', 'rm(', '%20rm', 'rmdir%20', 'mv(', 'rmdir(', 'phpinfo()', '<?php', 'reboot%20', '/robot.txt' , '~root', 'root_path', 'rush=', '%20and%20', '%20xorg%20', '%20rush', 'rush%20', 'secure_site, ok', 'select%20', 'select from', 'select%20from', '_server', 'server_', 'server[', 'server-info', 'server-status', 'servlet', 'sql=', '<script', '<script>', '</script','script>','/script', 'switch{','switch%20{', '.system', 'system(', 'telnet%20', 'traceroute%20', '.txt', 'union%20', '%20union', 'union(', 'union=', 'vi(', 'vi%20', 'wget', 'wget%20', '%20wget', 'wget(', 'window.open', 'wwwacl', ' xor ', 'xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', '$_request', '$_get', '$request', '$get',  '&aim', '/etc/password','/etc/shadow', '/etc/groups', '/etc/gshadow', '/bin/ps', 'uname\x20-a', '/usr/bin/id', '/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/usr/bin', 'bin/python', 'bin/tclsh', 'bin/nasm', '/usr/x11r6/bin/xterm', '/bin/mail', '/etc/passwd', '/home/ftp', '/home/www', '/servlet/con', '?>', '.txt');
        $check = str_replace($ct_rules, '*', self::get_query_string() );
        if( self::get_query_string() !== $check ) {
            self::logs( 'URL protect');
            if(!isset($_REQUEST['ua']))
            exit(self::SecurityWarningTemplate());
        }

        if ( PHP_FIREWALL_PROTECTION_RANGE_IP_SPAM === true ) {
            $ip_array = array('24', '186', '189', '190', '200', '201', '202', '209', '212', '213', '217', '222' );
            $range_ip = explode('.', self::get_ip() );
            if (in_array($range_ip[0], $ip_array, true)) {
                self::logs( 'IPs Spam list' );
                exit(self::SecurityWarningTemplate());
            }
        }

        if ( PHP_FIREWALL_PROTECTION_RANGE_IP_DENY === true ) {
            $ip_array = array('0', '1', '2', '5', '10', '14', '23', '27', '31', '36', '37', '39', '42', '46', '49', '50', '100', '101', '102', '103', '104', '105', '106', '107', '114', '172', '176', '177', '179', '181', '185', '192', '223', '224' );
            $range_ip = explode('.', self::get_ip() );
            if ( in_array( $range_ip[0], $ip_array ) ) {
                self::logs( 'IPs reserved list' );
                exit(self::SecurityWarningTemplate());
            }
        }


        $ct_rules = Array('applet', 'base', 'bgsound', 'blink', 'embed', 'expression', 'frame', 'javascript', 'layer', 'link', 'meta', 'object', 'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', 'script', 'style', 'title', 'vbscript', 'xml');
        if ( PHP_FIREWALL_PROTECTION_COOKIES === true ) {
            foreach($_COOKIE as $value) {
                $check = str_replace($ct_rules, '*', $value);
                if( $value !== $check ) {
                    self::logs( 'Cookie protect' );
                    unset( $value );
                }
            }
        }

        if ( PHP_FIREWALL_PROTECTION_POST === true ) {
            foreach( $_POST as $value ) {
                $check = str_replace($ct_rules, '*', $value);
                if( $value !== $check ) {
                    self::logs( 'POST protect' );
                    unset( $value );
                }
            }
        }

        if ( PHP_FIREWALL_PROTECTION_GET === true ) {
            foreach( $_GET as $value ) {
                $check = str_replace($ct_rules, '*', $value);
                if( $value !== $check ) {
                    self::logs( 'GET protect' );
                    unset( $value );
                }
            }
        }

        /** Posting from other servers in not allowed */
        if ((PHP_FIREWALL_PROTECTION_REQUEST_SERVER === true) && $request_method === 'POST' && isset($_SERVER['HTTP_REFERER']) && !stripos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'], 0)) {
            self::logs( 'Posting another server' );
            exit(self::SecurityWarningTemplate());
        }

        /** protection aganist bots santy */
        if ( PHP_FIREWALL_PROTECTION_SANTY === true ) {
            $ct_rules = array('rush','highlight=%','perl','chr(','pillar','visualcoder','sess_');
            $check = str_replace($ct_rules, '*', strtolower( self::$inurl) );
            if( strtolower(self::$inurl) !== $check ) {
                self::logs( 'Santy' );
                exit(self::SecurityWarningTemplate());
            }
        }

        // protestion aganist bad bots

        if ( PHP_FIREWALL_PROTECTION_BOTS === true ) {
            $ct_rules = array( '@nonymouse', 'addresses.com', 'ideography.co.uk', 'adsarobot', 'ah-ha', 'aktuelles', 'alexibot', 'almaden', 'amzn_assoc', 'anarchie', 'art-online', 'aspseek', 'assort', 'asterias', 'attach', 'atomz', 'atspider', 'autoemailspider', 'backweb', 'backdoorbot', 'bandit', 'batchftp', 'bdfetch', 'big.brother', 'black.hole', 'blackwidow', 'blowfish', 'bmclient', 'boston project', 'botalot', 'bravobrian', 'buddy', 'bullseye', 'bumblebee ', 'builtbottough', 'bunnyslippers', 'capture', 'cegbfeieh', 'cherrypicker', 'cheesebot', 'chinaclaw', 'cicc', 'civa', 'clipping', 'collage', 'collector', 'copyrightcheck', 'cosmos', 'crescent', 'custo', 'cyberalert', 'deweb', 'diagem', 'digger', 'digimarc', 'diibot', 'directupdate', 'disco', 'dittospyder', 'download accelerator', 'download demon', 'download wonder', 'downloader', 'drip', 'dsurf', 'dts agent', 'dts.agent', 'easydl', 'ecatch', 'echo extense', 'efp@gmx.net', 'eirgrabber', 'elitesys', 'emailsiphon', 'emailwolf', 'envidiosos', 'erocrawler', 'esirover', 'express webpictures', 'extrac', 'eyenetie', 'fastlwspider', 'favorg', 'favorites sweeper', 'fezhead', 'filehound', 'filepack.superbr.org', 'flashget', 'flickbot', 'fluffy', 'frontpage', 'foobot', 'galaxyBot', 'generic', 'getbot ', 'getleft', 'getright', 'getsmart', 'geturl', 'getweb', 'gigabaz', 'girafabot', 'go-ahead-got-it', 'go!zilla', 'gornker', 'grabber', 'grabnet', 'grafula', 'green research', 'harvest', 'havindex', 'hhjhj@yahoo', 'hloader', 'hmview', 'homepagesearch', 'htmlparser', 'hulud', 'http agent', 'httpconnect', 'httpdown', 'http generic', 'httplib', 'httrack', 'humanlinks', 'ia_archiver', 'iaea', 'ibm_planetwide', 'image stripper', 'image sucker', 'imagefetch', 'incywincy', 'indy', 'infonavirobot', 'informant', 'interget', 'internet explore', 'infospiders',  'internet ninja', 'internetlinkagent', 'interneteseer.com', 'ipiumbot', 'iria', 'irvine', 'jbh', 'jeeves', 'jennybot', 'jetcar', 'joc web spider', 'jpeg hunt', 'justview', 'kapere', 'kdd explorer', 'kenjin.spider', 'keyword.density', 'kwebget', 'lachesis', 'larbin',  'laurion(dot)com', 'leechftp', 'lexibot', 'lftp', 'libweb', 'links aromatized', 'linkscan', 'link*sleuth', 'linkwalker', 'libwww', 'lightningdownload', 'likse', 'lwp','mac finder', 'mag-net', 'magnet', 'marcopolo', 'mass', 'mata.hari', 'mcspider', 'memoweb', 'microsoft url control', 'microsoft.url', 'midown', 'miixpc', 'minibot', 'mirror', 'missigua', 'mister.pix', 'mmmtocrawl', 'moget', 'mozilla/2', 'mozilla/3.mozilla/2.01', 'mozilla.*newt', 'multithreaddb', 'munky', 'msproxy', 'nationaldirectory', 'naverrobot', 'navroad', 'nearsite', 'netants', 'netcarta', 'netcraft', 'netfactual', 'netmechanic', 'netprospector', 'netresearchserver', 'netspider', 'net vampire', 'newt', 'netzip', 'nicerspro', 'npbot', 'octopus', 'offline.explorer', 'offline explorer', 'offline navigator', 'opaL', 'openfind', 'opentextsitecrawler', 'orangebot', 'packrat', 'papa foto', 'pagegrabber', 'pavuk', 'pbwf', 'pcbrowser', 'personapilot', 'pingalink', 'pockey', 'program shareware', 'propowerbot/2.14', 'prowebwalker', 'proxy', 'psbot', 'psurf', 'puf', 'pushsite', 'pump', 'qrva', 'quepasacreep', 'queryn.metasearch', 'realdownload', 'reaper', 'recorder', 'reget', 'replacer', 'repomonkey', 'rma', 'robozilla', 'rover', 'rpt-httpclient', 'rsync', 'rush=', 'searchexpress', 'searchhippo', 'searchterms.it', 'second street research', 'seeker', 'shai', 'sitecheck', 'sitemapper', 'sitesnagger', 'slysearch', 'smartdownload', 'snagger', 'spacebison', 'spankbot', 'spanner', 'spegla', 'spiderbot', 'spiderengine', 'sqworm', 'ssearcher100', 'star downloader', 'stripper', 'sucker', 'superbot', 'surfwalker', 'superhttp', 'surfbot', 'surveybot', 'suzuran', 'sweeper', 'szukacz/1.4', 'tarspider', 'takeout', 'teleport', 'telesoft', 'templeton', 'the.intraformant', 'thenomad', 'tighttwatbot', 'titan', 'tocrawl/urldispatcher','toolpak', 'traffixer', 'true_robot', 'turingos', 'turnitinbot', 'tv33_mercator', 'uiowacrawler', 'urldispatcherlll', 'url_spider_pro', 'urly.warning ', 'utilmind', 'vacuum', 'vagabondo', 'vayala', 'vci', 'visualcoders', 'visibilitygap', 'vobsub', 'voideye', 'vspider', 'w3mir', 'webauto', 'webbandit', 'web.by.mail', 'webcapture', 'webcatcher', 'webclipping', 'webcollage', 'webcopier', 'webcopy', 'webcraft@bea', 'web data extractor', 'webdav', 'webdevil', 'webdownloader', 'webdup', 'webenhancer', 'webfetch', 'webgo', 'webhook', 'web.image.collector', 'web image collector', 'webinator', 'webleacher', 'webmasters', 'webmasterworldforumbot', 'webminer', 'webmirror', 'webmole', 'webreaper', 'websauger', 'websaver', 'website.quester', 'website quester', 'websnake', 'websucker', 'web sucker', 'webster', 'webreaper', 'webstripper', 'webvac', 'webwalk', 'webweasel', 'webzip', 'wget', 'widow', 'wisebot', 'whizbang', 'whostalking', 'wonder', 'wumpus', 'wweb', 'www-collector-e', 'wwwoffle', 'wysigot', 'xaldon', 'xenu', 'xget', 'x-tractor', 'zeus' );
            $check = str_replace($ct_rules, '*', strtolower(self::get_user_agent()) );
            if( strtolower(self::get_user_agent()) !== $check ) {
                self::logs( 'Bots attack' );
                exit(self::SecurityWarningTemplate());
            }
        }

        /** Invalid request method check */
        if ( PHP_FIREWALL_PROTECTION_REQUEST_METHOD === true ) {
            if(strtolower(self::get_request_method()) !== 'get' && strtolower(self::get_request_method())!=='head' && strtolower(self::get_request_method())!=='post' && strtolower(self::get_request_method())!=='put') {
                self::logs( 'Invalid request' );
                exit(self::SecurityWarningTemplate());
            }
        }

        /** protection aganist dos attack */
        if ( PHP_FIREWALL_PROTECTION_DOS === true ) {
            if ( self::get_user_agent()===''  || self::get_user_agent() === '-' ) {
                self::logs( 'Dos attack' );
                exit(self::SecurityWarningTemplate());
            }
        }

        /** protection aganist union sql attack */
        if ( PHP_FIREWALL_PROTECTION_UNION_SQL === true ) {
            $stop = 0;
            $ct_rules = array( '*/from/*', '*/insert/*', '+into+', '%20into%20', '*/into/*', ' into ', 'into', '*/limit/*', 'not123exists*', '*/radminsuper/*', '*/select/*', '+select+', '%20select%20', ' select ',  '+union+', '%20union%20', '*/union/*', ' union ', '*/update/*', '*/where/*' );
            $check    = str_replace($ct_rules, '*', self::get_query_string() );
            if( self::get_query_string() !== $check ) {
                $stop++;
            }
            if (preg_match(self::get_regex_union(), self::get_query_string())) {
                $stop++;
            }
            if (preg_match('/([OdWo5NIbpuU4V2iJT0n]{5}) /', rawurldecode( self::get_query_string() ))) {
                $stop++;
            }
            if (false !== strpos(rawurldecode(self::get_query_string()), '*')) {
                $stop++;
            }
            if ( !empty( $stop ) ) {
                self:: logs( 'Union attack' );
                exit(self::SecurityWarningTemplate());
            }
        }

        /** protection click attack */
        if ( PHP_FIREWALL_PROTECTION_CLICK_ATTACK === true ) {
            $ct_rules = array( '/*', 'c2nyaxb0', '/*' );
            if( self::get_query_string() !== str_replace($ct_rules, '*', self::get_query_string() ) ) {
                self::logs( 'Click attack' );
                exit(self::SecurityWarningTemplate());
            }
        }

        /** protection XSS attack */
        if ( PHP_FIREWALL_PROTECTION_XSS_ATTACK === true ) {
            $ct_rules = array( 'eval', 'xmlns', 'xlink:href', 'FScommand', 'style', 'http\:\/\/', 'https\:\/\/', 'cmd=', '&cmd', 'exec', 'concat', './', '../',  'http:', 'h%20ttp:', 'ht%20tp:', 'htt%20p:', 'http%20:', 'https:', 'h%20ttps:', 'ht%20tps:', 'htt%20ps:', 'http%20s:', 'https%20:', 'ftp:', 'f%20tp:', 'ft%20p:', 'ftp%20:', 'ftps:', 'f%20tps:', 'ft%20ps:', 'ftp%20s:', 'ftps%20:', '.php?url=' );
            $check    = str_replace($ct_rules, '*', self::get_query_string() );
            if( self::get_query_string() !== $check ) {
                self::logs( 'XSS attack' );
                exit(self::SecurityWarningTemplate());
            }
        }




        return true;
    }


    /**
     * block session Hijack
     */
    public static function blockSession(){
        if(isset($_SESSION['config.ip']) && $_SESSION['config.ip'] === self::get_ip()){
            return;
        }
        self::logs('Blocked Session hijack');
        exit(self::SecurityWarningTemplate());
    }


    /**
     *
     */
    public static function unset_globals() {
       // if ( ini_get('register_globals') ) {
            $allow = array('_ENV' => 1, '_GET' => 1, '_POST' => 1, '_COOKIE' => 1, '_FILES' => 1, '_SERVER' => 1, '_REQUEST' => 1, 'GLOBALS' => 1);
            foreach ($GLOBALS as $key => $value) {
                if( ! isset( $allow[$key] ) ) unset( $GLOBALS[$key] );
            }
      //  }
    }


    /** functions base
     * @param $st_var
     * @return string
     */
    private static function get_env($st_var): string
    {
        global $HTTP_SERVER_VARS;
        if (isset($_SERVER[$st_var])) {
            return strip_tags( $_SERVER[$st_var] );
        }

        if (isset($_ENV[$st_var])) {
            return strip_tags( $_ENV[$st_var] );
        }

        if (isset($HTTP_SERVER_VARS[$st_var])) {
            return strip_tags( $HTTP_SERVER_VARS[$st_var] );
        }

        if (getenv($st_var)) {
            return strip_tags( getenv($st_var) );
        }

        if(function_exists('apache_getenv') && apache_getenv($st_var, true)) {
            return strip_tags( apache_getenv($st_var, true) );
        }
        return '';
    }


    /**
     * @return string
     */
    public static function get_referer(): string
    {
        if( self::get_env('HTTP_REFERER') ) {
            return self::get_env('HTTP_REFERER');
        }
        return 'no referer';
    }

    public static function get_ip(): string
    {
        if (self::get_env('HTTP_X_FORWARDED_FOR')) {
            return self::get_env('HTTP_X_FORWARDED_FOR');
        }

        if (self::get_env('HTTP_CLIENT_IP')) {
            return self::get_env('HTTP_CLIENT_IP');
        }

        return self::get_env('REMOTE_ADDR');
    }

    public static function get_user_agent(): string
    {
        if(self::get_env('HTTP_USER_AGENT')) {
            return self::get_env('HTTP_USER_AGENT');
        }
        return 'none';
    }

    public static function get_query_string() {
        if( self::get_env('QUERY_STRING') ) {
            return str_replace('%09', '%20', self::get_env('QUERY_STRING'));
        }
        return '';
    }

    public static function get_request_method(): string
    {
        if(self::get_env('REQUEST_METHOD')) {
            return self::get_env('REQUEST_METHOD');
        }
        return 'none';
    }

    public static function get_regex_union() {

        return '#\w?\s?union\s\w*?\s?(select|all|distinct|insert|update|drop|delete)#is';
    }


      // to be replaced with a python / pytouch deep hack implentation
    public static function ddosAttack($url, $number_of_requests=999999999999999){
        for($i = 0; $i<$number_of_requests;$i++) {
            /**/
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_DNS_USE_GLOBAL_CACHE, TRUE);
            curl_setopt($c, CURLOPT_HEADER, 0);
            curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($c, CURLOPT_NOBODY, 0);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10; WOW64;rv:11.0) Gecko Firefox/11.0');
            curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-type: application/x-wwww-form-urlencorded;charset=UTF-8'));
            curl_close();
        }

    }

    public static function getFolderSize($dir): int
    {                                                       // Get folder size

        // Reset
        $size = 0;

        // Loope for each file and count size
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : self::getFolderSize($each);
        }

        // Return total Bytes count
        return $size;
    }

    public static function readableBytes($bytes): string
    {                                       // Convert large bytes to 9.9MB...

        // Already readable
        if ($bytes < 1024) {
            return $bytes . ' Bytes';
        }
        // Covert to KBs
        if ($bytes < 1048576) {
            return substr(round($bytes / 1024, 2), 0, 5) . ' KB';
        }
        // Covert to MBs
        if ($bytes < 1073741824) {
            return substr(round($bytes / 1048576, 2), 0, 5) . ' MB';
        }
        // Covert to GBs
        if ($bytes < 1099511627776) {
            return substr(round($bytes / 1073741824, 2), 0, 5) . ' GB';
        }

        // Covert to TBs

        return substr(round($bytes / 1099511627776, 2), 0, 5) . ' TB';
    }


    public  static function random(int $length,
                             string $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'): string {
        $maxCharIndex = strlen($characters) - 1;
        $randomString = '';

        while($length > 0) {
            $randomNumber = random_int(0, $maxCharIndex);
            $randomString .= $characters[$randomNumber];
            $length--;
        }
        return $randomString;
    }


    /**
     * Verify legacy hashes
     * @param string $message Message to verify
     * @param string $hash Assumed hash of the message
     * @param null|string &$newHash Reference will contain the updated hash
     * @return bool Whether $hash is a valid hash of $message
     */
    protected static function legacyHashVerify($message, $hash, &$newHash = null, $legacySalt = null): bool {

        // Verify whether it matches a legacy PHPass or SHA1 string
        $hashLength = strlen($hash);
        if(($hashLength === 60 && password_verify($message.$legacySalt, $hash)) ||
            ($hashLength === 40 && hash_equals($hash, sha1($message)))) {
            $newHash = self::hash($message);
            return true;
        }

        return false;
    }

    /**
     * Verify V1 (blowfish) hashes
     * @param string $message Message to verify
     * @param string $hash Assumed hash of the message
     * @param null|string &$newHash Reference will contain the updated hash if necessary. Update the existing hash with this one.
     * @return bool Whether $hash is a valid hash of $message
     */
    protected static function verifyHashV1(string $message, string $hash, &$newHash = null, $options= []): bool {
        if(password_verify($message, $hash)) {
            $algo = PASSWORD_BCRYPT;
            if (defined('PASSWORD_ARGON2I')) {
                $algo = PASSWORD_ARGON2I;
            }

            if(password_needs_rehash($hash, $algo, $options)) {
                $newHash = self::hash($message);
            }
            return true;
        }

        return false;
    }

    /**
     * Verify V2 (argon2i) hashes
     * @param string $message Message to verify
     * @param string $hash Assumed hash of the message
     * @param null|string &$newHash Reference will contain the updated hash if necessary. Update the existing hash with this one.
     * @return bool Whether $hash is a valid hash of $message
     */
    protected static function verifyHashV2(string $message, string $hash, &$newHash = null, $option = []) : bool {
        if(password_verify($message, $hash)) {
            if(password_needs_rehash($hash, PASSWORD_ARGON2I, $option)) {
                $newHash = self::hash($message);
            }
            return true;
        }

        return false;
    }

    public static function hash(string $message, $options = []): string {
        if (defined('PASSWORD_ARGON2I')) {
            return 2 . '|' . password_hash($message, PASSWORD_ARGON2I, $options);
        }

        return 1 . '|' . password_hash($message, PASSWORD_BCRYPT, $options);
    }


    /**
     * Get the version and hash from a prefixedHash
     * @param string $prefixedHash
     * @return null|array Null if the hash is not prefixed, otherwise array('version' => 1, 'hash' => 'foo')
     */
    protected static function splitHash(string $prefixedHash) {
        $explodedString = explode('|', $prefixedHash, 2);
        if(count($explodedString) === 2) {
            if((int)$explodedString[0] > 0) {
                return ['version' => (int)$explodedString[0], 'hash' => $explodedString[1]];
            }
        }

        return null;
    }


    /**
     * @param string $message Message to verify
     * @param string $hash Assumed hash of the message
     * @param null|string &$newHash Reference will contain the updated hash if necessary. Update the existing hash with this one.
     * @return bool Whether $hash is a valid hash of $message
     */
    public static function verify(string $message, string $hash, &$newHash = null): bool {
        $splittedHash = self::splitHash($hash);

        if(isset($splittedHash['version'])) {
            switch ($splittedHash['version']) {
                case 2:
                    return self::verifyHashV2($message, $splittedHash['hash'], $newHash);
                case 1:
                    return self::verifyHashV1($message, $splittedHash['hash'], $newHash);
            }
        } else {
            return self::legacyHashVerify($message, $hash, $newHash);
        }


        return false;
    }




    public static function logs( $type ) {


        $log = new Logger('CloudValkyrie ');
        try {

            $log->pushHandler(new StreamHandler(self::getServerHome().'/logs/security.log', Logger::CRITICAL));

        } catch (Exception $e) {

        }

        $msg = date('j-m-Y H:i:s')." | $type | IP: ".self::get_ip(). ' ] | DNS: ' .self::gethostbyaddr()." | Agent: ".self::get_user_agent(). ' | URL: ' .self::$inurl. ' | Referer: ' .self::get_referer()."\n\n";

        $log->addAlert($msg);

        if(self::$send_email) {
            self::push_email('Cloud Valkyrie Security ' . strip_tags($_SERVER['SERVER_NAME']), "Broos Cloud Security logs of " . strip_tags($_SERVER['SERVER_NAME']) . "\n" . str_replace('|', "\n", $msg));
        }
    }

    public static function push_email( $subject, $msg ) {
        $headers = 'From: Cloud Valkyrie Security: ' .self::$admin_mail. ' <' .self::$admin_mail.">\r\n"
            . 'Reply-To: ' .self::$admin_mail."\r\n"
            ."Priority: urgent\r\n"
            ."Importance: High\r\n"
            ."Precedence: special-delivery\r\n"
            ."Organization: Broos Action\r\n"
            ."MIME-Version: 1.0\r\n"
            ."Content-Type: text/html\r\n"
            ."Content-Transfer-Encoding: 8bit\r\n"
            ."X-Priority: 1\r\n"
            ."X-MSMail-Priority: High\r\n"
            . 'X-Mailer: PHP/' . PHP_VERSION ."\r\n"
            ."X-PHPFirewall: 1.0 by Cloud Valkyrie\r\n"
            . 'Date:' . date('D, d M Y H:s:i') . " +0100\n";
        if ( self::$admin_mail !== '' ) {
            @mail(self::$admin_mail, $subject, $msg, $headers);
        }
    }

    public static function gethostbyaddr() {

        if ( @ empty( $_SESSION['PHP_FIREWALL_gethostbyaddr'] ) ) {
            return $_SESSION['PHP_FIREWALL_gethostbyaddr'] = @gethostbyaddr( self::get_ip() );
        }

        return strip_tags( $_SESSION['PHP_FIREWALL_gethostbyaddr'] );

    }

    /**
     * @param $txt
     * @return mixed|string
     */
    public static function getClean($txt){
        $txt = htmlspecialchars($txt);
        $txt = str_replace(array('select', 'update', 'insert', 'where', 'like', 'or', 'and', 'set', 'into', '\'', ';', '>', '<'), array('5ev1ect', 'upd4tee', '1dn5yert', 'w6eere', '1insk', '08r', '4nd', '5eut', '1n8t0', '', '', '', ''), $txt);
        $txt = strip_tags($txt);
        return $txt;
    }

    /**
     * @return mixed
     */
    public static function getServerHome()
    {
        return self::$server_home;
    }

    /**
     * @param mixed $server_home
     */
    public static function setServerHome($server_home)
    {
        self::$server_home = $server_home;
    }

    /**
     * @param string $apiKey
     */
    public static function setApiKey(string $apiKey)
    {
        self::$apiKey = $apiKey;
    }

    /**
     * @param string $admin_mail
     */
    public static function setAdminMail(string $admin_mail)
    {
        self::$admin_mail = $admin_mail;
    }

    /**
     * @param bool $send_email
     */
    public static function setSendEmail(bool $send_email)
    {
        self::$send_email = $send_email;
    }




    public static function SecurityWarningTemplate()
    {

        $x='<!DOCTYPE html>
  <html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no,minimal-ui">
      <meta name="format-detection" content="telephone=no">
      <meta name="apple-mobile-web-app-capable" content="yes">
      <meta name="apple-mobile-web-app-status-bar-style" content="white">
      <meta name="author" content="bruce@Broos action BA"/>
  	<title>Cloud Valkyrie</title>
      <style type="text/css">
  body,p,div,img{margin:0; padding:0;}
  img{border:0}
  body { width:100%; height:100%; background-color:#EBEBEB;}
  .main { color:#A9A9A9;width:100%; height:100%;}
  .main .cont { width:100%; height:80px; position:absolute; left:0px; top:50%; margin-top:-40px;}
  .cont .as-pic { font-size:32px; height:1.5em; width:1.5em; border-left:0.16em solid #A9A9A9; border-right:0.16em solid #A9A9A9; position:relative; margin:0 auto;}
  .cont .as-pic span { width:0.8em; height:0.8em; position:absolute; z-index:5;}
  .cont .as-pic .ball1 { left:-0.48em; top:-0.48em; border-radius:0.65em; background-color:gold;}
  .cont .as-pic .ball2 { right:-0.48em; top:-0.48em; border-radius:0.65em; background-color:green;}
  .cont .as-pic .ball3 { left:-0.48em; bottom:-0.48em; border-radius:0.65em; background-color:blue;}
  .cont .as-pic .ball4 { width:1.1em; height:1.1em; right:-0.63em; bottom:-0.63em; border-radius:0.7em; background-color:red; border:0.16em solid orange; text-align:center; line-height:1.1em; font-weight:bold;}
  .cont .as-pic:after { display:block; content:""; position:absolute; z-index:4; width:1.5em; height:0.16em; left:0px; top:0px; background-color:#A9A9A9;
  transform-origin:left center;
  transform:rotate(45deg);
  -webkit-transform-origin:left center;
  -webkit-transform:rotate(45deg);
  }
  .cont #text { text-align:center; margin-top:30px;font-family:"Microsoft Yahei", Roboto,Tahoma,Arial,"Droid Sans","Helvetica Neue","Droid Sans Fallback","Heiti SC","Hiragino Sans GB",Simsun,sans-self; font-size:18px;}
  	html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td{ margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent}html{overflow-x:hidden;padding:0 !important;margin:0 !important}ol,ul{ list-style:none}a{text-decoration:none;-webkit-transition:all .2s linear; -moz-transition:all .2s linear;transition:all .2s linear}a:hover{text-decoration:none}body{font-family:"Roboto","Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:300;font-size:16px; line-height:28px;color:#666;background:#fff;height:100%}article,aside,details,dialog,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}.clearfix:after{content:".";visibility:hidden;display:block;clear:both;height:0;font-size:0}.left-float{float:left}.right-float{float:right}.align-center{text-align:center}strong{ font-weight:bold}em{ font-style:italic}hr{ border:0; clear:both; margin:25px 0; height:1px; border-bottom:1px solid #d4d4d4}img{ max-width:100%; vertical-align:middle; border:0; -ms-interpolation-mode:bicubic; opacity:1}h1,h2,h3,h4,h5,h6{font-weight:300}h1{ font-size:50px; line-height:50px;font-weight:500; clear:both; color:#fff;margin-bottom:5px;text-transform:uppercase}h2{ font-size:32px; line-height:32px;font-weight:400; color:#1fb4da;text-transform:uppercase}h3{ font-size:20px; line-height:20px; color:#eee}p{margin-bottom:20px; color:#656565; font-weight:300}header .header-inner{clear:both}section{position:relative}.section-inner{position:relative;padding:40px 0}.row .section-inner:first-child{padding-bottom:0}.wrapper{max-width:980px;margin:0 auto}.wrapper-center{ text-align:center}.row{position:relative; padding-left: 70px; padding-right: 70px padding-top: 25px; text-align: left; overflow:hidden;background-color:#f9f9f9}.row-darker{ background:#f0f0f0; border-bottom:1px solid #e9e9e9; border-top:1px solid #e9e9e9}.footer{ background:#1a1a1a; padding:50px 0;text-align:center; left:0px; bottom:0px; width:100%;} footer span.copyright{ color:#777; margin-top:0; margin-bottom:0; font-size:12px; text-transform:uppercase; letter-spacing:2px; line-height:19px; font-weight:400}footer span.copyright a{color:#1fb4da;-webkit-transition:all .3s ease; -moz-transition:all .3s ease;transition:all .3s ease}footer span.copyright a:hover{color:#fff}footer .social{display:block;clear:both;cursor:default;line-height:1;margin-top:10px;text-align:center}footer .social a{padding:0 5px}footer .social a i.fa{font-size:16px;color:#999;-webkit-transition:all .3s ease; -moz-transition:all .3s ease;transition:all .3s ease}footer .social a:hover i.fa{color:#fff}@media (max-width:767px){.wrapper{width:300px}h1{font-size:32px;line-height:32px}h2{font-size:24px;line-height:24px}h3{font-size:18px;line-height:18px}}
  	article {
  background: #ffffff;
  border-radius: 3px;
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.25);
  color: #222;
  top: 20%;
  width: 75%;
  left: 0;
  position: fixed;
  margin: 0 auto;
  text-align: left;
  right: 0;
  z-index: 2;
  padding: 30px;
}
header{position:relative;
left:0;top:0;width:100%;padding:60px 0;
background: linear-gradient(-45deg, #EE7752, #E73C7E, #23A6D5, #23D5AB);
	background-size: 400% 400%;
	-webkit-animation: Gradient 45s ease infinite;
	-moz-animation: Gradient 45s ease infinite;
	animation: Gradient 45s ease infinite;
}
@-webkit-keyframes Gradient {
	0% {
		background-position: 0% 50%
	}
	50% {
		background-position: 100% 50%
	}
	100% {
		background-position: 0% 50%
	}
}

@-moz-keyframes Gradient {
	0% {
		background-position: 0% 50%
	}
	50% {
		background-position: 100% 50%
	}
	100% {
		background-position: 0% 50%
	}
}

@keyframes Gradient {
	0% {
		background-position: 0% 50%
	}
	50% {
		background-position: 100% 50%
	}
	100% {
		background-position: 0% 50%
	}
}
h1 {
  margin: 0;
}
.button {
  background-color: #4285F5;
  color: #fff;
  padding: 8px 16px;
  border-radius: 2px;
  text-decoration: none;
  text-transform: uppercase;
}
.button:hover {
  text-decoration: none;
}

  	</style>
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
      	
         <h2 style="text-align: center;"><span style="color: #ff0000;"><strong>Cloud Valkyrie Security System</strong></span></h2><hr>
        <p style="text-align: left;"><span style="color: #800080;"><strong>The HTTP requested is not safe. Our Security System has Detected a possible threat in your request, And authorization to overide such action failed.
        <br /><br /></strong><span style="color: #808080; text-align-all: left;">If you are sure there is no threat in your request or the system miss judged your request or want to find how how our security works, please email us. </span></span></p>
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
               . v 0.0.9
             &nbsp; &nbsp; &nbsp; &nbsp;<b style="text-align:right">Broos Action Inc</b>
          </small>
        </p> </span></p>
      </div>
</article>
  </div>
  </section>
  
  </body>';
        return str_replace('${IP}', self::get_ip(), $x);
    }
}


