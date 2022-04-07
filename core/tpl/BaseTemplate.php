<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 1:29
 */

namespace Core\tpl;


use Core\joi\Start;

abstract class BaseTemplate
{


    /**
     * @var
     */
    private $file;

    /**
     * @var
     */
    private $theme;

    /**
     * @var array
     */
    private array $data = array();

    /**
     * BaseTemplate constructor.
     */
    public function __construct()
    {
        $this->_init();
    }

    /**
     * used to set the template compiler
     */
    public function _init(): void
    {

    }

}