<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Website\Admin\BannerController;
use App\Http\Controllers\Website\Admin\GalleryController;
use App\Http\Controllers\Website\Admin\NavigationController;
use App\Http\Controllers\Website\Admin\PagesController;
use App\Http\Controllers\Website\Admin\PostController;
use App\Http\Controllers\Website\Admin\ProfileController;
use App\Http\Controllers\Website\Admin\SEOController;
use App\Http\Controllers\Website\Admin\SettingsController;
use App\Http\Controllers\Website\Admin\TestimonialsController;
use App\Http\Controllers\Website\Admin\WebsiteFilesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::controller(HomeController::class)->group(function () {
    Route::GET('/', 'index')->name('home');
    Route::GET('/privacy', 'privacy')->name('privacy');
    Route::GET('/{title}', 'menu')->name('menu');
    Route::GET('/get/menus', 'getAllMenus')->name('get.menus');
    Route::post('/get-analytics', 'index')->name('get.analytics');
});

Route::controller(ProfileController::class)->group(function () {
    Route::GET('/user/profile', 'index')->name('profile.index');
    Route::POST('/user/profile/update', 'update')->name('profile.update');
    Route::POST('/user/update/logo', 'logo')->name('logo.update');
    Route::POST('/user/update/favicon', 'favicon')->name('favicon.update');
});

//WEBSITE ADMIN
Route::prefix('website/admin')->controller(BannerController::class)->group(function () {
    Route::get('/banner/index', 'index')->name('banner.index');
    Route::post('/banner/save', 'save')->name('banner.save');
    Route::get('/banner/edit/{id}', 'edit')->name('banner.edit');
    Route::post('/banner/update/{id}', 'update')->name('banner.update');
    Route::post('/banner/{status}/{id}', 'status')->name('banner.status');
    Route::delete('/banner/delete/{id}', 'delete')->name('banner.delete');
});

Route::prefix('website/admin')->controller(NavigationController::class)->group(function () {
    Route::get('/navigation/index', 'index')->name('navigation.index');
    Route::post('/navigation/save', 'save')->name('navigation.save');
    Route::get('/navigation/edit/{id}', 'edit')->name('navigation.edit');
    Route::post('/navigation/update/{id}', 'update')->name('navigation.update');
    Route::post('/navigation/{status}/{id}', 'status')->name('navigation.status');
    Route::delete('/navigation/delete/{id}', 'delete')->name('navigation.delete');
});

Route::prefix('website/admin')->controller(SEOController::class)->group(function () {
    Route::get('/seo/index', 'index')->name('seo.index');
    Route::post('/seo/save', 'save')->name('seo.save');
    Route::get('/seo/edit/{id}', 'edit')->name('seo.edit');
    Route::post('/seo/update/{id}', 'update')->name('seo.update');
    Route::post('/seo/{status}/{id}', 'status')->name('seo.status');
    Route::delete('/seo/delete/{id}', 'delete')->name('seo.delete');
});

Route::prefix('website/admin')->controller(SettingsController::class)->group(function () {
    Route::get('/settings/index', 'index')->name('settings.index');
    Route::post('/settings/save', 'save')->name('settings.save');
    // Route::get('/settings/edit/{id}', 'edit')->name('settings.edit');
    Route::post('/settings/update/{id}', 'update')->name('settings.update');
    // Route::post('/settings/{status}/{id}', 'status')->name('settings.status');
    Route::delete('/settings/delete/{id}', 'delete')->name('settings.delete');
});

Route::prefix('website/admin')->controller(TestimonialsController::class)->group(function () {
    Route::get('/testimonial/index', 'index')->name('testimonial.index');
    Route::post('/testimonial/save', 'save')->name('testimonial.save');
    Route::get('/testimonial/edit/{id}', 'edit')->name('testimonial.edit');
    Route::post('/testimonial/update/{id}', 'update')->name('testimonial.update');
    Route::post('/testimonial/{status}/{id}', 'status')->name('testimonial.status');
    Route::delete('/testimonial/delete/{id}', 'delete')->name('testimonial.delete');
});

Route::prefix('website/admin')->controller(GalleryController::class)->group(function () {
    Route::get('/gallery/index', 'index')->name('gallery.index');
    Route::post('/gallery/save', 'save')->name('gallery.save');
    Route::get('/gallery/edit/{id}', 'edit')->name('gallery.edit');
    Route::post('/gallery/update/{id}', 'update')->name('gallery.update');
    Route::post('/gallery/{status}/{id}', 'status')->name('gallery.status');
    Route::delete('/gallery/delete/{id}', 'delete')->name('gallery.delete');
});

Route::prefix('website/admin')->controller(PostController::class)->group(function () {
    Route::get('/post/index', 'index')->name('post.index');
    Route::post('/post/save', 'save')->name('post.save');
    Route::get('/post/edit/{id}', 'edit')->name('post.edit');
    Route::post('/post/update/{id}', 'update')->name('post.update');
    Route::post('/post/{status}/{id}', 'status')->name('post.status');
    Route::delete('/post/delete/{id}', 'delete')->name('post.delete');
});

Route::prefix('website/admin')->controller(PagesController::class)->group(function () {
    Route::get('/page/index', 'index')->name('page.index');
    Route::get('/page/create', 'create')->name('page.create');
    Route::get('/page/continue', 'continue')->name('page.continue');
    Route::post('/page/save', 'save')->name('page.save');
    Route::get('/page/edit/{id}', 'edit')->name('page.edit');
    Route::post('/page/update/{id}', 'update')->name('page.update');
    Route::post('/page/{status}/{id}', 'status')->name('page.status');
    Route::delete('/page/delete/{id}', 'delete')->name('page.delete');

    Route::get('/page/get/template', 'getTemplate')->name('page.getTemplate');
    Route::post('/page/add/new/element', 'addPageElement')->name('page.addPageElement');
    // Route::post('/editor/delete','delete')->name('editor.delete');
    // Route::post('/editor/save-reusable','saveReusable')->name('editor.saveReusable');
    // Route::post('/editor/oembed-proxy', 'oembedProxy')->name('editor.oembedProxy');
});

Route::prefix('website/admin')->controller(WebsiteFilesController::class)->group(function () {
    Route::delete('/delete/files/{id}', 'deleteFile')->name('delete.file');
});
// OPTIMIZE
Route::get('/admin/optimize/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    notyf()->success('Your request was processed successfully.');
    return redirect()->back();
})->name('optimize:clear');
//SITEMAP
Route::get('/admin/sitemap', [App\Http\Controllers\HomeController::class, 'sitemap'])->name('sitemap');
