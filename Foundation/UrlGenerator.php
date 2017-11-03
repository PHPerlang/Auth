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

        $root = env('ASSET_URL', env('APP_URL'));

        return $this->removeIndex($root) . '/' . trim($path, '/');
    }
}