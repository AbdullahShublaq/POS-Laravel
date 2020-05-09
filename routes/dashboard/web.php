<?php

Route::group(
[
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
], function(){ 

		Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function (){

			Route::get('/', 'WelcomeController@index')->name('welcome');

			// user routes
			Route::resource('users', 'UserController')->except(['show']);

			//category routes
            Route::resource('categories', 'CategoryController')->except(['show']);

            //Product routes
            Route::resource('products', 'ProductController')->except(['show']);

            //Client & Order routes
            Route::resource('clients', 'ClientController')->except(['show']);
            Route::resource('clients.orders', 'Client\OrderController')->except(['index', 'show', 'destroy']);

            //Order routes
            Route::resource('orders', 'OrderController')->only(['index', 'destroy']);
            Route::get('orders/{order}/products', 'OrderController2@products')->name('orders.products');

		});
});

