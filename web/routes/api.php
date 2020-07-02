<?php

use Illuminate\Http\Request;
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

// 新規登録
Route::post('/register', 'Auth\RegisterController@register')->name('register');
// ログイン
Route::post('/login', 'Auth\LoginController@login')->name('login');
// ログアウト
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
// userデータ送る
Route::get('/user', fn() => Auth::user())->name('user');

Route::group(['prefix' => '/photos'], function () {
  // 写真投稿
  Route::post('/', 'PhotoController@create')->name('photo.create');
  // 写真一覧
  Route::get('/', 'PhotoController@index')->name('photo.index');
  // 写真詳細
  Route::get('/{id}', 'PhotoController@show')->name('photo.show');
  // コメント機能
  Route::post('/{photo}/comments', 'PhotoController@addComment')->name('photo.comment');
  // いいね機能
  Route::put('/{id}/like', 'LikeController@like')->name('photo.like');
  // いいね解除
  Route::delete('/{id}/like', 'LikeController@unlike');
});