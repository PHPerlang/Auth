<?php

namespace Modules\Auth\Models;

use Jindowin\Model;
use Mockery\Exception;

class Permission extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';


    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = 'permission_id';

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


    /**
     * Set the relevance permissions.
     *
     * @param array $value
     */
    public function setPermissionRelevanceAttribute($value)
    {
        $this->attributes['permission_relevance'] = json_encode($value);
    }

    /**
     * Set the similar permissions.
     *
     * @param array $value
     */
    public function setPermissionLikeAttribute($value)
    {
        $this->attributes['permission_like'] = json_encode($value);
    }

    /**
     * Fork a permission from $this.
     */
    public function fork()
    {

    }

    /**
     * Render permisssion template.
     *
     * @param array $permissions
     * @param array $args
     *
     * @return array
     */
    public static function renderTemplate(array $permissions, array $args = [])
    {
        return $permissions;
    }

    /**
     * Permission belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(\Modules\Auth\Models\Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}