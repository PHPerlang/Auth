<?php

namespace Modules\Auth\Models;

use Gindowin\Model;

class Role extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'auth_roles';


    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = 'role_id';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module',
        'creator_id',
        'role_name',
        'role_desc',
        'permission_amount',
        'role_type',
        'role_status',
        'started_at',
        'expired_at',
    ];

    /**
     * Role own many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(\Modules\Auth\Models\Permission::class)
            ->using(\Modules\Auth\Models\RolePermissions::class);
    }
}