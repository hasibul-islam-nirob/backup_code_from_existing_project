<?php
use Illuminate\Support\Facades\Route;

// ,'offline'
Route::group(['middleware' => ['auth', 'permission'], 'prefix' => 'hms', 'namespace' => 'HMS'], function () {

    Route::get('/', 'DashboardController@index');
    ## ----------------------- Hall Settings Route -------------------------- ##
    Route::group(['namespace' => 'HallSettings'], function () {

        ## Building Route
        Route::group(['prefix' => 'building_setup'], function () {
            Route::any('/', 'BuildingController@index');
            Route::any('add', 'BuildingController@add');
            Route::any('edit/{id}', 'BuildingController@edit');
            Route::get('view/{id}', 'BuildingController@view');
            Route::any('delete/{id}', 'BuildingController@delete');
            Route::any('/insert/{status}/api', 'BuildingController@insert');
            Route::any('/update/{status}/api', 'BuildingController@update');
            Route::any('/get/{id}/api', 'BuildingController@get');
            Route::any('/send/{id}/api', 'BuildingController@send');
            Route::any('/getAll/api/{param?}', 'BuildingController@getAll')->name('hms_getBuilding');
            Route::any('isActive/{id}', 'BuildingController@isActive');
            Route::any('inActive/{id}', 'BuildingController@inActive');
        });

        ## Floor Route
        Route::group(['prefix' => 'floor_setup'], function () {
            Route::any('/', 'FloorController@index');
            Route::any('add', 'FloorController@add');
            Route::any('edit/{id}', 'FloorController@edit');
            Route::get('view/{id}', 'FloorController@view');
            Route::any('delete/{id}', 'FloorController@delete');
            Route::any('/insert/{status}/api', 'FloorController@insert');
            Route::any('/get/{id}/api', 'FloorController@get');
            Route::any('/update/{status}/api', 'FloorController@update');
            Route::any('/send/{id}/api', 'FloorController@send');
            Route::any('/getAll/api/{param?}', 'FloorController@getAll')->name('hms_getFloor');
            Route::any('isActive/{id}', 'FloorController@isActive');
            Route::any('inActive/{id}', 'FloorController@inActive');
        });

        ## Room Route
        Route::group(['prefix' => 'room_setup'], function () {
            Route::any('/', 'RoomController@index');
            Route::any('add', 'RoomController@add');
            Route::any('edit/{id}', 'RoomController@edit');
            Route::get('view/{id}', 'RoomController@view');
            Route::any('delete/{id}', 'RoomController@delete');
            Route::any('/insert/{status}/api', 'RoomController@insert');
            Route::any('/get/{id}/api', 'RoomController@get');
            Route::any('/update/{status}/api', 'RoomController@update');
            Route::any('/send/{id}/api', 'RoomController@send');
            Route::any('/getAll/api/{param?}', 'RoomController@getAll')->name('hms_getRoom');
            Route::any('/getRoomOnly/api/{param?}', 'RoomController@getRoomOnly')->name('hms_getRoomOnly');
            Route::any('isActive/{id}', 'RoomController@isActive');
            Route::any('inActive/{id}', 'RoomController@inActive');
            Route::any('/getseatvalidation/{id}/api', 'RoomController@getcapacity')->name('hms_getseatvalidation');
        });

        ## Seat Route
        Route::group(['prefix' => 'seat_setup'], function () {
            Route::any('/', 'SeatController@index');
            Route::any('add', 'SeatController@add');
            Route::any('addByFile', 'SeatController@insertByFile');
            Route::any('/insertByFile/api', 'SeatController@insert_by_file');
            Route::any('/downloadExampleFile', 'SeatController@exampleFileDownload');
            Route::any('edit/{id}', 'SeatController@edit');
            Route::get('view/{id}', 'SeatController@view');
            Route::any('delete/{id}', 'SeatController@delete');
            Route::any('/insert/{status}/api', 'SeatController@insert');
            Route::any('/get/{id}/api', 'SeatController@get');
            Route::any('/update/{status}/api', 'SeatController@update');
            Route::any('/send/{id}/api', 'SeatController@send');
            Route::any('/getAll/api/{param?}', 'SeatController@getAll')->name('hms_getSeat');
            Route::any('/getVacantSeat/api/{param?}', 'SeatController@getVacantSeat')->name('hms_getVacantSeat');
            Route::any('/getVacantSeatForStudent/api/{param?}/{param2?}', 'SeatController@getVacantSeatStudent')->name('hms_getVacantSeatForStudent');
            Route::any('/getAssignedSeat/api/{param?}', 'SeatController@getAssignedSeat')->name('hms_getAssignedSeat');
            Route::any('/getAssignedAndNotEmpty/api/{param?}', 'SeatController@hms_getAssignedAndNotEmptySeat')->name('hms_getAssignedAndNotEmptySeat');
            Route::any('isActive/{id}', 'SeatController@isActive');
            Route::any('inActive/{id}', 'SeatController@inActive');
        });

        Route::group(['prefix' => 'organization_setup'], function () {
            Route::any('/', 'OrganizationController@index');
            Route::any('add', 'OrganizationController@add');
            Route::get('view/{id}', 'OrganizationController@view');
            Route::any('edit/{id}', 'OrganizationController@edit');
            Route::any('/get/{id}/api', 'OrganizationController@get');
            Route::any('/insert/{status}/api', 'OrganizationController@insert');
            Route::any('/update/{status}/api', 'OrganizationController@update');
            Route::any('isActive/{id}', 'OrganizationController@isActive');
            Route::any('inActive/{id}', 'OrganizationController@inActive');
            Route::any('/getAll/api/{param?}', 'OrganizationController@getAll')->name('hms_getOrganization');
            Route::any('delete/{id}', 'OrganizationController@delete');
            Route::any('isActive/{id}', 'OrganizationController@isActive');
            Route::any('inActive/{id}', 'OrganizationController@inActive');
        });

        Route::group(['prefix' => 'scholarships'], function () {
            Route::any('/', 'ScholarshipsController@index');
            Route::any('add', 'ScholarshipsController@add');
            Route::get('view/{id}', 'ScholarshipsController@view');
            Route::any('edit/{id}', 'ScholarshipsController@edit');
            Route::any('/get/{id}/api', 'ScholarshipsController@get');
            Route::any('/insert/{status}/api', 'ScholarshipsController@insert');
            Route::any('/update/{status}/api', 'ScholarshipsController@update');
            Route::any('isActive/{id}', 'ScholarshipsController@isActive');
            Route::any('inActive/{id}', 'ScholarshipsController@inActive');
            Route::any('/getAll/api/{param?}', 'ScholarshipsController@getAll')->name('hms_getScholarships');
            Route::any('delete/{id}', 'ScholarshipsController@delete');
            // Route::any('isActive/{id}', 'ScholarshipsController@isActive');
            // Route::any('inActive/{id}', 'ScholarshipsController@inActive');
        });

        // Route::group(['prefix' => 'floor_wise_room_view'], function () {
        //     Route::any('/', 'RoomViewController@index');
        //     Route::any('/{id}', 'RoomViewController@index');
        //     Route::any('/view_seat/{id}', 'RoomViewController@view_seat');
        //     Route::any('/get/{id}/api', 'RoomViewController@get');
        //     Route::any('/insert/send/api', 'RoomViewController@insert');
        //     Route::any('inserttutor/send/api', 'RoomViewController@insert_tutor');
        //     Route::any('loadData', 'RoomViewController@loadData')->name('getRoomByFloor');
        // });

        Route::group(['prefix' => 'floor_wise_room_view'], function () {
            Route::any('/', 'FloorWiseRoomViewController@index');
            Route::any('/add/{seat_id}', 'FloorWiseRoomViewController@add');
            Route::any('/addremark/{student_id}', 'FloorWiseRoomViewController@addremark');
            Route::any('/loadData', 'FloorWiseRoomViewController@loadRoom');
            Route::get('view_seat/{id}', 'FloorWiseRoomViewController@view_seat');
            Route::get('/getfloorunderbuilding{id}', 'FloorWiseRoomViewController@getfloorunderbuilding')->name('getfloorunderbuilding');
            Route::get('/getroomunderbuilding{id}', 'FloorWiseRoomViewController@getroomunderbuilding')->name('getroomunderbuilding');
            Route::any('/get/{id}/api', 'FloorWiseRoomViewController@get');
            Route::any('delete/{id}', 'FloorWiseRoomViewController@delete');
            Route::any('/insert/{status}/api', 'FloorWiseRoomViewController@insert');
            Route::any('/insertremark/{status}/api', 'FloorWiseRoomViewController@insertremark');
            Route::any('/inserttutor/send/api', 'FloorWiseRoomViewController@insert_tutor');
        });
        Route::group(['prefix' => 'seat_cancel_reason'], function () {
            Route::any('/', 'SeatCancelReasonController@index');
            Route::any('add', 'SeatCancelReasonController@add');
            Route::any('edit/{id}', 'SeatCancelReasonController@edit');
            Route::any('/get/{id}/api', 'SeatCancelReasonController@get');
            Route::any('delete/{id}', 'SeatCancelReasonController@delete');
            Route::any('/insert/{status}/api', 'SeatCancelReasonController@insert');
            Route::any('/update/{status}/api', 'SeatCancelReasonController@update');
            Route::get('view/{id}', 'SeatCancelReasonController@view');
            Route::any('isActive/{id}', 'SeatCancelReasonController@isActive');
            Route::any('inActive/{id}', 'SeatCancelReasonController@inActive');
            Route::any('/getAll/api/{param?}', 'SeatCancelReasonController@getAll')->name('hms_getCancelReasons');
        });
    });
    ## ----------------------- Student Configuration Route -------------------------- ##
    Route::group(['namespace' => 'StudentConfig'], function () {

        ## Academic Department
        Route::group(['prefix' => 'academic_department'], function () {
            Route::any('/', 'AcademicDepartmentController@index');
            Route::any('add', 'AcademicDepartmentController@add');
            Route::any('/insert/{status}/api', 'AcademicDepartmentController@insert');
            Route::any('edit/{id}', 'AcademicDepartmentController@edit');
            Route::any('/get/{id}/api', 'AcademicDepartmentController@get');
            Route::any('/update/{status}/api', 'AcademicDepartmentController@update');
            Route::any('delete/{id}', 'AcademicDepartmentController@delete');
            Route::get('view/{id}', 'AcademicDepartmentController@view');
            Route::any('/getAll/api/{param?}', 'AcademicDepartmentController@getAll')->name('hms_getAcademicDepartment');

        });

        ## Academic Session
        Route::group(['prefix' => 'academic_session'], function () {
            Route::any('/', 'AcademicSessionController@index');
            Route::any('add', 'AcademicSessionController@add');
            Route::any('/insert/{status}/api', 'AcademicSessionController@insert');
            Route::any('edit/{id}', 'AcademicSessionController@edit');
            Route::any('/get/{id}/api', 'AcademicSessionController@get');
            Route::any('/update/{status}/api', 'AcademicSessionController@update');
            Route::any('delete/{id}', 'AcademicSessionController@delete');
            Route::get('view/{id}', 'AcademicSessionController@view');
            Route::any('/getAll/api/{param?}', 'AcademicSessionController@getAll')->name('hms_getSession');
        });

        ## Academic Status
        Route::group(['prefix' => 'academic_status'], function () {
            Route::any('/', 'AcademicStatusController@index');
            Route::any('add', 'AcademicStatusController@add');
            Route::any('/insert/{status}/api', 'AcademicStatusController@insert');
            Route::any('edit/{id}', 'AcademicStatusController@edit');
            Route::any('/get/{id}/api', 'AcademicStatusController@get');
            Route::any('/update/{status}/api', 'AcademicStatusController@update');
            Route::any('delete/{id}', 'AcademicStatusController@delete');
            Route::get('view/{id}', 'AcademicStatusController@view');
            Route::any('/getAll/api/{param?}', 'AcademicStatusController@getAll')->name('hms_getAcademicStatus');


        });

        Route::any('/hrm', 'HrmContoller@index');
        Route::any('/mrf', 'HrmContoller@index_mrf');

    });


    ## ----------------------- Student Route -------------------------- ##
    Route::group(['namespace' => 'Student'], function () {

        ## Student Info Route
        Route::group(['prefix' => 'student_admission'], function () {
            Route::any('/', 'StudentInfoController@index');
            Route::any('add', 'StudentInfoController@add');
            Route::any('edit/{id}', 'StudentInfoController@edit');
            Route::any('editResult/{id}', 'StudentInfoController@editResult');
            Route::get('view/{id}', 'StudentInfoController@view');
            Route::any('delete/{id}', 'StudentInfoController@delete');
            Route::any('/get/{id}/api', 'StudentInfoController@get');
            Route::any('/getAll/api/{param?}', 'StudentInfoController@getAll')->name('hms_getStudent');
            Route::any('/getAllBySession/api/{param?}', 'StudentInfoController@getAllBySession')->name('getAll_BySession');
            Route::any('/getResidentBySession/api/{param?}', 'StudentInfoController@getResidentBySession')->name('hms_getResidentBySession');
            Route::any('/getResidentBySessionButNotReleased/api/{param?}', 'StudentInfoController@getResidentBySessionButNotReleased')->name('hms_getResidentBySessionButNotRelesed');
            Route::any('/getNonResidentBySession/api/{param?}', 'StudentInfoController@getNonResidentBySession')->name('hms_getNonResidentBySession');
            Route::any('/getCurrentStudent/api/{param?}', 'StudentInfoController@getCurrentStudent')->name('hms_getCurrentStudent');
            Route::any('/getCurrentStudentSession/api/{param?}', 'StudentInfoController@getCurrentStudent_Session')->name('hms_getCurrentStudent_Session');
            Route::any('/getResidence/api/{param?}', 'StudentInfoController@getResidence')->name('hms_getResidence');
            Route::any('/getNonResidence/api/{param?}', 'StudentInfoController@getNonResidence')->name('hms_getNonResidence');
            Route::any('/getPromoStatus/api/{param?}', 'StudentInfoController@getPromoStatus')->name('hms_getPromoStatus');
            Route::any('/getDegreeCompleted/api/{param?}', 'StudentInfoController@getDegreeCompleted')->name('hms_getDegreeCompleted');
            Route::any('/insert/{status}/api', 'StudentInfoController@insert');
            Route::any('/update/{status}/api', 'StudentInfoController@update');
            Route::any('/updateresult/{status}/api', 'StudentInfoController@updateresult');
            Route::any('addByFile', 'StudentInfoController@insertByFile');
            Route::any('/insertByFile/api', 'StudentInfoController@insert_by_file');
            Route::any('/downloadExampleFile', 'StudentInfoController@exampleFileDownload');
            Route::any('/gethmsDistrict/api', 'StudentInfoController@getDistricts')->name('hms_getDistrict');
            Route::any('/gethmsTitle/api', 'StudentInfoController@getTitles')->name('hms_getTitle');
            Route::any('/hallid/{id}/check', 'StudentInfoController@hallidCheck');
            Route::any('/hallid2/{id}/check', 'StudentInfoController@hallidChecklimited');
        });

        Route::group(['prefix' => 'student_remark'], function () {
            Route::any('/', 'StudentRemarkController@index');
            Route::any('add', 'StudentRemarkController@add');
            Route::any('/add/{seat_id}', 'StudentRemarkController@add');
            Route::any('edit/{id}', 'StudentRemarkController@edit');
            Route::any('/get/{id}/api', 'StudentRemarkController@get');
            Route::any('/update/{status}/api', 'StudentRemarkController@update');
            Route::any('delete/{id}', 'StudentRemarkController@delete');
            Route::any('/insert/{status}/api', 'StudentRemarkController@insert');
            Route::get('view/{id}', 'StudentRemarkController@view');
            Route::get('/getremarkby', 'StudentRemarkController@getremarkby')->name('hms_getremarkby');
        });
        ## Student Seat Assign
        Route::group(['prefix' => 'student_seat_assign'], function () {
            Route::any('/', 'SeatAssignController@index');
            Route::any('add', 'SeatAssignController@add');
            Route::any('edit/{id}', 'SeatAssignController@edit');
            Route::get('view/{id}', 'SeatAssignController@view');
            Route::any('delete/{id}', 'SeatAssignController@delete');
            Route::any('/get/{id}/api', 'SeatAssignController@get');
            Route::any('/getInfoByStudentId/{id}/api', 'SeatAssignController@getInfoByStudentId')->name('hms_getStudentAssign');
            Route::any('/insert/{status}/api', 'SeatAssignController@insert');
            Route::any('/update/{status}/api', 'SeatAssignController@update');
            Route::any('addByFile', 'SeatAssignController@insertByFile');
            Route::any('/insertByFile/api', 'SeatAssignController@insert_by_file');
            Route::any('/downloadExampleFile', 'SeatAssignController@exampleFileDownload');
        });
        ## Student Seat Assign
        Route::group(['prefix' => 'student_terminate'], function () {
            Route::any('/', 'StudentTerminateController@index');
            Route::any('add', 'StudentTerminateController@add');
            Route::any('edit/{id}', 'StudentTerminateController@edit');
            Route::get('view/{id}', 'StudentTerminateController@view');
            Route::any('delete/{id}', 'StudentTerminateController@delete');
            Route::any('/get/{id}/api', 'StudentTerminateController@get');
            Route::any('/insert/{status}/api', 'StudentTerminateController@insert');
            Route::any('/update/{status}/api', 'StudentTerminateController@update');
        });

        ## Seat Transfer
        Route::group(['prefix' => 'student_seat_transfer'], function () {
            Route::any('/', 'SeatTransferController@index');
            Route::any('add', 'SeatTransferController@add');
            Route::any('edit/{id}', 'SeatTransferController@edit');
            Route::get('view/{id}', 'SeatTransferController@view');
            Route::any('/get/{id}/api', 'SeatTransferController@get');
            Route::any('delete/{id}', 'SeatTransferController@delete');
            Route::any('/update/{status}/api', 'SeatTransferController@update');
            Route::any('/insert/{status}/api', 'SeatTransferController@insert');

        });
        ## Student alumni
        Route::group(['prefix' => 'student_alumni'], function () {
            Route::any('/', 'StudentAlumniController@index');
            Route::any('add', 'StudentAlumniController@add')->name('addAlumni');
            Route::any('edit/{id}', 'StudentAlumniController@edit');
            Route::get('view/{id}', 'StudentAlumniController@view');
            Route::any('/get/{id}/api', 'StudentAlumniController@get');
            Route::any('delete/{id}', 'StudentAlumniController@delete');
            Route::any('/insert/{status}/api', 'StudentAlumniController@insert');
            Route::any('/loadData', 'StudentAlumniController@loadData');

        });

        ## Seat Cancel
        Route::group(['prefix' => 'student_seat_cancelation'], function () {
            Route::any('/', 'SeatCancelController@index');
            Route::any('add', 'SeatCancelController@add');
            Route::any('edit/{id}', 'SeatCancelController@edit');
            Route::get('view/{id}', 'SeatCancelController@view');
            Route::any('delete/{id}', 'SeatCancelController@delete');
            Route::any('/get/{id}/api', 'SeatCancelController@get');
            Route::any('/getInfoByStudentId/{id}/api', 'SeatCancelController@getInfoByStudentId')->name('hms_getStudentCancel');
            Route::any('/insert/{status}/api', 'SeatCancelController@insert');
            Route::any('/update/{status}/api', 'SeatCancelController@update');
            Route::any('/loadStudent', 'SeatCancelController@loadStudent')->name('loadStudent');
        });

        ##seat remove
        Route::group(['prefix' => 'student_seat_remove'], function () {
            Route::any('/', 'SeatReleaseController@index');
            Route::any('add', 'SeatReleaseController@add');
            // Route::any('edit/{id}', 'SeatReleaseController@edit');
            // Route::get('view/{id}', 'SeatReleaseController@view');
            // Route::any('delete/{id}', 'SeatReleaseController@delete');
            // Route::any('/get/{id}/api', 'SeatReleaseController@get');
            Route::any('/insert/{status}/api', 'SeatReleaseController@insert');
            Route::any('/getAssignedStudent/api/{param?}', 'SeatReleaseController@getInfoBySeatId')->name('hms_getAssignedStudent');
            // Route::any('/update/{status}/api', 'SeatReleaseController@update');
        });

        ## Srudent Readmissions
        Route::group(['prefix' => 'student_readmission'], function () {
            Route::any('/', 'StudentReadmissionController@index');
            Route::any('add', 'StudentReadmissionController@add');
            Route::any('edit/{id}', 'StudentReadmissionController@edit');
            Route::get('view/{id}', 'StudentReadmissionController@view');
            Route::any('delete/{id}', 'StudentReadmissionController@delete');
            Route::any('/get/{id}/api', 'StudentReadmissionController@get');
            Route::any('/insert/{status}/api', 'StudentReadmissionController@insert');
            Route::any('/update/{status}/api', 'StudentReadmissionController@update');
        });

        ## Student Result
        Route::group(['prefix' => 'promote_year'], function () {
            Route::any('/', 'StudentPromoteYearController@index');
            Route::any('add', 'StudentPromoteYearController@add')->name('promoteYear');
            Route::any('edit/{id}', 'StudentPromoteYearController@edit');
            Route::get('view/{id}', 'StudentPromoteYearController@view');
            Route::any('/get/{id}/api', 'StudentPromoteYearController@get');
            Route::any('delete/{id}', 'StudentPromoteYearController@delete');
            Route::any('/insert/{status}/api', 'StudentPromoteYearController@insert');
            Route::any('/loadData', 'StudentPromoteYearController@loadData');
        });

        Route::group(['prefix' => 'promote_program'], function () {
            Route::any('/', 'StudentPromoteProgramController@index');
            Route::any('add', 'StudentPromoteProgramController@add')->name('promoteProgram');
            Route::any('edit/{id}', 'StudentPromoteProgramController@edit');
            Route::get('view/{id}', 'StudentPromoteProgramController@view');
            Route::any('/get/{id}/api', 'StudentPromoteProgramController@get');
            Route::any('delete/{id}', 'StudentPromoteProgramController@delete');
            Route::any('/insert/{status}/api', 'StudentPromoteProgramController@insert');
            Route::any('/loadData', 'StudentPromoteProgramController@loadData');
        });

        ## ID Issue
        Route::group(['prefix' => 'issue_id_card'], function () {
            Route::any('/', 'StudentIdCardIssueController@index');
            Route::any('add', 'StudentIdCardIssueController@add');
            Route::any('edit/{id}', 'StudentIdCardIssueController@edit');
            Route::get('view/{id}', 'StudentIdCardIssueController@view');
            Route::any('/get/{id}/api', 'StudentIdCardIssueController@get');
            Route::any('delete/{id}', 'StudentIdCardIssueController@delete');
            Route::any('/insert/{status}/api', 'StudentIdCardIssueController@insert');
            Route::any('/loadData', 'StudentIdCardIssueController@loadData');
            Route::any('/update/{status}/api', 'StudentIdCardIssueController@update');
        });

        ## ID ReIssue
        Route::group(['prefix' => 'reissue_id_card'], function () {
            Route::any('/', 'StudentIdCardReIssueController@index');
            Route::any('add', 'StudentIdCardReIssueController@add');
            Route::any('edit/{id}', 'StudentIdCardReIssueController@edit');
            Route::get('view/{id}', 'StudentIdCardReIssueController@view');
            Route::any('/get/{id}/api', 'StudentIdCardReIssueController@get');
            Route::any('delete/{id}', 'StudentIdCardReIssueController@delete');
            Route::any('/insert/{status}/api', 'StudentIdCardReIssueController@insert');
            Route::any('/loadData', 'StudentIdCardReIssueController@loadData');
            Route::any('/update/{status}/api', 'StudentIdCardReIssueController@update');
        });

        ##Scholarship
        Route::group(['prefix' => 'student_scholarship'], function () {
            Route::any('/', 'StudentScholarshipController@index');
            Route::any('add', 'StudentScholarshipController@add');
            Route::any('edit/{id}', 'StudentScholarshipController@edit');
            Route::get('view/{id}', 'StudentScholarshipController@view');
            Route::any('/get/{id}/api', 'StudentScholarshipController@get');
            Route::any('delete/{id}', 'StudentScholarshipController@delete');
            Route::any('/insert/{status}/api', 'StudentScholarshipController@insert');
            Route::any('/loadData', 'StudentScholarshipController@loadData');
            Route::any('/update/{status}/api', 'StudentScholarshipController@update');
        });

        #student_certificate
        Route::group(['prefix' => 'student_certificate'], function () {
            Route::any('/', 'StudentCertificateController@index');
            Route::any('add', 'StudentCertificateController@add');
            Route::any('edit/{id}', 'StudentCertificateController@edit');
            Route::get('view/{id}', 'StudentCertificateController@view');
            Route::any('/get/{id}/api', 'StudentCertificateController@get');
            Route::any('delete/{id}', 'StudentCertificateController@delete');
            Route::any('/insert/{status}/api', 'StudentCertificateController@insert');
            Route::any('/loadData', 'StudentCertificateController@loadData');
            Route::any('/update/{status}/api', 'StudentCertificateController@update');
        });

        Route::any('/student_payment', 'SeatAssignController@payment');
        // Route::any('/student_statement', 'SeatAssignController@statement');
    });


    ## ----------------------- House Tutor Route -------------------------- ##
    Route::group(['namespace' => 'HouseTutor'], function () {

        ## HT Setup/Assign Route
        Route::group(['prefix' => 'ht_setup'], function () {
            Route::any('/', 'HTutorController@index');
            Route::any('add', 'HTutorController@add');
            Route::any('edit/{id}', 'HTutorController@edit');
            Route::any('/insert/{status}/api', 'HTutorController@insert');
            Route::get('view/{id}', 'HTutorController@view');
            Route::any('/get/{id}/api', 'HTutorController@get');
            Route::any('delete/{id}', 'HTutorController@delete');
            Route::any('/getActive/api/{param?}', 'HTutorController@getActive')->name('hms_getOnlyTutor');
            Route::any('/getInactive/api/{param?}', 'HTutorController@getInactive')->name('hms_getInactiveTutor');
            Route::any('/getalltutor/api/{param?}', 'HTutorController@getalltutor')->name('hms_getAllTutor');
            Route::any('/getDesignation', 'HTutorController@getallDesignation')->name('hr_getDesg');
            Route::any('/update/{status}/api', 'HTutorController@update');
            Route::any('addByFile', 'HTutorController@insertByFile');
            Route::any('/insertByFile/api', 'HTutorController@insert_by_file');
            Route::any('/downloadExampleFile', 'HTutorController@exampleFileDownload');
        });

        ## HT Setup/Assign Route
        Route::group(['prefix' => 'ht_assign'], function () {
            Route::any('/', 'HTutorAssignController@index');
            Route::any('add', 'HTutorAssignController@add');
            Route::any('edit/{id}', 'HTutorAssignController@edit');
            Route::any('/insert/{status}/api', 'HTutorAssignController@insert');
            Route::get('view/{id}', 'HTutorAssignController@view');
            Route::any('/get/{id}/api', 'HTutorAssignController@get');
            Route::any('delete/{id}', 'HTutorAssignController@delete');
            Route::any('/getAll/api/{param?}', 'HTutorAssignController@getAll');
            Route::any('/update/{status}/api', 'HTutorAssignController@update');
        });

        ## HT Transfer Route
        Route::group(['prefix' => 'ht_transfer'], function () {
            Route::any('/', 'HTutorTransferController@index');
            Route::any('add', 'HTutorTransferController@add');
            Route::any('/insert/{status}/api', 'HTutorTransferController@insert');
            Route::any('edit/{id}', 'HTutorTransferController@edit');
            Route::get('view/{id}', 'HTutorTransferController@view');
            Route::any('/get/{id}/api', 'HTutorTransferController@get');
            Route::any('delete/{id}', 'HTutorTransferController@delete');
            Route::any('/update/{status}/api', 'HTutorTransferController@update');
        });

        ## HT Cancel Route
        Route::group(['prefix' => 'ht_cancelation'], function () {
            Route::any('/', 'HTutorCancelController@index');
            Route::any('add', 'HTutorCancelController@add');
            Route::any('/insert/{status}/api', 'HTutorCancelController@insert');
            Route::any('edit/{id}', 'HTutorCancelController@edit');
            Route::get('view/{id}', 'HTutorCancelController@view');
            Route::any('delete/{id}', 'HTutorCancelController@delete');
            Route::any('/get/{id}/api', 'HTutorCancelController@get');
            Route::any('/update/{status}/api', 'HTutorCancelController@update');
        });

    });


    ## ----------------------- Reports Route -------------------------- ##
    Route::group(['namespace' => 'Reports', 'prefix' => 'reports'], function () {

        ## Student List Report
        Route::group(['prefix' => 'student'], function () {
            Route::any('/', 'StudentReportsController@getAllStudent');
            Route::any('/loadData', 'StudentReportsController@getAllStudent');
        });

        ## Resident Student Report
        Route::group(['prefix' => 'resident_student'], function () {
            Route::any('/', 'StudentReportsController@getResidentStudent');
            Route::any('/loadData', 'StudentReportsController@getResidentStudent');
        });

        ## Non Resident Student Report
        Route::group(['prefix' => 'non_resident_student'], function () {
            Route::any('/', 'StudentReportsController@getNonResidentStudent');
            Route::any('/loadData', 'StudentReportsController@getNonResidentStudent');
        });

        ## Fee Generation Student Report
        Route::group(['prefix' => 'fee_generation'], function () {
            Route::any('/', 'StudentReportsController@getFeegenerationStudent');
            Route::any('/loadData', 'StudentReportsController@getFeegenerationStudent');
        });

        ## Invoice Generation Student Report
        Route::group(['prefix' => 'invoice_generation'], function () {
            Route::any('/', 'StudentReportsController@getinvoicegenerationStudent');
            Route::any('/loadData', 'StudentReportsController@getinvoicegenerationStudent');
        });

        ## Alumni Student Report
        Route::group(['prefix' => 'alumni'], function () {
            Route::any('/', 'StudentReportsController@getAlumni');
            Route::any('/loadData', 'StudentReportsController@getAlumni');
        });

        Route::group(['prefix' => 'seat_wise_student'], function () {
            Route::any('/', 'StudentReportsController@getSeatWiseStudent');
            Route::any('/loadData', 'StudentReportsController@getSeatWiseStudent');
        });
        Route::group(['prefix' => 'seat_wise_student_report'], function () {
            Route::any('/', 'StudentReportsController@getSeatWiseStudentReport');
            Route::any('/loadData', 'StudentReportsController@getSeatWiseStudentReport');
        });

        Route::group(['prefix' => 'seat_cancel_student'], function () {
            Route::any('/', 'StudentReportsController@getSeatCancelStudent');
            Route::any('/loadData', 'StudentReportsController@getSeatCancelStudent');
        });

        Route::group(['prefix' => 'seat_transferred_student'], function () {
            Route::any('/', 'StudentReportsController@getSeatTransferredStudent');
            Route::any('/loadData', 'StudentReportsController@getSeatTransferredStudent');
        });

        ## Student Statement
        Route::group(['prefix' => 'student_statement'], function () {
            Route::any('/', 'StudentStatementController@index');
            Route::any('/loadData', 'StudentStatementController@loadData');
        });
        // Route::any('/alumni', 'StudentReportsController@alumni');
        Route::any('/paid_student', 'StudentReportsController@paidStudent');
        Route::any('/non_paid_student', 'StudentReportsController@nonPaidStudent');
        // Route::any('/seat_wise_student', 'StudentReportsController@seatWiseStudent');
        // Route::any('/seat_cancel_student', 'StudentReportsController@getSeatCancelStudent');
        // Route::any('/seat_transferred_student', 'StudentReportsController@getSeatTransferredStudent');



        ## HT Reports
        Route::group(['prefix' => 'house_tutor'], function () {
            Route::any('/', 'HouseTutorReportsController@getHouseTutor');
            Route::any('/loadData', 'HouseTutorReportsController@getHouseTutor');
        });

        Route::group(['prefix' => 'room_wise_ht'], function () {
            Route::any('/', 'HouseTutorReportsController@getRoomWiseHT');
            Route::any('/loadData', 'HouseTutorReportsController@getRoomWiseHT');
        });

        Route::group(['prefix' => 'ht_cancelation'], function () {
            Route::any('/', 'HouseTutorReportsController@getCanceledHT');
            Route::any('/loadData', 'HouseTutorReportsController@getCanceledHT');
        });

        Route::group(['prefix' => 'ht_room_transfer'], function () {
            Route::any('/', 'HouseTutorReportsController@getTransferredHT');
            Route::any('/loadData', 'HouseTutorReportsController@getTransferredHT');
        });

        // Route::any('/house_tutor', 'HouseTutorReportsController@houseTutor');
        // Route::any('/room_wise_ht', 'HouseTutorReportsController@roomWiseHT');
        // Route::any('/ht_cancelation', 'HouseTutorReportsController@htCancelation');
        // Route::any('/ht_room_transfer', 'HouseTutorReportsController@htRoomTransfer');
    });

    ## ----------------------- Hall Fee Route -------------------------- ##
    Route::group(['namespace' => 'HallFee'], function () {

        ## Fee Category
        Route::group(['prefix' => 'fee_category'], function () {
            Route::any('/', 'FeeCategoryController@index');
            Route::any('add', 'FeeCategoryController@add');
            Route::any('/insert/{status}/api', 'FeeCategoryController@insert');
            Route::any('edit/{id}', 'FeeCategoryController@edit');
            Route::any('/update/{status}/api', 'FeeCategoryController@update');
            Route::any('/get/{id}/api', 'FeeCategoryController@get');
            Route::get('view/{id}', 'FeeCategoryController@view');
            Route::any('delete/{id}', 'FeeCategoryController@delete');
            Route::any('/getAll/api/{param?}', 'FeeCategoryController@getAll')->name('hms_getFeeCategory');
            Route::any('isActive/{id}', 'FeeCategoryController@isActive');
            Route::any('inActive/{id}', 'FeeCategoryController@inActive');
        });

        ## Fee Category
        Route::group(['prefix' => 'hall_fee_package'], function () {
            Route::any('/', 'PackageController@index');
            Route::any('add', 'PackageController@add');
            Route::any('/insert/{status}/api', 'PackageController@insert');
            Route::any('edit/{id}', 'PackageController@edit');
            Route::any('/update/{status}/api', 'PackageController@update');
            Route::any('/get/{id}/api', 'PackageController@get');
            Route::get('view/{id}', 'PackageController@view');
            Route::any('delete/{id}', 'PackageController@delete');
            Route::any('isActive/{id}', 'PackageController@isActive');
            Route::any('inActive/{id}', 'PackageController@inActive');
            Route::any('/getAll/api/{param?}', 'PackageController@getAll')->name('hms_getFeePackage');
        });


        ## Fee Feneration
        Route::group(['prefix' => 'generate_fee'], function () {
            Route::any('/', 'FeeGenerateController@index');
            Route::any('add', 'FeeGenerateController@add')->name('genereateFee');
            Route::any('edit/{id}', 'FeeGenerateController@edit');
            Route::get('view/{id}', 'FeeGenerateController@view');
            Route::any('/delete', 'FeeGenerateController@delete')->name('removeFee');
            Route::any('/loadData', 'FeeGenerateController@loadData');
        });

        ## Fee Receipt [Backup]
        Route::group(['prefix' => 'generate_receipt'], function () {
            Route::any('/', 'InvoiceController@index');
            Route::any('add', 'InvoiceController@add')->name('createInvoice');
            Route::any('/insert/{status}/api', 'InvoiceController@insert');
            Route::any('edit/{id}', 'InvoiceController@edit');
            Route::get('view/{id}', 'InvoiceController@view');
            Route::any('delete/{id}', 'InvoiceController@delete');
            Route::any('/loadData', 'InvoiceController@loadData');
        });

        ## Invoice Generation
        Route::group(['prefix' => 'generate_invoice'], function () {
            Route::any('/', 'InvoiceController@index');
            Route::any('add', 'InvoiceController@add')->name('createInvoice');
            Route::any('/insert/{status}/api', 'InvoiceController@insert');
            Route::any('edit/{id}', 'InvoiceController@edit');
            Route::get('view/{id}', 'InvoiceController@view');
            Route::any('delete/{id}', 'InvoiceController@delete');
            Route::any('/loadData', 'InvoiceController@loadData');
            Route::any('/get/{id}/api', 'InvoiceController@get');
            Route::any('/update/{status}/api', 'InvoiceController@update');
            Route::any('/loadPackage', 'InvoiceController@loadPackage')->name('loadPackage');
            Route::any('/approvePayment', 'InvoiceController@approvePayment');
        });

        ## Invoice
        Route::group(['prefix' => 'create_invoice'], function () {
            Route::any('/', 'SampleInvoiceController@index');
            Route::any('/add', 'SampleInvoiceController@invoice')->name('createInvoice');
        });

       ## Fee Payment
        Route::group(['prefix' => 'fee_payment'], function () {
            Route::any('/', 'HallFeePaymentController@index');
            Route::any('add', 'HallFeePaymentController@add')->name('addPayment');
            Route::any('edit/{id}', 'HallFeePaymentController@edit');
            Route::get('view/{id}', 'HallFeePaymentController@view');
            Route::any('/get/{id}/api', 'HallFeePaymentController@get');
            Route::any('delete/{id}', 'HallFeePaymentController@delete');
            Route::any('/insert/{status}/api', 'HallFeePaymentController@insert');
            Route::any('/loadData', 'HallFeePaymentController@loadData');
        });

    });

});
