<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 14 /Apr, 2020 @ 4:57
 */

namespace Core\joi\System;


use function gethostname;

class Os
{


    /** @var */
    protected $osname;


    /**
     * @return bool
     */
    public function supported() {
        return true;
    }

    /**
     * @return string
     */
    public function getHostname() {
        return shell_exec('hostname');
    }

    /**
     * @return string
     */
    public function getMemory() {
        $memory = shell_exec('cat /proc/meminfo  | grep -i \'MemTotal\' | cut -f 2 -d ":" | awk \'{$1=$1}1\'');
        $memory = explode(' ', $memory);
        $memory = round($memory[0] / 1024);
        if ($memory < 1024) {
            $memory .= ' MB';
        } else {
            $memory = round($memory / 1024, 1) . ' GB';
        }
        return $memory;
    }

    /**
     * @return string
     */
    public function getCPUName() {
        $cpu   = shell_exec('cat /proc/cpuinfo  | grep -i \'Model name\' | cut -f 2 -d ":" | awk \'{$1=$1}1\'');
        $cores = shell_exec('cat /proc/cpuinfo  | grep -i \'cpu cores\' | cut -f 2 -d ":" | awk \'{$1=$1}1\'');
        if ($cores === 1) {
            $cores = ' (' . $cores . ' core)';
        } else {
            $cores = ' (' . $cores . ' cores)';
        }
        return $cpu . ' ' . $cores;
    }

    /**
     * @return string
     */
    public function getTime() {
        return shell_exec('date');
    }

    /**
     * @return string
     */
    public function getUptime() {
        return shell_exec('uptime -p');
    }

    /**
     * @return string
     */
    public function getTimeServers() {
        $servers = shell_exec('cat /etc/ntp.conf |grep  \'^pool\' | cut -f 2 -d " "');
        $servers .= ' ' . shell_exec('cat /etc/systemd/timesyncd.conf |grep  \'^NTP=\' | cut -f 2 -d " "');
        return $servers;
    }

    /**
     * @return array
     */
    public function getNetworkInfo() {
        $result = [];
        $result['hostname'] = gethostname();
        $dns = shell_exec('cat /etc/resolv.conf |grep -i \'^nameserver\'|head -n1|cut -d \' \' -f2');
        $result['dns'] = $dns;
        $gw = shell_exec('ip route | awk \'/default/ { print $3 }\'');
        $result['gateway'] = $gw;
        return $result;
    }

