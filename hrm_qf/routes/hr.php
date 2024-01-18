<?php

use App\Http\Controllers\HR\Process\ApplicationProcessController;
use App\Model\HR\Bonus;
use App\Model\HR\EmployeeLeaveCategory;
use App\Model\HR\Gratuity;
use App\Model\HR\Insurance;
use App\Model\HR\SalaryIncrement;
use App\Model\HR\PayrollDeductionConfigModel;
use App\Model\HR\Loan;
use App\Model\HR\SecurityMoney;
use App\Model\HR\OSF;
use App\Model\HR\PensionScheme;
use App\Model\HR\PensionSchemeSetting;
use App\Model\HR\SalaryStructure;
use App\Model\HR\WelfareFund;
use App\Model\HR\ProvidientFund;
use App\Model\HR\PayrollConfiguration;
use App\Model\HR\PayrollDonationSectorModel;
use App\Model\HR\PayrollPayScaleMigration;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'hr', 'namespace' => 'HR'], function () {


    /* -------------------------------------Dashboard------------------------------ */
    Route::get('/', 'DashboardController@index');
    /* -------------------------------------Dashboard------------------------------ */

    /* -------------------------------------Others--------------------------------- */
    Route::group(['namespace' => 'Others'], function () {

        Route::post('/getDistricts', 'CommonController@getDistricts');
        Route::post('/getUpazilas', 'CommonController@getUpazilas');
        Route::post('/getUnions', 'CommonController@getUnions');
        Route::post('/getVillages', 'CommonController@getVillages');

        Route::post('/getBanks', 'CommonController@getBanks')->name('getBanks');
        Route::post('/getBankBranches', 'CommonController@getBankBranches')->name('getBankBranches');
        Route::post('/getProjectType', 'CommonController@getProjectType')->name('getProjectType');
        // Route::get('/getEmployeesByBranch/{id?}', 'CommonController@get_employee_by_branch')->name('getEmployeesByBranch');
        Route::get('/getEmployeesOptionsByBranch/{id?}', 'CommonController@get_employees_options_by_branch')->name('getEmployeesOptionsByBranch');
        Route::post('/searchEmployeeAndGetOptions', 'CommonController@search_employee_and_get_options')->name('searchEmployeeAndGetOptions');
    });
    /* -------------------------------------Others--------------------------------- */



    /* -------------------------------------Reports Start--------------------------------- */


    Route::group(['namespace' => 'Reports', 'prefix' => 'reports'], function () {


        //Register Reports
        Route::group(['namespace' => 'RegisterReports'], function () {

            ## Employee Resign Report
            Route::group(['prefix' => 'emp_resign'], function () {
                Route::any('/', 'RegisterReportsController@getEmpResign');
                Route::any('/loadData', 'RegisterReportsController@loadEmpResign');
            });

            ## Employee Leave Report
            Route::group(['prefix' => 'emp_leave'], function () {
                Route::any('/', 'RegisterReportsController@getEmpLeave');
                Route::any('/loadData', 'RegisterReportsController@loadEmpLeave');
            });

            ## Employee Movement Report
            Route::group(['prefix' => 'emp_movement'], function () {
                Route::any('/', 'RegisterReportsController@getEmpMovement');
                Route::any('/loadData', 'RegisterReportsController@loadEmpMovement');
            });

            // ## Holoday Report
            // Route::group(['prefix' => 'emp_movement'], function () {
            //     Route::any('/', 'RegisterReportsController@getEmpMovement');
            //     Route::any('/loadData', 'RegisterReportsController@loadEmpMovement');
            // });

            ## Employee Transfer Report
            Route::group(['prefix' => 'emp_transfer'], function () {
                Route::any('/', 'RegisterReportsController@getEmpTransfer');
                Route::any('/loadData', 'RegisterReportsController@loadEmpTransfer');
            });

            ## Employee Terminate Report
            Route::group(['prefix' => 'emp_terminate'], function () {
                Route::any('/', 'RegisterReportsController@getEmpTerminate');
                Route::any('/loadData', 'RegisterReportsController@loadEmpTerminate');
            });

            ## Employee Promotion Report
            Route::group(['prefix' => 'emp_promotion'], function () {
                Route::any('/', 'RegisterReportsController@getEmpPromotion');
                Route::any('/loadData', 'RegisterReportsController@loadEmpPromotion');
            });

            ## Employee Demotion Report
            Route::group(['prefix' => 'emp_demotion'], function () {
                Route::any('/', 'RegisterReportsController@getEmpDemotion');
                Route::any('/loadData', 'RegisterReportsController@loadEmpDemotion');
            });

            ## Employee Dismiss Report
            Route::group(['prefix' => 'emp_dismiss'], function () {
                Route::any('/', 'RegisterReportsController@getEmpDismiss');
                Route::any('/loadData', 'RegisterReportsController@loadEmpDismiss');
            });

            ## Employee Retirement Report
            Route::group(['prefix' => 'emp_retirement'], function () {
                Route::any('/', 'RegisterReportsController@getEmpRetirement');
                Route::any('/loadData', 'RegisterReportsController@loadEmpRetirement');
            });

            ## Employee Increment Report
            Route::group(['prefix' => 'emp_increment'], function () {
                Route::any('/', 'RegisterReportsController@getEmpIncrement');
                Route::any('/loadData', 'RegisterReportsController@loadEmpIncrement');
            });

            ## Employee Increment Held Report
            Route::group(['prefix' => 'emp_increment_held'], function () {
                Route::any('/', 'RegisterReportsController@getEmpIncrementHeld');
                Route::any('/loadData', 'RegisterReportsController@loadEmpIncrementHeld');
            });

            ##Active Responsibility Report
            Route::group(['prefix' => 'act_respons'], function () {
                Route::any('/', 'RegisterReportsController@getActiveResponsibility');
                Route::any('/loadData', 'RegisterReportsController@loadActiveResponsibility');
            });

            ##Active Responsibility Extend Report
            Route::group(['prefix' => 'act_respons_ext'], function () {
                Route::any('/', 'RegisterReportsController@getActiveResponsibilityExtend');
                Route::any('/loadData', 'RegisterReportsController@loadActiveResponsibilityExtend');
            });

            ##Employee Contract Conclude Report
            Route::group(['prefix' => 'emp_cont_conclude'], function () {
                Route::any('/', 'RegisterReportsController@getEmpContractConclude');
                Route::any('/loadData', 'RegisterReportsController@loadEmpContractConclude');
            });

            ## Employee Contract Extend Report
            Route::group(['prefix' => 'emp_cont_ext'], function () {
                Route::any('/', 'RegisterReportsController@getEmpContractExtend');
                Route::any('/loadData', 'RegisterReportsController@loadEmpContractExtend');
            });

        });

        ## Leave report
        Route::group(['namespace' => 'LeaveReports'], function () {

            ## Consume Report
            Route::group(['prefix' => 'consume'], function () {
                Route::any('/', 'LeaveReportController@getConsume');
                Route::any('/consume_report_body', 'LeaveReportController@loadConsume');
            });

            ## Consume Report
            Route::group(['prefix' => 'balance'], function () {
                Route::any('/', 'LeaveReportController@getBalance');
                Route::any('/balance_report_body', 'LeaveReportController@loadBalance');
            });

            ## Consume Report
            Route::group(['prefix' => 'balance_2'], function () {
                Route::any('/', 'LeaveReportController@getBalance2');
                Route::any('/balance_report_body_2', 'LeaveReportController@loadBalance2');
            });

            ## Employee Leave Report
            // EmployeeLeaveReportController
            Route::group(['prefix' => 'employee_leave_report'], function () {
                Route::any('/', 'EmployeeLeaveReportController@index');
                Route::any('/body', 'EmployeeLeaveReportController@reportBody');
            });


        });

        ## Stuff Reports
        Route::group(['namespace' => 'StuffReports'], function () {

            ## Employee Report
            Route::group(['prefix' => 'employee_report'], function () {
                Route::any('/', 'StuffReportController@getEmployeeReport');
                Route::any('/loadData', 'StuffReportController@loadEmployeeReport');
            });

            ## Stuff Report
            Route::group(['prefix' => 'stf_report'], function () {
                Route::any('/', 'StuffReportController@getStuffReport');
                Route::any('/loadData', 'StuffReportController@loadStuffReport');
            });

            ## New Appoinment Report
            Route::group(['prefix' => 'new_app_report'], function () {
                Route::any('/', 'StuffReportController@getNewAppointedReport');
                Route::any('/loadData', 'StuffReportController@loadNewAppointedReport');
            });

            ## New Consolited Report
            Route::group(['prefix' => 'consolited_report'], function () {
                Route::any('/', 'StuffReportController@getConsolitedReport');
                Route::any('/loadData', 'StuffReportController@loadConsolitedReport');
            });


        });

        // Attendance Reports
        Route::group(['namespace' => 'AttendanceReports'], function () {

            ## Status Report
            Route::group(['prefix' => 'attendance_status'], function () {
                Route::any('/', 'AttendanceStatusReportController@getStatus');
                Route::any('/body', 'AttendanceStatusReportController@loadstatus');
            });

            ## In/Out Report
            Route::group(['prefix' => 'attendance_in_out'], function () {
                Route::any('/', 'AttendanceReportsController@getInOut');
                Route::any('/body', 'AttendanceReportsController@loadInOut');
            });

            ## Employee/Emp Wise Attendance Sheet
            Route::group(['prefix' => 'emp_attendance_sheet'], function () {
                Route::any('/', 'EmpWiseAttendanceReportsController@index');
                Route::any('/body', 'EmpWiseAttendanceReportsController@reportBody');
            });

            ## Daily Attendance Sheet/Report
            // php artisan make:controller HR/Reports/AttendanceReports/DailyAttendanceReportsController
            Route::group(['prefix' => 'daily_attendance_report'], function () {
                Route::any('/', 'DailyAttendanceReportsController@getStatus');
                Route::any('/body', 'DailyAttendanceReportsController@loadstatus');
            });

        });

        ## Holoday Report
        Route::group(['namespace' => 'HolidayReports'], function () {

            Route::group(['prefix' => 'holiday_report'], function () {
                Route::any('/', 'HolidaysReportController@getHoliday');
                Route::any('/body', 'HolidaysReportController@loadHolidays');
            });
        });


        ## Payroll Report
        ## php artisan make:controller HR/Reports/PayrollReports/SalaryReportController;
        Route::group(['namespace' => 'PayrollReports'], function () {

            Route::group(['prefix' => 'salary_report'], function () {
                Route::any('/', 'SalaryReportController@index');
                Route::any('/body', 'SalaryReportController@loadSalary');
            });
        });

    });



    /* -------------------------------------Reports End--------------------------------- */



    /* -------------------------------------Holiday-------------------------------- */
    Route::group(['namespace' => 'Holiday'], function () {

        ## Govt Holiday Route
        Route::group(['prefix' => 'govtholiday'], function () {

            Route::get('/', function () {
                return view('HR.Holiday.GovtHoliday.index');
            });
            Route::post('/', 'GovtHolidayController@index'); // Api

            Route::get('/add', function () {
                return view('HR/Holiday/GovtHoliday/add');
            });

            Route::get('/edit/{id}', function ($id) {
                return view('HR/Holiday/GovtHoliday/edit', compact('id'));
            });

            Route::get('/view/{id}', function ($id) {
                return view('HR/Holiday/GovtHoliday/view', compact('id'));
            });

            Route::post('/insert/api', 'GovtHolidayController@insert'); // Api
            Route::any('/get/{id}/api', 'GovtHolidayController@get');
            Route::any('/update/api', 'GovtHolidayController@update');
            Route::any('/delete/{id}', 'GovtHolidayController@delete');
        });


        ## Company Holiday Route
        Route::group(['prefix' => 'compholiday'], function () {

            Route::get('/', function () {
                return view('HR.Holiday.CompanyHoliday.index');
            });
            Route::post('/', 'CompHolidayController@index'); // Api


            Route::get('/add', function () {
                return view('HR/Holiday/CompanyHoliday/add');
            });


            Route::get('/view/{id}', function ($id) {
                return view('HR/Holiday/CompanyHoliday/view', compact('id'));
            });

            Route::get('/edit/{id}', function ($id) {
                return view('HR/Holiday/CompanyHoliday/edit', compact('id'));
            });

            Route::post('/insert/api', 'CompHolidayController@insert'); // Api
            Route::any('/get/{id}/api', 'CompHolidayController@get');
            Route::post('/update/api', 'CompHolidayController@update');
            Route::any('getData', 'CompHolidayController@getData');
            Route::any('/delete/{id}', 'CompHolidayController@delete');
            Route::get('CheckDayEnd', 'CompHolidayController@CheckDayEnd');

        });

        ## Special Holiday Route
        Route::group(['prefix' => 'specialholiday'], function () {

            Route::get('/', function () {
                return view('HR.Holiday.SpecialHoliday.index');
            });
            Route::post('/', 'SpecialHolidayController@index'); // Api

            Route::get('/add', function () {
                return view('HR/Holiday/SpecialHoliday/add');
            });

            Route::get('/edit/{id}', function ($id) {
                return view('HR/Holiday/SpecialHoliday/edit', compact('id'));
            });

            Route::get('/view/{id}', function ($id) {
                return view('HR/Holiday/SpecialHoliday/view', compact('id'));
            });

            Route::post('/insert/api', 'SpecialHolidayController@insert'); // Api
            Route::post('/update/api', 'SpecialHolidayController@update');
            Route::any('/delete/{id}', 'SpecialHolidayController@delete');
            Route::any('/get/{id}/api', 'SpecialHolidayController@get');
            Route::any('getData', 'SpecialHolidayController@getData'); // API
            Route::get('CheckDayEnd', 'SpecialHolidayController@CheckDayEnd');

        });


        ## Schedule Office Time Route
        Route::group(['prefix' => 'rescheduleholiday'], function () {

            Route::get('/', function () {
                return view('HR.Holiday.RescheduleHoliday.index');
            });
            Route::post('/', 'RescheduleHolidayController@index'); // Api

            Route::get('/add', function () {
                return view('HR/Holiday/RescheduleHoliday/add');
            });

            Route::get('/edit/{id}', function ($id) {
                return view('HR/Holiday/RescheduleHoliday/edit', compact('id'));
            });

            Route::get('/view/{id}', function ($id) {
                return view('HR/Holiday/RescheduleHoliday/view', compact('id'));
            });

            Route::post('/insert/api', 'RescheduleHolidayController@insert'); // Api
            Route::post('/update/api', 'RescheduleHolidayController@update');
            Route::any('/delete/{id}', 'RescheduleHolidayController@delete');
            Route::any('/get/{id}/api', 'RescheduleHolidayController@get');
            Route::any('getData', 'RescheduleHolidayController@getData'); // API
            Route::get('CheckDayEnd', 'RescheduleHolidayController@CheckDayEnd');

        });

        ## Holiday Calender Route
        Route::group(['prefix' => 'holidaycalendar'], function () {

            Route::any('/', 'CalendarController@index')->name('CalendarData');
        });
    });
    /* -------------------------------------Holiday-------------------------------- */



    /* -------------------------------------Employee------------------------------- */
    Route::group(['namespace' => 'Employee'], function () {

        ## Employee Route
        Route::group(['prefix' => 'employee'], function () {
            Route::any('/', 'EmployeeController@index')->name('employeeDatatableHR');
            Route::any('add', 'EmployeeController@add');
            Route::any('edit/{id}', 'EmployeeController@edit');
            Route::get('view/{id}', 'EmployeeController@view');
            Route::get('delete', 'EmployeeController@delete');
            Route::any('publish/{id}', 'EmployeeController@isActive');
            Route::any('destroy/{id}', 'EmployeeController@destroy');
            Route::post('getEmployeeCode/', 'EmployeeController@generateEmployeeCode');
            Route::post('approveEmployee/', 'EmployeeController@approveEmployee');
            Route::post('getPayScale/', 'EmployeeController@getPayScale');
            Route::any('getSalaryInformation/', 'EmployeeController@getSalaryInformation');
            Route::any('getStepData/', 'EmployeeController@getStepData');

            Route::any('profile_update/', 'EmployeeController@profileUpdate');
            Route::any('profile_view/', 'EmployeeController@profileView');

            Route::any('add/draft', 'EmployeeController@add_draft');
            Route::any('edit/{id}/draft', 'EmployeeController@edit_draft');

            Route::any('getBranchData/', 'EmployeeController@getBranchData');
            Route::any('getDivisionData/', 'EmployeeController@getDivisionData');
            Route::any('getDistrictData/', 'EmployeeController@getDistrictData');
            Route::any('getUpazilaData/', 'EmployeeController@getUpazilaData');
            Route::any('getUnionData/', 'EmployeeController@getUnionData');
            Route::any('getVillageData/', 'EmployeeController@getVillageData');

            // Route::get('searchDistrict', 'EmployeeController@selectDistrictData');
            // Route::get('searchUpazila', 'EmployeeController@selectUpazilaData');
            Route::get('getData', 'EmployeeController@getData');

        });
    });
    /* -------------------------------------Employee-------------------------------- */



    /* -------------------------------------Applications---------------------------- */
    Route::group(['namespace' => 'Applications'], function () {

        ## Employee resign
        Route::group(['prefix' => 'employee_resign'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeResign/index');
            });
            Route::post('/', 'EmployeeResignController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/EmployeeResign/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeResign/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeResign/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeResignController@delete');


            Route::any('/insert/{status}/api', 'EmployeeResignController@insert');
            Route::any('/update/{status}/api', 'EmployeeResignController@update');
            Route::any('/get/{id}/api', 'EmployeeResignController@get');
            Route::any('/send/{id}/api', 'EmployeeResignController@send');
        });

        ## Employee promotion
        Route::group(['prefix' => 'employee_promotion'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeePromotion/index');
            });
            Route::post('/', 'EmployeePromotionController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/EmployeePromotion/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeePromotion/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeePromotion/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeePromotionController@delete');


            Route::any('/insert/{status}/api', 'EmployeePromotionController@insert');
            Route::any('/update/{status}/api', 'EmployeePromotionController@update');
            Route::any('/get/{id}/api', 'EmployeePromotionController@get');
            Route::any('/send/{id}/api', 'EmployeePromotionController@send');

            Route::any('/getData/{id}/api', 'EmployeePromotionController@getData');
        });

        ## Employee demotion
        Route::group(['prefix' => 'employee_demotion'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeDemotion/index');
            });
            Route::post('/', 'EmployeeDemotionController@index'); // api

            Route::any('/add', function () {
                return view('HR/Applications/EmployeeDemotion/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeDemotion/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeDemotion/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeDemotionController@delete');


            Route::any('/insert/{status}/api', 'EmployeeDemotionController@insert');
            Route::any('/update/{status}/api', 'EmployeeDemotionController@update');
            Route::any('/get/{id}/api', 'EmployeeDemotionController@get');
            Route::any('/send/{id}/api', 'EmployeeDemotionController@send');
        });

        ## Employee terminate
        Route::group(['prefix' => 'employee_terminate'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeTerminate/index');
            });
            Route::post('/', 'EmployeeTerminateController@index'); // api

            Route::any('/add', function () {
                return view('HR/Applications/EmployeeTerminate/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeTerminate/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeTerminate/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeTerminateController@delete');


            Route::any('/insert/{status}/api', 'EmployeeTerminateController@insert');
            Route::any('/update/{status}/api', 'EmployeeTerminateController@update');
            Route::any('/get/{id}/api', 'EmployeeTerminateController@get');
            Route::any('/send/{id}/api', 'EmployeeTerminateController@send');
        });

        ## Employee dismiss
        Route::group(['prefix' => 'employee_dismiss'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeDismiss/index');
            });
            Route::post('/', 'EmployeeDismissController@index'); // api

            Route::any('/add', function () {
                return view('HR/Applications/EmployeeDismiss/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeDismiss/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeDismiss/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeDismissController@delete');


            Route::any('/insert/{status}/api', 'EmployeeDismissController@insert');
            Route::any('/update/{status}/api', 'EmployeeDismissController@update');
            Route::any('/get/{id}/api', 'EmployeeDismissController@get');
            Route::any('/send/{id}/api', 'EmployeeDismissController@send');
        });

        ## Employee transfer
        Route::group(['prefix' => 'employee_transfer'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeTransfer/index');
            });
            Route::post('/', 'EmployeeTransferController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/EmployeeTransfer/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeTransfer/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeTransfer/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeTransferController@delete');


            Route::any('/insert/{status}/api', 'EmployeeTransferController@insert');
            Route::any('/update/{status}/api', 'EmployeeTransferController@update');
            Route::any('/get/{id}/api', 'EmployeeTransferController@get');
            Route::any('/send/{id}/api', 'EmployeeTransferController@send');
        });

        ## Employee retirement
        Route::group(['prefix' => 'employee_retirement'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeRetirement/index');
            });
            Route::post('/', 'EmployeeRetirementController@index'); // api

            // Route::any('/add', function () {
            //     return view('HR/Applications/EmployeeRetirement/add');
            // });

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/EmployeeRetirement/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeRetirement/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeRetirement/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeRetirementController@delete');


            Route::any('/insert/{status}/api', 'EmployeeRetirementController@insert');
            Route::any('/update/{status}/api', 'EmployeeRetirementController@update');
            Route::any('/get/{id}/api', 'EmployeeRetirementController@get');
            Route::any('/send/{id}/api', 'EmployeeRetirementController@send');
        });

        ## Employee contract conclude
        Route::group(['prefix' => 'contract_conclude'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeContractConclude/index');
            });
            Route::post('/', 'EmployeeContractConcludeController@index'); // api

            Route::any('/add', function () {
                return view('HR/Applications/EmployeeContractConclude/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeContractConclude/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeContractConclude/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeContractConcludeController@delete');


            Route::any('/insert/{status}/api', 'EmployeeContractConcludeController@insert');
            Route::any('/update/{status}/api', 'EmployeeContractConcludeController@update');
            Route::any('/get/{id}/api', 'EmployeeContractConcludeController@get');
            Route::any('/send/{id}/api', 'EmployeeContractConcludeController@send');
        });

        ## Employee active responsibility
        Route::group(['prefix' => 'active_responsibility'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeActiveResponsibility/index');
            });
            Route::post('/', 'EmployeeActiveResponsibilityController@index'); // api

            Route::any('/add', function () {
                return view('HR/Applications/EmployeeActiveResponsibility/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeActiveResponsibility/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeActiveResponsibility/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeActiveResponsibilityController@delete');


            Route::any('/insert/{status}/api', 'EmployeeActiveResponsibilityController@insert');
            Route::any('/update/{status}/api', 'EmployeeActiveResponsibilityController@update');
            Route::any('/get/{id}/api', 'EmployeeActiveResponsibilityController@get');
            Route::any('/send/{id}/api', 'EmployeeActiveResponsibilityController@send');
        });

        ## Employee leave
        Route::group(['prefix' => 'employee_leave'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeLeave/index');
            });
            Route::post('/', 'EmployeeLeaveController@index'); // api

            // Route::any('/add', function () {
            //     return view('HR/Applications/EmployeeLeave/add');
            // });

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/EmployeeLeave/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeLeave/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeLeave/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeLeaveController@delete');


            Route::any('/insert/{status}/api', 'EmployeeLeaveController@insert');
            Route::any('/update/{status}/api', 'EmployeeLeaveController@update');
            Route::any('/get/{id}/api', 'EmployeeLeaveController@get');
            Route::any('/send/{id}/api', 'EmployeeLeaveController@send');
            Route::any('/getLeaveInfo/{id}/{app_date}/api', 'EmployeeLeaveController@getLeaveInfo');
            Route::any('/checkLeaveElizable/{id}/{app_date}/api', 'EmployeeLeaveController@checkLeaveElizable');

            Route::any('/getEmpLeave/{create}/{approved}/api', 'EmployeeLeaveController@getEmpInfoFromSysEmpID');
        });


        ## Employee Adjustment leave
        Route::group(['prefix' => 'employee_leave_adjustment'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeLeaveAdjustment/index');
            });
            Route::post('/', 'EmployeeLeaveAdjustmentController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/EmployeeLeaveAdjustment/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeLeaveAdjustment/edit', compact('id'));
            });
            // Route::any('/view/{id}', function ($id) {
            //     return view('HR/Applications/EmployeeLeaveAdjustment/view', compact('id'));
            // });
            Route::any('/delete/{id}', 'EmployeeLeaveAdjustmentController@delete');


            Route::any('/insert/{status}/api', 'EmployeeLeaveAdjustmentController@insert');
            Route::any('/update/{status}/api', 'EmployeeLeaveAdjustmentController@update');
            Route::any('/get/{id}/api', 'EmployeeLeaveAdjustmentController@get');
            // Route::any('/send/{id}/api', 'EmployeeLeaveAdjustmentController@send');
            Route::any('/getLeaveInfo/{id}/{app_date}/api', 'EmployeeLeaveAdjustmentController@getLeaveInfo');
            // Route::any('/checkLeaveElizable/{id}/{app_date}/api', 'EmployeeLeaveAdjustmentController@checkLeaveElizable');
        });


        ## Employee movement
        Route::group(['prefix' => 'employee_movement'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeMovement/index');
            });
            Route::post('/', 'EmployeeMovementController@index'); // api

            // Route::any('/add', function () {
            //     return view('HR/Applications/EmployeeMovement/add');
            // });

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/EmployeeMovement/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeMovement/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeMovement/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeMovementController@delete');


            Route::any('/insert/{status}/api', 'EmployeeMovementController@insert');
            Route::any('/update/{status}/api', 'EmployeeMovementController@update');
            Route::any('/get/{id}/api', 'EmployeeMovementController@get');
            Route::any('/send/{id}/api', 'EmployeeMovementController@send');

            Route::any('/getEmp/{create}/{approved}/api', 'EmployeeMovementController@getEmpInfoFromSysEmpID');


        });


        ## Employee Increment
        // EmployeeIncrementController
        // php artisan make:model Model/HR/EmployeeIncrement

        Route::group(['prefix' => 'employee_increment'], function () {

            Route::get('/', function () {
                return view('HR/Applications/EmployeeIncrement/index');
            });
            Route::post('/', 'EmployeeIncrementController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/EmployeeIncrement/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/EmployeeIncrement/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/EmployeeIncrement/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeIncrementController@delete');


            Route::any('/insert/{status}/api', 'EmployeeIncrementController@insert');
            Route::any('/update/{status}/api', 'EmployeeIncrementController@update');
            Route::any('/get/{id}/api', 'EmployeeIncrementController@get');
            Route::any('/send/{id}/api', 'EmployeeIncrementController@send');
        });

        ## Advance Salary
        // php artisan make:controller HR/Application/AdvanceSalaryController
        // php artisan make:model Model/HR/AppAdvanceSalary
        Route::group(['prefix' => 'advance_salary'], function () {

            Route::get('/', function () {
                return view('HR/Applications/AdvanceSalary/index');
            });
            Route::post('/', 'AdvanceSalaryController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/AdvanceSalary/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/AdvanceSalary/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/AdvanceSalary/view', compact('id'));
            });
            Route::any('/delete/{id}', 'AdvanceSalaryController@delete');


            Route::any('/insert/{status}/api', 'AdvanceSalaryController@insert');
            Route::any('/update/{status}/api', 'AdvanceSalaryController@update');
            Route::any('/get/{id}/api', 'AdvanceSalaryController@get');
            Route::any('/send/{id}/api', 'AdvanceSalaryController@send');
        });

        ## Security Money
        // php artisan make:controller HR/Application/SecurityMoneyController
        // php artisan make:model Model/HR/AppSecurityMoney
        Route::group(['prefix' => 'security_money'], function () {

            Route::get('/', function () {
                return view('HR/Applications/SecurityMoney/index');
            });
            Route::post('/', 'SecurityMoneyController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/SecurityMoney/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/SecurityMoney/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/SecurityMoney/view', compact('id'));
            });
            Route::any('/delete/{id}', 'SecurityMoneyController@delete');


            Route::any('/insert/api', 'SecurityMoneyController@insert');
            Route::any('/update/{status}/api', 'SecurityMoneyController@update');
            Route::any('/get/{id}/api', 'SecurityMoneyController@get');
            Route::any('/send/{id}/api', 'SecurityMoneyController@send');
        });

        ## PF Loan
        // php artisan make:controller HR/Application/PfLoanController
        // php artisan make:model Model/HR/HrApplicationLoan
        Route::group(['prefix' => 'pf_loan'], function () {

            Route::get('/', function () {
                return view('HR/Applications/PfLoan/index');
            });
            Route::post('/', 'PfLoanController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/PfLoan/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/PfLoan/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/PfLoan/view', compact('id'));
            });
            Route::any('/delete/{id}', 'PfLoanController@delete');


            Route::any('/insert/{status}/api', 'PfLoanController@insert');
            Route::any('/update/{status}/api', 'PfLoanController@update');
            Route::any('/get/{id}/api', 'PfLoanController@get');
            Route::any('/send/{id}/api', 'PfLoanController@send');
        });


        ## Vehicle Loan
        // php artisan make:controller HR/Application/VehicleLoanController
        // php artisan make:model Model/HR/HrApplicationLoan
        Route::group(['prefix' => 'vehicle_loan'], function () {

            Route::get('/', function () {
                return view('HR/Applications/VehicleLoan/index');
            });
            Route::post('/', 'VehicleLoanController@index'); // api

            Route::any('/add/{appFor?}', function ($appFor = null) {
                return view('HR/Applications/VehicleLoan/add', compact('appFor'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Applications/VehicleLoan/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Applications/VehicleLoan/view', compact('id'));
            });
            Route::any('/delete/{id}', 'VehicleLoanController@delete');


            Route::any('/insert/{status}/api', 'VehicleLoanController@insert');
            Route::any('/update/{status}/api', 'VehicleLoanController@update');
            Route::any('/get/{id}/api', 'VehicleLoanController@get');
            Route::any('/send/{id}/api', 'VehicleLoanController@send');
        });

        // php artisan make:controller HR/Applications/AdvanceSalaryController
        // php artisan make:model Model/HR/AppAdvanceSalary
        // php artisan make:controller HR/Applications/SecurityMoneyController
        // php artisan make:model Model/HR/AppSecurityMoney
        // php artisan make:controller HR/Applications/PfLoanController
        // php artisan make:model Model/HR/HrApplicationLoan
        // php artisan make:controller HR/Applications/VehicleLoanController

    });
    /* -------------------------------------Applications---------------------------- */



    /* -------------------------------------Process--------------------------------- */
    Route::group(['namespace' => 'Process'], function () {

        ## Application process
        Route::group(['prefix' => 'application_process'], function () {
            Route::any('/{applStatus}', 'ApplicationProcessController@index');
            Route::any('/view/{applStatus}/{applCat}/{applId}', function ($applStatus, $applCat, $applId) {
                $model = '\\App\\Model\\HR\\' . $applCat;
                $con = new ApplicationProcessController();
                $applType = $con->get_application_type(new $model);
                if ($applStatus == 3) {
                    $curr_stage = explode('-', $model::find($applId)->current_stage);
                    $dmp = $curr_stage[3];
                    return view('HR.Process.ApplicationProcess.pendingApplicationView', compact('applStatus', 'applId', 'applType', 'dmp'));
                } else {
                    return view('HR.Process.ApplicationProcess.approvedOrRejctedApplicationView', compact('applId', 'applType'));
                }
            });
            Route::any('/get_appl_with_notes/{applId}/{applCat}/api', 'ApplicationProcessController@get_application_details_with_notes');
            Route::any('/proceed/{applCat}/{status}/{dmp}/api', 'ApplicationProcessController@proceed_application');


            Route::any('/getProcess/{create}/{approved}/api', 'ApplicationProcessController@getEmpInfoFromSysEmpID');
        });

        ## Appointment Letter
        Route::group(['prefix' => 'appointment_letter'], function () {
            Route::any('/', 'AppointmentLetterController@format');
            Route::any('/list', 'AppointmentLetterController@index');
            Route::any('/list/view/{id}', 'AppointmentLetterController@view');
            Route::any('/employee_letter_view/{id}', 'AppointmentLetterController@employeeAppointmentLetter');
            Route::any('/employee_letter_save', 'AppointmentLetterController@saveLetter')->name('appointmentLetters');
        });
    });
    /* -------------------------------------Process--------------------------------- */



    /* -------------------------------------Configuration--------------------------- */
    Route::group(['namespace' => 'Configuration'], function () {

        ## Fiscal Year Route
        Route::group(['prefix' => 'fiscal_year'], function () {
            Route::get('/', function () {
                return view('HR/Configuration/FiscalYear/index');
            });

            Route::any('/add', function () {
                return view('HR/Configuration/FiscalYear/add');
            });

            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/FiscalYear/view', compact('id'));
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/FiscalYear/edit', compact('id'));
            });

            Route::post('/', 'FiscalYearController@index'); // api
            Route::any('/insert/api', 'FiscalYearController@insert');
            Route::any('/get/{id}/api', 'FiscalYearController@view');
            Route::any('/update/api', 'FiscalYearController@update');
            Route::any('/delete/{id}', 'FiscalYearController@delete');
        });

        ## Approval Configuration
        Route::group(['prefix' => 'approval_config'], function () {
            Route::any('/', 'ApprovalConfigController@get_modules');
            Route::any('m/{moduleId}', 'ApprovalConfigController@get_events');
            Route::any('m/{moduleId}/{eventId}', 'ApprovalConfigController@index');
            Route::any('/add/{eventId}', function ($eventId) {
                return view('HR/Configuration/ApprovalConfig/add', compact('eventId'));
            });
            Route::any('/edit/{id}', function ($con) {
                return view('HR/Configuration/ApprovalConfig/edit', compact('con'));
            });
            Route::any('/view/{id}', function ($con) {
                return view('HR/Configuration/ApprovalConfig/view', compact('con'));
            });
            Route::any('/delete/{con}', 'ApprovalConfigController@delete');
            Route::any('/insert/api', 'ApprovalConfigController@insert');
            Route::any('/update/api', 'ApprovalConfigController@update');
            Route::any('/get/{id}/{arr}/api', 'ApprovalConfigController@get');
        });

        ## Designation Hierarchy
        Route::group(['prefix' => 'designationHierarchy'], function () {
            Route::any('/', 'DesignationHierarchyController@index');
            Route::any('/add', 'DesignationHierarchyController@add');
        });

        ## Employee Leave Category Route
        Route::group(['prefix' => 'leave_category'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/EmployeeLeaveCategory/index');
            });
            Route::post('/', 'EmployeeLeaveCategoryController@index'); // api

            Route::any('/add', function () {
                $rec_type = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])->get();
                $leave_type = DB::table('gnl_dynamic_form_value')->where([['type_id', 3],['form_id', 1]])->get();
                return view('HR/Configuration/EmployeeLeaveCategory/add', compact('rec_type', 'leave_type'));
            });

            Route::any('/edit/{id}', function ($id) {
                $rec_type = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])->get();
                $leave_type = DB::table('gnl_dynamic_form_value')->where([['type_id', 3],['form_id', 1]])->get();
                $edit_data = EmployeeLeaveCategory::where('id', decrypt($id))->with('leave_details')->first();
                return view('HR/Configuration/EmployeeLeaveCategory/edit', compact('id', 'rec_type', 'leave_type', 'edit_data'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/EmployeeLeaveCategory/view', compact('id'));
            });
            Route::any('/delete/{id}', 'EmployeeLeaveCategoryController@delete');
            Route::any('/publish/{id}', 'EmployeeLeaveCategoryController@change_status');
            Route::any('/unpublish/{id}', 'EmployeeLeaveCategoryController@change_status');


            Route::any('/insert/api', 'EmployeeLeaveCategoryController@insert');
            Route::any('/update/api', 'EmployeeLeaveCategoryController@update');
            Route::any('/get/{id}/api', 'EmployeeLeaveCategoryController@get');
        });

        ## Department Route
        Route::group(['prefix' => 'department'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/Department/index');
            });

            Route::any('/add', function () {
                return view('HR/Configuration/Department/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/Department/edit', compact('id'));
            });

            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/Department/view', compact('id'));
            });

            Route::post('/', 'DepartmentController@index'); // Api
            Route::any('/insert/api', 'DepartmentController@insert');
            Route::any('/update/api', 'DepartmentController@update');
            Route::any('/delete/{id}', 'DepartmentController@delete');
            Route::any('/get/{id}/api', 'DepartmentController@get');


        });

        ## Room Route
        Route::group(['prefix' => 'room'], function () {

            Route::any('/', 'RoomController@index')->name('hrRoomDatatable');
            Route::any('add', 'RoomController@add');
            Route::any('edit/{id}', 'RoomController@edit');
            Route::get('view/{id}', 'RoomController@view');
            Route::any('delete/{id}', 'RoomController@delete');
            Route::any('publish/{id}', 'RoomController@isActive');
        });

        ## Designation Route
        Route::group(['prefix' => 'designation'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/Designation/index');
            });

            Route::get('/add', function () {
                return view('HR/Configuration/Designation/add');
            });

            Route::get('/edit/{id}', function ($id) {
                return view('HR/Configuration/Designation/edit', compact('id'));
            });

            Route::get('/view/{id}', function ($id) {
                return view('HR/Configuration/Designation/view', compact('id'));
            });

            Route::post('/', 'DesignationController@index'); // Api
            Route::any('/get/{id}/api', 'DesignationController@get');
            Route::post('/insert/api', 'DesignationController@insert'); // Api
            Route::any('/update/api', 'DesignationController@update');
            Route::any('/delete/{id}', 'DesignationController@delete');

        });

        ## Banksn Route
        Route::group(['prefix' => 'banks'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/Banks/index');
            });
            Route::post('/', 'BankController@index'); // Api

            Route::any('/add', function () {
                return view('HR/Configuration/Banks/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/Banks/edit', compact('id'));
            });

            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/Banks/view', compact('id'));
            });

            Route::post('/insert/api', 'BankController@insert');
            Route::any('/delete/{id}', 'BankController@delete');
            Route::any('/update/api', 'BankController@update');
            Route::any('/get/{id}/api', 'BankController@get');


        });

        ## Branches Route
        Route::group(['prefix' => 'branches'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/Branchs/index');
            });
            Route::post('/', 'BanksBranchController@index'); // Api

            Route::any('/add', function () {
                return view('HR/Configuration/Branchs/add');
            });
            Route::post('/add', 'BanksBranchController@bankData'); // Api

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/Branchs/edit',compact('id'));
            });


            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/Branchs/view', compact('id'));
            });

            Route::any('/get/{id}/api', 'BanksBranchController@get');
            Route::post('/insert/api', 'BanksBranchController@insert');
            Route::any('/delete/{id}', 'BanksBranchController@delete');
            Route::any('/update/api', 'BanksBranchController@update');


        });

        ## Recruitment Type Route
        Route::group(['prefix' => 'recruitment_type'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/RecruitmentType/index');
            });
            Route::post('/', 'RecruitmentTypeController@index'); // api

            Route::any('/add', function () {
                return view('HR/Configuration/RecruitmentType/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/RecruitmentType/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($con) {
                return view('HR/Configuration/RecruitmentType/view', compact('con'));
            });
            Route::any('/delete/{id}', 'RecruitmentTypeController@delete');
            Route::any('/publish/{id}', 'RecruitmentTypeController@change_status');
            Route::any('/unpublish/{id}', 'RecruitmentTypeController@change_status');

            Route::any('getData', 'RecruitmentTypeController@getData'); // API
            Route::any('/insert/api', 'RecruitmentTypeController@insert');
            Route::any('/update/api', 'RecruitmentTypeController@update');
            Route::any('/get/{id}/api', 'RecruitmentTypeController@get');
        });

        ## Garde & Levels Route
        Route::group(['prefix' => 'grade_levels'], function () {
            Route::any('/', 'GradeLevelsController@index');
            Route::any('/duplicate_check', 'GradeLevelsController@duplicateCheck')->name('duplicateCheck');
        });

        ## Employee Required Field Route
        Route::any('/empAdmissionReqField', 'EmployeeAdmissionRequiredCheckController@index');

        ## General Config Route
        Route::group(['prefix' => 'general_configuration'], function(){
            Route::any('/', 'GeneralConfigController@index');
            Route::any('/emp_code', 'GeneralConfigController@emp_code')->name('getEmployeeCode');
        });

        ## Organ Ogram Route
        Route::any('/organogram', 'OrganogramController@index')->name('organogram');

        ##Application Reasons
        Route::group(['prefix' => 'appl_reasons'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/ApplicationReasons/index');
            });
            Route::post('/', 'ApplicationReasonsController@index'); // api

            Route::any('/add', function () {
                return view('HR/Configuration/ApplicationReasons/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/ApplicationReasons/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/ApplicationReasons/view', compact('id'));
            });
            Route::any('/delete/{id}', 'ApplicationReasonsController@delete');

            Route::any('getData', 'ApplicationReasonsController@getData'); // API
            Route::any('/insert/api', 'ApplicationReasonsController@insert');
            Route::any('/update/api', 'ApplicationReasonsController@update');
            Route::any('/get/{id}/api', 'ApplicationReasonsController@get');
            Route::any('/send/{id}/api', 'ApplicationReasonsController@send');
        });

        ##Pay Scale
        Route::group(['prefix' => 'pay_scale'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/PayScale/index');
            });
            Route::post('/', 'PayScaleController@index'); // api

            Route::any('/add', function () {
                return view('HR/Configuration/PayScale/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/PayScale/edit', compact('id'));
            });

            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/PayScale/view', compact('id'));
            });

            Route::any('/delete/{id}', 'PayScaleController@delete');
            Route::any('/insert/api', 'PayScaleController@insert');
            Route::any('/update/api', 'PayScaleController@update');
            Route::any('/get/{id}/api', 'PayScaleController@get');
            Route::any('/send/{id}/api', 'PayScaleController@send');
        });

        ##Attendance Rules
        Route::group(['prefix' => 'attendance_rules'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/AttendanceRules/index');
            });

            Route::any('/add', function () {
                return view('HR/Configuration/AttendanceRules/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/AttendanceRules/edit', compact('id'));
            });

            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/AttendanceRules/view', compact('id'));
            });

            Route::post('/', 'AttendanceRulesController@index'); // api
            Route::any('/insert/{status}/api', 'AttendanceRulesController@insert');
            Route::any('/get/{id}/api', 'AttendanceRulesController@view');
            Route::any('/update/{status}/api', 'AttendanceRulesController@update');
            Route::any('/delete/{id}', 'AttendanceRulesController@delete');
        });


        ##Attendance Late Rules
        Route::group(['prefix' => 'attendance_late_rules'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/AttendanceLateRules/index');
            });
            Route::post('/', 'AttendanceLateRulesController@index'); // api

            Route::any('/add', function () {
                return view('HR/Configuration/AttendanceLateRules/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/AttendanceLateRules/edit', compact('id'));
            });

            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/AttendanceLateRules/view', compact('id'));
            });


            Route::any('/insert/{status}/api', 'AttendanceLateRulesController@insert');
            Route::any('/get/{id}/api', 'AttendanceLateRulesController@view');
            Route::any('/update/{status}/api', 'AttendanceLateRulesController@update');
            Route::any('/delete/{id}', 'AttendanceLateRulesController@delete');
        });

        ## Relation
        # php artisan make:controller HR/Configuration/ConfigRelationController
        # php artisan make:model Model/HR/ConfigRelation
        Route::group(['prefix' => 'relation'], function () {

            Route::get('/', function () {
                return view('HR/Configuration/ConfigRelation/index');
            });
            Route::post('/', 'ConfigRelationController@index'); // api

            Route::any('/add', function () {
                return view('HR/Configuration/ConfigRelation/add');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Configuration/ConfigRelation/edit', compact('id'));
            });

            Route::any('/view/{id}', function ($id) {
                return view('HR/Configuration/ConfigRelation/view', compact('id'));
            });


            Route::any('/insert/api', 'ConfigRelationController@insert');
            Route::any('/get/{id}/api', 'ConfigRelationController@get');
            Route::any('/update/api', 'ConfigRelationController@update');
            Route::any('/delete/{id}', 'ConfigRelationController@delete');
        });

        ## Designation and Role Mapping
        Route::group(['prefix' => 'designation_and_role_mapping'], function () {

            Route::get('/', function () {

                return view('HR/Configuration/DesignationRoleMapping/index');
            });
            Route::any('/update/api', 'DesignationRoleMappingController@update');
        });
    });
    /* -------------------------------------Configuration--------------------------- */



    /* -------------------------------------Api------------------------------------- */
    Route::group(['namespace' => 'Api'], function () {

        ## Common api route
        Route::group(['prefix' => 'common_api'], function () {
            Route::any('get_des_by_emp_id/{empId}/api', 'CommonApiController@get_des_by_emp_id')->name('get_des_by_emp_id');
        });
    });
    /* -------------------------------------Api------------------------------------- */



    /* -------------------------------------Payroll---------------------------------- */
    Route::group(['namespace' => 'Payroll', 'prefix' => 'payroll'], function () {

        ## Payroll Configuration
        # php artisan make:controller HR/Payroll/PayrollConfigurationController
        # php artisan make:model Model/HR/PayrollConfiguration
        Route::group(['prefix' => 'configuration'], function () {
            Route::get('/', function () {
                return view('HR/Payroll/PayrollConfig/index');
            });
            Route::post('/', 'PayrollConfigurationController@index');

            Route::any('/add', function () {
                return view('HR/Payroll/PayrollConfig/add');
            });

            Route::any('/edit/{id}', function ($id) {
                $editData = PayrollConfiguration::find(decrypt($id));
                return view('HR/Payroll/PayrollConfig/edit', compact('editData'));
            });
            Route::any('/view/{id}', function ($id) {
                $viewData = PayrollConfiguration::find(decrypt($id));
                return view('HR/Payroll/PayrollConfig/view', compact('viewData'));
            });

            Route::any('/delete/{id}', 'PayrollConfigurationController@delete');
            Route::any('/get/{id}/api', 'PayrollConfigurationController@get');
            Route::any('/insert/api', 'PayrollConfigurationController@insert');
            Route::any('/update/api', 'PayrollConfigurationController@update');
        });


        ## Payroll Menu Configuration
        # php artisan make:controller HR/Payroll/PayrollDeductionConfigurationController
        # php artisan make:model Model/HR/PayrollDeductionConfigModel
        Route::group(['prefix' => 'deduction_configuration'], function () {
            Route::get('/', function () {
                return view('HR/Payroll/PayrollDeductionConfig/index');
            });

            Route::post('/', 'PayrollDeductionConfigurationController@index');

            Route::any('/add', function () {
                return view('HR/Payroll/PayrollDeductionConfig/add');
            });

            Route::any('/edit/{id}', function ($id) {
                $editData = PayrollDeductionConfigModel::find(decrypt($id));
                return view('HR/Payroll/PayrollDeductionConfig/edit', compact('editData'));
            });
            Route::any('/view/{id}', function ($id) {
                $viewData = PayrollDeductionConfigModel::find(decrypt($id));
                return view('HR/Payroll/PayrollDeductionConfig/view', compact('viewData'));
            });

            Route::any('/delete/{id}', 'PayrollDeductionConfigurationController@delete');
            Route::any('/insert/api', 'PayrollDeductionConfigurationController@insert');
            Route::any('/update/api', 'PayrollDeductionConfigurationController@update');
            Route::any('/get/{id}/api', 'PayrollDeductionConfigurationController@get');

            // Route::any('/getStatus/{value}/api', 'PayrollDeductionConfigurationController@getStatus');
        });


        ##Setting
        Route::group(['namespace' => 'Settings', 'prefix' => 'settings'], function () {

            ##PF setup => 1
            Route::group(['prefix' => 'pf'], function () {
                Route::get('/', function () {
                    return view('HR/Payroll/Settings/ProvidientFund/index');
                });

                Route::post('/', 'ProvidientFundController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/ProvidientFund/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = ProvidientFund::find(decrypt($id));
                    return view('HR/Payroll/Settings/ProvidientFund/edit', compact('editData'));
                });
                Route::any('/view/{id}', function ($id) {
                    $viewData = ProvidientFund::find(decrypt($id));
                    return view('HR/Payroll/Settings/ProvidientFund/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'ProvidientFundController@delete');
                Route::any('/publish/{id}', 'ProvidientFundController@change_status');
                Route::any('/unpublish/{id}', 'ProvidientFundController@change_status');
                Route::any('/insert/api', 'ProvidientFundController@insert');
                Route::any('/update/api', 'ProvidientFundController@update');
            });

            ##WF setup => 2
            Route::group(['prefix' => 'wf'], function () {

                Route::get('/', function () {
                    return view('HR/Payroll/Settings/WelfareFund/index');
                });

                Route::post('/', 'WelfareFundController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/WelfareFund/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = WelfareFund::find(decrypt($id));
                    return view('HR/Payroll/Settings/WelfareFund/edit', compact('editData'));
                });
                Route::any('/view/{id}', function ($id) {
                    $viewData = WelfareFund::find(decrypt($id));
                    return view('HR/Payroll/Settings/WelfareFund/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'WelfareFundController@delete');
                Route::any('/publish/{id}', 'WelfareFundController@change_status');
                Route::any('/unpublish/{id}', 'WelfareFundController@change_status');
                Route::any('/insert/api', 'WelfareFundController@insert');
                Route::any('/update/api', 'WelfareFundController@update');
            });

            ##Bonus setup => 4
            Route::group(['prefix' => 'bonus'], function () {
                Route::get('/', function () {
                    return view('HR/Payroll/Settings/Bonus/index');
                });

                Route::post('/', 'BonusController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/Bonus/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = Bonus::find(decrypt($id));
                    return view('HR/Payroll/Settings/Bonus/edit', compact('editData'));
                });
                Route::any('/view/{id}', function ($id) {
                    $viewData = Bonus::find(decrypt($id));
                    return view('HR/Payroll/Settings/Bonus/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'BonusController@delete');
                Route::any('/publish/{id}', 'BonusController@change_status');
                Route::any('/unpublish/{id}', 'BonusController@change_status');
                Route::any('/insert/api', 'BonusController@insert');
                Route::any('/update/api', 'BonusController@update');
            });

            ##Gratuity setup => 5
            Route::group(['prefix' => 'gratuity'], function () {
                Route::get('/', function () {
                    return view('HR/Payroll/Settings/Gratuity/index');
                });

                Route::post('/', 'GratuityController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/Gratuity/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = Gratuity::find(decrypt($id));
                    return view('HR/Payroll/Settings/Gratuity/edit', compact('editData'));
                });
                Route::any('/view/{id}', function ($id) {
                    $viewData = Gratuity::find(decrypt($id));
                    return view('HR/Payroll/Settings/Gratuity/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'GratuityController@delete');
                Route::any('/publish/{id}', 'GratuityController@change_status');
                Route::any('/unpublish/{id}', 'GratuityController@change_status');
                Route::any('/insert/api', 'GratuityController@insert');
                Route::any('/update/api', 'GratuityController@update');
            });
            ##Insurance setup => 6
            Route::group(['prefix' => 'insurance'], function () {
                Route::get('/', function () {
                    return view('HR/Payroll/Settings/Insurance/index');
                });

                Route::post('/', 'InsuranceController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/Insurance/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = Insurance::find(decrypt($id));
                    return view('HR/Payroll/Settings/Insurance/edit', compact('editData'));
                });
                Route::any('/view/{id}', function ($id) {
                    $viewData = Insurance::find(decrypt($id));
                    return view('HR/Payroll/Settings/Insurance/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'InsuranceController@delete');
                Route::any('/publish/{id}', 'InsuranceController@change_status');
                Route::any('/unpublish/{id}', 'InsuranceController@change_status');
                Route::any('/insert/api', 'InsuranceController@insert');
                Route::any('/update/api', 'InsuranceController@update');
            });
            ##Loan setup => 7
            Route::group(['prefix' => 'loan'], function () {

                Route::get('/', function () {
                    return view('HR/Payroll/Settings/Loan/index');
                });

                Route::post('/', 'LoanController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/Loan/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = Loan::find(decrypt($id));
                    return view('HR/Payroll/Settings/Loan/edit', compact('editData'));
                });
                Route::any('/view/{id}', function ($id) {
                    $viewData = Loan::find(decrypt($id));
                    return view('HR/Payroll/Settings/Loan/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'LoanController@delete');
                Route::any('/publish/{id}', 'LoanController@change_status');
                Route::any('/unpublish/{id}', 'LoanController@change_status');
                Route::any('/insert/api', 'LoanController@insert');
                Route::any('/update/api', 'LoanController@update');

            });

            ##EPS setting => 8
            Route::group(['prefix' => 'eps_setting'], function () {
                Route::get('/', function () {
                    return view('HR/Payroll/Settings/PensionSchemeSetting/index');
                });

                Route::post('/', 'PensionSchemeSettingController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/PensionSchemeSetting/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = PensionSchemeSetting::find(decrypt($id));
                    return view('HR/Payroll/Settings/PensionSchemeSetting/edit', compact('editData'));
                });
                Route::any('/view/{id}', function ($id) {
                    $viewData = PensionSchemeSetting::find(decrypt($id));
                    return view('HR/Payroll/Settings/PensionSchemeSetting/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'PensionSchemeSettingController@delete');
                Route::any('/publish/{id}', 'PensionSchemeSettingController@change_status');
                Route::any('/unpublish/{id}', 'PensionSchemeSettingController@change_status');
                Route::any('/insert/api', 'PensionSchemeSettingController@insert');
                Route::any('/update/api', 'PensionSchemeSettingController@update');
            });

            ##EPS setup => 3
            Route::group(['prefix' => 'eps'], function () {
                Route::get('/', function () {
                    return view('HR/Payroll/Settings/PensionScheme/index');
                });

                Route::post('/', 'PensionSchemeController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/PensionScheme/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = PensionScheme::find(decrypt($id));
                    return view('HR/Payroll/Settings/PensionScheme/edit', compact('editData'));
                });
                Route::any('/view/{id}', function ($id) {
                    $viewData = PensionScheme::find(decrypt($id));
                    return view('HR/Payroll/Settings/PensionScheme/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'PensionSchemeController@delete');
                Route::any('/publish/{id}', 'PensionSchemeController@change_status');
                Route::any('/unpublish/{id}', 'PensionSchemeController@change_status');
                Route::any('/insert/api', 'PensionSchemeController@insert');
                Route::any('/update/api', 'PensionSchemeController@update');
            });

            ##OSF setup => 9
            Route::group(['prefix' => 'osf'], function () {
                Route::get('/', function () {
                    return view('HR/Payroll/Settings/OSF/index');
                });

                Route::post('/', 'OSFController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/OSF/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = OSF::find(decrypt($id));
                    return view('HR/Payroll/Settings/OSF/edit', compact('editData'));
                });
                Route::any('/view/{id}', function ($id) {
                    $viewData = OSF::find(decrypt($id));
                    return view('HR/Payroll/Settings/OSF/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'OSFController@delete');
                Route::any('/publish/{id}', 'OSFController@change_status');
                Route::any('/unpublish/{id}', 'OSFController@change_status');
                Route::any('/insert/api', 'OSFController@insert');
                Route::any('/update/api', 'OSFController@update');
            });


            ## Payroll Donation Sector
            # php artisan make:controller HR/Payroll/PayrollDonationSectorController
            # php artisan make:model Model/HR/PayrollDonationSectorModel
            Route::group(['prefix' => 'donation'], function () {
                Route::get('/', function () {
                    return view('HR/Payroll/Settings/DonationSector/index');
                });
                Route::post('/', 'PayrollDonationSectorController@index');

                Route::any('/add', function () {
                    return view('HR/Payroll/Settings/DonationSector/add');
                });

                Route::any('/edit/{id}', function ($id) {
                    $editData = PayrollDonationSectorModel::find(decrypt($id));
                    return view('HR/Payroll/Settings/DonationSector/edit', compact('editData'));
                });

                Route::any('/view/{id}', function ($id) {
                    $viewData = PayrollDonationSectorModel::find(decrypt($id));
                    return view('HR/Payroll/Settings/DonationSector/view', compact('viewData'));
                });

                Route::any('/delete/{id}', 'PayrollDonationSectorController@delete');
                Route::any('/get/{id}/api', 'PayrollDonationSectorController@get');
                Route::any('/insert/api', 'PayrollDonationSectorController@insert');
                Route::any('/update/api', 'PayrollDonationSectorController@update');
            });

        });



        ## Security Money setup => 10
        Route::group(['prefix' => 'security_money'], function () {
            Route::get('/', function () {
                return view('HR/Payroll/Settings/SecurityMoney/index');
            });
            Route::post('/', 'SecurityMoneyController@index');

            Route::any('/add', function () {
                return view('HR/Payroll/Settings/SecurityMoney/add');
            });

            Route::any('/edit/{id}', function ($id) {
                $editData = SecurityMoney::find(decrypt($id));
                return view('HR/Payroll/Settings/SecurityMoney/edit', compact('editData'));
            });

            Route::any('/view/{id}', function ($id) {
                $viewData = SecurityMoney::find(decrypt($id));
                return view('HR/Payroll/Settings/SecurityMoney/view', compact('viewData'));
            });

            Route::any('/insert/api', 'SecurityMoneyController@insert');
            Route::any('/delete/{id}', 'SecurityMoneyController@delete');
            Route::any('/update/api', 'SecurityMoneyController@update');
            // Route::any('/publish/{id}', 'SecurityMoneyController@change_status');
            // Route::any('/unpublish/{id}', 'SecurityMoneyController@change_status');

            // Route::any('/get/{id}/api', 'SecurityMoneyController@get');


        });


        ## Payroll Pay Scale Migration
        # php artisan make:controller HR/Payroll/PayrollPayScaleMigrationController
        # php artisan make:model Model/HR/PayrollPayScaleMigration
        Route::group(['prefix' => 'pay_scale_migration'], function () {
            Route::get('/', function () {
                return view('HR/Payroll/PayScaleMigration/index');
            });
            Route::post('/', 'PayrollPayScaleMigrationController@index');

            Route::any('/add', function () {
                return view('HR/Payroll/PayScaleMigration/add');
            });

            Route::any('/edit/{id}', function ($id) {
                $editData = PayrollPayScaleMigration::find(decrypt($id));
                return view('HR/Payroll/PayScaleMigration/edit', compact('editData'));
            });
            Route::any('/view/{id}', function ($id) {
                $viewData = PayrollPayScaleMigration::find(decrypt($id));
                return view('HR/Payroll/PayScaleMigration/view', compact('viewData'));
            });

            Route::any('/delete/{id}', 'PayrollPayScaleMigrationController@delete');
            Route::any('/get/{id}/api', 'PayrollPayScaleMigrationController@get');
            Route::any('/insert/api', 'PayrollPayScaleMigrationController@insert');
            Route::any('/update/api', 'PayrollPayScaleMigrationController@update');
            Route::any('/getBeforeSsData', 'PayrollPayScaleMigrationController@getBeforeSsData');
            Route::any('/getAfterSsData', 'PayrollPayScaleMigrationController@getAfterSsData');
        });


        Route::group(['prefix' => 'config'], function () {

            ##Allowance setup
            Route::group(['prefix' => 'allowance_setup'], function () {

                Route::get('/', function () {
                    return view('HR/Payroll/Configuration/AllowanceSetup/index');
                });
                Route::post('/', 'AllowanceSetupController@index'); // api

                Route::any('/add', function () {
                    $benifits = DB::table('gnl_dynamic_form_value')->where('type_id', 3)->where('form_id', 2)->get();
                    return view('HR/Payroll/Configuration/AllowanceSetup/add', compact('benifits'));
                });

                Route::any('/edit/{id}', function ($id) {
                    $benifits = DB::table('gnl_dynamic_form_value')->where('type_id', 3)->where('form_id', 2)->get();
                    $editData = DB::table('hr_payroll_allowance')->find(decrypt($id));
                    return view('HR/Payroll/Configuration/AllowanceSetup/edit', compact('editData', 'benifits'));
                });
                Route::any('/view/{id}', function ($id) {
                    return view('HR/Payroll/Configuration/AllowanceSetup/view', compact('id'));
                });
                Route::any('/delete/{id}', 'AllowanceSetupController@delete');


                Route::any('/insert/api', 'AllowanceSetupController@insert');
                Route::any('/update/api', 'AllowanceSetupController@update');
                Route::any('/get/{id}/api', 'AllowanceSetupController@get');
                Route::any('/send/{id}/api', 'AllowanceSetupController@send');
            });


            ##Salary structure setup
            Route::group(['prefix' => 'salary_structure_setup'], function () {

                Route::get('/', function () {
                    return view('HR/Payroll/Configuration/SalaryStructure/index');
                });
                Route::post('/', 'SalaryStructureController@index'); // api

                Route::any('/add', function () {
                    $allowance = DB::table('hr_payroll_allowance as alw')->where([['alw.is_active', 1], ['alw.is_delete', 0]])
                                ->join('gnl_dynamic_form_value as df', function($join){
                                    $join->on([['alw.benifit_type_uid', 'df.uid']]);
                                    $join->where([['df.type_id', 3],['df.form_id', 2]]);
                                })
                                ->select('alw.*','df.name as benifit_name', 'df.value_field as value_field')
                                ->get();

                    $designations = DB::table('hr_designations')->where('is_delete', 0)->where('is_active', 1)->get();
                    $payScale = DB::table('hr_payroll_payscale')->where('is_delete', 0)->where('is_active', 1)->get();
                    $companies = DB::table('gnl_companies')->where('is_delete', 0)->where('is_active', 1)->get();
                    $projects = DB::table('gnl_projects')->where('is_delete', 0)->where('is_active', 1)->get();
                    $gradeLevel = DB::table('hr_config')->where('title', 'grade')->orWhere('title', 'level')->get()->pluck('content', 'title');
                    $recruitmrntType = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])
                                        ->whereBetween('salary_method', ['auto', 'both'])->get();

                    return view('HR/Payroll/Configuration/SalaryStructure/add', compact('allowance', 'designations', 'payScale', 'companies', 'projects', 'gradeLevel', 'recruitmrntType'));
                });

                Route::any('/edit/{id}', function ($id) {

                    $allowance = DB::table('hr_payroll_allowance as alw')->where([['alw.is_active', 1], ['alw.is_delete', 0]])
                                ->join('gnl_dynamic_form_value as df', function($join){
                                    $join->on([['alw.benifit_type_uid', 'df.uid']]);
                                    $join->where([['df.type_id', 3],['df.form_id', 2]]);
                                })
                                ->select('alw.*','df.name as benifit_name', 'df.value_field as value_field')
                                ->get();

                    $designations = DB::table('hr_designations')->where('is_delete', 0)->where('is_active', 1)->get();
                    $payScale = DB::table('hr_payroll_payscale')->where('is_delete', 0)->where('is_active', 1)->get();
                    $companies = DB::table('gnl_companies')->where('is_delete', 0)->where('is_active', 1)->get();
                    $projects = DB::table('gnl_projects')->where('is_delete', 0)->where('is_active', 1)->get();
                    $gradeLevel = DB::table('hr_config')->where('title', 'grade')->orWhere('title', 'level')->get()->pluck('content', 'title');
                    $recruitmrntType = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])
                                        ->whereBetween('salary_method', ['auto', 'both'])->get();

                    $editData = SalaryStructure::where('is_delete', 0)->where('is_active', 1)->where('id', decrypt($id))->with('salary_structure_details')->first();

                    return view('HR/Payroll/Configuration/SalaryStructure/edit', compact('editData', 'allowance', 'designations', 'payScale', 'companies', 'projects', 'gradeLevel', 'recruitmrntType'));
                });
                Route::any('/view/{id}', function ($id) {
                    // $allowance = DB::table('hr_payroll_allowance as alw')
                    //             ->join('gnl_dynamic_form_value as df', function($join){
                    //                 $join->on([['alw.benifit_type_uid', 'df.uid']]);
                    //                 $join->where([['df.type_id', 3],['df.form_id', 2]]);
                    //             })
                    //             ->select('alw.*','df.name as benifit_name')
                    //             ->get();
                    // $salStruct = SalaryStructure::where('is_delete', 0)->where('is_active', 1)->where('id', decrypt($id))->with('salary_structure_details')->first();
                    // $viewData = [];
                    // $viewData['grade']        = $salStruct->grade;
                    // $viewData['level']        = $salStruct->level;
                    // $viewData['basic']        = $salStruct->basic;
                    // $viewData['pay_scale']        = $salStruct->pay_scale()->name;
                    // $viewData['company']        = $salStruct->company()->comp_name;
                    // $viewData['designations']        = $salStruct->designations();
                    // $viewData['recruitment_type']        = $salStruct->recruitment_type();
                    // $viewData['project']        = $salStruct->project()->project_name;
                    // $viewData['acting_benefit_amount']        = $salStruct->acting_benefit_amount;
                    // $viewData['pf']        = ($salStruct->is_pf_applicible == 1) ? 'Yes' : 'No';
                    // $viewData['ps']        = ($salStruct->is_ps_applicible == 1) ? 'Yes' : 'No';
                    // $viewData['wf_amount']        = ($salStruct->wf_amount > 0) ? $salStruct->wf_amount : '0';
                    // $viewData['status']        = ($salStruct->is_active == 1) ? 'Active' : 'Inactive';
                    // $viewData['salary_structure_details']        = $salStruct->salary_structure_details;

                    $allowance = DB::table('hr_payroll_allowance as alw')->where([['alw.is_active', 1], ['alw.is_delete', 0]])
                                ->join('gnl_dynamic_form_value as df', function($join){
                                    $join->on([['alw.benifit_type_uid', 'df.uid']]);
                                    $join->where([['df.type_id', 3],['df.form_id', 2]]);
                                })
                                ->select('alw.*','df.name as benifit_name', 'df.value_field as value_field')
                                ->get();

                    $designations = DB::table('hr_designations')->where('is_delete', 0)->where('is_active', 1)->get();
                    $payScale = DB::table('hr_payroll_payscale')->where('is_delete', 0)->where('is_active', 1)->get();
                    $companies = DB::table('gnl_companies')->where('is_delete', 0)->where('is_active', 1)->get();
                    $projects = DB::table('gnl_projects')->where('is_delete', 0)->where('is_active', 1)->get();
                    $gradeLevel = DB::table('hr_config')->where('title', 'grade')->orWhere('title', 'level')->get()->pluck('content', 'title');
                    $recruitmrntType = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])
                                        ->whereBetween('salary_method', ['auto', 'both'])->get();

                    $editData = SalaryStructure::where('is_delete', 0)->where('is_active', 1)->where('id', decrypt($id))->with('salary_structure_details')->first();

                    return view('HR/Payroll/Configuration/SalaryStructure/view', compact('editData', 'allowance', 'designations', 'payScale', 'companies', 'projects', 'gradeLevel', 'recruitmrntType'));
                });
                Route::any('/delete/{id}', 'SalaryStructureController@delete');


                Route::any('/insert/api', 'SalaryStructureController@insert');
                Route::any('/update/api', 'SalaryStructureController@update');
                Route::any('/get/{id}/api', 'SalaryStructureController@get');
                Route::any('/send/{id}/api', 'SalaryStructureController@send');
                Route::any('/getData', 'SalaryStructureController@getData');
                Route::any('/getWfData', 'SalaryStructureController@getWfData');
                Route::any('/getDeducData', 'SalaryStructureController@getDeducData');

                Route::any('/getStatus/api', 'SalaryStructureController@getStatus');
            });


        });

        ##View Salary structure
        Route::group(['prefix' => 'salary_structure'], function () {

            Route::get('/', 'SalaryStructureController@viewSalaryStructure');
            Route::get('/body', 'SalaryStructureController@viewSalaryStructureBody');
            Route::any('/getData', 'SalaryStructureController@getData');
        });


        ## Salary Generate
        Route::group(['prefix' => 'salary_genarate'], function () {

            Route::get('/', function () {
                return view('HR/Payroll/SalaryGenerate/index');
            });
            Route::post('/', 'PayrollSalaryGenerateController@index'); // api
            Route::any('/add', function () {
                return view('HR/Payroll/SalaryGenerate/add');
            });

            Route::any('/view/{id}', function ($id) {
                return view('HR/Payroll/SalaryGenerate/view',compact('id'));
            });


            Route::any('/insert/api', 'PayrollSalaryGenerateController@insert');
            Route::any('/get/{id}/api', 'PayrollSalaryGenerateController@get');
            Route::any('/delete/{id}', 'PayrollSalaryGenerateController@delete');
        });

    });
    /* -------------------------------------Payroll---------------------------------- */



    /* -------------------------------------Attendance------------------------------ */
    Route::group(['namespace' => 'Attendance'], function () {

        ## Employee Attendance
        Route::group(['prefix' => 'employee_attendance'], function () {

            Route::get('/', function () {
                return view('HR/Attendance/index');
            });
            Route::post('/', 'EmployeeAttendanceController@index'); // api

            Route::any('/add', function () {
                return view('HR/Attendance/add');
            });

            Route::any('/addByFile', function () {
                return view('HR/Attendance/fileUpload');
            });

            Route::any('/edit/{id}', function ($id) {
                return view('HR/Attendance/edit', compact('id'));
            });
            Route::any('/view/{id}', function ($id) {
                return view('HR/Attendance/view', compact('id'));
            });
            /* Route::any('/fileUpload', function () {
                return view('HR/Attendance/fileUpload');
            }); */
            Route::any('/delete/{id}', 'EmployeeAttendanceController@delete');


            Route::any('getData', 'EmployeeAttendanceController@getData'); // API
            Route::any('getDesigData', 'EmployeeAttendanceController@getDesignationData'); // API

            Route::any('/employeeInfo/{id}/api', 'EmployeeAttendanceController@getEmployeeInfo');

            Route::any('/insert/api', 'EmployeeAttendanceController@insert');
            Route::any('/update/api', 'EmployeeAttendanceController@update');
            Route::any('/get/{id}/api', 'EmployeeAttendanceController@get');
            Route::any('/insertByFile/api', 'EmployeeAttendanceController@insert_by_file');
            Route::any('/downloadExampleFile', 'EmployeeAttendanceController@exampleFileDownload');
        });
    });
    /* -------------------------------------Attendance------------------------------ */

});
