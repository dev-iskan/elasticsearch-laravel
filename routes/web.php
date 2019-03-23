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

Route::get('/', function (\Illuminate\Http\Request $request) {
    $users = \App\User::search($request->q)->paginate(3);

    dd($users);
});

Route::get('/delete', function (\Illuminate\Http\Request $request) {
    \App\User::find($request->id)->delete();
});
