<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'crm', 'namespace' => 'CRM'], function () {
    Route::get('/', 'DashboardController@index');
    Route::group(['prefix' => 'customers'], function () {
        Route::any('/', 'CustomerController@index');
        Route::any('add', 'CustomerController@add');
        Route::any('edit/{id}', 'CustomerController@edit');
        Route::get('view/{id}', 'CustomerController@view');
        Route::post('delete', 'CustomerController@delete');
        Route::any('/districts', 'CustomerController@getDistricts')->name('getDistrict');
        Route::any('/upazilla', 'CustomerController@getUpazilla')->name('getUpazila');
        Route::any('/union', 'CustomerController@getUnion')->name('getUnion');
        Route::any('/uniqueMobileno', 'CustomerController@getUniqueMobileno')->name('uniqueMobileno');
    });
    Route::group(['prefix' => 'followups'], function () {
        Route::any('/', 'FollowupsController@index');
        Route::any('add', 'FollowupsController@add');
        Route::any('edit/{id}', 'FollowupsController@edit');
        Route::get('view/{id}', 'FollowupsController@show');
        Route::post('delete', 'FollowupsController@delete');
        Route::any('duplicateCheck','FollowupsController@duplicateCheck')->name('duplicateCheck');
    });
});