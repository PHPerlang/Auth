<?php

namespace Modules\Auth\Models;

class Guest extends Member
{
    /**
     * The guest member id.
     *
     * @var integer
     */
    protected static $id;

    /**
     * Cache member data.
     *
     * @var Member
     */
    protected static $member;


    /**
     * permission generated from guest accessed route.
     *
     * @var integer
     */
    protected static $route_permission;

    /**
     * Set guest id to the static attribute.
     *
     * @param $guest_id
     */
    public static function init($guest_id)
    {
        self::$id = $guest_id;
    }

    /**
     * Set the guest route permission.
     *
     * @param $route_permission
     */
    public static function setRoutePermission($route_permission = null)
    {
        self::$route_permission = $route_permission;
    }

    /**
     * Get the guest member id.
     *
     * @return int
     */
    public static function id()
    {
        return self::$id;
    }

    /**
     * Get the guest member instance
     *
     * @return Member
     */
    public static function instance()
    {
        if (!self::$member) {

            self::$member = self::find(self::$id);
        }

        return self::$member ? self::$member : new Member();
    }

    /**
     * Check the guest permissions in frontend templates.
     *
     * Notice: this method is used in frontend templates to determine some view components
     * visibility, not used in api controllers.
     *
     * @return bool
     */
    public function can($permission, $scope = [])
    {
        $permissions = $this->permissions();
        if (array_key_exists($permission, $permissions)) {
            if (count($scope) == 0) {
                return true;
            } else {
                $own_scope = [];
                foreach ($permissions as $permission_id => $permission_scopes) {
                    foreach ($permission_scopes as $permission_scope) {
                        if ($permission_scope != '*') {
                            $params = json_decode($permission_scope);
                            foreach ($params as $key => $data) {
                                if ($key == 'member_id' && $data = '$') {
                                    $data = self::$id;
                                }
                                if (!isset($own_scope[$key])) {
                                    $own_scope[$key] = [];
                                }
                                if (is_array($data)) {
                                    array_merge($own_scope[$key], $data);
                                } else {
                                    $own_scope[$key][] = $data;
                                }

                            }
                        }
                    }
                }
                foreach ($scope as $key => $value) {
                    if (!array_key_exists($key, $own_scope)) {
                        return false;
                    } else if (!in_array($value, $own_scope[$key])) {
                        return false;
                    }
                }
                return true;
            }

        }

        return false;
    }

    /**
     * Get guest role permissions limit scope via the route.
     *
     * @param array $guest_permissions
     *
     * @return array
     */
    public
    function scope(array $guest_permissions = [])
    {
        $guard_fields = [];

        $guest_permissions = $guest_permissions ? $guest_permissions : self::permissions();

        foreach ($guest_permissions as $guest_permission) {

            if (!$guest_permission['limit_parse']) {

                continue;
            }

            $fields = json_decode($guest_permission['limit_parse'], true);

            foreach ($fields as $filed => $value) {

                if (key_exists($filed, $guard_fields)) {

                    if (is_array($value)) {

                        $guard_fields[$filed] = array_merge($guard_fields[$filed], $value);

                    } else {

                        array_push($guard_fields[$filed], $value);
                    }

                } else {

                    $guard_fields[$filed] = $value;
                }
            }
        }

        foreach ($guard_fields as $field => $values) {

            foreach ($values as $key => $value) {
                switch ($value) {
                    case '*':
                        unset($guard_fields[$field]);
                        break;
                    case 'guest':
                        $scope[$field][$key] = (string)self::id();
                        break;
                }
            }

            $guard_fields[$field] = array_unique($guard_fields[$field]);
        }


        return $guard_fields;
    }

    /**
     * Get the guest roles.
     *
     * @return array
     */
    public
    function roles()
    {
        return MemberRole::where('member_id', self::$id)->pluck('role_id')->toArray();
    }

    /**
     * Get the guest role permissions via route permission.
     *
     *
     * @return array
     */
    public
    function permissions()
    {
        $permissions = [];

        $role_permissions = RolePermission::whereIn('role_id', self::roles())
            ->select('permission_id', 'permission_scope')->get()->toArray();

        $member_permissions = MemberPermission::where('member_id', self::$id)
            ->select('permission_id', 'permission_scope', 'permission_type', 'started_at', 'expired_at')
            ->get()->toArray();

        foreach ($role_permissions as $role_permission) {
            $permissions[$role_permission['permission_id']][] = $role_permission['permission_scope'];
        }

        foreach ($member_permissions as $member_permission) {
            if ($member_permission->permission_type == 2) {
                if (strtotime($member_permission['started_at']) - time() > 0 && strtotime($member_permission['expired_at']) - time() < 0) {
                    $permissions[$member_permission['permission_id']][] = $member_permission['permission_scope'];
                }
            }
        }

        return $permissions;
    }


    /**
     * Get guest all role permissions.
     *
     * @return array
     */
    public
    function allPermissions()
    {
        return RolePermission::whereIn('role_id', self::roles())->get();
    }

    /**
     * Add guest resource query limit condition.
     *
     * @param $query
     *
     * @return mixed
     */
    public
    function guardPermissionScope($query)
    {
        $scope = self::scope();

        foreach ($scope as $field => $values) {

            switch (count($scope[$field])) {
                case 0:
                    continue;
                case 1:
                    $query = $query->where($field, $scope[$field][0]);
                    break;
                default:
                    $query = $query->whereIn($field, $scope[$field]);
            }

        }

        return $query;
    }

}