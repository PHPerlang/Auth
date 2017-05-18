<?php

namespace Modules\Core\Exceptions;

use Exception;
use Modules\Core\Models\Status;


class StatusException extends Exception
{

    protected $code, $data;

    /**
     * Create a new status exception.
     *
     * @param string $code
     * @param string $data
     *
     */
    public function __construct($code, $data)
    {
        $this->code = $code;
        $this->data = $data;

        parent::__construct();
    }

    /**
     * Get a status.
     *
     * @param $request
     *
     * @return Status
     */
    public function getStatus($request)
    {
        return new Status($request, $this->code, null, $this->data);
    }

}