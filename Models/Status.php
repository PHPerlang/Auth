<?php

namespace Modules\Core\Models;

class Status
{

    public $code, $message, $data;

    public function __construct($request, $code, $message = '', $data = null)
    {
        $this->code = $code;
        $this->message = $this->getMessage($request, $code);
        $this->data = $data;
    }

    public function __toString()
    {
        return json_encode($this);
    }

    /**
     * Get module name from the route action.
     *
     * @param $request
     *
     * @return string
     */
    protected function getModuleName($request)
    {
        $explod = explode('\\', $request->route()->getActionName());

        return isset($explod[1]) ? $explod[1] : '';
    }

    /**
     * Get route codes
     *
     * @param $request
     *
     * @return mixed
     */
    public function getCodes($request)
    {
        return $request->route()->codes;
    }

    /**
     * Get code message
     *
     * @param $request
     * @param $code
     *
     * @return null/string
     */
    public function getMessage($request, $code)
    {
        $codes = $this->getCodes($request);

        $lang = $request->header('Accept-Language');

        $module = $this->getModuleName($request);

        $method = strtolower($request->getMethod());

        $uri = $request->path();

        app()->setLocale($lang);

        $message = trans($module . '::global-codes.' . $code);

        if ($code > 1000) {

            $message = trans($module . '::codes.' . $method . '@' . $uri . '.' . $code);

        }

        if (preg_match('/.+::.*/', $message)) {

            $message = trans('core::global-codes.' . $code);

            if (preg_match('/.+::global-codes\..*/', $message)) {

                return isset($codes[$code]) ? $codes[$code] : null;
            }
        }

        return $message;
    }


}
