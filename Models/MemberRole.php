<?php

namespace Modules\Core\Models;

class MemberRole extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'member_roles';

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