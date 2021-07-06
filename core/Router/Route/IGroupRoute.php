<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */
namespace  Core\Router\Route;
use Core\Router\Handlers\IExceptionHandler;
use Core\Router\Http\Request;


interface IGroupRoute extends IRoute
{
    /**
     * Method called to check if a domain matches
     *
     * @param Request $request
     * @return bool
     */
    public function matchDomain(Request $request): bool;

    /**
     * Add exception handler
     *
     * @param IExceptionHandler|string $handler
     * @return static
     */
    public function addExceptionHandler($handler): self;

    /**
     * Set exception-handlers for group
     *
     * @param array $handlers
     * @return static
     */
    public function setExceptionHandlers(array $handlers): self;

    /**
     * Get exception-handlers for group
     *
     * @return array
     */
    public function getExceptionHandlers(): array;

    /**
     * Get domains for domain.
     *
     * @return array
     */
    public function getDomains(): array;

    /**
     * Set allowed domains for group.
     *
     * @param array $domains
     * @return static
     */
    public function setDomains(array $domains): self;

    /**
     * Prepends prefix while ensuring that the url has the correct formatting.
     *
     * @param string $url
     * @return static
     */
    public function prependPrefix(string $url): self;

    /**
     * Set prefix that child-routes will inherit.
     *
     * @param string $prefix
     * @return static
     */
    public function setPrefix(string $prefix): self;

    /**
     * Get prefix.
     *
     * @return string|null
     */
    public function getPrefix(): ?string;
}