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

    /**
     * Get fixed roles where role_type in 1 or 2.
     *
     * @param string $module
     *
     * @return mixed
     */
    public static function getFixedRoles($module = null)
    {
        $query = Role::where('role_type', 1)->where('role_status', 1);

        if ($module) {
            $query->where('module', $module);
        }

        $query->orWhere(function ($query) use ($module) {
            $query->where('role_type', 2)->where('role_status', 1)->where('expired_at', '>', timestamp());
            if ($module) {
                $query->where('module', $module);
            }
        });

        return $query->get();
    }
}