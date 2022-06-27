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
  if (Auth::check()) {
    return redirect('/cms/admin/users');
  } else {
    return redirect('/cms/login');
  }
});

Route::get('login', 'Cms\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Cms\Auth\LoginController@login');

Route::middleware(['auth'])->group(function () {
  Route::resource('banner', 'Cms\BannerController');
  Route::resource('dashboard', 'Cms\DashboardController');
  Route::resource('configurations', 'Cms\ConfigurationController');
  Route::resource('pages', 'Cms\PageController');

  Route::prefix('blog')->group(function () {
    Route::resource('blog_categories', 'Cms\BlogCategoriesController');
    Route::resource('blog_posts', 'Cms\BlogPostsController');
    Route::resource('blog_posts.gallery', 'Cms\BlogGalleryController');
    Route::post('upload-images', 'Cms\UploadImageController@editorUpload')->name('upload-images');
    Route::get('/preview/{slug}', 'Cms\BlogPostsController@preview')->name('blog.preview');
});

  Route::post('logout', 'Cms\Auth\LoginController@logout')->name('logout');

  Route::prefix('admin')->group(function () {
    Route::resource('groups', 'Cms\GroupsController');
    Route::resource('users', 'Cms\UsersController');
    Route::resource('configurations', 'Cms\ConfigurationController');
  });
});
