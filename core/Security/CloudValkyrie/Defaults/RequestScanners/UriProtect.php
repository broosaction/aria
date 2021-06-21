<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 15:54
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\joi\System\Utils;
use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Security\Utils\ValkyrieUtils;
use Core\Tools\Timer;

class UriProtect extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_URL) {
            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            $ct_rules = ['absolute_path', 'ad_click', 'alert(', 'alert%20', ' and ', 'basepath', 'bash_history',
                '.bash_history', 'cgi-', 'chmod(', 'chmod%20', '%20chmod', 'chmod=', 'chown%20', 'chgrp%20', 'chown(',
                '/chown', 'chgrp(', 'chr(', 'chr=', 'chr%20', '%20chr', 'chunked', 'cookie=', 'cmd', 'cmd=', '%20cmd',
                'cmd%20', '.conf', 'configdir', 'config.php', 'cp%20', '%20cp', 'cp(', 'diff%20', 'dat?', 'db_mysql.inc',
                'document.location', 'document.cookie', 'drop%20', 'echr(', '%20echr', 'echr%20', 'echr=', '}else{',
                '.eml', 'esystem(', 'esystem%20', '.exe', 'exploit', 'file\://', 'fopen', 'fwrite', '~ftp', 'ftp:',
                'ftp.exe', 'getenv', '%20getenv', 'getenv%20', 'getenv(', 'grep%20', '_global', 'global_', 'global[',
                'http:', '_globals', 'globals_', 'globals[', 'grep(', 'g\+\+', 'halt%20', '.history', '?hl=', '.htpasswd',
                'http_', 'http-equiv', 'http/1.', 'http_php', 'http_user_agent', 'http_host', '&icq', 'if{', 'if%20{',
                'img src', 'img%20src', '.inc.php', '.inc', 'insert%20into', 'ISO-8859-1', 'ISO-', 'javascript\://', '.jsp',
                '.js', 'kill%20', 'kill(', 'killall', '%20like', 'like%20', 'locate%20', 'locate(', 'lsof%20', 'mdir%20',
                '%20mdir', 'mdir(', 'mcd%20', 'motd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', 'mcd(', 'mrd(', 'mcd=',
                'mod_gzip_status', 'modules/', 'mrd=', 'mv%20', 'nc.exe', 'new_password', 'nigga(', '%20nigga', 'nigga%20',
                '~nobody', 'org.apache', '+outfile+', '%20outfile%20', '*/outfile/*', ' outfile ', 'outfile', 'password=',
                'passwd%20', '%20passwd', 'passwd(', 'phpadmin', 'perl%20', '/perl', 'phpbb_root_path', '*/phpbb_root_path/*',
                'p0hh', 'ping%20', '.pl', 'powerdown%20', 'rm(', '%20rm', 'rmdir%20', 'mv(', 'rmdir(', 'phpinfo()', '<?php',
                'reboot%20', '/robot.txt', '~root', 'root_path', 'rush=', '%20and%20', '%20xorg%20', '%20rush', 'rush%20',
                'secure_site, ok', 'select%20', 'select from', 'select%20from', '_server', 'server_', 'server[',
                'server-info', 'server-status', 'servlet', 'sql=', '<script', '<script>', '</script', 'script>', '/script',
                'switch{', 'switch%20{', '.system', 'system(', 'telnet%20', 'traceroute%20', '.txt', 'union%20', '%20union',
                'union(', 'union=', 'vi(', 'vi%20', 'wget', 'wget%20', '%20wget', 'wget(', 'window.open', 'wwwacl', ' xor ',
                'xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', '$_request', '$_get', '$request', '$get',
                '&aim', '/etc/password', '/etc/shadow', '/etc/groups', '/etc/gshadow', '/bin/ps', 'uname\x20-a',
                '/usr/bin/id', '/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/usr/bin', 'bin/python', 'bin/tclsh',
                'bin/nasm', '/usr/x11r6/bin/xterm', '/bin/mail', '/etc/passwd', '/home/ftp', '/home/www', '/servlet/con',
                '?>', '.txt'];

            $check = str_replace($ct_rules, '*', ValkyrieUtils::get_query_string());


            if (ValkyrieUtils::get_query_string() !== $check) {

                $this->results->setIsThreat(true);
                $message = 'URL protect';
                $this->fireEventLog($message);
                $this->defaultEventHandler->log($message);

                    if ($this->intlligentProcess) {
                        $this->results->setSeverityLevel(2);
                        ValkyrieUtils::set_query_string($check);
                        $this->results->setAction(DefaultActions::ANALYZE);
                    } else {
                        $this->results->setSeverityLevel(7);
                        $this->results->setAction(DefaultActions::BLOCK);
                    }
            }
            $runtime->finish();
            $this->results->setBadParameters([ValkyrieUtils::get_query_string()]);
            $this->results->setParameters([ValkyrieUtils::get_query_string()]);
            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);

        }
        return $this->results;
    }

}