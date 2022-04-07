<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 10:23
 */

namespace Core\Security\CloudValkyrie;


class ScanResults
{

    protected $scanner = '';

    protected $event = '';

    protected $isThreat = false;

    protected $action = '';

    protected $parameters = array();

    protected $badParameters = array();

    protected $runTime = 0;

    protected $severityLevel = 0;

    protected $message = '';

    /**
     * ScanResults constructor.
     * @param string $scanner
     */
    public function __construct(string $scanner)
    {
        $this->scanner = $scanner;
    }

    /**
     * @return string
     */
    public function getScanner(): string
    {
        return $this->scanner;
    }

    /**
     * @param string $scanner
     */
    public function setScanner(string $scanner): void
    {
        $this->scanner = $scanner;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     */
    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    /**
     * @return bool
     */
    public function isThreat(): bool
    {
        return $this->isThreat;
    }

    /**
     * @param bool $isThreat
     */
    public function setIsThreat(bool $isThreat): void
    {
        $this->isThreat = $isThreat;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getBadParameters(): array
    {
        return $this->badParameters;
    }

    /**
     * @param array $badParameters
     */
    public function setBadParameters(array $badParameters): void
    {
        $this->badParameters = $badParameters;
    }

    /**
     * @return int
     */
    public function getRunTime(): int
    {
        return $this->runTime;
    }


    public function setRunTime($runTime): void
    {
        $this->runTime = $runTime;
    }

    /**
     * @return int
     */
    public function getSeverityLevel(): int
    {
        return $this->severityLevel;
    }

    /**
     * @param int $severityLevel
     */
    public function setSeverityLevel(int $severityLevel): void
    {
        $this->severityLevel = $severityLevel;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }



}