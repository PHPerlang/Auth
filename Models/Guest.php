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
        return self::find(self::$id);
    }

    /**
     * Check the guest permissions in frontend templates.
     *
     * Notice: this method is used in frontend templates to determine some view components
     * visibility, not used in api controllers.
     *
     * @return bool
     */
    public static function can()
    {
        return true;
    }

    /**
     * Get guest role permissions limit params via the route.
     *
     * @param array $guest_permissions
     *
     * @return array
     */
    public static function params(array $guest_permissions = [])
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
                        $params[$field][$key] = (string)self::id();
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
    public static function roles()
    {
        return MemberRole::where('member_id', self::$id)->pluck('role_id');
    }

    /**
     * Get the guest role permissions via route permission.
     *
     *
     * @return array
     */
    public static function permissions()
    {
        return RolePermission::where('permission_id', self::$route_permission)
            ->whereIn('role_id', self::roles())
            ->get(['permission_id', 'limit_params', 'limit_parse', 'permission_type', 'expired_at']);
    }


    /**
     * Get guest all role permissions.
     *
     * @return array
     */
    public static function allPermissions()
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
    public static function guardPermissionParams($query)
    {
        $params = self::params();

        foreach ($params as $field => $values) {

            switch (count($params[$field])) {
                case 0:
                    continue;
                case 1:
                    $query = $query->where($field, $params[$field][0]);
                    break;
                default:
                    $query = $query->whereIn($field, $params[$field]);
            }

        }

        return $query;
    }

}