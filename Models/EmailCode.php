<?php

namespace Modules\Auth\Models;

use Gindowin\Model;

class EmailCode extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_codes';


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