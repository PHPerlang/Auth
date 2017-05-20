<?php

namespace Modules\Auth\Models;

class RolePermissions extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_permissions';

    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = ['role_id', 'permission_id'];

    /**
     * Indicates if the model primary auto incrementing.
     *
     * @var bool
     */
    public $incrementing = false;


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

}