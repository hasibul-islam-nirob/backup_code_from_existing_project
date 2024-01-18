<?php

use Illuminate\Support\Facades\Route;

// , 'permission'
Route::group(['middleware' => ['auth', 'permission'], 'prefix' => 'fam', 'namespace' => 'FAM'], function () {

    // Controllers Within The "App\Http\Controllers\Admin" Namespace

    Route::any('/', 'DashboardController@index');

    Route::any('/branch_status', 'DashboardController@branchStatus')->name('FAMBranchStatus');

    ## Supplier  Route C
    Route::group(['namespace' => 'Configuration\Supplier', 'prefix' => 'supplier'], function () {

        Route::any('/', 'SupplierController@index')->name('FAMsupplierDatatable');
        Route::any('add', 'SupplierController@add');
        Route::any('edit/{id}', 'SupplierController@edit');
        Route::get('view/{id}', 'SupplierController@view');
        Route::any('delete/{id}', 'SupplierController@delete');
        Route::any('publish/{id}', 'SupplierController@isActive');
        Route::any('destroy/{id}', 'SupplierController@destroy');
    });

    ## Supplier Payment Route C
    Route::group(['namespace' => 'Configuration\Supplier', 'prefix' => 'supplier_payment'], function () {
        Route::any('/', 'SupplierPaymentController@index');
        Route::any('add', 'SupplierPaymentController@add');
        Route::any('edit/{id}', 'SupplierPaymentController@edit');
        Route::get('view/{id}', 'SupplierPaymentController@view');
        Route::get('delete/{id}', 'SupplierPaymentController@delete');
        Route::any('publish/{id}', 'SupplierPaymentController@isActive');

        Route::post('getData', 'SupplierPaymentController@getData');
    });

    ## AutoVoucher Route  working on hold
    Route::group(['prefix' => 'auto_v_config'], function () {

        Route::any('/', 'AutoVoucherConfigController@index');
        Route::any('add', 'AutoVoucherConfigController@add');
        Route::any('edit/{id}', 'AutoVoucherConfigController@edit');
        Route::get('view/{id}', 'AutoVoucherConfigController@view');
        Route::any('delete', 'AutoVoucherConfigController@delete');
        Route::any('publish/{id}', 'AutoVoucherConfigController@isActive');
        Route::post('/getData', 'AutoVoucherConfigController@getData');

    });

    ## MIS Configaration Route
    Route::group(['prefix' => 'mis_config'], function () {

        Route::any('/', 'MisConfigController@index');
        Route::any('add', 'MisConfigController@add');
        Route::any('edit/{id}', 'MisConfigController@edit');
        Route::get('view/{id}', 'MisConfigController@view');
        Route::any('delete', 'MisConfigController@delete');
        Route::any('publish/{id}', 'MisConfigController@isActive');

    });

    ## Product Settings --------------------------

    // Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'pro_name'], function () {
       
    // });

    ## Product Name Route
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'pro_name'], function () {
        Route::any('/', 'ProductNameController@index');
        Route::any('add', 'ProductNameController@add');
        Route::any('edit/{id}', 'ProductNameController@edit');
        Route::get('view/{id}', 'ProductNameController@view');
        Route::any('delete/{id}', 'ProductNameController@delete');
        Route::any('publish/{id}', 'ProductNameController@isActive');
        Route::any('destroy/{id}', 'ProductNameController@destroy');
    });

    ## Product Type Route
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'pro_type'], function () {
        Route::any('/', 'ProductTypeController@index');
        Route::any('add', 'ProductTypeController@add');
        Route::any('edit/{id}', 'ProductTypeController@edit');
        Route::get('view/{id}', 'ProductTypeController@view');
        Route::any('delete/{id}', 'ProductTypeController@delete');
        Route::any('publish/{id}', 'ProductTypeController@isActive');
        Route::any('destroy/{id}', 'ProductTypeController@destroy');
    });

    ## Group Route
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'group'], function () {
        Route::any('/', 'PGroupController@index')->name('fam_pGrpDatatable');
        Route::any('add', 'PGroupController@add');
        Route::any('edit/{id}', 'PGroupController@edit');
        Route::get('view/{id}', 'PGroupController@view');
        Route::any('delete/{id}', 'PGroupController@delete');
        Route::any('publish/{id}', 'PGroupController@isActive');
        Route::any('destroy/{id}', 'PGroupController@destroy');
    });

    ## category Route
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'category'], function () {

        Route::any('/', 'PCategoryController@index')->name('fam_pcatDatatable');
        Route::any('add', 'PCategoryController@add');
        Route::any('edit/{id}', 'PCategoryController@edit');
        Route::get('view/{id}', 'PCategoryController@view');
        Route::any('delete/{id}', 'PCategoryController@delete');
        Route::any('publish/{id}', 'PCategoryController@isActive');
        Route::any('destroy/{id}', 'PCategoryController@destroy');

    });

    ## Subcategory Route
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'subcategory'], function () {

        Route::any('/', 'PSubCategoryController@index')->name('fam_pSubDatatable');
        Route::any('add', 'PSubCategoryController@add');
        Route::any('edit/{id}', 'PSubCategoryController@edit');
        Route::get('view/{id}', 'PSubCategoryController@view');
        Route::any('delete/{id}', 'PSubCategoryController@delete');
        Route::any('publish/{id}', 'PSubCategoryController@isActive');
        Route::any('destroy/{id}', 'PSubCategoryController@destroy');
    });

    ## Brand Route
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'brand'], function () {
        Route::any('/', 'PBrandController@index')->name('fam_brandDatatable');
        Route::any('add', 'PBrandController@add');
        Route::any('edit/{id}', 'PBrandController@edit');
        Route::get('view/{id}', 'PBrandController@view');
        Route::any('delete', 'PBrandController@delete');
        Route::any('publish/{id}', 'PBrandController@isActive');
        Route::any('destroy/{id}', 'PBrandController@destroy');
    });

    ## Model Route
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'model'], function () {
        Route::any('/', 'PModelController@index')->name('fam_pmDatatable');
        Route::any('add', 'PModelController@add');
        Route::any('edit/{id}', 'PModelController@edit');
        Route::get('view/{id}', 'PModelController@view');
        Route::any('delete/{id}', 'PModelController@delete');
        Route::any('publish/{id}', 'PModelController@isActive');
        Route::any('destroy/{id}', 'PModelController@destroy');
    });

    ## Size Route C
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'size'], function () {
        Route::any('/', 'ProductSizeController@index')->name('fam_pSzDatatable');
        Route::any('add', 'ProductSizeController@add');
        Route::any('edit/{id}', 'ProductSizeController@edit');
        Route::get('view/{id}', 'ProductSizeController@view');
        Route::any('delete/{id}', 'ProductSizeController@delete');
        Route::any('publish/{id}', 'ProductSizeController@isActive');
        Route::any('destroy/{id}', 'ProductSizeController@destroy');

        Route::any('loadModelSize', 'ProductSizeController@ajaxSelectModelLoad');
    });

    ## Color Route C
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'color'], function () {

        Route::any('/', 'ProductColorController@index')->name('fam_pColorDatatable');
        Route::any('add', 'ProductColorController@add');
        Route::any('edit/{id}', 'ProductColorController@edit');
        Route::get('view/{id}', 'ProductColorController@view');
        Route::any('delete/{id}', 'ProductColorController@delete');
        Route::any('publish/{id}', 'ProductColorController@isActive');
        Route::any('destroy/{id}', 'ProductColorController@destroy');

        Route::any('loadModelColor', 'ProductColorController@ajaxSelectModelLoad');

    });

    ##  Product UOM Route C
    Route::group(['namespace' => 'Configuration\ProductSettings', 'prefix' => 'uom'], function () {

        Route::any('/', 'ProductUOMController@index')->name('fam_pUomDatatable');
        Route::any('add', 'ProductUOMController@add');
        Route::any('edit/{id}', 'ProductUOMController@edit');
        Route::get('view/{id}', 'ProductUOMController@view');
        Route::any('delete/{id}', 'ProductUOMController@delete');
        Route::any('publish/{id}', 'ProductUOMController@isActive');
        Route::any('destroy/{id}', 'ProductUOMController@destroy');

    });

    ## Product Route
    Route::group(['namespace' => 'Product', 'prefix' => 'product'], function () {
        Route::any('/', 'ProductController@index')->name('FAMproductDatatable');
        Route::any('add', 'ProductController@add');
        Route::any('edit/{id}', 'ProductController@edit');
        Route::get('view/{id}', 'ProductController@view');
        Route::any('delete/{id}', 'ProductController@delete');
        Route::any('publish/{id}', 'ProductController@isActive');
        Route::any('destroy/{id}', 'ProductController@destroy');
        Route::post('getData', 'ProductController@getData');
        // Route::any('loadModelProduct', 'ProductController@ajaxSelectModelLoad');

        Route::any('loadModelForProduct', 'ProductController@ajaxLoadModelFP');
        // ->withoutMiddleware(['permission'])
        Route::any('loadSizeForProduct', 'ProductController@ajaxLoadSizeFP');
        Route::any('loadColorForProduct', 'ProductController@ajaxLoadColorFP');
    });

    ## Additional CHarges Route
    Route::group(['namespace' => 'Product', 'prefix' => 'additional_charge'], function () {
        Route::any('/', 'AdditionalChargeController@index');
        Route::any('add', 'AdditionalChargeController@add');
        Route::any('edit/{id}', 'AdditionalChargeController@edit');
        Route::get('view/{id}', 'AdditionalChargeController@view');
        Route::get('delete/{id}', 'AdditionalChargeController@delete');
        Route::any('publish/{id}', 'AdditionalChargeController@isActive');

        Route::post('getData', 'AdditionalChargeController@getData');
    });

    ## Additional Product Route
    Route::group(['namespace' => 'Product', 'prefix' => 'pro_additional'], function () {
        Route::any('/', 'AdditionalProductController@index');
        Route::any('add', 'AdditionalProductController@add');
        Route::any('edit/{id}', 'AdditionalProductController@edit');
        Route::get('view/{id}', 'AdditionalProductController@view');
        Route::any('delete/{id}', 'AdditionalProductController@delete');
        Route::any('publish/{id}', 'AdditionalProductController@isActive');
        Route::any('destroy/{id}', 'AdditionalProductController@destroy');
    });

    ##process

    ## Generate Depriciation  Route
    Route::group(['namespace' => 'Process', 'prefix' => 'gen_dep'], function () {
        Route::any('/', 'GenerateDepreciationController@index');
        Route::any('add', 'GenerateDepreciationController@add');
        Route::any('edit/{id}', 'GenerateDepreciationController@edit');
        Route::get('view/{id}', 'GenerateDepreciationController@view');
        Route::any('delete/{id}', 'GenerateDepreciationController@delete');
        Route::any('publish/{id}', 'GenerateDepreciationController@isActive');
        Route::any('destroy/{id}', 'GenerateDepreciationController@destroy');
    });
    ## product code print Route
    Route::group(['namespace' => 'Process', 'prefix' => 'assets_id_print'], function () {

        Route::any('/', 'ProductCodeController@index');
        // Route::any('/', 'ProductCodeController@index');
        Route::post('getData', 'ProductCodeController@getData');
    });
    ## Transaction

    ##Order able Purchase Route
    Route::group(['prefix' => 'purchase_orderable'], function () {
        Route::any('/', 'OrderablePurchaseController@index')->name('fam_FAMOrderablePurchaseList');
        Route::any('add', 'OrderablePurchaseController@add');
        Route::any('edit/{id}', 'OrderablePurchaseController@edit');
        Route::get('view/{id}', 'OrderablePurchaseController@view');
        Route::any('delete/{id}', 'OrderablePurchaseController@delete');
        // Route::any('publish/{id}', 'PurchaseController@isActive');
        Route::post('popUpSupplierData', 'OrderablePurchaseController@popUpSupplierDataInsert');
    });
   


    ## Sales Route
    Route::group(['namespace' => 'Transactions', 'prefix' => 'sales'], function () {
        Route::any('/', 'SalesController@index');
        Route::any('add', 'SalesController@add');
        Route::any('edit/{id}', 'SalesController@edit');
        Route::get('view/{id}', 'SalesController@view');
        Route::get('delete/{id}', 'SalesController@delete');
        Route::any('publish/{id}', 'SalesController@isActive');

        Route::post('getData', 'SalesController@getData');
    });

    ## Write Off Route
    Route::group(['namespace' => 'Transactions', 'prefix' => 'write_off'], function () {
        Route::any('/', 'WriteOffController@index');
        Route::any('add', 'WriteOffController@add');
        Route::any('edit/{id}', 'WriteOffController@edit');
        Route::get('view/{id}', 'WriteOffController@view');
        Route::get('delete/{id}', 'WriteOffController@delete');
        Route::any('publish/{id}', 'WriteOffController@isActive');

        Route::post('getData', 'WriteOffController@getData');
    });

    ## Transfer Route
    Route::group(['namespace' => 'Transactions', 'prefix' => 'transfer'], function () {

        Route::any('/', 'TransferController@index')->name('FAMtransferDatatable');
        Route::any('add', 'TransferController@add');
        Route::any('edit/{id}', 'TransferController@edit');
        Route::get('view/{id}', 'TransferController@view');
        Route::any('delete/{id}', 'TransferController@delete');
        Route::any('publish/{id}', 'TransferController@isActive');
        Route::post('getData', 'TransferController@getData');

    });



    ## USE Route C
    Route::group(['namespace' => 'Transactions\Uses', 'prefix' => 'use'], function () {

        Route::any('/', 'UsesController@index')->name('fam_FAMUsesList');
        Route::any('add', 'UsesController@add');
        Route::any('edit/{id}', 'UsesController@edit');
        Route::any('view/{id}', 'UsesController@view');
        Route::any('delete/{id}', 'UsesController@delete');
        Route::any('invoice/{id}', 'UsesController@invoice');
        // Route::any('publish/{id}', 'UsesController@isActive');
        Route::post('getData', 'UsesController@getData');

        Route::any('/ajEmpLoadDeptWise', 'UsesController@ajaxEmployeeLoad');
        Route::any('/ajReqLoad', 'UsesController@ajaxRequisitionLoad');
        Route::any('/ajReqLoadDeptWise', 'UsesController@ajaxRequisitionLoadForDept');
        Route::any('/ajReqLoadEmpBranchWise', 'UsesController@ajaxEmpLoadBranchWise');

    });

    ## Sales Return Route C
    Route::group(['namespace' => 'Transactions\Uses', 'prefix' => 'use_return'], function () {

        Route::any('/', 'UseReturnController@index')->name('fam_famUseRList');
        Route::any('add', 'UseReturnController@add');
        Route::any('edit/{id}', 'UseReturnController@edit');
        Route::get('view/{id}', 'UseReturnController@view');
        Route::any('delete/{id}', 'UseReturnController@delete');
        Route::any('publish/{id}', 'UseReturnController@isActive');
        Route::post('getData', 'UseReturnController@getData');
    });

    // ## Requisition Route
    Route::group(['namespace' => 'Transactions\Requisition', 'prefix' => 'requisition'], function () {
        Route::any('/', 'RequisitionController@index');
        Route::any('add', 'RequisitionController@add');
        Route::any('edit/{id}', 'RequisitionController@edit');
        Route::get('view/{id}', 'RequisitionController@view');
        Route::any('delete/{id}', 'RequisitionController@delete');
        Route::any('approve/{id}', 'RequisitionController@isApprove');
    });

    // ## Employee Requisition Route
    Route::group(['namespace' => 'Transactions\Requisition', 'prefix' => 'requisition_emp'], function () {
        Route::any('/', 'EmployeeRequisitionController@index');
        Route::any('add', 'EmployeeRequisitionController@add');
        Route::any('edit/{id}', 'EmployeeRequisitionController@edit');
        Route::get('view/{id}', 'EmployeeRequisitionController@view');
        Route::any('delete/{id}', 'EmployeeRequisitionController@delete');
        Route::any('approve/{id}', 'EmployeeRequisitionController@isApprove');
        Route::post('getData', 'EmployeeRequisitionController@getData');
    });

    // ## Product Order Route
    Route::group(['prefix' => 'product_order'], function () {
        Route::any('/', 'OrderController@index');
        Route::any('add', 'OrderController@add');
        Route::any('edit/{id}', 'OrderController@edit');
        Route::get('view/{id}', 'OrderController@view');
        Route::any('delete/{id}', 'OrderController@delete');
        Route::any('approve/{id}', 'OrderController@isApprove');
    });

    ## Report Route C
    Route::group(['prefix' => 'report'], function () {

        Route::group(['namespace' => 'Reports'], function(){

            /* start ---------------------- Stock Reports */
            Route::any('stock_branch', 'StockReportController@stockBranch');
            Route::any('stock_ho', 'StockReportController@stockHO');
            Route::any('stock_fam_branch', 'StockReportController@stockFamBranch');
            Route::any('stock_fam_ho', 'StockReportController@stockFamHO');
            /* end ---------------------- Stock Reports */

            /* start ---------------------- Requisition Reports */
            Route::any('requisition', 'RequisitionReportController@getRequisition');
            Route::any('requisition_emp', 'RequisitionReportController@getRequisitionEmployee');
            /* end ---------------------- Requisition Reports */

            /* start ----------------------  ProductOrderReports */
            Route::any('product_order', 'ProductOrderReportController@getProdOrder');
            /* end ----------------------  ProductOrderReports */

            /* start ----------------------  PurchaseReports */
            Route::any('purchase', 'PurchaseReportController@getPurchaseAll');
            Route::any('purchase_return', 'PurchaseReportController@getPurchaseReturnAll')->name('FAMPurReturnDataTable');
            /* end ----------------------  PurchaseReports */

            /* start ---------------------- Uses Reports */
            Route::any('use', 'UsesReportController@getUse')->name('fam_FAMuseDataTableReport');
            Route::any('use_return', 'UsesReportController@getUseReturn')->name('fam_FAMuseRetDataTableReport');
            /* end ---------------------- Uses Reports */

            /* start ---------------------- Sales Reports */
            Route::any('sales', 'SalesReportController@getsale')->name('FAMsaleRepDatatable');
            /* end ---------------------- Sales Reports */

            /* start ---------------------- Transfer Reports */
            Route::any('transferin', 'TransferReportController@getTransferIn')->name('fam_FAMtransferinDataTable');
            Route::any('transferout', 'TransferReportController@getTransferOut')->name('fam_FAMtransferoutDataTable');
            /* end ---------------------- Transfer Reports */

            /* start ---------------------- Writeoff Reports */
            Route::any('writeoff', 'WriteoffReportController@getwriteoff')->name('FAMWriteoffrepDatatable');
            /* end ---------------------- Writeoff Reports */

            /* start ---------------------- Depreciation Reports */
            Route::any('depreciation', 'DepreciationReportController@getdepreciation')->name('FAMDeprepDatatable');
            Route::any('depreciation_details', 'DepreciationReportController@getdepreciationDetails')->name('FAMDepDetailsRepDatatable');
            /* end ---------------------- Depreciation Reports */

            /* start ---------------------- Register Reports */
            Route::any('register_rep', 'RegisterReportController@getRegisterRep');
            /* end ---------------------- Register Reports */
            
            /* start ---------------------- Schedule Reports */
            Route::any('schedule_rep', 'ScheduleReportController@getScheduleRep');
            /* end ---------------------- Schedule Reports */
        });

        /* start ---------------------- Sales Reports */
        
        

        
        // Route::any('salesr', 'ReportController@getsale')->name('INVsaleRDatatable');
        // Route::any('sales_details', 'ReportController@getsaleDetails');
        // Route::any('tsales_summary', 'ReportController@getTSalesSummary')->name('fam_INVTSalesSummaryDatatable');
        // Route::any('area_sales', 'ReportController@getAreaWiseSales');
        // Route::any('zone_sales', 'ReportController@getZoneSales');

        // Under COnstruction
        // Route::any('region_sales', 'ReportController@getRegionSales');
        // Route::any('sales_return', 'ReportController@getSalesReturn')->name('fam_INVSalesReturnDataTable');;

        /* end ---------------------- Sales Reports */

        /* start ---------------------- Purchase Reports */

        
        /* end ---------------------- Purchase Reports */

        /* start ---------------------- Issue Reports */
        Route::any('issue', 'ReportController@getIssueAll')->name('fam_FAMissueDataTableReport');
        Route::any('issue_return', 'ReportController@getIssueReturnAll')->name('fam_FAMissueReturnDataTableReport');
        /* end ---------------------- Issue Reports */

        

        
        
        
        // Route::any('branch_customer', 'ReportController@getbranchcustomer')->name('fam_FAMbranchcustomerDatatable');


        

        // Route::any('branchr', 'ReportController@getbranch')->name('fam_FAMbranchRDatatable');
        // Route::any('arear', 'ReportController@getarea')->name('fam_FAMareaRDatatable');
        // Route::any('zoner', 'ReportController@getzone')->name('fam_FAMzoneRDatatable');

        
    });
});
