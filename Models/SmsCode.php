<?php

namespace Modules\Auth\Models;

use Jindowin\Model;

class SmsCode extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sms_codes';


    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = 'id';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

}