    /**
     * @return array
     */
    public function getNetworkInterfaces() {
        $interfaces = glob('/sys/class/net/*');
        $result = [];

        foreach ($interfaces as $interface) {
            $iface              = [];
            $iface['interface'] = basename($interface);
            $iface['mac']       = shell_exec('ip addr show dev ' . $iface['interface'] . ' | grep "link/ether " | cut -d \' \' -f 6  | cut -f 1 -d \'/\'');
            $iface['ipv4']      = shell_exec('ip addr show dev ' . $iface['interface'] . ' | grep "inet " | cut -d \' \' -f 6  | cut -f 1 -d \'/\'');
            $iface['ipv6']      = shell_exec('ip -o -6 addr show ' . $iface['interface'] . ' | sed -e \'s/^.*inet6 \([^ ]\+\).*/\1/\'');
            if ($iface['interface'] !== 'lo') {
                $iface['status'] = shell_exec('cat /sys/class/net/' . $iface['interface'] . '/operstate');
                $iface['speed']  = shell_exec('cat /sys/class/net/' . $iface['interface'] . '/speed');
                if ($iface['speed'] !== '') {
                    $iface['speed'] .= 'Mbps';
                } else {
                    $iface['speed'] = 'unknown';
                }

                $duplex = shell_exec('cat /sys/class/net/' . $iface['interface'] . '/duplex');
                if ($duplex !== '') {
                    $iface['duplex'] = 'Duplex: ' . $duplex;
                } else {
                    $iface['duplex'] = '';
                }
            } else {
                $iface['status'] = 'up';
                $iface['speed']  = 'unknown';
                $iface['duplex'] = '';
            }
            $result[] = $iface;
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getDiskInfo() {
        $blacklist = ['', 'Type', 'tmpfs', 'devtmpfs'];
        $data  = shell_exec('df -T');
        $lines = preg_split('/[\r\n]+/', $data);

        foreach ($lines as $line) {
            $entry = preg_split('/\s+/', trim($line));
            if (isset($entry[1]) && !in_array($entry[1], $blacklist, true)) {
                $items = [];
                $items['device']    = $entry[0];
                $items['fs']        = $entry[1];
                $items['used']      = $entry[3];
                $items['available'] = $entry[4];
                $items['percent']   = $entry[5];
                $items['mount']     = $entry[6];
                $result[] = $items;
            }
        }
        return $result;
    }

    public function getDiskData() {
        $disks = $this->getDiskInfo();
        $data = array();
        $i = 0;
        foreach ($disks as $disk) {
            $data[$i] = [
                round(($disk['used']) / 1024 / 1024, 1),
                round($disk['available'] / 1024 / 1024, 1)
            ];
            $i++;
        }

//		debug data
        //		$data = array('0'=>array(1,2),'1'=>array(4,5),'2'=>array(3,1));

        return $data;
    }


    /**
     * Get current CPU load average
     *
     * @return array load average with three values, 1/5/15 minutes average.
     */
    protected function getProcessorUsage() {
        // get current system load average.
        $loadavg = sys_getloadavg();

        // check if we got any values back.
        if (!(is_array($loadavg) && count($loadavg) === 3)) {
            // either no array or too few array keys.
            // returning back zeroes to prevent any errors on JS side.
            $loadavg = 'N/A';
        }

        return [
            'loadavg' => $loadavg
        ];
    }

    /**
     * Get available and free memory including both RAM and Swap
     *
     * @return array with the two values 'mem_free' and 'mem_total'
     */
    protected function getMemoryUsage() {
        $memoryUsage = false;
        if (@is_readable('/proc/meminfo')) {
            // read meminfo from OS
            $memoryUsage = file_get_contents('/proc/meminfo');
        }
        //If FreeBSD is used and exec()-usage is allowed
        if (PHP_OS === 'FreeBSD' && $this->is_function_enabled('exec')) {
            //Read Swap usage:
            exec("/usr/sbin/swapinfo", $return, $status);
            if ($status === 0 && count($return) > 1) {
                $line = preg_split("/[\s]+/", $return[1]);
                if(count($line) > 3) {
                    $swapTotal = (int)$line[3];
                    $swapFree = $swapTotal - (int)$line[2];
                }
            }
            unset($status, $return);
            //Read Memory Usage
            exec("/sbin/sysctl -n hw.physmem hw.pagesize vm.stats.vm.v_inactive_count vm.stats.vm.v_cache_count vm.stats.vm.v_free_count", $return, $status);
            if ($status === 0) {
                $return = array_map('intval', $return);
                if ($return === array_filter($return, 'is_int')) {
                    return [
                        'mem_total' => (int)$return[0]/1024,
                        'mem_free' => (int)$return[1] * ($return[2] + $return[3] + $return[4]) / 1024,
                        'swap_free' => (isset($swapFree)) ? $swapFree : 'N/A',
                        'swap_total' => (isset($swapTotal)) ? $swapTotal : 'N/A'
                    ];
                }
            }
        }
        // check if determining memoryUsage failed
        if ($memoryUsage === false) {
            return ['mem_free' => 'N/A', 'mem_total' => 'N/A', 'swap_free' => 'N/A', 'swap_total' => 'N/A'];
        }
        $array = explode(PHP_EOL, $memoryUsage);
        // the last value is a empty string after explode, skip it
        $values = array_slice($array, 0, count($array) - 1);
        $data = [];
        foreach($values as $value) {
            [$k, $v] = preg_split('/[\s:]+/', $value);
            $data[$k] = $v;
        }

        if (array_key_exists('MemAvailable', $data)) {
            // MemAvailable is only present in newer kernels (after 2014).
            $available = $data['MemAvailable'];
        } else {
            $available = $data['MemFree'];
        }

        return [
            'mem_free' => (int)$available,
            'mem_total' => (int)$data['MemTotal'],
            'swap_free' => (int)$data['SwapFree'],
            'swap_total' => (int)$data['SwapTotal']
        ];
    }

    /**
     * Checks if a function is available. Borrowed from
     * https://github.com/nextcloud/server/blob/2e36069e24406455ad3f3998aa25e2a949d1402a/lib/private/legacy/helper.php#L475
     *
     * @param string $function_name
     * @return bool
     */
    public function is_function_enabled($function_name) {
        if (!function_exists($function_name)) {
            return false;
        }
       // if ($this->phpIni->listContains('disable_functions', $function_name)) {
         //   return false;
        //}
        return true;
    }
}