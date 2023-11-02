<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 14 /Apr, 2020 @ 5:24
 */

namespace Core\Joi\System;


use Core\Joi\Start;

class Statistics
{

    private $offset5Minutes = 300;

    private $offset1Hour = 3600;

    private $offset1Day = 86400;

    private $server;

    /**
     * Statistics constructor.
     * @param Start $server
     */
    public function __construct(Start $server)
    {
        $this->server = $server;
    }


    public function getPhpStatistics(): array
    {
        return [
            'version' => $this->cleanVersion(PHP_VERSION),
            //    'memory_limit' => $this->phpIni->getBytes('memory_limit'),
            //  'max_execution_time' => $this->phpIni->getNumeric('max_execution_time'),
            //  'upload_max_filesize' => $this->phpIni->getBytes('upload_max_filesize'),
        ];
    }

    /**
     * @return array (string => string|int)
     */
    public function getDatabaseStatistics(): array
    {

        return [
            'type' => $this->server->getConfig()->db_connection,
            'version' => $this->databaseVersion(),
            'size' => $this->databaseSize(),
        ];
    }


    public function databaseVersion()
    {
        switch ($this->server->getConfig()->db_connection) {
            case 'sqlite':
            case 'sqlite3':
                $sql = 'SELECT sqlite_version() AS version';
                break;
            case 'mysql':
            case 'pgsql':
            default:
                $sql = 'SELECT VERSION() AS version';
                break;
        }
        $result = $this->server->getDatabase()->getDatabaseEngine()->fetch($sql);

        if ($result) {
            return $this->cleanVersion($result->version);
        }
        return 'N/A';
    }

    /**
     * Copy of phpBB's get_database_size()
     * @link https://github.com/phpbb/phpbb/blob/release-3.1.6/phpBB/includes/functions_admin.php#L2908-L3043
     *
     * @copyright (c) phpBB Limited <https://www.phpbb.com>
     * @license GNU General Public License, version 2 (GPL-2.0)
     *
     * @return int|string
     */
    public function databaseSize()
    {
        $database_size = false;
        // This code is heavily influenced by a similar routine in phpMyAdmin 2.2.0
        switch ($this->server->getConfig()->db_connection) {
            case 'mysql':
                $db_name = $this->server->getConfig()->db_database;
                $sql = 'SHOW TABLE STATUS FROM `' . $db_name . '`';
                $result = $this->server->getDatabase()->getDatabaseEngine()->fetchAll($sql);
                $database_size = 0;
                foreach ($result as $row) {
                    if ((isset($row->Type) && $row->Type !== 'MRG_MyISAM') || (isset($row->Engine) && ($row->Engine === 'MyISAM' || $row->Engine === 'InnoDB'))) {
                        $database_size += $row->Data_length + $row->Index_length;
                    }
                }
                break;
            case 'sqlite':
            case 'sqlite3':
                if (file_exists($this->server->getConfig()->db_host)) {
                    $database_size = filesize($this->server->getConfig()->db_host);
                }
                break;
            case 'pgsql':
                $sql = "SELECT proname
					FROM pg_proc
					WHERE proname = 'pg_database_size'";
                $result = $this->server->getDatabase()->getDatabaseEngine()->fetch($sql);

                if ($result->proname === 'pg_database_size') {
                    $database = $this->server->getConfig()->db_database;
                    if (strpos($database, '.') !== false) {
                        [$database,] = explode('.', $database);
                    }
                    $sql = "SELECT oid
						FROM pg_database
						WHERE datname = ?";
                    $result = $this->server->getDatabase()->getDatabaseEngine()->fetch($sql, $database);

                    $oid = $result->oid;
                    $sql = 'SELECT pg_database_size(?) as size';
                    $result = $this->server->getDatabase()->getDatabaseEngine()->fetch($sql, $oid);

                    $database_size = $result->size;
                }
                break;

        }
        return ($database_size !== false) ? $database_size : 'N/A';
    }

    public function get_db_max_memory()
    {
        return $this->server->getDatabase()->getDatabaseEngine()->fetch('SELECT ( @@key_buffer_size + @@query_cache_size + @@innodb_buffer_pool_size + @@innodb_log_buffer_size + @@max_connections * ( @@read_buffer_size + @@read_rnd_buffer_size + @@sort_buffer_size + @@join_buffer_size + @@binlog_cache_size + @@thread_stack + @@tmp_table_size ) ) / (1024 * 1024 * 1024) AS MAX_MEMORY_GB;')->MAX_MEMORY_GB;
    }

    /**
     * Try to strip away additional information
     *
     * @param string $version E.g. `5.5.30-1+deb.sury.org~trusty+1`
     * @return string `5.5.30`
     */
    protected function cleanVersion($version)
    {
        $matches = [];
        preg_match('/^(\d+)(\.\d+)(\.\d+)/', $version, $matches);
        if (isset($matches[0])) {
            return $matches[0];
        }
        return $version;
    }


    public static function getResourceUsage($timedif)
    {
        $cpuUsage = [];
        foreach (getrusage() as $key => $val) {
            $cpuUsage[$key] = $val;

        }
        //todo
        $time = ((time() / $timedif) * 10);

        $userUsage = -round(($cpuUsage['ru_utime.tv_sec'] * 1e6 + $cpuUsage['ru_utime.tv_usec']) / $time / 10000);
        $systemUsage = -round(($cpuUsage['ru_stime.tv_sec'] * 1e6 + $cpuUsage['ru_stime.tv_usec']) / ($timedif / time()) / 10000);

        return array(
            'user' => $userUsage,
            'system' => $systemUsage,
        );
    }

    public static function memoryPick()
    {
        return number_format(memory_get_peak_usage() / 1000000, 2, '.', ' ');
    }


}