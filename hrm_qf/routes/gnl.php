<?php

use Illuminate\Support\Facades\Route;

// $this->middleware(['auth', 'permission']);
// ['auth', 'web']
// 'auth'

// Employee Transfer routes

/* Route::group(['middleware' => ['auth', 'permission','offline'], 'prefix' => 'gnl'], function () {
    // Employee Transfer routes
    Route::group(['prefix' => 'employeeTransfer'], function () {
        Route::any('/', 'HR\EmployeeTransferController@index');
        Route::any('add', 'HR\EmployeeTransferController@add');
        Route::any('edit/{id}', 'HR\EmployeeTransferController@edit');
        Route::get('view/{id}', 'HR\EmployeeTransferController@view');
        Route::any('delete/{id}', 'HR\EmployeeTransferController@delete');
        Route::any('approve', 'HR\EmployeeTransferController@approve');
        Route::any('getData', 'HR\EmployeeTransferController@getData');
    });

    // Employee Terminate routes
    Route::group(['prefix' => 'employeeTerminate'], function () {
        Route::any('/', 'HR\EmployeeTerminateController@index');
        Route::any('add', 'HR\EmployeeTerminateController@add');
        Route::any('edit/{id}', 'HR\EmployeeTerminateController@edit');
        Route::get('view/{id}', 'HR\EmployeeTerminateController@view');
        Route::any('delete/{id}', 'HR\EmployeeTerminateController@delete');
        Route::any('approve', 'HR\EmployeeTerminateController@approve');
        Route::any('getData', 'HR\EmployeeTerminateController@getData');
    });

}); */

# manage day end of different modules
// Route::group(['middleware' => ['auth'], 'prefix' => 'gnl/managedayend'], function () {
//     Route::get('/', 'GNL\ManageDayEndController@index');
//     Route::post('/getinfo', 'GNL\ManageDayEndController@getInfo');
//     Route::post('/delete', 'GNL\ManageDayEndController@delete');
// });

// Route::group(['middleware' => ['auth'], 'prefix' => 'gnl/managedayend'], function () {
//     return view('errors.under_construction');
// });

