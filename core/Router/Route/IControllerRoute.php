<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace  Core\Router\Route;

interface IControllerRoute extends IRoute
{
    /**
     * Get controller class-name
     *
     * @return string
     */
    public function getController(): string;

    /**
     * Set controller class-name
     *
     * @param string $controller
     * @return static
     */
    public function setController(string $controller): self;

}