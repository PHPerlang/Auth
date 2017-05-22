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

});

