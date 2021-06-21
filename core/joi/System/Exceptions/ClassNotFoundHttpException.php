<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 31 /May, 2021 @ 20:41
 */

namespace Core\joi\System\Exceptions;


use Exception;
use Throwable;

class ClassNotFoundHttpException extends NotFoundHttpException
{
    protected $class;
    protected $method;

    public function __construct(string $class, ?string $method = null, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->class = $class;
        $this->method = $method;
    }

    /**
     * Get class name
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Get method
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

}