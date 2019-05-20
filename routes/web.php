<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return "<a href='/panel'>Panel</a>";
});

Route::get('panel/{vue_capture?}', function () {
    return view('panel.index');
})->where('vue_capture', '[\/\w\.-]*');
