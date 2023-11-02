<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/2/2019
 * Time: 12:36
 */

namespace Core\Drivers;



use Core\Config\Config;
use Core\Joi\System\Utils;
use Core\Security\CloudValkyrie\AnalyzeResults;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\Guid;

class Sessions
{
  // needs a live session aggregation method

    private $driver;

    private static $valid = false;

    private static $active = false;

    public function __construct($driver='')
    {

        if($driver === '' || $driver==='array') {
            if (session_id() === '') {
                session_start();
                static::$active = true;
            }

            self::protect();
        }

/**
        if($this->driver ==='file'){
            // file driver
        }

        if($this->driver === 'object'){

            // object..
        }

*/
    }

    /**
     *
     */
    public static function protect()
    {

        $ip = Utils::get_ip_address();
        $user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";

        if(self::exists('config.ip') && self::exists("config.user_agent")){
            //$ip === self::get("config.ip")
            if( $user_agent === self::get("config.user_agent")){

                self::$valid = true;
                self::put("config.ip", $ip);

            }else{

                self::$valid = false;
               self::set("config.user_agent", $user_agent);
               $result = [];
               $scanResult = new ScanResults('Session Driver');
               $scanResult->setIsThreat(true);
               $scanResult->setAction(DefaultActions::IGNORE);
               $scanResult->setSeverityLevel(10);
               $scanResult->setMessage('wow hijack');
               $scanResult->setEvent('Session');
               $result[] = $scanResult;
              (new AnalyzeResults())->analyze($result);

            }
        }else{
            self::put("config.ip", $ip);
            self::put("config.user_agent",$user_agent);
            self::$valid = true;
        }


    }

    /**
     * @return bool
     */
    public static function isValid()
    {
        return self::$valid;
    }

    /**
     * @param $name
     * @return bool
     */
    public static function exists($name)
    {
        return isset($_SESSION[$name]);
    }

    public static function isActive(): bool
    {
        return static::$active;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function put($key, $value){
       self::set($key, $value);
    }


    /**
     * @param string $id
     * @param mixed $value
     */
    public static function set(string $id, $value): void
    {
        $data = [
            serialize($value),
            (new Config)->app_secrete,
        ];

        $data = Guid::encrypt((new Config)->app_secrete, implode('|', $data));

        $_SESSION[$id] = $data;
    }


    /**
     * @param $key
     * @return null
     */
    public static function get($id, $defaultValue = null)
    {
        if (static::exists($id) === true) {

            $value = $_SESSION[$id];

            if (trim($value) !== '') {

                $app_secrete = (new Config)->app_secrete;
                $value = Guid::decrypt($app_secrete, $value);
                $data = explode('|', $value);

                if (\is_array($data) && trim(end($data)) === $app_secrete) {
                    return unserialize($data[0], ['allowed_classes' => true]);
                }

            }
        }

        return $defaultValue;
    }

    /**
     * @param $key
     */
    public static function delete($key)
    {
        if(self::exists($key)){
            unset($_SESSION[$key]);
        }
    }

    /**
     * @param $key
     * @param string $string
     * @return null
     */
    public static function flash($key, $string ='')
    {
        if(self::exists($key)){
            $session = self::get($key);
            self::delete($key);
            return $session;
        }

        self::put($key, $string);
        return null;
    }

    /**
     *
     */
    public static function destroy()
    {

        unset($_SESSION);
        session_unset();
        $_SESSION = [];
        session_destroy();
    }

    public static function clear(){
        self::destroy();
    }


    /**
     * Wrapper around session_regenerate_id
     *
     * @param bool $deleteOldSession Whether to delete the old associated session file or not.
     * @param bool $updateToken Wheater to update the associated auth token
     * @return void
     */
    public static function regenerateId(bool $deleteOldSession = true, bool $updateToken = false) {
        $oldId = null;

        if ($updateToken) {
            // Get the old id to update the token
            try {
                $oldId = self::getId();
            } catch (\Exception $e) {
                // We can't update a token if there is no previous id
                $updateToken = false;
            }
        }

        try {
            @session_regenerate_id($deleteOldSession);
        } catch (\Exception $e) {

        }
    }

    /**
     * Wrapper around session_id
     *
     * @return string
     * @throws \Exception
     * @since 9.1.0
     */
    public static function getId(): string {
        $id = self::invoke('session_id', [], true);
        if ($id === '') {
            throw new \Exception('Session not avirable');
        }
        return $id;
    }

    /**
     * @param string $functionName the full session_* function name
     * @param array $parameters
     * @param bool $silence whether to suppress warnings
     * @return mixed
     */
    private static function invoke(string $functionName, array $parameters = [], bool $silence = false) {
        try {
            if($silence) {
                return @call_user_func_array($functionName, $parameters);
            }

            return call_user_func_array($functionName, $parameters);
        } catch(\Exception $e) {
            //$this->trapError($e->getCode(), $e->getMessage());
        }
        return false;
    }

}