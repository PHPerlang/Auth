<?php

namespace Modules\Auth\Foundation;

use Illuminate\Support\Str;
use Illuminate\Routing\UrlGenerator as LaravelUrlGenerator;

class UrlGenerator extends LaravelUrlGenerator
{

    /**
     * Generate the URL to an application asset.
     *
     * @param  string $path
     * @param  bool|null $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }

        // Once we get the root URL, we will check to see if it contains an index.php
        // file in the paths. If it does, we will remove it since it is not needed
        // for asset paths, but only for routes to endpoints in the application.
        $root = $this->formatRoot($this->autoFormatScheme($secure, env('APP_URL', null)), env('APP_URL', null));

        $version = env('APP_VERSION', '1.0.0');
        $url = $this->removeIndex($root) . '/' . trim($path, '/');

//        if (strpos($url, '?') !== false) {
//
//            return $url . '&version=' . $version;
//        }
//
//        return $url . '?version=' . $version;

        return $url;
    }

    /**
     * Generate an absolute URL to the given path.
     *
     * @param  string $path
     * @param  mixed $extra
     * @param  bool|null $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = null)
    {
        // First we will check if the URL is already a valid URL. If it is we will not
        // try to generate a new one but will simply return the URL as is, which is
        // convenient since developers do not always have to check if it's valid.
        if ($this->isValidUrl($path)) {
            return $path;
        }

        $tail = implode('/', array_map(
                'rawurlencode', (array)$this->formatParameters($extra))
        );

        // Once we have the scheme we will compile the "tail" by collapsing the values
        // into a single string delimited by slashes. This just makes it convenient
        // for passing the array of parameters to this URL as a list of segments.
        $root = $this->formatRoot($this->autoFormatScheme($secure, env('APP_URL', null)), env('APP_URL', null));


        list($path, $query) = $this->extractQueryString($path);

        return $this->format(
                $root, '/' . trim($path . '/' . $tail, '/')
            ) . $query;
    }

    /**
     * Get the default scheme for a raw URL.
     *
     * @param  bool|null $secure
     * @param  string $path
     * @return string
     */
    public function autoFormatScheme($secure, $path = null)
    {
        if (!is_null($secure)) {
            return $secure ? 'https://' : 'http://';
        }

        if (!is_null($path)) {
            return Str::startsWith($path, 'http://') ? 'http://' : 'https://';
        }

        if (is_null($this->cachedSchema)) {
            $this->cachedSchema = $this->forceScheme ?: $this->request->getScheme() . '://';
        }

        return $this->cachedSchema;
    }
}