Route::group(['middleware' => ['auth', 'permission', 'offline'], 'prefix' => 'gnl', 'namespace' => 'GNL'], function () {

    // Controllers Within The "App\Http\Controllers\Admin" Namespace
    Route::get('/', 'DashboardController@index');
    Route::get('/scripts', 'DashboardController@script');
    Route::get('/scripts/{id}', 'DashboardController@script');

    /* -------------------------------------Others--------------------------------- */
    Route::group(['namespace' => 'Others'], function () {

        Route::post('/getDistricts', 'CommonController@getDistricts');
        Route::post('/getUpazilas', 'CommonController@getUpazilas');
        Route::post('/getUnions', 'CommonController@getUnions');
        Route::post('/getVillages', 'CommonController@getVillages');

        Route::post('/gnl_getBanks', 'CommonController@getBanks')->name('gnl_getBanks');
        Route::post('/gnl_getBankBranches', 'CommonController@getBankBranches')->name('gnl_getBankBranches');
        Route::post('/gnl_getProjectType', 'CommonController@getProjectType')->name('gnl_getProjectType');
        Route::get('/gnl_getEmployeesOptionsByBranch/{id?}', 'CommonController@get_employees_options_by_branch')->name('gnl_getEmployeesOptionsByBranch');
        Route::post('/gnl_searchEmployeeAndGetOptions', 'CommonController@search_employee_and_get_options')->name('gnl_searchEmployeeAndGetOptions');
    });
    /* -------------------------------------Others--------------------------------- */

    /* -------------------------------------Api------------------------------------- */
    Route::group(['namespace' => 'Api'], function () {

        ## Common api route
        Route::group(['prefix' => 'common_api'], function () {
            Route::any('gnl_get_des_by_emp_id/{empId}/api', 'CommonApiController@get_des_by_emp_id')->name('gnl_get_des_by_emp_id');
        });
    });
    /* -------------------------------------Api------------------------------------- */


    // // ## Payment Account  Route
    Route::group(['prefix' => 'payment_acc'], function () {

        Route::any('/', 'PaymentAccountController@index');
        Route::any('add', 'PaymentAccountController@add');
        Route::any('edit/{id}', 'PaymentAccountController@edit');
        Route::get('view/{id}', 'PaymentAccountController@view');
        Route::any('delete/{id}', 'PaymentAccountController@delete');
        Route::any('publish/{id}', 'PaymentAccountController@isActive');

    });

    // // ## Payment Account  Route
    Route::group(['prefix' => 'payment_sys'], function () {

        Route::any('/', 'PaymentSystemController@index');
        Route::any('add', 'PaymentSystemController@add');
        Route::any('edit/{id}', 'PaymentSystemController@edit');
        Route::get('view/{id}', 'PaymentSystemController@view');
        Route::any('delete/{id}', 'PaymentSystemController@delete');
        Route::any('publish/{id}', 'PaymentSystemController@isActive');

    });

    // // ## Group Route
    // match(['get', 'post'],
    Route::group(['prefix' => 'group'], function () {

        Route::any('/', 'GroupController@index')->name('grpDatatable');
        Route::any('add', 'GroupController@add');
        Route::any('edit/{id}', 'GroupController@edit');
        Route::get('view/{id}', 'GroupController@view');
        Route::any('delete/{id}', 'GroupController@delete');
        Route::any('publish/{id}', 'GroupController@isActive');

    });

    // // ## Company Configaration
    Route::group(['prefix' => 'company_config'], function () {

        Route::any('/', 'CompanyController@get_modules');
        Route::any('m/{moduleId}', 'CompanyController@get_form');

    });

    // // ## Company Route
    Route::group(['prefix' => 'company'], function () {

        Route::any('/', 'CompanyController@index')->name('comDatatable');
        Route::any('add', 'CompanyController@add');
        Route::any('edit/{id}', 'CompanyController@edit');
        Route::get('view/{id}', 'CompanyController@view');
        Route::any('delete/{id}', 'CompanyController@delete');
        Route::any('publish/{id}', 'CompanyController@isActive');

    });

    // // ## Company Type Route
    Route::group(['prefix' => 'com_type'], function () {

        Route::any('/', 'CompanyTypeController@index');
        Route::any('add', 'CompanyTypeController@add');
        Route::any('edit/{id}', 'CompanyTypeController@edit');
        Route::get('view/{id}', 'CompanyTypeController@view');
        Route::post('delete', 'CompanyTypeController@delete');
        Route::any('publish', 'CompanyTypeController@isActive');

    });

    // // ## Project Route
    Route::group(['prefix' => 'project'], function () {

        Route::any('/', 'ProjectController@index')->name('proDatatable');
        Route::any('add', 'ProjectController@add');
        Route::any('edit/{id}', 'ProjectController@edit');
        Route::get('view/{id}', 'ProjectController@view');
        Route::any('delete/{id}', 'ProjectController@delete');
        Route::any('publish/{id}', 'ProjectController@isActive');

    });

    // // ## ProjectType Route
    Route::group(['prefix' => 'project_type'], function () {

        Route::any('/', 'ProjectTypeController@index')->name('pTypeDatatable');
        Route::any('add', 'ProjectTypeController@add');
        Route::any('edit/{id}', 'ProjectTypeController@edit');
        Route::get('view/{id}', 'ProjectTypeController@view');
        Route::any('delete/{id}', 'ProjectTypeController@delete');
        Route::any('publish/{id}', 'ProjectTypeController@isActive');

        // Route::get('ajaxProject', 'ProjectTypeController@ajaxProjectLoad');

    });

    // // ## Branch Route
    Route::group(['prefix' => 'branch'], function () {

        Route::any('/', 'BranchController@index')->name('branchDatatable');
        Route::any('add', 'BranchController@add');
        Route::any('edit/{id}', 'BranchController@edit');
        Route::get('view/{id}', 'BranchController@view');
        Route::any('delete/{id}', 'BranchController@delete');
        Route::any('publish/{id}', 'BranchController@isActive');
        Route::get('approve/{id}', 'BranchController@isApprove');
        Route::get('/getRegion', 'BranchController@getRegion')->name('getRegion');
        Route::get('/getArea', 'BranchController@getArea')->name('getArea');


        // Route::get('ajaxProjectType', 'BranchController@ajaxProjectTypeLoad');

    });

    // // ## Division Route
    Route::group(['prefix' => 'division'], function () {

        Route::any('/', 'AddressController@divIndex')->name('divDatatable');
        Route::any('add', 'AddressController@divAdd');
        Route::any('edit/{id}', 'AddressController@divEdit');
        Route::get('view/{id}', 'AddressController@divView');
        Route::any('delete/{id}', 'AddressController@divDelete');
        Route::any('publish/{id}', 'AddressController@divIsactive');

    });

    // // ## District Route
    Route::group(['prefix' => 'district'], function () {

        Route::any('/', 'AddressController@disIndex')->name('DistrictDatatable');
        Route::any('add', 'AddressController@disAdd');
        Route::any('edit/{id}', 'AddressController@disEdit');
        Route::get('view/{id}', 'AddressController@disView');
        Route::any('delete/{id}', 'AddressController@disDelete');
        Route::any('publish/{id}', 'AddressController@disIsactive');

    });

    // // ## Upzilla Route
    Route::group(['prefix' => 'upazila'], function () {

        Route::any('/', 'AddressController@upIndex')->name('UpazilaDatatable');
        Route::any('add', 'AddressController@upAdd');
        Route::any('edit/{id}', 'AddressController@upEdit');
        Route::get('view/{id}', 'AddressController@upView');
        Route::any('delete/{id}', 'AddressController@upDelete');
        Route::any('publish/{id}', 'AddressController@upIsactive');

    });

    // // ## Union Route
    Route::group(['prefix' => 'union'], function () {

        Route::any('/', 'AddressController@unionIndex')->name('unionDatatable');
        Route::any('add', 'AddressController@unionAdd');
        Route::any('edit/{id}', 'AddressController@unionEdit');
        Route::get('view/{id}', 'AddressController@unionView');
        Route::any('delete/{id}', 'AddressController@unionDelete');
        Route::any('publish/{id}', 'AddressController@unionIsactive');

    });

    // // ##  village Route
    Route::group(['prefix' => 'village'], function () {

        Route::any('/', 'AddressController@villageIndex')->name('VillageDatatable');
        Route::any('add', 'AddressController@villageAdd');
        Route::any('edit/{id}', 'AddressController@villageEdit');
        Route::get('view/{id}', 'AddressController@villageView');
        Route::any('delete/{id}', 'AddressController@villageDelete');
        Route::any('publish/{id}', 'AddressController@villageIsactive');

    });

    // // ## Area Route
    Route::group(['prefix' => 'area'], function () {

        Route::any('/', 'AreaController@index')->name('areaDatatable');
        Route::any('add', 'AreaController@add');
        Route::any('edit/{id}', 'AreaController@edit');
        Route::get('view/{id}', 'AreaController@view');
        Route::any('delete/{id}', 'AreaController@delete');
        Route::any('publish/{id}', 'AreaController@isActive');

        Route::get('ajaxAreaList', 'AreaController@ajaxAreaListLoad');

    });

    // // ##  Zone Route
    Route::group(['prefix' => 'zone'], function () {

        Route::any('/', 'ZoneController@index')->name('zoneDatatable');
        Route::any('add', 'ZoneController@add');
        Route::any('edit/{id}', 'ZoneController@edit');
        Route::get('view/{id}', 'ZoneController@view');
        Route::any('delete/{id}', 'ZoneController@delete');
        Route::any('publish/{id}', 'ZoneController@isActive');

        Route::get('ajaxZoneList', 'ZoneController@ajaxZoneListLoad');

    });

    // // ## Region Route
    Route::group(['prefix' => 'region'], function () {

        Route::any('/', 'RegionController@index')->name('regionDatatable');
        Route::any('add', 'RegionController@add');
        Route::any('edit/{id}', 'RegionController@edit');
        Route::get('view/{id}', 'RegionController@view');
        Route::any('delete/{id}', 'RegionController@delete');
        Route::any('publish/{id}', 'RegionController@isActive');

        Route::get('ajaxRegion', 'RegionController@ajaxRegionLoad');
        Route::get('ajaxAreaLoad', 'RegionController@ajaxAreaListLoad');

    });

    // // ## Fiscal Year Route
    Route::group(['prefix' => 'fiscal_year'], function () {

        Route::any('/', 'HR\FiscalYearController@index')->name('fiscalYearDatatable');
        Route::any('add', 'HR\FiscalYearController@add');
        Route::any('edit/{id}', 'HR\FiscalYearController@edit');
        Route::get('view/{id}', 'HR\FiscalYearController@view');
        Route::any('delete', 'HR\FiscalYearController@delete');
        Route::any('publish/{id}', 'HR\FiscalYearController@isActive');

    });

    // // ## Govt Holiday Route
    Route::group(['prefix' => 'govtholiday'], function () {

        Route::any('/', 'HR\GovtHolidayController@index')->name('gnlHrgovHolDatatable');
        Route::any('add', 'HR\GovtHolidayController@add');
        Route::any('edit/{id}', 'HR\GovtHolidayController@edit');
        Route::get('view/{id}', 'HR\GovtHolidayController@view');
        Route::any('delete', 'HR\GovtHolidayController@delete');
        Route::any('publish/{id}', 'HR\GovtHolidayController@isActive');

    });

    // // ## Company Holiday Route
    Route::group(['prefix' => 'compholiday'], function () {

        Route::any('/', 'HR\CompHolidayController@index')->name('gnlHrComHolDatatable');
        Route::any('add', 'HR\CompHolidayController@add');
        Route::any('edit/{id}', 'HR\CompHolidayController@edit');
        Route::get('view/{id}', 'HR\CompHolidayController@view');
        Route::any('delete/{id}', 'HR\CompHolidayController@delete');
        Route::any('publish/{id}', 'HR\CompHolidayController@isActive');
        Route::get('/CheckDayEnd', 'HR\CompHolidayController@CheckDayEnd');

    });

    // // ## Special Holiday Route
    Route::group(['prefix' => 'specialholiday'], function () {

        Route::any('/', 'HR\SpecialHolidayController@index')->name('gnlHrspecialholidayDatatable');
        Route::any('add', 'HR\SpecialHolidayController@add');
        Route::any('edit/{id}', 'HR\SpecialHolidayController@edit');
        Route::get('view/{id}', 'HR\SpecialHolidayController@view');
        Route::any('delete/{id}', 'HR\SpecialHolidayController@delete');
        Route::any('publish/{id}', 'HR\SpecialHolidayController@isActive');

        Route::get('/CheckDayEnd', 'HR\SpecialHolidayController@CheckDayEnd');

    });
    // Terms and conditions Route
    Route::group(['prefix' => 'terms_conditions'], function () {

        Route::any('/', 'TermsConditionsController@index')->name('tcDatatable');
        Route::any('add', 'TermsConditionsController@add');
        Route::any('edit/{id}', 'TermsConditionsController@edit');
        Route::get('view/{id}', 'TermsConditionsController@view');
        Route::any('delete', 'TermsConditionsController@delete');
        Route::any('publish/{id}', 'TermsConditionsController@isActive');

    });
    // // ## System module Route
    Route::group(['prefix' => 'sys_module'], function () {

        Route::any('/', 'SysModuleController@index')->name('sysModDatatable');
        Route::any('add', 'SysModuleController@add');
        Route::any('edit/{id}', 'SysModuleController@edit');
        // Route::get('view/{id}', 'SysModuleController@view');
        Route::any('delete/{id}', 'SysModuleController@delete');
        Route::any('publish/{id}', 'SysModuleController@isActive');
        Route::any('destroy/{id}', 'SysModuleController@destroy');

        // Route::get('active/{id}', 'SysModuleController@isActive');

    });

    // // ## System menus Route
    Route::group(['prefix' => 'sys_menu'], function () {

        Route::any('/', 'SysUserMenusController@index')->name('sysUserMenusDatatable');
        Route::any('add', 'SysUserMenusController@add');
        Route::any('edit/{id}', 'SysUserMenusController@edit');
        Route::any('delete', 'SysUserMenusController@delete');
        Route::any('publish/{id}', 'SysUserMenusController@isActive');
        Route::any('destroy/{id}', 'SysUserMenusController@destroy');

    });

    // // ## system menu action / oparetor Route
    Route::group(['prefix' => 'sys_menu_action'], function () {

        Route::any('/', 'SysMenuActionsController@index')->name('sysUserActionMenusDatatable');

        Route::any('/add', 'SysMenuActionsController@add');
        Route::any('/edit/{id}', 'SysMenuActionsController@edit');
        Route::any('/delete', 'SysMenuActionsController@delete');
        Route::any('/publish/{id}', 'SysMenuActionsController@isActive');
        Route::any('/destroy/{id}', 'SysMenuActionsController@destroy');

    });

    // // ## system permission Route (Depretiate)
    Route::group(['prefix' => 'sys_permission'], function () {

        Route::any('/{mid}', 'SysUserMenusController@indexPermission');
        Route::any('/{mid}/add', 'SysUserMenusController@addPermission');
        Route::any('/{mid}/edit/{id}', 'SysUserMenusController@editPermission');
        Route::any('/{mid}/delete/{id}', 'SysUserMenusController@deletePermission');
        Route::any('/{mid}/publish/{id}', 'SysUserMenusController@isActivePermission');
        Route::any('/{mid}/destroy/{id}', 'SysUserMenusController@destroyPermission');

    });

    // // ## system user Route
    Route::group(['prefix' => 'sys_user'], function () {

        Route::any('/', 'SysUserController@index');
        Route::any('add', 'SysUserController@add');
        Route::any('edit/{id}', 'SysUserController@edit');
        Route::get('view/{id}', 'SysUserController@view');
        Route::any('delete/{id}', 'SysUserController@delete');
        Route::any('publish/{id}', 'SysUserController@isActive');
        Route::any('destroy/{id}', 'SysUserController@destroy');

        Route::any('change_pass/{id}', 'SysUserController@changePassword');
    });


    // // ## System user role Route
    Route::group(['prefix' => 'sys_role'], function () {

        Route::any('/', 'SysUserRoleController@index');
        Route::any('add', 'SysUserRoleController@add');
        Route::any('edit/{id}', 'SysUserRoleController@edit');
        Route::any('delete/{id}', 'SysUserRoleController@delete');
        Route::any('publish/{id}', 'SysUserRoleController@isActive');
        Route::any('destroy/{id}', 'SysUserRoleController@destroy');

        // // ##    system permission assign
        Route::any('passign/{id}', 'SysUserRoleController@assignPermission');
    });

    // // ## Branch Db table route
    Route::group(['prefix' => 'br_db'], function () {

        Route::any('/', 'BranchDBController@index')->name('brDbDatatable');
        Route::any('add', 'BranchDBController@add');
        Route::any('edit/{id}', 'BranchDBController@edit');
        Route::any('delete', 'BranchDBController@delete');
        Route::any('publish/{id}', 'BranchDBController@isActive');

    });
    // // ## HO Db table route
    Route::group(['prefix' => 'ho_db'], function () {

        Route::any('/', 'HODBController@index')->name('hodbDatatable');
        Route::any('add', 'HODBController@add');
        Route::any('edit/{id}', 'HODBController@edit');
        Route::any('delete', 'HODBController@delete');
        Route::any('publish/{id}', 'HODBController@isActive');
    });

    // // ## HO Db table route
    Route::group(['prefix' => 'ho_db_ignore'], function () {
        Route::any('/', 'HOIGController@index')->name('hoigDatatable');
        Route::any('add', 'HOIGController@add');
        Route::any('edit/{id}', 'HOIGController@edit');
        Route::any('delete', 'HOIGController@delete');
        Route::any('publish/{id}', 'HOIGController@isActive');
    });

    // // ## Signature Setting
    Route::group(['prefix' => 'signature_set'], function () {
        Route::any('/', 'SignatureSettingsController@index');
        Route::get('/view/{wareaId}', 'SignatureSettingsController@view');
        Route::match(['get', 'post'], 'add', 'SignatureSettingsController@add');
        Route::match(['get', 'post'], '/edit/{id}', 'SignatureSettingsController@edit');
        Route::any('/publish/{id}', 'SignatureSettingsController@isActive');
        Route::any('/delete/{id}', 'SignatureSettingsController@delete');
    });

    //----------------------------- Employee------------------------------------>
    Route::group(['prefix' => 'employee'], function () {

        Route::any('/', 'HR\EmployeeController@index')->name('employeeDatatableGnl');
        Route::any('add', 'HR\EmployeeController@add');
        Route::any('edit/{id}', 'HR\EmployeeController@edit');
        Route::get('view/{id}', 'HR\EmployeeController@view');
        Route::any('delete/{id}', 'HR\EmployeeController@delete');
        Route::any('publish/{id}', 'HR\EmployeeController@isActive');
        Route::any('destroy/{id}', 'HR\EmployeeController@destroy');

    });

    Route::group(['namespace' => 'HR'], function () {
        ## Employee resign
        Route::group(['prefix' => 'employeeResign'], function () {

            Route::get('/', function () {
                return view('GNL/HR/EmployeeResign/index');
            });
            Route::post('/', 'EmployeeResignController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('GNL/HR/EmployeeResign/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('GNL/HR/EmployeeResign/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('GNL/HR/EmployeeResign/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeResignController@delete');


            Route::any('/insert/{status}/api', 'EmployeeResignController@insert');
            Route::any('/update/{status}/api', 'EmployeeResignController@update');
            Route::any('/get/{id}/api', 'EmployeeResignController@get');
            Route::any('/send/{id}/api', 'EmployeeResignController@send');
        });

        ## Employee promotion
        Route::group(['prefix' => 'employeePromotion'], function () {

            Route::get('/', function () {
                return view('GNL/HR/EmployeePromotion/index');
            });
            Route::post('/', 'EmployeePromotionController@index'); // api

            Route::any('/add', function () {
                return view('GNL/HR/EmployeePromotion/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('GNL/HR/EmployeePromotion/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('GNL/HR/EmployeePromotion/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeePromotionController@delete');


            Route::any('/insert/{status}/api', 'EmployeePromotionController@insert');
            Route::any('/update/{status}/api', 'EmployeePromotionController@update');
            Route::any('/get/{id}/api', 'EmployeePromotionController@get');
            Route::any('/send/{id}/api', 'EmployeePromotionController@send');

            Route::any('/getData/{id}/api', 'EmployeePromotionController@getData');
        });

        ## Employee terminate
        Route::group(['prefix' => 'employeeTerminate'], function () {

            Route::get('/', function () {
                return view('GNL/HR/EmployeeTerminate/index');
            });
            Route::post('/', 'EmployeeTerminateController@index'); // api

            Route::any('/add', function () {
                return view('GNL/HR/EmployeeTerminate/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('GNL/HR/EmployeeTerminate/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('GNL/HR/EmployeeTerminate/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeTerminateController@delete');


            Route::any('/insert/{status}/api', 'EmployeeTerminateController@insert');
            Route::any('/update/{status}/api', 'EmployeeTerminateController@update');
            Route::any('/get/{id}/api', 'EmployeeTerminateController@get');
            Route::any('/send/{id}/api', 'EmployeeTerminateController@send');
        });

        ## Employee transfer
        Route::group(['prefix' => 'employeeTransfer'], function () {

            Route::get('/', function () {
                return view('GNL/HR/EmployeeTransfer/index');
            });
            Route::post('/', 'EmployeeTransferController@index'); // api

            Route::any('/add', function () {
                return view('GNL/HR/EmployeeTransfer/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('GNL/HR/EmployeeTransfer/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('GNL/HR/EmployeeTransfer/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeTransferController@delete');


            Route::any('/insert/{status}/api', 'EmployeeTransferController@insert');
            Route::any('/update/{status}/api', 'EmployeeTransferController@update');
            Route::any('/get/{id}/api', 'EmployeeTransferController@get');
            Route::any('/send/{id}/api', 'EmployeeTransferController@send');
        });

        ## Designation and Role Mapping
        Route::group(['prefix' => 'designation_and_role_mapping'], function () {

            Route::get('/', function () {

                return view('GNL/HR/DesignationRoleMapping/index');
            });
            Route::any('/update/api', 'DesignationRoleMappingController@update');
        });
    });

    /* Route::group(['prefix' => 'employeeTransfer'], function () {
        Route::any('/', 'HR\EmployeeTransferController@index');
        Route::any('add', 'HR\EmployeeTransferController@add');
        Route::any('edit/{id}', 'HR\EmployeeTransferController@edit');
        Route::get('view/{id}', 'HR\EmployeeTransferController@view');
        Route::any('delete/{id}', 'HR\EmployeeTransferController@delete');
        Route::any('approve', 'HR\EmployeeTransferController@approve');
        Route::any('getData', 'HR\EmployeeTransferController@getData');
    });

    // Employee Terminate routes
    Route::group(['prefix' => 'employeeTerminate'], function () {
        Route::any('/', 'HR\EmployeeTerminateController@index');
        Route::any('add', 'HR\EmployeeTerminateController@add');
        Route::any('edit/{id}', 'HR\EmployeeTerminateController@edit');
        Route::get('view/{id}', 'HR\EmployeeTerminateController@view');
        Route::any('delete/{id}', 'HR\EmployeeTerminateController@delete');
        Route::any('approve', 'HR\EmployeeTerminateController@approve');
        Route::any('getData', 'HR\EmployeeTerminateController@getData');
    }); */

    //----------------------------- Employee------------------------------------>

    // Employee Transfer
    // Route::group(['prefix' => 'employeeTransfer'], function () {
    //     Route::any('/', 'EmployeeTransferController@index');
    //     Route::any('add', 'EmployeeTransferController@add');
    //     Route::any('edit/{id}', 'EmployeeTransferController@edit');
    //     Route::get('view/{id}', 'EmployeeTransferController@view');
    //     Route::any('delete', 'EmployeeTransferController@delete');
    //     Route::any('approve', 'EmployeeTransferController@approve');
    //     Route::any('getData', 'EmployeeTransferController@getData');
    // });
    // // ## Notice Route
    Route::group(['prefix' => 'notice'], function () {
        Route::any('/', 'NoticeController@index')->name('noticeDatatableG');
        Route::any('add', 'NoticeController@add');
        Route::any('edit/{id}', 'NoticeController@edit');
        Route::get('view/{id}', 'NoticeController@view');
        Route::any('delete/{id}', 'NoticeController@delete');
        Route::any('publish/{id}', 'NoticeController@isActive');
        Route::any('destroy/{id}', 'NoticeController@destroy');
    });

    // // ## Department Route
    Route::group(['prefix' => 'department'], function () {

        Route::any('/', 'HR\DepartmentController@index')->name('gnlHrDepDatatable');
        Route::any('add', 'HR\DepartmentController@add');
        Route::any('edit/{id}', 'HR\DepartmentController@edit');
        Route::get('view/{id}', 'HR\DepartmentController@view');
        Route::any('delete', 'HR\DepartmentController@delete');
        Route::any('publish/{id}', 'HR\DepartmentController@isActive');

    });

    // // ## Room Route
    Route::group(['prefix' => 'room'], function () {

        Route::any('/', 'RoomController@index')->name('roomDatatable');
        Route::any('add', 'RoomController@add');
        Route::any('edit/{id}', 'RoomController@edit');
        Route::get('view/{id}', 'RoomController@view');
        Route::any('delete', 'RoomController@delete');
        Route::any('publish/{id}', 'RoomController@isActive');

    });

    // // ## Designation Route
    Route::group(['prefix' => 'designation'], function () {

        Route::any('/', 'HR\DesignationController@index')->name('gnlHrDesDatatable');
        Route::any('add', 'HR\DesignationController@add');
        Route::any('edit/{id}', 'HR\DesignationController@edit');
        Route::get('view/{id}', 'HR\DesignationController@view');
        Route::any('delete/{id}', 'HR\DesignationController@delete');
        Route::any('publish/{id}', 'HR\DesignationController@isActive');

    });

    // // ## Dynamic Type
    Route::group(['prefix' => 'dynamic_type'], function () {
        Route::any('/', 'DTypeController@index')->name('dTypeDatatable');
        Route::any('add', 'DTypeController@add');
        Route::any('edit/{id}', 'DTypeController@edit');
        // Route::get('view/{id}', 'DTypeController@view');
        Route::any('delete/{id}', 'DTypeController@delete');
        Route::any('publish/{id}', 'DTypeController@isActive');
        Route::any('destroy/{id}', 'DTypeController@destroy');
    });

    // // ## Dynamic Form
    Route::group(['prefix' => 'dynamic_form'], function () {
        Route::any('/', 'DFormController@index')->name('dFormDatatable');
        Route::any('add', 'DFormController@add');
        Route::any('edit/{id}', 'DFormController@edit');
        // Route::get('view/{id}', 'DFormController@view');
        Route::any('delete/{id}', 'DFormController@delete');
        Route::any('publish/{id}', 'DFormController@isActive');
        Route::any('destroy/{id}', 'DFormController@destroy');
    });

    // // ## Dynamic Forms Value
    Route::group(['prefix' => 'dynamic_value'], function () {
        Route::any('/', 'DFormValueController@index')->name('dFormValueDatatable');
        Route::any('add', 'DFormValueController@add');
        Route::any('edit/{id}', 'DFormValueController@edit');
        // Route::get('view/{id}', 'DFormValueController@view');
        Route::any('delete/{id}', 'DFormValueController@delete');
        Route::any('publish/{id}', 'DFormValueController@isActive');
        Route::any('destroy/{id}', 'DFormValueController@destroy');
    });

    /*Route for sms forward*/
    Route::group(['prefix' => 'sms_forward'], function () {
        Route::any('/', 'HR\SmsForwardController@index');
        Route::any('/add/{status}', 'HR\SmsForwardController@add')->name('send_sms');
        Route::any('/edit/{status}', 'HR\SmsForwardController@edit')->name('edit_sms');
        Route::any('/delete/{id}', 'HR\SmsForwardController@delete')->name('delete_sms');

        Route::any('/send_sms/{id}', 'HR\SmsForwardController@send_sms')->name('draft_send_sms');

        Route::any('/get_sms/{id}', 'HR\SmsForwardController@get_sms')->name('get_sms');
        Route::any('/get_samity_by_branch/{id}', 'HR\SmsForwardController@get_samity_by_branch')->name('get_samity_by_branch');
        //Route::any('/view/{id}', 'HR\SmsForwardController@view');
    });

    ##Feedback_Panel

        Route::group(['prefix' => 'feedback_panel'], function () {

            Route::get('/', function () {
                return view('GNL/Feedbackpanel/index');
            });
            Route::post('/', 'FeedbackController@index'); // api
            Route::post('/updateStatus', 'FeedbackController@updateStatus'); // api
            Route::post('/updateAction', 'FeedbackController@updateAction'); // api

            Route::any('/add', function () {
                return view('GNL/Feedbackpanel/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('GNL/Feedbackpanel/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('GNL/Feedbackpanel/view', compact('id'));
            });
            Route::any('/delete/{id}', 'FeedbackController@delete');


            Route::any('/insert/{status}/api', 'FeedbackController@insert');
            Route::any('/update/{status}/api', 'FeedbackController@update');
            Route::any('/get/{id}/api', 'FeedbackController@get');
            Route::any('/send/{id}/api', 'FeedbackController@send');
        });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // // // ## working with Supplier
    // Route::get('/supplier', 'SupplierController@index');
    // Route::post('/supplier/ajaxsupplierIndex', 'SupplierController@ajaxsupplierIndex')->name('supplierDatatable');
    // Route::get('/supplier/new', 'SupplierController@add');
    // Route::post('/supplier/storesupplier', 'SupplierController@add')->name('storesupplier');
    // Route::get('/supplier/edit/{id}', 'SupplierController@edit');
    // Route::post('/supplier/updatesupplier/{id}', 'SupplierController@edit');
    // Route::get('/supplier/view/{id}', 'SupplierController@view');
    // Route::get('/supplier/delete/{id}', 'SupplierController@delete');
    // Route::get('/supplier/publish/{id}', 'SupplierController@isactive');

    // // ## Routes
    Route::get('/routes', 'RouteOperationController@index');
    Route::post('/ajaxRoutesIndex', 'RouteOperationController@ajaxRoutesIndex');
    Route::get('/refreshRoutes', 'RouteOperationController@refreshRoutes');
});
