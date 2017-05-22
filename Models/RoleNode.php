<?php

namespace Modules\Auth\Models;

use Jindowin\Model;

class RoleNode extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_nodes';


    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = false;


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

}