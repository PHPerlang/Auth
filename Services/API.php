<?php

namespace Modules\Auth\Services;

use Jindowin\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class API
{

    public static function headers($headers = [])
    {

    }

    public static function post($uri, $data = [])
    {

        //$request = app('request');
//        $_SERVER['REQUEST_URI'] = $uri;
//        $_SERVER['REQUEST_METHOD'] = 'POST';
//        $_SERVER['QUERY_STRING'] = 'POST';
//        dd($_SERVER);
//        $request->setMethod('get');
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $request = Request::createFromBase(SymfonyRequest::create(
            $uri,
            $method = 'POST',
            $parameters = $data,
            $cookies = $_COOKIE,
            $files = $_FILES,
            $server = $_SERVER,
            $content = null)
        );
        $response = $kernel->handle($request);

        return $response;
    }

    public static function get($uri, $params)
    {

    }

    public static function put($uri, $data = [])
    {

    }

    public static function delete($uri, $params)
    {

    }

    protected static function make()
    {

    }

}