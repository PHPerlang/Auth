<?php

namespace Modules\Auth\Models;

use Jindowin\Model;

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


    /**
     * Set the relevance permissions.
     *
     * @param array $value
     *
     * @throws \Exception
     */
    public function setPermissionIdAttribute($value)
    {

        $transit = strpos($value, '?') === false ? $value . '?' : $value;

        if (!preg_match('/(.+):(.+@.+)(\?)(.*)/', $transit, $matches)) {

            throw new \Exception('Role permission format should be scope:method@uri[?limit], but ' . $transit . ' be provided . ');
        }

        $this->attributes['scope'] = $matches[1];
        $this->attributes['permission_id'] = $matches[2];
        $this->attributes['restrict_fields'] = $matches[4];
    }

}