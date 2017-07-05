<?php

namespace Modules\Auth\Services;

use Jindowin\Request;
use Jindowin\Status;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class API
{

    protected static $headers = [];


    public static function headers($headers = [])
    {
        foreach ($headers as $header => $value) {

            $_SERVER["HTTP_$header"] = $value;
        }

        return new static;
    }

    public static function post($uri, $data = [])
    {
        return self::make('POST', $uri, $data);
    }

    public static function get($uri, $params)
    {
        return self::make('GET', $uri, $params);
    }

    public static function put($uri, $data = [])
    {
        return self::make('PUT', $uri, $data);
    }

    public static function delete($uri, $params)
    {
        return self::make('DELETE', $uri, $params);
    }

    protected static function make($method, $uri, $parameters)
    {
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $request = Request::createFromBase(SymfonyRequest::create(
            $uri = '/' . ltrim($uri, '/'),
            $method,
            $parameters,
            $cookies = $_COOKIE,
            $files = $_FILES,
            $server = $_SERVER,
            $content = null)
        );

        $response = $kernel->handle($request);

        if ($response->original instanceof Status) {

            return json_decode($response->getContent());
        }

        return false;
    }

}