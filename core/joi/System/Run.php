<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 15 /Apr, 2020 @ 13:04
 */

namespace Core\Joi\System;


class Run
{

    private $pipes = array();

    private $descr = array();
    private $command;
    private $output = '';

    /**
     * Run constructor.
     * @param string $command
     * @param null $descr
     * @param null $pipes
     */
    public function __construct(string $command, $descr = null, $pipes = null)
    {
        $this->command = $command;

        if (!isset($descr)) {
            $this->descr = array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w')
            );
        } else {
            $this->descr = $descr;
        }

        if (!isset($pipes)) {
            $this->pipes = $pipes;
        }

        $this->now();
    }

    /**
     *
     */
    private function now(): void
    {
        $process = proc_open($this->command, $this->descr, $this->pipes);
        if (is_resource($process)) {
            while ($f = fgets($this->pipes[1])) {
                $this->output .= $f;
            }
            fclose($this->pipes[1]);
            while ($f = fgets($this->pipes[2])) {
                $this->output .= $f;
            }
            fclose($this->pipes[2]);
            proc_close($process);
        }
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }


}