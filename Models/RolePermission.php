<?php

namespace Modules\Auth\Models;

use Gindowin\Model;

class RolePermission extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'auth_role_permissions';

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

        if (!preg_match('/(.+@.+)(\?)(.*)/', $transit, $matches)) {

            throw new \Exception('Role permission format should be method@uri[?params], but ' . $transit . ' be provided . ');
        }

        $limit_parse = [];
        $fields = explode('&', $matches[3]);

        foreach ($fields as $filed) {

            $explode = explode('=', $filed);

            if (count($explode) === 2) {
                if (strpos($explode[1], ',')) {
                    $limit_parse[$explode[0]] = explode(',', $explode[1]);
                } else {
                    $limit_parse[$explode[0]] = [$explode[1]];
                }
            }
        }

        $this->attributes['permission_id'] = $matches[1];
        $this->attributes['limit_params'] = $matches[3] ? $matches[3] : '*';
        $this->attributes['limit_parse'] = count($limit_parse) > 0 ? json_encode($limit_parse) : null;
    }

}