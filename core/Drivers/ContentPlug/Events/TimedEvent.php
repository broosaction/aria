<?php

namespace Core\Drivers\ContentPlug\Events;

class TimedEvent implements SmartPlugEvent
{
    /**
     * The time interval between two event triggers.
     *
     * @var int
     */
    protected $period = 1;

    /**
     * The creation time of the event.
     *
     * @var int
     */
    private $start = 0;

    /**
     * @inheritdoc
     */
    public function check()
    {
        if ($this->start === 0)
            $this->start = time();
        return Utils::timeMod($this->start, $this->period) === 0;
    }

    /**
     * Get Updated Data.
     *
     * @return string
     */
    public function update()
    {
        // TODO: Implement update() method.
    }
}