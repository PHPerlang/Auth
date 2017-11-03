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

        return $this->removeIndex($root) . '/' . trim($path, '/');
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