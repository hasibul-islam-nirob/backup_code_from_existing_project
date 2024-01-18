<?php

use Illuminate\Support\Facades\Route;

// MFN config setting
Route::any('mfn/initialSetting', 'MFN\GConfig\SettingController@add');

Route::group(['middleware' => ['auth', 'CheckMfnSetting', 'permission', 'DecimalConfig', 'TxSchedule'], 'prefix' => 'mfn', 'namespace' => 'MFN'], function () {

    Route::get('/', 'DashboardController@index');
    Route::get('/branch-status', 'DashboardController@branchStatus')->name('mfnBranchStatus');

    // COMMON THINGS GOES HERE

    Route::group(['namespace' => 'Others'], function () {
        // this class is used to do miscellaneous tasks
        Route::get('/miscellaneous/{branchId?}', 'MiscellaneousController@script');
        Route::get('/mybTesting', 'MiscellaneousController@mybTesting'); //for testing purpose

        //scripts
        Route::group(['prefix' => 'scripts'], function () {
            Route::any('/', 'ScriptController@index');
            // Route::post('/runScript', 'ScriptController@runScript')->name('runScript');

            // ------
            Route::post('getData', 'ScriptController@getData');
            Route::post('generateSummary', 'ScriptController@executeSummary');

            Route::group(['prefix' => 'counter'], function () {

                Route::any('/monthEndSummary', 'ScriptController@monthEndSummaryCount');
                Route::any('/loanSchedule', 'ScriptController@loanScheduleCount');
            });
        });

        // this class is used to find out bugs
        Route::get('/findbug/{branchId?}', 'CheckInappropriateDataController@script');
        Route::get('/findbugForm', 'CheckInappropriateDataController@dataIndex');
        Route::get('/execute', 'CheckInappropriateDataController@execute')->name('execute');

        // get a single row from tables
        Route::post('/getObject', 'CommonController@getObject');

        // get samity list on selecting branch
        Route::post('/getSamities', 'CommonController@getSamities');
        // get branch date of a branch
        Route::post('/getBranchDate', 'CommonController@getBranchDate');

        // get Active member
        Route::post('/getMember', 'CommonController@getMember');

        // get member details
        Route::post('/getMemberDetails', 'CommonController@getMemberDetails');

        // get Loan Accounts
        Route::post('/getLoanAccounts', 'CommonController@getLoanAccounts');

        // get Savings Accounts
        Route::post('/getSavingsAccounts', 'CommonController@getSavingsAccounts');

        // get address
        Route::post('/getDivisions', 'CommonController@getDivisions');
        Route::post('/getDistricts', 'CommonController@getDistricts');
        Route::post('/getUpazilas', 'CommonController@getUpazilas');
        Route::post('/getUnions', 'CommonController@getUnions');
        Route::post('/getVillages', 'CommonController@getVillages');

        Route::any('/area-and-branch-of-zone', 'CommonController@getAreasAndBranchOfZone')->name('getAreasAndBranchOfZone');
        Route::any('/branch-of-area', 'CommonController@getBranchOfArea')->name('getBranchOfArea');
        Route::any('/samity-of-branch', 'CommonController@getSamityOfBranch')->name('getSamityOfBranch');
        Route::any('/member-of-samity', 'CommonController@getMemberOfSamity')->name('getMemberOfSamity');
        Route::any('/fieldofficer-of-branch', 'CommonController@getFieldOfficerOfBranch')->name('getFieldOfficerOfBranch');
        Route::any('/samity-of-feildOfficer', 'CommonController@getSamityOfFieldOfficer')->name('getSamityOfFieldOfficer');
        Route::any('/member-of-samity', 'CommonController@getMemberOfSamity')->name('getMemberOfSamity');

        // Route::any('/monthendofbranch','CommonController@getMonthEndOfBranch')->name('getMonthEndOfBranch');

        Route::any('/product-of-branch', 'CommonController@getProductOfBranch')->name('getProductOfBranch');
        Route::any('/getLoanProducts', 'CommonController@getLoanProducts')->name('getLoanProducts');
        Route::any('/getSavingsProducts', 'CommonController@getSavingsProducts')->name('getSavingsProducts');

        //get banck ledger for mfn
        Route::any('/getBankLedgerId', 'CommonController@getBankLedgerId');

        ## Get Credit Officer
        Route::any('/getCreditOfficers', 'CommonController@getCreditOfficerOfBranch')->name('getCreditOfficerOfBranch');

    });

    Route::group(['namespace' => 'Samity'], function () {
        //------------------------Route For Samity --------//

        Route::group(['prefix' => 'samity'], function () {
            Route::any('/', 'SamityController@index');
            Route::get('/view/{samityId}', 'SamityController@viewSamity');
            Route::any('/add', 'SamityController@addSamity');
            Route::any('/edit/{samityId?}', 'SamityController@editSamity');
            Route::post('/delete', 'SamityController@deleteSamity');
        });

        // // ## Samity Field Officer Change Route
        Route::group(['prefix' => 'samityFieldOfficerChange'], function () {
            Route::any('/', 'SamityFieldOfficerChangeController@index');
            Route::get('/view/{samityFOCId}', 'SamityFieldOfficerChangeController@view');
            Route::any('/add', 'SamityFieldOfficerChangeController@add');
            Route::any('/fo_wise_add', 'SamityFieldOfficerChangeController@foWiseadd');
            Route::any('/edit/{samityFOCId?}', 'SamityFieldOfficerChangeController@edit');
            Route::any('/delete', 'SamityFieldOfficerChangeController@delete');
            Route::post('/getSamityInfo', 'SamityFieldOfficerChangeController@getSamityInfo');
            Route::any('/getFieldOfficers', 'SamityFieldOfficerChangeController@getFieldOfficers');
            Route::any('/getFieldOfficerInfo', 'SamityFieldOfficerChangeController@getFieldOfficerInfo');

            Route::any('/getCurrentFieldOfficers', 'SamityFieldOfficerChangeController@getCurrentFieldOfficers');

        });

        // // ## Samity Day Change Route
        Route::group(['prefix' => 'samityDayChange'], function () {
            Route::any('/', 'SamityDayChangeController@index');
            Route::get('/view/{samityDC}', 'SamityDayChangeController@view');
            Route::any('add', 'SamityDayChangeController@add');
            Route::any('edit/{samityDC?}', 'SamityDayChangeController@edit');
            Route::post('getCurrentSamityDay', 'SamityDayChangeController@getCurrentSamityDay');
            Route::post('/delete', 'SamityDayChangeController@delete');
        });

        // // ## Samity Closing Route
        Route::group(['prefix' => 'samityclosing'], function () {
            Route::any('/', 'SamityClosingController@index');
            Route::match(['get', 'post'], 'add', 'SamityClosingController@add');
            Route::post('/delete', 'SamityClosingController@delete');
        });
    });

    Route::group(['namespace' => 'Member'], function () {

        // // ## Member Route
        Route::group(['prefix' => 'member'], function () {
            Route::any('/', 'MemberController@index');
            Route::any('add', 'MemberController@add');
            Route::any('edit/{id}', 'MemberController@edit');
            Route::get('view/{memberId}', 'MemberController@view');
            Route::get('view', 'MemberController@view');

            Route::post('delete', 'MemberController@delete');

            Route::any('saveingsDetails', 'MemberController@showsavingsDetails')->name('saveingsDetails');
            Route::any('loanDetails', 'MemberController@showloanDetails')->name('loanDetails');
            /* get admission related information on add or update */
            Route::post('getData', 'MemberController@getData');
        });

        // // ## Member Samity Transfe Route
        Route::group(['prefix' => 'memberSamityTransfer'], function () {
            Route::any('/', 'MemberSamityTransferController@index');
            Route::any('add', 'MemberSamityTransferController@add');
            Route::get('view/{id}', 'MemberSamityTransferController@view');
            Route::any('edit/{id}', 'MemberSamityTransferController@edit');
            Route::any('getData', 'MemberSamityTransferController@getData');
            Route::post('delete', 'MemberSamityTransferController@delete');
        });

        // // ## Member Primary Product Transfer Route
        Route::group(['prefix' => 'memberPrimaryProductTransfer'], function () {

            Route::any('/', 'MemberPrimaryProductTransferController@index');
            Route::any('add', 'MemberPrimaryProductTransferController@add');
            Route::get('view/{id}', 'MemberPrimaryProductTransferController@view');
            Route::any('edit/{id}', 'MemberPrimaryProductTransferController@edit');
            Route::post('delete', 'MemberPrimaryProductTransferController@delete');
            Route::post('/getData', 'MemberPrimaryProductTransferController@getData');
        });

        // // ## Member Closing Route
        Route::group(['prefix' => 'memberclosing'], function () {
            Route::any('/', 'MemberClosingController@index');
            Route::any('add', 'MemberClosingController@add');
            Route::get('view/{id}', 'MemberClosingController@view');
            Route::post('delete/{id}', 'MemberClosingController@delete');
        });

        // // ## Member blacklist Route
        Route::group(['prefix' => 'member_blacklist'], function () {
            Route::any('/', 'BlacklistController@index');
            Route::any('add', 'BlacklistController@add');
            Route::any('add/{id}', 'BlacklistController@addselected');

            Route::get('view/{id}', 'BlacklistController@view');
            Route::post('delete/{id}', 'BlacklistController@delete');
        });

        ## Member Mamla Route
        Route::group(['prefix' => 'member_mamla'], function () {
            Route::any('/', 'MemberMamlaController@index');
            Route::any('add', 'MemberMamlaController@add');
            Route::any('edit/{id}', 'MemberMamlaController@edit');
            Route::get('view/{id}', 'MemberMamlaController@view');
            Route::get('view', 'MemberMamlaController@view');
            Route::post('delete/{id}', 'MemberMamlaController@delete');
            Route::post('/getData', 'MemberMamlaController@getData');
        });

    });

    Route::group(['namespace' => 'Savings', 'prefix' => 'savings'], function () {

        Route::group(['prefix' => 'account'], function () {
            Route::any('/', 'SavingsAccountController@index');
            Route::any('/add', 'SavingsAccountController@add');
            Route::any('/edit/{id}', 'SavingsAccountController@edit');
            Route::any('/view', 'SavingsAccountController@view');
            Route::any('/view/{id}', 'SavingsAccountController@view');
            Route::post('/delete', 'SavingsAccountController@delete');
            Route::post('/getData', 'SavingsAccountController@getData');
            Route::post('/mipLedger', 'SavingsAccountController@mipLedger')->name('mipLedger');
            Route::post('/mipReceipt', 'SavingsAccountController@mipReceipt')->name('mipReceipt');
            Route::post('/ssReceipt', 'SavingsAccountController@ssReceipt')->name('ssReceipt');
        });

        Route::group(['prefix' => 'deposit'], function () {
            Route::any('/', 'DepositController@index');
            Route::any('/add', 'DepositController@add');
            Route::any('/edit/{id}', 'DepositController@edit');
            Route::any('/view', 'DepositController@view');
            Route::any('/view/{id}', 'DepositController@view');
            Route::post('/delete', 'DepositController@delete');
            Route::post('/getData', 'DepositController@getData');
        });

        Route::group(['prefix' => 'withdraw'], function () {
            Route::any('/', 'WithdrawController@index');
            Route::any('/add', 'WithdrawController@add');
            Route::any('/edit/{id}', 'WithdrawController@edit');
            Route::any('/view', 'WithdrawController@view');
            Route::any('/view/{id}', 'WithdrawController@view');
            Route::post('/delete', 'WithdrawController@delete');
            Route::post('/getData', 'WithdrawController@getData');
        });
        Route::group(['prefix' => 'closing'], function () {
            Route::any('/', 'ClosingController@index')->name('closingsDatatable');
            Route::any('closingaccountDetails', 'ClosingController@closingaccountDetails')->name('closingaccountDetails');
            Route::any('/add', 'ClosingController@add');
            Route::any('/view/{id}', 'ClosingController@view');
            Route::post('/delete', 'ClosingController@delete');
            Route::post('/getData', 'ClosingController@getData');
        });

        Route::group(['prefix' => 'status'], function () {
            Route::any('/', 'StatusController@index');
            Route::any('SavingsStatusDetails', 'StatusController@SavingsStatusDetails')->name('SavingsStatusDetails');
            Route::any('/view/{id}/{date?}', 'StatusController@view');
        });

        // Route::group(['prefix' => 'interest_payment'], function () {
        //     Route::any('/', 'SavingInterestController@index');
        //     Route::any('/generate', 'SavingInterestController@generate');
        //     Route::any('/add', 'SavingInterestController@add')->name('payInterest');
        //     Route::any('/delete', 'SavingInterestController@delete');
        //     Route::any('/view', 'SavingInterestController@view');
        //     Route::post('/getData', 'SavingInterestController@getData');
        // });

        Route::group(['prefix' => 'interest_payment'], function () {

            Route::get('/', function () {
                return view('MFN/Savings/InterestPayment/index');
            });

            Route::post('/', 'SavingInterestController@index'); // api

            Route::get('/add', function () {
                return view('MFN/Savings/InterestPayment/add');
            });

            Route::post('/add', 'SavingInterestController@add')->name('payInterest');

            Route::any('/generate/api', 'SavingInterestController@generate');

            Route::any('/view/{id}', function ($id) {
                return view('MFN/Savings/InterestPayment/view', compact('id'));
            });
            Route::any('/view/{id}/api', 'SavingInterestController@view');

            Route::any('/delete/{id}', 'SavingInterestController@delete');
            Route::post('/getData', 'SavingInterestController@getData');
        });
    });

    Route::group(['namespace' => 'Savings', 'prefix' => 'savings_ob'], function () {
        Route::any('/', 'SavingsOBController@index');
        Route::any('SavingsOBDetails', 'SavingsOBController@SavingsStatusDetails')->name('SavingsOBDetails');
        // Route::group(['prefix' => 'status'], function () {
        //     Route::any('/', 'StatusController@index');
        //     Route::any('SavingsStatusDetails', 'StatusController@SavingsStatusDetails')->name('SavingsStatusDetails');
        //     Route::any('/view/{id}', 'StatusController@view');
        // });
    });

    // // ## Share account Route
    Route::group(['namespace' => 'Share', 'prefix' => 'share_acc'], function () {
        Route::any('/', 'ShareAccountController@index');
        Route::any('/add', 'ShareAccountController@add');
        Route::any('/edit/{id}', 'ShareAccountController@edit');
        Route::any('/view/{id}', 'ShareAccountController@view');
        Route::post('/delete', 'ShareAccountController@delete');
        Route::post('/getData', 'ShareAccountController@getData');
    });

    // // ## Share withdraw Route
    Route::group(['namespace' => 'Share', 'prefix' => 'share_withdraw'], function () {
        Route::any('/', 'ShareWithdrawController@index');
        Route::any('/add', 'ShareWithdrawController@add');
        Route::any('/edit/{id}', 'ShareWithdrawController@edit');
        Route::any('/view/{id}', 'ShareWithdrawController@view');
        Route::post('/delete', 'ShareWithdrawController@delete');
        Route::post('/getData', 'ShareWithdrawController@getData');
    });

    Route::group(['namespace' => 'Loan'], function () {

        // // ## Regular Loan Route
        Route::group(['prefix' => 'regularloan'], function () {
            Route::any('/', 'LoanController@index');
            Route::any('/add', 'LoanController@add');
            Route::any('/view', 'LoanController@view');
            Route::any('/view/{id}', 'LoanController@view');
            Route::any('/edit/{id}', 'LoanController@edit');
            Route::post('/delete', 'LoanController@delete');
            Route::any('getData', 'LoanController@getData');
            Route::any('getCashBankBalance', 'LoanController@getCashBankBalance');
        });

        // // ## Onetime Loan Route
        Route::group(['prefix' => 'oneTimeLoan'], function () {
            Route::any('/', 'LoanController@index');
            Route::any('/add', 'LoanController@add');
            Route::any('/view/{id}', 'LoanController@view');
            Route::any('/edit/{id}', 'LoanController@edit');
            Route::post('/delete', 'LoanController@delete');
            Route::any('getData', 'LoanController@getData');
        });

        // // ## Loan Reschedule
        Route::group(['prefix' => 'loanReschedule'], function () {
            Route::any('/', 'LoanRescheduleController@index');
            Route::any('add', 'LoanRescheduleController@add');
            Route::any('edit/{id}', 'LoanRescheduleController@edit');
            Route::post('delete', 'LoanRescheduleController@delete');
            Route::post('getData', 'LoanRescheduleController@getData');
        });

        Route::group(['prefix' => 'massLoanReschedule'], function () {
            Route::any('/', 'MassLoanRescheduleController@index');
            Route::any('/add', 'MassLoanRescheduleController@add');
            Route::any('/edit/{id}', 'MassLoanRescheduleController@edit');
            Route::any('/delete', 'MassLoanRescheduleController@delete');
            Route::any('/view/{id}', 'MassLoanRescheduleController@view');
            Route::post('/getData', 'MassLoanRescheduleController@getData');
        });

        // // ## Regular Loan Transaction Route
        Route::group(['prefix' => 'regularloanTransaction'], function () {
            Route::any('/', 'LoanTransactionController@index');
            Route::match(['get', 'post'], 'add', 'LoanTransactionController@add');
            Route::any('/edit/{id}', 'LoanTransactionController@edit');
            Route::post('/delete', 'LoanTransactionController@delete');
            Route::any('/view', 'LoanTransactionController@view');
            // Route::any('/view/{id}', 'LoanTransactionController@viewPage');
            Route::any('/view/{id}', 'LoanTransactionController@view');
            Route::any('getData', 'LoanTransactionController@getData');
        });

        // // ## Over Due Loan Transaction Route
        Route::group(['prefix' => 'overdueLoanRecovery'], function () {
            Route::any('/', 'OverdueLoanRecovery@index');
            Route::match(['get', 'post'], 'add', 'OverdueLoanRecovery@add');
            Route::any('/edit/{id}', 'OverdueLoanRecovery@edit');
            Route::post('/delete', 'OverdueLoanRecovery@delete');
            Route::any('/view', 'OverdueLoanRecovery@view');
            Route::any('/view/{id}', 'OverdueLoanRecovery@view');
            Route::any('getData', 'OverdueLoanRecovery@getData');
        });

        /*
         * One time loan transaction route
         */
        Route::group(['prefix' => 'oneTimeLoanTransaction'], function () {
            Route::any('/', 'LoanTransactionController@index');
            Route::match(['get', 'post'], 'add', 'LoanTransactionController@add');
            Route::any('/edit/{id}', 'LoanTransactionController@edit');
            Route::post('/delete', 'LoanTransactionController@delete');
            Route::any('/view', 'LoanTransactionController@view');
            Route::any('getData', 'LoanTransactionController@getData');
        });

        /*
         * Loan Rebate route
         */
        Route::group(['prefix' => 'loanRebate'], function () {
            Route::any('/', 'RebateController@index');
            Route::match(['get', 'post'], 'add', 'RebateController@add');
            Route::any('/edit/{id}', 'RebateController@edit');
            Route::post('/delete', 'RebateController@delete');
            Route::any('/view', 'RebateController@view');
            Route::any('getData', 'RebateController@getData');
        });

        /*
         * Write off route
         */
        Route::group(['prefix' => 'writeOff'], function () {
            Route::any('eligibleList', 'WriteOffController@eligibleList');
            Route::any('/', 'WriteOffController@index');
            Route::match(['get', 'post'], 'add/{id}', 'WriteOffController@add');
            Route::post('/delete', 'WriteOffController@delete');
            Route::any('getData', 'WriteOffController@getData');
        });

        /*
         * Loan Waiver route
         */
        Route::group(['prefix' => 'loanWaiver'], function () {
            Route::any('/', 'WaiverController@index');
            Route::match(['get', 'post'], 'add', 'WaiverController@add');
            Route::post('/delete', 'WaiverController@delete');
            Route::any('/view', 'WaiverController@view');
            Route::any('getData', 'WaiverController@getData');
        });

        /*
         * Loan adjustment route
         */
        Route::group(['prefix' => 'loanAdjustment'], function () {
            Route::any('/', 'AdjustmentController@index');
            Route::match(['get', 'post'], 'add', 'AdjustmentController@add');
            Route::any('/edit/{id}', 'AdjustmentController@edit');
            Route::post('/delete', 'AdjustmentController@delete');
            Route::post('/approve', 'AdjustmentController@approve');
            Route::any('/view', 'AdjustmentController@view');
            Route::any('/view/{id}', 'AdjustmentController@view');
            Route::any('getData', 'AdjustmentController@getData');
        });

        /*
         * Write off Collection route
         */
        Route::group(['prefix' => 'writeOffCollection'], function () {
            Route::any('/list', 'WriteOffCollectionController@list');
            Route::any('/', 'WriteOffCollectionController@index');
            Route::match(['get', 'post'], 'add/{id}', 'WriteOffCollectionController@add');
            Route::any('/edit/{id}', 'WriteOffCollectionController@edit');
            Route::any('/view', 'WriteOffCollectionController@view');
            Route::post('/delete', 'WriteOffCollectionController@delete');
            Route::any('getData', 'WriteOffCollectionController@getData');
        });
    });

    Route::group(['namespace' => 'GConfig'], function () {
        // // ## Working Area Route
        Route::group(['prefix' => 'workingarea'], function () {
            Route::any('/', 'WorkingAreaController@index')->name('workingareaDatatable');
            Route::get('/view/{wareaId}', 'WorkingAreaController@view');
            Route::match(['get', 'post'], 'add', 'WorkingAreaController@add');
            Route::match(['get', 'post'], '/edit/{wareaId?}', 'WorkingAreaController@edit');
            Route::post('/delete', 'WorkingAreaController@delete');
        });

        // // ## Field Officer Route
        Route::group(['prefix' => 'fieldofficer'], function () {
            Route::get('/', 'FieldOfficerController@index');
            Route::match(['get', 'post'], 'add', 'FieldOfficerController@add');
            Route::match(['get', 'post'], '/edit/{fieldOfficerId?}', 'FieldOfficerController@edit');
        });

        // // ## Savings Products Route
        Route::group(['prefix' => 'savingsProduct'], function () {
            Route::any('/', 'SavingsProductController@index');
            Route::any('/view/{savingsProductId?}', 'SavingsProductController@view');
            Route::any('add', 'SavingsProductController@add');
            Route::any('/edit/{savingsProductId?}', 'SavingsProductController@edit');
            Route::post('/delete/{savingsProductId?}', 'SavingsProductController@delete');
        });

        // // ## Savings Product's Interest Route
        Route::group(['prefix' => 'savingsProduct/interest'], function () {
            Route::any('add/{productId?}', 'SavingsProductInterestController@add');
            Route::get('view/{inetrestId}', 'SavingsProductInterestController@view');
        });

        // // ## Funding Organizations Route
        Route::group(['prefix' => 'fundingOrganization'], function () {
            Route::any('/', 'FundingOrganizationController@index');
            Route::match(['get', 'post'], 'add', 'FundingOrganizationController@add');
            Route::match(['get', 'post'], '/edit/{id?}', 'FundingOrganizationController@edit');
            Route::post('/delete', 'FundingOrganizationController@delete');
        });

        // // ## Loan Products Route
        Route::group(['prefix' => 'loanProducts'], function () {
            Route::any('/', 'LoanProductController@index');
            Route::any('view/{loanProductId}', 'LoanProductController@view');
            Route::match(['get', 'post'], 'add', 'LoanProductController@add');
            Route::match(['get', 'post'], '/edit/{loanProductId?}', 'LoanProductController@edit');
            Route::post('/delete', 'LoanProductController@delete');
        });

        // // ## Loan Products Interest Rates
        Route::group(['prefix' => 'loanProducts/interest'], function () {
            Route::any('add/{productId?}', 'LoanProductInterestController@add');
            Route::post('getProductFrequencyWiseInstallments', 'LoanProductInterestController@getProductFrequencyWiseInstallments');
        });

        // // ## Loan Product Category Route
        Route::group(['prefix' => 'loanProductCategory'], function () {
            Route::any('/', 'LoanProductCategoryController@index')->name('loanProdCatDatatable');
            Route::any('delete/{loanProdCatId}', 'LoanProductCategoryController@delete');
            Route::get('/view/{loanProdCatId}', 'LoanProductCategoryController@view');
            Route::match(['get', 'post'], 'add', 'LoanProductCategoryController@add');
            Route::match(['get', 'post'], '/edit/{loanProdCatId?}', 'LoanProductCategoryController@edit');
        });

        // // ## Product Assign Route
        Route::group(['prefix' => 'branchProduct'], function () {
            Route::any('/', 'BranchProductController@index')->name('prodAssignDatatable');
            Route::any('delete/{prodAssignId}', 'BranchProductController@delete');
            Route::get('/view/{prodAssignId}', 'BranchProductController@view');
            Route::match(['get', 'post'], 'add', 'BranchProductController@add');
            Route::match(['get', 'post'], '/edit/{prodAssignId?}', 'BranchProductController@edit');
        });

        // // ## PKSF Funds Route
        Route::group(['prefix' => 'pksfFunds'], function () {
            Route::any('/', 'PksfFundsController@index')->name('pksfFundsDatatable');
            Route::match(['get', 'post'], 'add', 'PksfFundsController@add');
            Route::match(['get', 'post'], '/edit/{pksfFundId?}', 'PksfFundsController@edit');
            Route::post('/delete', 'PksfFundsController@delete');
        });

        // // ## Auto Voucher Config
        Route::group(['prefix' => 'autoVoucherConfig'], function () {
            Route::any('/', 'AutoVoucherConfigurationController@add');
            Route::any('/loadDiv', 'AutoVoucherConfigurationController@makeConfigDiv');
        });

        // // ## Opening Loan Information Route
        Route::group(['prefix' => 'openingLoanInfo'], function () {
            Route::any('/', 'OpeningLoanInfoController@index')->name('openingLoanDatatable');
            Route::get('/view/{loanId}', 'OpeningLoanInfoController@view');
            Route::match(['get', 'post'], 'add', 'OpeningLoanInfoController@add');
            Route::match(['get', 'post'], '/edit/{loanId?}', 'OpeningLoanInfoController@edit');
            Route::post('/delete/{loanId}', 'OpeningLoanInfoController@delete');
        });

        // // ## Opening Savings Information Route
        Route::group(['prefix' => 'openingSavingsInfo'], function () {
            Route::any('/', 'OpeningSavingsController@index')->name('openingSavingsDatatable');
            Route::get('/view/{savingsId}', 'OpeningSavingsController@view');
            Route::match(['get', 'post'], 'add', 'OpeningSavingsController@add');
            Route::match(['get', 'post'], '/edit/{savingsId?}', 'OpeningSavingsController@edit');
            Route::post('/delete/{savingsId}', 'OpeningSavingsController@delete');
        });

        // // ## Global Cnfiguration Route
        Route::group(['prefix' => 'global_config'], function () {
            Route::any('/', 'SettingController@scriptIndex');
        });

        // // ## Notice Route
        Route::group(['prefix' => 'notice'], function () {
            Route::any('/', 'NoticeController@index')->name('noticeDatatable');
            Route::match(['get', 'post'], 'add', 'NoticeController@add');
            Route::match(['get', 'post'], '/edit/{noticeId?}', 'NoticeController@edit');
            Route::post('/delete/{noticeId}', 'NoticeController@delete');
        });

        ## Processing Fee Route
        Route::group(['prefix' => 'config_photographic'], function () {

            Route::any('/', 'PhotograpicFeeController@index')->name('phFeeDatatable');
            Route::any('add', 'PhotograpicFeeController@add');
            Route::any('edit/{id}', 'PhotograpicFeeController@edit');
            Route::any('view/{id}', 'PhotograpicFeeController@view');

            Route::any('delete/{id}', 'PhotograpicFeeController@delete');
            Route::any('publish', 'PhotograpicFeeController@isActive');
            Route::any('destroy/{id}', 'PhotograpicFeeController@destroy');
            Route::post('getData', 'PhotograpicFeeController@getData');

        });

        ## Provision Configuration
        Route::group(['prefix' => 'provision_config'], function () {
            Route::any('/', 'ProvisionConfigController@index');
            Route::any('/add', 'ProvisionConfigController@add');
            Route::any('edit/{id}', 'ProvisionConfigController@edit');
            Route::any('view/{id}', 'ProvisionConfigController@view');
            Route::any('/delete', 'ProvisionConfigController@delete');
            Route::any('/add/getinfo', 'ProvisionConfigController@getInfo');
        });

        ## Independent Branch
        Route::group(['prefix' => 'independent_branch'], function () {
            Route::any('/', 'IndependentBranchController@add');
            // Route::any('/add', 'ProvisionConfiguration@add');
            // Route::any('edit/{id}', 'ProvisionConfiguration@edit');
            // Route::any('/delete', 'ProvisionConfiguration@delete');
        });

        ## Loan Purpose
        Route::group(['prefix' => 'loan_purpose'], function () {
            Route::any('/', 'LoanPurposeController@index');
            Route::any('/add', 'LoanPurposeController@add');
            Route::any('edit/{id}', 'LoanPurposeController@edit');
            Route::any('/delete', 'LoanPurposeController@delete');
        });

        ## Court Entry
        Route::group(['prefix' => 'court'], function () {
            Route::any('/', 'CourtController@index');
            Route::any('/add', 'CourtController@add');
            Route::any('edit/{id}', 'CourtController@edit');
            Route::any('/delete', 'CourtController@delete');
        });

        ## Advocate Entry
        Route::group(['prefix' => 'advocate'], function () {
            Route::any('/', 'AdvocateController@index');
            Route::any('/add', 'AdvocateController@add');
            Route::any('edit/{id}', 'AdvocateController@edit');
            Route::any('/delete', 'AdvocateController@delete');
            Route::any('/getData', 'AdvocateController@getData');
        });

        ## MFN Settings
        Route::group(['prefix' => 'mfnSettings'], function () {
            Route::any('/', 'MfnSettingsController@add');
        });
    });

    Route::group(['namespace' => 'ProductInterest'], function () {

        // // ## Product Interest Rate
        Route::group(['prefix' => 'productInterest'], function () {
            Route::any('/', 'ProductInterestController@index');
            Route::get('/view/{prodInterestId}', 'ProductInterestController@view');
            Route::match(['get', 'post'], '/edit/{prodInterestId?}', 'ProductInterestController@edit');
        });
    });

    // // ## Day End  Route
    Route::group(['namespace' => 'Process'], function () {

        Route::group(['prefix' => 'day_end'], function () {
            Route::any('/', 'MfnDayEndController@index');

            // Route::post('end', 'MfnDayEndController@end');
            // Route::any('delete/{id}', 'MfnDayEndController@delete');
            // Route::post('mfndayendDatatable', 'MfnDayEndController@mfndayendDatatable')->name('mfndayendDatatable');
            Route::any('mfndayendexecute', 'MfnDayEndController@end')->name('mfndayendexecute');
            Route::any('delete', 'MfnDayEndController@delete')->name('deletemfndayend');
        });
        Route::group(['prefix' => 'month_end'], function () {
            Route::any('/', 'MfnMonthEndController@index')->name('mfnmonthendDatatable');

            Route::get('checkDayEndData', 'MfnMonthEndController@checkDayEndData');
            Route::post('execute', 'MfnMonthEndController@execute');
            Route::any('delete', 'MfnMonthEndController@delete');
            //
            //
            // Route::get('delete', 'AccMonthEndController@isDelete');
            // Route::post('end', 'MfnDayEndController@end');

            // Route::post('mfndayendDatatable', 'MfnDayEndController@mfndayendDatatable')
            // Route::any('mfndayendexecute', 'MfnDayEndController@end')->name('mfndayendexecute');
            // Route::any('deletemfndayend', 'MfnDayEndController@delete')->name('deletemfndayend');

        });
        // script
        Route::group(['prefix' => 'month_end_summary'], function () {
            Route::any('/', 'GenerateMonthEndSummaryController@scriptIndex');
            Route::any('/monthendofbranch', 'GenerateMonthEndSummaryController@getMonthEndOfBranch')->name('getMonthEndOfBranch');
            Route::any('/monthEndSummaryData', 'GenerateMonthEndSummaryController@monthEndSummaryData')->name('monthEndSummaryData');
            // Route::any('/fixFirstRepayDate', 'GenerateMonthEndSummaryController@fixFirstRepayDate')->name('fixFirstRepayDate');
        });

        Route::group(['prefix' => 'generate_auto_voucher'], function () {
            Route::any('/', 'GenerateAutoVoucherController@scriptIndex');
            Route::any('/datesofbranch', 'GenerateAutoVoucherController@getDatesOfBranch')->name('getDatesOfBranch');
            Route::any('/datesDataofbranch', 'GenerateAutoVoucherController@datesDataofBranch')->name('branchDatesData');
        });
        Route::group(['prefix' => 'year_end'], function () {
            Route::any('/', 'MfnYearEndController@index');

            Route::post('execute', 'MfnYearEndController@execute');
            Route::get('checkMonthEndData', 'MfnYearEndController@checkMonthEndData');
            Route::get('delete', 'MfnYearEndController@isDelete');
        });

        Route::group(['prefix' => 'trnsc_auth'], function () {
            Route::any('/', 'TransactionAuthController@index');

            Route::post('transactionauthDatatable', 'TransactionAuthController@transactionauthDatatable')->name('transactionauthDatatable');
            Route::any('ajaxTransactionAuth', 'TransactionAuthController@ajaxTransactionAuth')->name('ajaxTransactionAuth');
            Route::any('/loadData', 'TransactionAuthController@loadData')->name('transactionAuthLoadData');
        });

        Route::group(['prefix' => 'auto_pro'], function () {
            Route::any('/', 'AutoProcessController@index')->name('DatatabledetailsSamity');

            Route::any('/add/{samityId?}/{autoProcessDate?}', 'AutoProcessController@add');
            Route::any('/edit/{samityId?}/{autoProcessDate?}', 'AutoProcessController@edit');
        });

        Route::group(['prefix' => 'trnsc_unauth'], function () {
            Route::any('/', 'TransactionUnauthController@index');

            Route::post('transactionUnauthDatatable', 'TransactionUnauthController@transactionUnauthDatatable')->name('transactionUnauthDatatable');

            Route::get('/view/{id?}/{date?}', 'TransactionUnauthController@view');

            Route::any('ajaxTransactionUnauth', 'TransactionUnauthController@ajaxTransactionUnauth')->name('ajaxTransactionUnauth');

            Route::any('ajaxTransactionUnauthMultiSamity', 'TransactionUnauthController@transactionUnauth')->name('transactionUnauth');
        });

        Route::group(['prefix' => 'pass_book'], function () {
            Route::any('/', 'PassBookController@index');
            Route::post('/store', 'PassBookController@store');
        });

        // Route::group(['prefix' => 'generate_interest_provision'], function () {
        //     Route::any('/', 'GenerateInterestProvisionController@index');
        //     Route::any('/add', 'GenerateInterestProvisionController@add');
        //     Route::any('/delete', 'GenerateInterestProvisionController@delete');
        //     Route::any('/view', 'GenerateInterestProvisionController@view');
        //     Route::post('/getData', 'GenerateInterestProvisionController@getData');
        // });

        Route::group(['prefix' => 'generate_interest_provision'], function () {

            Route::get('/', function () {
                return view('MFN/GenerateInterestProvision/index');
            });

            Route::post('/', 'GenerateInterestProvisionController@index'); // api

            Route::any('/add/', function () {
                return view('MFN/GenerateInterestProvision/add');
            });
            Route::any('/insert/api', 'GenerateInterestProvisionController@add');

            Route::any('/view/{id}', function ($id) {
                return view('MFN/GenerateInterestProvision/view', compact('id'));
            });
            Route::any('/view/{id}/api', 'GenerateInterestProvisionController@view');

            Route::any('/delete/{id}', 'GenerateInterestProvisionController@delete');
            Route::post('/getData', 'GenerateInterestProvisionController@getData');
            Route::post('/getSavingsProduct', 'GenerateInterestProvisionController@getSavingsProduct');
        });

        Route::group(['prefix' => 'day_back_script'], function () {
            Route::any('/', 'MfnDayBackController@index');
            Route::post('/getinfo', 'MfnDayBackController@getInfo');
            Route::post('/day_back', 'MfnDayBackController@dayBack');
            Route::any('delete', 'MfnDayEndController@delete')->name('deletemfndayend');
        });
    });

    // // ## Report Route C

    Route::group(['namespace' => 'Reports', 'prefix' => 'reports'], function () {
        Route::group(['namespace' => 'Register'], function () {
            Route::group(['namespace' => 'Regular'], function () {
                Route::group(['prefix' => 'top_sheet'], function () {
                    Route::any('/', 'TopSheet@index');
                    Route::any('/loadData', 'TopSheet@loadDate');
                    Route::any('/loadData/getDefaulters', 'TopSheet@getDefaulters');

                    // Route::any('/add', function () {
                    //     return view('MFN/Reports.TopSheet.reportBody/add');
                    // });
                });

            });
        });
        Route::group(['namespace' => 'SavingsReport'], function () {
            Route::group(['namespace' => 'SavingsRegister'], function () {
                Route::any('savingsRegister', 'SavingsRegisterReportController@getSavingsRegister')->name('savingsRegisterDataTable');
                Route::any('savingsRegisterReport', 'SavingsRegisterReportController@loadReportData')->name('savingsRegisterReportTable');
            });
            Route::group(['namespace' => 'SavingsInterest'], function () {
                Route::any('savingsInterest', 'SavingsInterestReportController@index');
                Route::any('savingsInterestReport', 'SavingsInterestReportController@loadReportData');
            });
        });
        Route::group(['namespace' => 'RegularGeneralReports'], function () {
            Route::any('memberLedger', 'MemberLedgerReportController@getmemberLedger')->name('memberLedgerDataTable');
        });

        /*
         * waiverReport, writeOff, rebateReport reports
         */
        Route::group(['namespace' => 'RegularGeneralReports'], function () {

            // Route::any('waiverReport', 'WaiverReportController@getWaiverData');
            Route::group(['prefix' => 'waiverReport'], function () {
                Route::any('/', 'WaiverReportController@getWaiverData');
                Route::any('/loadData', 'WaiverReportController@getWaiverData');
            });
            Route::group(['prefix' => 'writeOffReport'], function () {
                Route::any('/', 'WriteOffReportController@getWriteOffData');
                Route::any('/loadData', 'WriteOffReportController@getWriteOffData');
            });
            // Route::any('writeOffReport', 'WaiverReportController@getWriteOffData');
            Route::group(['prefix' => 'rebateReport'], function () {
                Route::any('/', 'RebateReportController@getRebateData');
                Route::any('/loadData', 'RebateReportController@getRebateData');
            });
            // Route::any('rebateReport', 'WaiverReportController@getRebateData');
            // Route::any('waiverGetData', 'WaiverReportController@getData');

            /*
             * Preriodic collection report component wise
             */
            Route::group(['prefix' => 'periodic_collection_component_wise'], function () {
                Route::any('/', 'PccoReportController@index');
                Route::post('/getData', 'PccoReportController@getData');
                // Route::any('/loadReportData', 'PccoReportController@loadReportData');
                Route::any('/loadReportDataSamityWise', 'PccoReportController@loadReportDataSamityWise');
                Route::any('/loadReportDataMemberWise', 'PccoReportController@loadReportDataMemberWise');
            });

            Route::group(['prefix' => 'periodic_report'], function () {
                Route::any('/', 'PeriodicReportController@index');
                Route::post('/getData', 'PeriodicReportController@getData');
                Route::any('/loadData', 'PeriodicReportController@loadData');
            });

            // Route::any('PCRComponentWise', 'PCRComponentWiseReport@getPCRComponentWiseFilterPart');
            // Route::any('PCRCWReport', 'PCRComponentWiseReport@getPCRComponentWiseViewPart');
            // Route::any('pcrGetData', 'PCRComponentWiseReport@getData');

            /*
             * daily collection component wise report route
             */
            Route::any('dailyCollectionComponentWise', 'DailyCollectionComponentWiseReportController@getDailyCollectionCompWiseFilterPart');
            Route::any('dailyCollectionComponentWiseTablePart', 'DailyCollectionComponentWiseReportController@getDailyCollectionCompWiseTablePart');
            Route::any('dcGetData', 'DailyCollectionComponentWiseReportController@getData');

            # New collection sheet
            Route::group(['prefix' => 'sheet_for_collection'], function () {
                Route::any('/{sheetFor}', function ($sheetFor = null) {
                    return view('MFN/Reports/RegularGeneralReports/CollectionSheet/index', compact('sheetFor'));
                });
                Route::any('/{sheetFor}/api', 'CollectionSheetController@allCollectionSheet');
                Route::any('/{sheetFor}/getData', 'CollectionSheetController@getData');

            });

            # old collection sheet
            Route::group(['prefix' => 'collection_sheet'], function () {
                // Route::any('/', 'back_09092021_CollectionSheetController@index');
                // Route::any('/loadRreport', 'back_09092021_CollectionSheetController@printReport');
                // Route::any('/getData', 'back_09092021_CollectionSheetController@getData');

                Route::any('/', 'back_06112021_CollectionSheetController@index');
                Route::any('/loadRreport', 'back_06112021_CollectionSheetController@printReport');
                Route::any('/getData', 'back_06112021_CollectionSheetController@getData');

                // Route::any('/', 'MfnSamityMonthySavingsLoanController@index');
                // Route::any('mfnReportgSamity', 'MfnSamityMonthySavingsLoanController@getDataSamity')->name('mfnReportgSamity');
                // Route::any('/loadMfnCollectionReport', 'MfnSamityMonthySavingsLoanController@ShowReport');
                // Route::any('mfnReportgSamitygetYears', 'MfnSamityMonthySavingsLoanController@getDataYears')->name('mfnReportgSamitygetYears');
            });

            Route::group(['prefix' => 'collect_sheet'], function () {
                Route::any('/', 'CollectionSheetForSavLoanController@index');
                Route::any('/loadReport', 'CollectionSheetForSavLoanController@loadReport');
                // Route::any('/getData', 'back_06112021_CollectionSheetController@getData');
            });

            Route::group(['prefix' => 'day_basis_collection_sheet'], function () {
                Route::any('/', 'DayBasisCollectionSheetController@index');
                Route::any('/loadRreport', 'DayBasisCollectionSheetController@loadRreport');
                Route::any('/getData', 'DayBasisCollectionSheetController@getData');
            });
            Route::group(['prefix' => 'daily_collection_sheet'], function () {
                Route::any('/', 'DailyCollectionSheetController@index');
                Route::any('/loadRreport', 'DailyCollectionSheetController@printReport');
            });
        });

        Route::group(['namespace' => 'Others', 'prefix' => 'member_migration_balance'], function () {
            Route::any('/', 'MfnMemberMigrationBalanceController@index');
            Route::any('/print_report', 'MfnMemberMigrationBalanceController@printReport');
            Route::any('/getData', 'MfnMemberMigrationBalanceController@getData');
        });

        Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'branch_manager_report'], function () {
            Route::any('/', 'BranchManagerReportController@index');
            Route::post('getData', 'BranchManagerReportController@getData');
        });

        Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'co_wise_report'], function () {
            Route::any('/', 'COWiseReportController@index');
            Route::any('/loadData', 'COWiseReportController@loadData');
            Route::post('getData', 'COWiseReportController@getData');
        });


        // Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'loan_collection_reg'], function () {
        //     Route::any('/', 'MfnLoanCollectionRegisterController@index');
        //     Route::any('/loadData', 'MfnLoanCollectionRegisterController@getData');
        // });

        // Route::group(['namespace' => 'RegisterReport', 'prefix' => 'loan_disburse_reg'], function () {
        //     Route::any('/', 'MfnLoanDisburseRegisterController@index');
        //     Route::any('/loadData', 'MfnLoanDisburseRegisterController@getData');
        // });

        // Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'dual_loanee_info'], function () {
        //     Route::any('/', 'MfnDualLoaneeInformationController@index');
        //     Route::any('/loadData', 'MfnDualLoaneeInformationController@getData');
        // });

        Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'savings_interest_report'], function () {
            Route::any('/', 'SavingsInterestReportController@index');
            Route::any('/loadData', 'SavingsInterestReportController@getData');
        });

        Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'savingsProvision'], function () {
            Route::any('/', 'SavingsProvisionReportController@getFilterPart');
            Route::any('/singleTableView', 'SavingsProvisionReportController@getSingleTableView');
            Route::any('/getData', 'SavingsProvisionReportController@getData');
        });

        Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'advance_loan_reg'], function () {
            Route::any('/', 'AdvanceRegisterReportController@index')->name('advanceRegisterDataTable');
            Route::any('/loadReportData', 'AdvanceRegisterReportController@loadReportData');
            Route::any('/getData', 'AdvanceRegisterReportController@getData');
        });
        Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'member_closing'], function () {
            Route::any('/', 'MemberClosingReportController@index');
            Route::any('/loadData', 'MemberClosingReportController@getData');
        });

        Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'pass_book'], function () {
            Route::any('/', 'PassBookReportController@index');
            Route::any('/loadData', 'PassBookReportController@getData');
        });

        Route::group(['namespace' => 'RegisterReport\Topsheet', 'prefix' => 'admission_reg'], function () {
            Route::any('/', 'AdmissionRegisterReportController@index');
            Route::any('/loadData', 'AdmissionRegisterReportController@getData');
        });

        ##Register Report route
        Route::group(['namespace' => 'RegisterReport'], function () {
            ### -------------General Register--------------------###

            ## Samity
            Route::group(['namespace' => 'General', 'prefix' => 'samity_reg'], function () {
                Route::any('', 'GeneralRegisterReportController@samityReg');
                Route::any('loadData', 'GeneralRegisterReportController@getSamitydata');
            });

            ## Member
            Route::group(['namespace' => 'General', 'prefix' => 'member_reg'], function () {
                Route::any('/', 'GeneralRegisterReportController@memberReg');
                // Route::any('loadData', 'GeneralRegisterReportController@getMemberdata');
            });

            ## Primary Product Transfer
            Route::group(['namespace' => 'General', 'prefix' => 'primary_prod_transfer_reg'], function () {
                Route::any('', 'GeneralRegisterReportController@primaryProductTransferReg');
                Route::any('loadData', 'GeneralRegisterReportController@getPrimaryProductTransferData');
            });

            ## Samity Transfer
            Route::group(['namespace' => 'General', 'prefix' => 'samity_transfer_reg'], function () {
                Route::any('', 'GeneralRegisterReportController@samityTransferReg');
                Route::any('loadData', 'GeneralRegisterReportController@getSamityTransferData');
            });

            ## Member Cancellation
            Route::group(['namespace' => 'General', 'prefix' => 'member_closing'], function () {
                Route::any('', 'GeneralRegisterReportController@memberClosingReg');
                Route::any('loadData', 'GeneralRegisterReportController@getMemberClosingRegData');
            });

            ### ------------- General Register END --------------------###

            ##----------------savings register --------------------###
            ##savings Account
            Route::group(['namespace' => 'Savings', 'prefix' => 'savings_acc_reg'], function () {
                Route::any('/', 'SavingsRegisterReportController@savingsAccReg'); // ajax data table load implemented
                // Route::any('loadData', 'SavingsRegisterReportController@savingsAccRegData');

            });

            ##deposit Register
            Route::group(['namespace' => 'Savings', 'prefix' => 'deposit_reg'], function () {
                Route::any('/', 'SavingsRegisterReportController@DepositReg');
                // Route::any('loadData', 'SavingsRegisterReportController@DepositRegData');

            });

            ##withdraw Register
            Route::group(['namespace' => 'Savings', 'prefix' => 'withdraw_reg'], function () {
                Route::any('/', 'SavingsRegisterReportController@withdrawReg');
                // Route::any('loadData', 'SavingsRegisterReportController@withdrawRegData');

            });
            ##interest_provision Register
            Route::group(['namespace' => 'Savings', 'prefix' => 'interest_provision_reg'], function () {
                Route::any('/', 'SavingsRegisterReportController@interestProvisionReg');
                Route::any('loadData', 'SavingsRegisterReportController@interestProvisionRegData');

            });
            ##interest_payment Register
            Route::group(['namespace' => 'Savings', 'prefix' => 'interest_payment_reg'], function () {
                Route::any('/', 'SavingsRegisterReportController@interestPaymentReg');
                Route::any('loadData', 'SavingsRegisterReportController@interestPaymentRegData');

            });

            /***
             *
             * these two report exchanged kron lagbo
             */
            ##savings_closing Register
            Route::group(['namespace' => 'Savings', 'prefix' => 'savings_closing_reg'], function () {
                Route::any('/', 'SavingsClosingReportController@savingsClosingReg');
                Route::any('/loadData', 'SavingsClosingReportController@savingsClosingRegData');

            });
            ##savings_refund_register Register
            Route::group(['namespace' => 'Savings', 'prefix' => 'savings_refund_register'], function () {
                Route::any('/', 'SavingsRefundRegisterController@savingsRefundRegisterReg');
                Route::any('loadData', 'SavingsRefundRegisterController@savingsRefundRegisterRegData');

            });

            // Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'savings_closing'], function () {

            // });

            // Route::group(['namespace' => 'RegisterReport\Regular', 'prefix' => 'savings_refund_register'], function () {
            //     Route::any('/', 'SavingsRefundRegisterController@index');
            //     Route::any('/loadData', 'SavingsRefundRegisterController@getData');
            // });

            ##----------------savings register END--------------------###

            ### ------------- Loan Register--------------------###
            ##Loan Accounts
            Route::group(['namespace' => 'Loan', 'prefix' => 'loan_accounts_reg'], function () {
                Route::any('', 'LoanRegisterReportController@loanAccountsReg');
                Route::any('loadData', 'LoanRegisterReportController@getLoanAccountsData');
            });

            ##Loan Disbursement
            Route::group(['namespace' => 'Loan', 'prefix' => 'loan_disburse_reg'], function () {
                Route::any('/', 'LoanRegisterReportController@loanDisbursementReg');
                Route::any('/loadData', 'LoanRegisterReportController@getLoanDisbursementData');
            });

            ##Loan Collection
            Route::group(['namespace' => 'Loan', 'prefix' => 'loan_collection_reg'], function () {
                Route::any('/', 'LoanRegisterReportController@loanCollectionReg');
                Route::any('/loadData', 'LoanRegisterReportController@getLoanCollectionData');
            });

            ##Loan Waiver
            Route::group(['namespace' => 'Loan', 'prefix' => 'loan_waiver_reg'], function () {
                Route::any('/', 'LoanRegisterReportController@loanWaiverReg');
                Route::any('/loadData', 'LoanRegisterReportController@getLoanWaiverData');
            });

            ##Loan Rebate
            Route::group(['namespace' => 'Loan', 'prefix' => 'loan_rebate_reg'], function () {
                Route::any('/', 'LoanRegisterReportController@loanRebateReg');
                Route::any('/loadData', 'LoanRegisterReportController@getLoanRebateData');
            });

            ##Loan WriteOff
            Route::group(['namespace' => 'Loan', 'prefix' => 'loan_write_off_reg'], function () {
                Route::any('/', 'LoanRegisterReportController@loanWriteOffReg');
                Route::any('/loadData', 'LoanRegisterReportController@getLoanWriteOffData');
            });

            ##Loan WriteOff Collection
            Route::group(['namespace' => 'Loan', 'prefix' => 'write_off_col_reg'], function () {
                Route::any('/', 'LoanRegisterReportController@loanWriteOffCollectionReg');
                Route::any('/loadData', 'LoanRegisterReportController@getLoanWriteOffCollectionData');
            });

            ## Dual Lonee Info
            Route::group(['namespace' => 'Loan', 'prefix' => 'dual_loanee_info'], function () {
                Route::any('/', 'LoanRegisterReportController@dualLoaneeReg');
                Route::any('/loadData', 'LoanRegisterReportController@getDualLoaneeData');
            });

            ## Full Paid Loan
            Route::group(['namespace' => 'Loan', 'prefix' => 'full_paid_loan'], function () {
                Route::any('/', 'LoanRegisterReportController@fullPaidLoan');
                Route::any('/loadData', 'LoanRegisterReportController@getFullPaidLoanData');
            });
            ##----------------Loan register END--------------------###

            ##----------------Due register END--------------------###

            Route::group(['namespace' => 'DueReports', 'prefix' => 'dueRegister'], function () {
                Route::any('/', 'DueRegisterReportController@getDueRegister')->name('dueRegisterDataTable');
            });

            // Route::group(['namespace' => 'DueReports', 'prefix' => 'regulardueRegister'], function () {
            //     Route::any('/', 'DueRegisterReportController@getRegularDueRegister')->name('dueRegularRegisterDataTable');
            // });

            Route::group(['namespace' => 'DueReports', 'prefix' => 'current_due_reg'], function () {
                Route::any('/', 'DueRegisterReportController@getCurrentDueRegister')->name('dueCurrentRegisterDataTable');
            });
            Route::group(['namespace' => 'DueReports', 'prefix' => 'over_due_reg'], function () {
                Route::any('/', 'DueRegisterReportController@getOverDueRegister')->name('dueOverRegisterDataTable');
            });

            ## ---------------- Regular due ----------------------###
            Route::group(['namespace' => 'DueReports', 'prefix' => 'regulardueRegister'], function () {
                Route::any('/', 'DueRegisterReportController@indexregular');
                Route::any('/loadData', 'DueRegisterReportController@loadDataregular');
                Route::any('/getBorrowers', 'DueRegisterReportController@getBorrowers');
            });

            ## ---------------- New due ----------------------###
            Route::group(['namespace' => 'DueReports', 'prefix' => 'new_due_reg'], function () {
                Route::any('/', 'DueRegisterReportController@indexNewDue');
                Route::any('/loadData', 'DueRegisterReportController@loadDataNewDue');
                Route::any('/getBorrowers', 'DueRegisterReportController@newDueGetBorrowers');
            });

            ##----------------Due register END--------------------###

            ## ----------------MIP Statement ----------------------###
            Route::group(['namespace' => 'MIPStatement', 'prefix' => 'mip_statement'], function () {
                Route::any('/', 'MIPStatementController@index');
                Route::any('/loadData', 'MIPStatementController@loadDate');
                Route::any('/getData', 'MIPStatementController@getData');

            });

        });

        ## ------------------ Member Report Route -------------- ###
        Route::group(['namespace' => 'Member'], function () {
            ## Member Statement Report Route
            Route::group(['prefix' => 'member_statement'], function () {
                Route::any('/', 'MemberStatementController@index');
                Route::any('/loadData', 'MemberStatementController@loadData')->name('loadData');
            });

            ## Member Audit Report Route
            Route::group(['prefix' => 'member_info'], function () {
                Route::any('/', 'MemberAuditReportController@index')->name('memberInformation');
                Route::any('/loadData', 'MemberAuditReportController@getMemberdata');
            });

            ## Member Mamla Report Route
            Route::group(['prefix' => 'member_mamla'], function () {
                Route::any('/', 'MemberMamlaReportController@index');
                Route::any('/loadData', 'MemberMamlaReportController@loadData');
            });
        });

        Route::group(['namespace' => 'Others'], function () {
            Route::any('/loanStatement', 'LoanStatementReportController@getLoanStatement')->name('loanStatementDataTble');
        });

        Route::group(['namespace' => 'PksfReports', 'prefix' => 'pksf'], function () {

            /*
             * POMIS-2 Report
             */
            Route::any('pomis2', 'PksfPomisTwoController@getPksfPomisTwoFilterPart');
            Route::any('pomis2/viewPart', 'PksfPomisTwoController@getPksfPomisTwoViewPart');

            /*
             * POMIS-2A Report
             */
            Route::any('pomis2A', 'PksfPomisTwoController@getPksfPomisTwoAFilterPart');
            Route::any('pomis2A/viewPart', 'PksfPomisTwoController@getPksfPomisTwoAViewPart');

            /*
             * POMIS-3 Report
             */
            Route::any('pomis3', 'PksfPomisThreeController@getPksfPomisThreeFilterPart');
            Route::any('pomis3/viewPart', 'PksfPomisThreeController@getPksfPomisThreeViewPart');

            /*
             * POMIS-5 Report
             */
            Route::any('pomis5', 'PksfPomisFiveController@getPksfPomisFiveFilterPart');
            Route::any('pomis5/viewPart', 'PksfPomisFiveController@getPksfPomisFiveViewPart');
        });

        Route::group(['namespace' => 'PksfReports', 'prefix' => 'pksf'], function () {
            Route::group(['prefix' => 'pomis1'], function () {

                //POMIS-1 Report
                Route::any('/', 'PksfPomisOneController@index');
                Route::any('/loadData', 'PksfPomisOneController@getData');
            });
        });

        Route::group(['namespace' => 'PeriodicalProgressReport', 'prefix' => 'periodical_progress'], function () {
            Route::any('/', 'PeriodicalProgressController@index');
            Route::any('/loadData', 'PeriodicalProgressController@getData');
        });
    });

    // Route::any('/meSummary/{branchId}/{month}', function ($branchId, $month) {
    //     return app('App\Http\Controllers\MFN\Process\MonthEndSummary')->storeMonthEndSummary($branchId, $month);
    // });

    // Route::any('/dayChange', function () {
    //     return app('App\Http\Controllers\MFN\Samity\SamityDayChangeController')->isDayChangeEffectLoanSchedule();
    // });

    Route::any('/sendMail', function () {

        $subject = 'Laravel Test';
        $name    = 'Mr. Saiful';
        $body    = 'This is test mail from ' . env('MAIL_FROM_NAME');

        // Bellow line is commented, but should be uncomment
        // Mail::to('saiful.b1k996@gmail.com')->send(new App\Mail\SendMail($subject, $name, $body));
    });

    Route::any('/memAdmissionReqField', 'MemberAdmissionRequiredCheckController@scriptIndex');
    Route::any('/branch_independence', 'GConfig\BranchIndependenceController@scriptIndex');
    Route::any('/branch_independence/view-inependence', 'GConfig\BranchIndependenceController@viewData')->name('viewIndependenceData');
    // Route::any('/appointment_letter', 'GConfig\AppointmentLetterController@index');
    Route::any('/configurationforreporpomis5a', 'GConfig\ConfigurationReportPomis5aController@scriptIndex');

});

Route::get('mfn/mail/verify/{memberId}/{eToken}', 'MFN\Mail\MailVarificationController@isVerified');
