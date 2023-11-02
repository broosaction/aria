<?php

namespace Core\Drivers\ContentPlug\Events;

interface SmartPlugEvent
{
    /**
     * Check for continue to send event.
     *
     * @return bool
     */
    public function check();

    /**
     * Get Updated Data.
     *
     * @return string
     */
    public function update();
}