<?php

namespace Modules\Auth\Contracts;

use Modules\Auth\Models\Member;
use Modules\Auth\Models\Status;

interface Request
{
    /**
     * Gain the params from the url.
     *
     * @param string /null $field
     * @param string /null $default
     *
     * @return string/integer/null
     */
    public function query($field = null, $default = null);


    /**
     * Gain header filed value.
     *
     * @param string $field
     * @param string /null $default
     *
     * @return mixed
     */
    public function header($field, $default = null);

    /**
     * Gain the data from request body.
     *
     * @param string /null $filed
     * @param string /null $default
     *
     * @return mixed
     */
    public function data($filed = null, $default = null);


    /**
     * Gain the request input.
     *
     * @param string /null $filed
     * @param string /null $default
     *
     * @return mixed
     */
    public function input($filed = null, $default = null);


    /**
     * Generate a http response.
     *
     * @param mixed $result
     * @param int $httpCode
     * @param array $headers
     *
     * @return mixed
     */
    public function response($result = null, $httpCode = 200, $headers = []);

    /**
     * Generate a operation status response.
     *
     * @param int $code
     * @param mixed $data
     * @param int $httpCode
     * @param array $headers
     *
     * @return Status
     */
    public function status($code, $data = null, $httpCode = 200, $headers = []);


    /**
     * Generate a html response
     *
     * @param string $path
     * @param array $data
     *
     * @return string
     */
    public function view($path, $data = []);


    /**
     * Get the guest member.
     *
     * @return Member
     */
    public function guest();

}