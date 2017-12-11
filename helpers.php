<?php

/**
 * Check the guest permissions in frontend templates.
 *
 * Notice: this method is used in frontend templates to determine some view components
 * visibility, not used in api controllers.
 *
 * @param string $permission
 * @param array $scope
 *
 * @return bool
 */
function can($permission, $scope = [])
{

    if (\Modules\Auth\Models\Guest::id()) {

        return \Modules\Auth\Models\Guest::instance()->can($permission, $scope);
    }

    return false;
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

