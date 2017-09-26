<?php

namespace Modules\Auth\Models;

use Gindowin\Model;
use Mockery\Exception;

class Permission extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'auth_permissions';


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

}