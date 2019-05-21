<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');

    Route::namespace('Core')->prefix('core')->group(function () {
        Route::apiResource('roles', 'RolesController');
        Route::post('roles/{id}/perms', 'RolesController@change_permissions');
        Route::apiResource('users', 'UsersController');
        Route::post('ban-users', 'UsersController@ban');
        Route::namespace('Logs')->prefix('logs')->group(function () {
            Route::get('user-access', 'AccessController@user_access');
            Route::get('user-activity', 'UserActivityController@user_activity');
            /**
             * Logs del sistema
             */
            Route::get('/system', 'SystemController@list_logs');
            Route::get('/system/{fecha}/download', 'SystemController@download_log_date');
            Route::post('/system/remove', 'SystemController@delete');
            Route::get('/system/{fecha}/{tipo}', 'SystemController@view_log');
        });
        Route::apiResource('catalogs', 'CatalogController');
        Route::apiResource('catalog-details', 'CatalogDetailsController')->except(['index']);
        /**
         * Selects
         */
        Route::prefix('select')->group(function () {
            Route::get('users', 'UsersController@select_users');
            Route::get('tables', 'Logs\UserActivityController@select_tables');
        });
    });
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});
