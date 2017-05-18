<?php

/*
|--------------------------------------------------------------------------
| Defined admin routes.
|--------------------------------------------------------------------------
|
*/
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'admin', 'prefix' => '/admin/core', 'namespace' => 'Modules\Core\Http\Controllers\Admin'], function () {

    Route::get('/utility/routes', 'UtilityController@getRoutes')->name('coreGetProjectRoutes');

});

