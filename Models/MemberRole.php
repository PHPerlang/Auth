<?php

namespace Modules\Auth\Models;

use Gindowin\Model;

class MemberRole extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'auth_member_roles';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = ['member_id', 'role_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

}