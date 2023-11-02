<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 14:31
 */

namespace Core\Security;


use Core\Security\CloudValkyrie\AnalyzeResults;
use Core\Security\CloudValkyrie\Scanners\RequestScanner;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieAuthorizator;
use Core\Security\CloudValkyrie\ValkyrieConfig;

class Valkyrie
{

    protected $results = [];

    Public RequestScanner $requestScanner;

    public AnalyzeResults $analyzeResults;
    private ValkyrieAuthorizator $ValkyrieAuthorizer;

    /**
     * Valkyrie constructor.
     */
    public function __construct()
    {
        $this->requestScanner = new RequestScanner();
        $this->prepareDefaults();
        $this->analyzeResults = new AnalyzeResults();
        $this->ValkyrieAuthorizer = new ValkyrieAuthorizator();
    }

    /**
     *
     */
    protected function prepareDefaults(): void
    {

        $this->requestScanner->prepareDefaults();

    }

    /**
     *
     */
    public function scan(): void
    {

        $this->results['requestScanner'] = $this->requestScanner->scan(ValkyrieConfig::$INTELLIGENCE_PROCESS, ValkyrieConfig::$ACTIVE_LOG);

        $this->analyzeResults->analyze($this->results['requestScanner']);
    }


    /**
     *
     */
    public function invoke($message)
    {
        $this->analyzeResults->_invoke_just_block($message);
    }




    /**
     * @param $name
     * @param $callback
     */
    public function addEventListener($name, $callback): void
    {
        $this->requestScanner->addEventListener($name, $callback);
    }

    /**
     *
     * @param $name
     * @param $callback
     */
    private function addScanEvent($name, $callback): void
    {
        $this->requestScanner->register($name, $callback);
    }

    /**
     * @return ValkyrieAuthorizator
     */
    public function getValkyrieAuthorizer(): ValkyrieAuthorizator
    {
        return $this->ValkyrieAuthorizer;
    }


}
