<?php

namespace Modules\Core\Foundation;

use Modules\Core\Models\Status;
use Modules\Core\Contracts\Context as ContextInterface;

class Context implements ContextInterface
{

    public $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function query($field = null, $default = null)
    {
        if ($field) {

            if (in_array($this->request->route()->query, $field)) {

                return $this->request->query($field, $default);

            } else {

                throw new \Exception('The query filed "' . $field . '" is not defined in route');
            }

        } else {

            $query = $this->request->query();

            foreach ($query as $key => $value) {

                if (!in_array($this->request->route()->query, $key)) {

                    unset($query[$key]);
                }
            }

            return $query;
        }

    }

    public function header($header, $default = null)
    {
        $value = $this->request->header($header);

        return $value ? $value : $default;
    }

    public function data($field = null, $default = null)
    {

        switch ($this->header('Content-Type')) {

            case 'application/json':

                $data = $this->request->json()->all();

                break;

            case 'application/x-www-form-urlencoded':

                $data = $this->request->request->all();
                break;

            default:
                $data = $this->request->getContent();
        }


        return is_string($field) ? (isset($data[$field]) ? $data[$field] : $default) : $data;
    }

    public function input($field = null, $default = null)
    {
        return $this->request->input($field, $default);
    }

    public function response($result = '', $httpCode = 200, $headers = [])
    {
        return response($result, $httpCode, $headers);
    }


    public function status($code, $data = null, $httpCode = 200, $headers = [])
    {

        return $this->response(new Status($this->request, $code, null, $data), $httpCode, $headers);
    }

    public function view($path, $data = [])
    {
        return view($path, $data);
    }

    public function guest()
    {
        return defined('SYS_RUNTIME_GUEST_ID') ? SYS_RUNTIME_GUEST_ID : null;
    }
}

