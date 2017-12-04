<?php

/*
|--------------------------------------------------------------------------
| Defined admin routes.
|--------------------------------------------------------------------------
|
*/
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'admin', 'prefix' => '/admin/auth', 'namespace' => 'Modules\Auth\Http\Admin'], function () {

    Route::get('/utility/routes', 'UtilityController@getRoutes')->name('coreGetProjectRoutes');
    Route::get('/members', 'MemberController@getMembersView');
    Route::get('/users', 'MemberController@getUsersView');
    Route::get('/member/editor', 'MemberController@getMemberEditor');
    Route::get('/member/profile', 'MemberController@getProfile');
    Route::get('/member/setting', 'MemberController@getSetting');
    Route::get('/member/login/log', 'MemberController@getLoginLog');
    Route::get('/roles', 'RoleController@getRolesView');
    Route::get('/role/editor', 'RoleController@getRoleEditor');
    Route::get('/permissions', 'PermissionController@getPermissionsView');

});

