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