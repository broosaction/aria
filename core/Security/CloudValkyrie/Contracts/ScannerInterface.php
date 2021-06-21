<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 9:26
 */

namespace Core\Security\CloudValkyrie\Contracts;


interface ScannerInterface
{

    public function register($name, $callback);

    /**
     * @param $name
     * @param $callback
     * @return mixed
     */
    public function addEventListener($name, $callback);

    public function scan($intelligentProcess = true, $activeLogging = true);

    /**
     * @param $callback
     * @param $even
     * @return mixed
     */
    public function fireEvent($callback, $even);

    /**
     * @return mixed
     */
    public function getResults();

    /**
     *
     */
    public function prepareDefaults();
}