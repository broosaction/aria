<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 08 /Nov, 2020 @ 15:38
 */

namespace Core\Router\Http;


use Nette\Utils\Arrays;
use Nette\Utils\Json;

class ApiResponse
{
    private array $response;


    /**
     * ApiResponse constructor.
     * @param array $response
     */
    public function __construct(array $response = array())
    {
        $this->response = Arrays::mergeTree([
            'date' => date("d-m-Y"),
            'status' => false,
            'data' => '',
        ], $response);

    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    public function getJSONResponse()
    {
        return Json::encode($this->response);
    }

    /**
     * @param array $response
     */
    public function setResponse(array $response): void
    {
        $this->response = $response;
    }

    public function addData($key, $value): ApiResponse
    {
        $this->response[$key] = $value;
        return $this;
    }

    public function getData($key){
       return $this->response[$key] ?? null;
    }
}