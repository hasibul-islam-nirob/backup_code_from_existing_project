<?php

use Illuminate\Support\Facades\Route;


// COMMENT KORE RAKHA HOILO, NA LAGLE DELETE KORE DIBO
# manage task of different modules
// Route::group(['middleware' => ['auth'], 'prefix' => 'gnl/managedayend'], function () {
//     Route::get('/', 'GNL\ManageDayEndController@index');
//     Route::post('/getinfo', 'GNL\ManageDayEndController@getInfo');
//     Route::post('/delete', 'GNL\ManageDayEndController@delete');
// });

Route::group(['middleware' => ['auth', 'permission'], 'prefix' => 'gnl', 'namespace' => 'GNL'], function () {

    /* -------------------------------------Api------------------------------------- */
    Route::group(['namespace' => 'Api'], function () {

        ## Common api route
        Route::group(['prefix' => 'common_api'], function () {
            Route::any('gnl_get_des_by_emp_id/{empId}/api', 'CommonApiController@get_des_by_emp_id')->name('gnl_get_des_by_emp_id');
        });
    });
    /* -------------------------------------Api------------------------------------- */

    Route::group(['namespace' => 'TMS'], function () {

        ## Assign Task Route
        Route::group(['prefix' => 'new_task'], function () {

            Route::get('/', function () {
                return view('GNL/TMS/TaskManagment/index');
            });

            Route::any('/add', function () {
                return view('GNL/TMS/TaskManagment/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('GNL/TMS/TaskManagment/edit', compact('id'));
            });

            Route::any('/view/{id}', function ($id) {
                return view('GNL/TMS/TaskManagment/view', compact('id'));
            });

            Route::post('/', 'NewTaskController@index'); // api
            Route::any('/insert/{status}/api', 'NewTaskController@insert');
            Route::any('/update/{status}/api', 'NewTaskController@update');
            Route::any('/get/{id}/api', 'NewTaskController@get');
            Route::any('/delete/{id}', 'NewTaskController@delete');
            // Route::any('approve/{task_code}', 'NewTaskController@isApprove');
            Route::any('/approve', 'NewTaskController@isApprove')->name('approve');
            Route::any('getData', 'NewTaskController@getData');

        });

        ## Task Type Route
        Route::group(['prefix' => 'task_type'], function () {

            Route::get('/', function () {
                return view('GNL/TMS/TaskType/index');
            });

            Route::any('/add', function () {
                return view('GNL/TMS/TaskType/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('GNL/TMS/TaskType/edit', compact('id'));
            });

            Route::any('/view/{id}', function ($id) {
                return view('GNL/TMS/TaskType/view', compact('id'));
            });

            // api
            Route::post('/', 'TaskTypeController@index');
            Route::any('/insert/{status}/api', 'TaskTypeController@insert');
            Route::any('/update/{status}/api', 'TaskTypeController@update');
            Route::any('/get/{id}/api', 'TaskTypeController@get');
            Route::any('/delete/{id}', 'TaskTypeController@delete');

        });

        ## Employee daily Task record
        Route::group(['prefix' => 'emp_task_record_daily'], function () {

            Route::get('/', function () {
                return view('GNL/TMS/DailyTaskRecord/index');
            });

            Route::any('/add', function () {
                return view('GNL/TMS/DailyTaskRecord/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('GNL/TMS/DailyTaskRecord/edit', compact('id'));
            });

            Route::any('/view/{id}', function ($id) {
                return view('GNL/TMS/DailyTaskRecord/view', compact('id'));
            });

            Route::post('/', 'DailyTaskRecordController@index'); // api
            Route::any('/insert/{status}/api', 'DailyTaskRecordController@insert');
            Route::any('/update/{status}/api', 'DailyTaskRecordController@update');
            Route::any('/get/{id}/api', 'DailyTaskRecordController@get');
            Route::any('/delete/{id}', 'DailyTaskRecordController@delete');
            // Route::any('approve/{task_code}', 'DailyTaskRecordController@isApprove');
            Route::any('getData', 'DailyTaskRecordController@getData');

        });
    });
});
