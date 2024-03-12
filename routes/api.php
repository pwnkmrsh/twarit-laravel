<?php

use Illuminate\Support\Facades\Route;

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

Route::post('register_product', 'Api\ActivationController@handle');

Route::get('users', function () {
    $query = request()->query('query');

    return response()->json(\App\User::where('username', 'LIKE', "%$query%")->get());
});

Route::get('posts', function () {
    $query = request()->query('query');

    return response()->json(\App\Post::where('title', 'LIKE', "%$query%")->get());
});

Route::get('tags', function () {
    $query = request()->query('query');

    return response()->json(\App\Tag::where('name', 'LIKE', "%$query%")->get());
});
