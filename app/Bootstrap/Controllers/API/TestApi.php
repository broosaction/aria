<?php


namespace App\Bootstrap\Controllers\API;


class TestApi
{

    /**
     * @method ['get', 'post']
     * @path /api/{key}
     * @id Api
     * @defaultParameterRegex .*?
     */
    public function api($key)
    {
        response()->json([
            'status' => 'ok',
            'type' => 'empty',
            'message' => 'hello world',
            'data' => 'empty',
            'joi' => 'Joi'
        ]);
    }

}