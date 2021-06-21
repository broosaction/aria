<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 10:17
 */

namespace Core\Security\CloudValkyrie\Contracts;


use Core\Security\CloudValkyrie\ScanResults;

interface SecurityEventHandler
{

    /**
     * executed before the scanner
     * @return mixed
     */
    public function beforeScan();


    /**
     * executed after the scan, returns the scan results
     * @param ScanResults $results
     * @return mixed
     */
    public function afterScan(ScanResults $results);

    /**
     * used to write the scan logs
     * @param $string
     * @return mixed
     */
    public function log($string);

}