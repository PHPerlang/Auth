<?php

namespace Modules\Auth\Services;

use Mews\Captcha\Captcha as Base;

class Captcha extends Base
{
    /**
     * Generate captcha image source
     *
     * @param null $config
     * @return string
     */
    public function src($config = null)
    {
        return url('/api/auth/captcha' . ($config ? '/' . $config : '/default')) . '?' . $this->str->random(8);
    }
}