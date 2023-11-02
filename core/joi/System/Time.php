<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 08 /Aug, 2020 @ 9:08
 */

namespace Core\Joi\System;


class Time
{

    public function isDay(): bool
    {
        $var_date = date('G');
        return (int)$var_date > 5 && (int)$var_date < 18;
    }

    public function isNight(): bool
    {
        $var_date = date('G');
        return (int)$var_date < 4 && (int)$var_date > 20;
    }

    public function isMorning(): bool
    {
        $var_date = date('G');
        return (int)$var_date > 3 && (int)$var_date < 12;
    }

    public function isAfternoon()
    {
        $var_date = date('G');
        return (int)$var_date > 11 && (int)$var_date < 17;
    }

    public function isEvening()
    {
        $var_date = date('G');
        return (int)$var_date > 16 && (int)$var_date < 21;
    }

    public function stamp2period($timestamp)
    {
        $time = $timestamp;
        //litebase time function by Bruce Mubangwa
        $cur_time = time();
        $time_elapsed = $cur_time - $time;
        //we now get the seconds
        $seconds = $time_elapsed;
        $minutes = round($time_elapsed / 60);
        $hours = round($time_elapsed / 3600);
        $days = round($time_elapsed / 86401);
        $weeks = round($time_elapsed / 604800);
        $months = round($time_elapsed / 2600640);
        $years = round($time_elapsed / 31207680);

        if ($seconds <= 60) {
            return "Just Now";
        }

        if ($minutes <= 60) {
            if ($minutes === 1) {
                return "one minute ago";
            }
            return "$minutes minutes ago";

        }

        if ($hours <= 24) {
            if ($hours === 1) {
                return "an hour ago";
            }
            return "$hours hours ago";

        }

        if ($days <= 7) {
            if ($days === 1) {
                return "yesturday";
            }
            return "$days days ago";

        }

        if ($weeks <= 4.3) {
            if ($weeks === 1) {
                return "a week ago";
            }
            return "$weeks weeks ago";

        }

        if ($months <= 12) {
            if ($months === 1) {
                return "a month ago";
            }

            return "$months months ago";
        }

        if ($years === 1) {
            return "one year ago";
        }
        return "$years years ago";
    }


    public function getGreetings()
    {
        $greeting = (string)$this->isAfternoon();

        if ($this->isMorning()) {
            return 'Good morning';
        }

        if ($this->isAfternoon()) {
            return 'Good Afternoon';
        }

        if ($this->isEvening()) {
            return 'Good Evening';
        }

        if (!$this->isDay()) {
            return 'Sweet Dreams';
        }


        if (date('d-m') === '1-1') {
            $greeting = 'Happy new year';
        }

        return $greeting;
    }

    public static function time_Elapsed_String($ptime)
    {

        $etime = time() - $ptime;
        if ($etime < 45) {
            return 'Just now';
        }
        if ($etime >= 45 && $etime < 90) {
            return 'about a minute ago';
        }
        $day = 24 * 60 * 60;
        if ($etime > $day * 30 && $etime < $day * 45) {
            return 'about a month ago';
        }
        $a = array(
            365 * 24 * 60 * 60 => "year",
            30 * 24 * 60 * 60 => "month",
            24 * 60 * 60 => "day",
            60 * 60 => "hour",
            60 => "minute",
            1 => "second"
        );
        $a_plural = array(
            'year' => "years",
            'month' => "months",
            'day' => "days",
            'hour' => "hours",
            'minute' => "minutes",
            'second' => "seconds"
        );
        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);

                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ' . "ago";
            }
        }
    }

    /**
     * Returns an estimated reading time in a string
     * idea from @link http://briancray.com/posts/estimated-reading-time-web-design/
     * @param string $content the content to be read
     * @return string          estimated read time eg. 1 minute, 30 seconds
     */
    public function estimate_reading_time($content)
    {
        $word_count = str_word_count(strip_tags($content));

        $minutes = floor($word_count / 200);
        $seconds = floor($word_count % 200 / (200 / 60));

        // $str_minutes = ($minutes === 1) ? "minute" : "minutes";
        // $str_seconds = ($seconds === 1) ? "second" : "seconds";
        $str_minutes = ($minutes === 1) ? "min" : "min ";
        $str_seconds = ($seconds === 1) ? "sec" : "sec ";

        if ($minutes === 0) {
            return "{$seconds} {$str_seconds}";
        }
        return "{$minutes} {$str_minutes}: {$seconds} {$str_seconds}";
    }

}