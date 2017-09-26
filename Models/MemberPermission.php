<?php

namespace Modules\Auth\Models;

use Gindowin\Model;

class MemberPermission extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'auth_member_permissions';

    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = ['member_id', 'permission_id'];

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
    public $timestamps = false;

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

            throw new \Exception('Role permission format should be method@uri[?scope], but ' . $transit . ' be provided . ');
        }

        $scope = [];
        $fields = explode('&', $matches[3]);

        foreach ($fields as $filed) {

            $explode = explode('=', $filed);

            if (count($explode) === 2) {
                if (strpos($explode[1], ',')) {
                    $scope[$explode[0]] = explode(',', $explode[1]);
                } else {
                    $scope[$explode[0]] = [$explode[1]];
                }
            }
        }
        $this->attributes['permission_id'] = $matches[1];
        $this->attributes['permission_scope'] = $this->count($scope) > 0 ? json_encode($scope) : '*';
        $this->attributes['created_at'] = timestamp();
    }

}