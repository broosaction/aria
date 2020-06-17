<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Core\Tools\ipinfo;

/**
 * Holds formatted data for a single IP address.
 */
class Details
{
  public function __construct($raw_details)
  {
    foreach ($raw_details as $property => $value) {
      $this->$property = $value;
    }
    $this->all = $raw_details;
  }
}
