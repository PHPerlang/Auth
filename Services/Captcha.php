<?php

namespace Modules\Auth\Services;

use Mews\Captcha\Captcha as Base;

class Captcha extends Base
{
    /**
     * Generate captcha text
     *
     * @return string
     */
    protected function generate()
    {
        $characters = str_split($this->characters);

        $bag = '';
        for ($i = 0; $i < $this->length; $i++) {
            $bag .= $characters[rand(0, count($characters) - 1)];
        }

        session(['captcha' => [
            'sensitive' => $this->sensitive,
            'key' => $this->hasher->make($this->sensitive ? $bag : $this->str->lower($bag))
        ]]);

        return $bag;
    }

    /**
     * Captcha check
     *
     * @param $value
     * @return bool
     */
    public function check($value)
    {
        if (!$this->session->has('captcha')) {
            return false;
        }

        $key = $this->session->get('captcha.key');

        if (!$this->session->get('captcha.sensitive')) {
            $value = $this->str->lower($value);
        }

        $this->session->remove('captcha');

        return $this->hasher->check($value, $key);
    }

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