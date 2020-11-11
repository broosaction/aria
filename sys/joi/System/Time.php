<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 08 /Aug, 2020 @ 9:08
 */

namespace Core\joi\System;


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
        return (int)$var_date < 4 && (int)$var_date > 19;
    }

    public function isMorning(): bool
    {
        $var_date = date('G');
        return (int)$var_date > 3 && (int)$var_date < 12;
    }

    public function isAfternoon(){
        $var_date = date('G');
        return (int)$var_date > 11 && (int)$var_date < 17;
    }

    public function isEvening(){
        $var_date = date('G');
        return (int)$var_date > 16 && (int)$var_date < 20;
    }

    public function stamp2period($timestamp){
        $time = $timestamp;
        //litebase time function by Bruce Mubangwa
        $cur_time= time();
        $time_elapsed = $cur_time-$time;
        //we now get the seconds
        $seconds = $time_elapsed;
        $minutes = round($time_elapsed/60);
        $hours = round($time_elapsed/3600);
        $days = round($time_elapsed/86401);
        $weeks =round($time_elapsed/604800);
        $months= round($time_elapsed/2600640);
        $years= round($time_elapsed/31207680);

        if($seconds<=60){
            return "Just Now";
        }
        elseif($minutes<=60){
            if($minutes===1){
                return "one minute ago";
            }
                return "$minutes minutes ago";

        }
        elseif($hours<=24){
            if($hours===1) {
                return "an hour ago";
            }
                return "$hours hours ago";

        }elseif($days<=7){
            if($days===1){
                return "yesturday";
            }
                return "$days days ago";

        }elseif($weeks <= 4.3){
            if($weeks === 1){
                return "a week ago";
            }
                return "$weeks weeks ago";

        }elseif($months <= 12){
            if($months === 1){
                return "a month ago";
            }

            return "$months months ago";
        }else {

            if ($years === 1) {
                return "one year ago";
            }
            return "$years years ago";

        }
    }
 

    public function getGreetings(){
        $greeting = (string)$this->isAfternoon();
        if ($this->isMorning()) {
         return 'Good morning';
        }

        if($this->isAfternoon()) {
            return 'Good Afternoon';
        }

        if($this->isNight()){
            return 'Sweet Dreams';
        }

        if($this->isEvening()){
              return 'Good Evening';
          }



          if(date('d-m') === '1-1'){
              $greeting = 'Happy new year';
          }

          return $greeting;
    }

}