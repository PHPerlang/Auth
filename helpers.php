<?php

/**
 * Render role permissions with variables.
 *
 * @param array $permissions
 * @param array $vars
 *
 * @return array
 */
function render_permission(array $permissions, array $vars = [])
{
    foreach ($vars as $var => $value) {

        foreach ($permissions as $key => $permission) {
            $permission = str_replace(' ', '', $permission);
            $permissions[$key] = preg_replace('/{{\$' . $var . '}}/', $value, $permission);
        }
    }
    return $permissions;
}

/**
 * Add guest resource query limit condition.
 *
 * @param $query
 * @return mixed
 */
function guard($query)
{
    return \Modules\Auth\Models\Guest::guardPermissionParams($query);
}

