<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */
namespace Core\Drivers\ContentPlug;


use Core\Drivers\ContentPlug\Events\SmartPlugEvent;
use Core\Joi\System\Exceptions\InvalidArgumentException;
use Nette\Utils\Json;

class SmartPlug
{

    private $functions = [];

    private $data = [];



    public function __construct()
    {

    }

    /**
     * Add data
     *
     * @param string $var
     * @param mixed $value
     */
    public function addData($var, $value, $fn = null)
    {
        $var = str_replace(" ", "_", $var);
        $this->data[$var] = $value;


            $this->addMethod($var, $fn);


    }


    /**
     * @param $method
     * @param $name
     * @param bool $paramsComplex
     * @param bool $security
     */
    private function addMethod($name, $function, $paramsComplex = false, $security = false)
    {
        $this->prepareMethod($function, [
            'paramsComplex' => $paramsComplex,
            'security' => $security,
            'name' => $name,
        ]);

    }

    private function prepareMethod($function, $options)
    {
        if (is_callable($function)) {
            $params = [];

            try {
                $r = new \ReflectionFunction($function);
                $p = $r->getParameters();
                foreach ($p as $param) {
                    $params[] = $param->getName();
                }

                $m = [
                    "security" => $options['security'],
                    "name" => $options['name'] ?? $r->getName(),
                    "params" => $params,
                    "params_complex" => $options['paramsComplex'],
                    "fn" => $function,
                ];

                $this->functions[$options['name'] ?? $r->getName()] = $m;

            } catch (\ReflectionException $e) {
                throw new InvalidArgumentException($e);
            }

        }

    }


    /**
     * Publish methods: send a json to browser
     */
    private function publish($interval = 3)
    {
        echo Json::encode($this->data);
    }




    /**
     * Login, logout, publish methods or execute a method
     *
     */
    public function go()
    {

            // Client need execute a specific method?
            if (isset($_GET['r'])) {
                $method = $_GET['r'];

                if (!isset($this->functions[$method])) {
                    return 'Method does not exist';
                }
              
                echo  Json::encode(['data' => $this->run($method)]);
                die();

            }

            // Then client need publish!
            $this->publish();

    }


    /**
     * Execute a method
     *
     * @param string $method
     *
     * @return mixed
     */
    private function run($func){
     $parameters = [];
     foreach ($this->functions[$func]['params'] as $p) {
         if (isset($_POST[$p])) {
             $parameters[] = $_POST[$p];
         }
     }
     Return  (new \ReflectionFunction($this->functions[$func]['fn']))->invokeArgs($parameters);
 }

    /**
     * JSON Encode
     *
     * @param mixed $data
     *
     * @return string
     */
    final static function jsonEncode($data)
    {
        if (is_array($data) || is_object($data)) {
            $islist = is_array($data) && (empty($data) || array_keys($data) === range(0, count($data) - 1));

            if ($islist) $json = '[' . implode(',', array_map('self::jsonEncode', $data)) . ']';
            else {
                $items = [];
                foreach ($data as $key => $value) {
                    $items[] = self::jsonEncode((string)$key) . ':' . self::jsonEncode($value);
                }
                $json = '{' . implode(',', $items) . '}';
            }
        } elseif (self::isString($data)) {
            $string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
            $json = '';
            $len = strlen($string);
            for ($i = 0; $i < $len; $i++) {
                $char = $string[$i];
                $c1 = ord($char);
                if ($c1 < 128) {
                    $json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
                    continue;
                }
                $c2 = ord($string[++$i]);
                if (($c1 & 32) === 0) {
                    $json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
                    continue;
                }
                $c3 = ord($string[++$i]);
                if (($c1 & 16) === 0) {
                    $json .= sprintf("\\u%04x", (($c1 - 224) << 12) + (($c2 - 128) << 6) + ($c3 - 128));
                    continue;
                }
                $c4 = ord($string[++$i]);
                if (($c1 & 8) === 0) {
                    $u = (($c1 & 15) << 2) + (($c2 >> 4) & 3) - 1;

                    $w1 = (54 << 10) + ($u << 6) + (($c2 & 15) << 2) + (($c3 >> 4) & 3);
                    $w2 = (55 << 10) + (($c3 & 15) << 6) + ($c4 - 128);
                    $json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
                }
            }
        } else {
            $json = strtolower(var_export($data, true));
        }

        return $json;
    }

    /**
     * Secure is_string
     *
     * @param mixed $value
     *
     * @return boolean
     */
    final static function isString($value)
    {
        if (is_string($value)) {
            return true;
        }
        if (is_object($value) && method_exists($value, "__toString")) {
            return true;
        }

        return false;
    }

}