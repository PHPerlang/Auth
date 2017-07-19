<?php

namespace Modules\Auth\Models;

use Gindowin\Model;

class MemberOperation extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'member_operations';


    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = 'op_id';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

}