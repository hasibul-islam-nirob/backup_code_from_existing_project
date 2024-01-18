<?php

use Illuminate\Support\Facades\Route;
## Synchronize Data
Route::group(['middleware' => ['auth'], 'namespace' => 'POS'], function () {
    Route::get('/dataSynchronization', 'SynchronizeController@synchronizeData');
    // Route::get('/dataSynchronization', 'SynchronizeController@fntest');

});

Route::group(['middleware' => ['auth'], 'prefix' => 'pos', 'namespace' => 'POS'], function () {

    //scripts
    Route::group(['prefix' => 'scripts'], function () {

        Route::any('/', function () {
            return view('POS/Scripts/index');
        });
        Route::post('getData', 'ScriptForPosController@getData');
        Route::post('generateSummary', 'ScriptForPosController@executeSummary');
    });

    Route::any('day_end/update_day_end_script', 'Process\DayEndController@scriptDayEnd');
    Route::any('month_end/update_month_end_script', 'Process\MonthEndController@scriptMonthEnd');
    Route::any('sales_cash/update_sales_script', 'Transaction\Sales\SalesController@scriptUpdateSales');
    Route::any('sales_installment/update_sales_script', 'Transaction\Sales\SalesController@scriptUpdateSales');
    Route::any('purchase/entry_purchase_payment_script', 'Transaction\PurchaseController@scriptEntryPurchasePayment');
    // Route::any('purchase/update_purchase_payment_script', 'PurchaseController@scriptUpdatePurchasePayment');

    // Route::any('gold_stock/update_script', 'Reports\StockReportController@stockUpdateScript');

    Route::any('hibernate_script/hibernate', 'Process\HibernateController@index');
    Route::any('hibernate_script/getData', 'Process\HibernateController@getData');
    Route::any('set_branch_sql_id', 'Process\SqliteBranchSetController@index');
});

// , 'permission'
Route::group(['middleware' => ['auth', 'permission', 'offline'], 'prefix' => 'pos', 'namespace' => 'POS'], function () {

    // Controllers Within The "App\Http\Controllers\Admin" Namespace

    Route::any('/', 'DashboardController@index')->name('Dashboard');
    Route::any('/branch_status', 'DashboardController@branchStatus')->name('POSBranchStatus');

    ## Configaration --------------------------
    Route::group(['namespace' => 'Configaration'], function () {
        ## Customer --------------------------
        Route::group(['namespace' => 'Customer'], function () {

            ## Customer  Route C
            Route::group(['prefix' => 'customer'], function () {
                Route::any('/', 'CustomerController@index')->name('customerDatatable');
                Route::any('add', 'CustomerController@add');
                Route::any('edit/{id}', 'CustomerController@edit');
                Route::get('view/{id}', 'CustomerController@view');
                Route::any('delete/{id}', 'CustomerController@delete');
                Route::any('publish/{id}', 'CustomerController@isActive');
                Route::any('destroy/{id}', 'CustomerController@destroy');
            });

            ## guarantor  Route
            Route::group(['prefix' => 'guarantor'], function () {

                Route::any('/', 'GuarantorController@index')->name('GuarantorDatatable');
                Route::any('add', 'GuarantorController@add');
                Route::any('edit/{id}', 'GuarantorController@edit');
                Route::get('view/{id}', 'GuarantorController@view');
                Route::any('delete/{id}', 'GuarantorController@delete');
                Route::any('publish/{id}', 'GuarantorController@isActive');
                Route::get('destroy/{id}', 'GuarantorController@destroy');
            });
        });

        ## barcode  Route
        Route::group(['prefix' => 'barcode_config'], function () {

            Route::any('/', 'BarcodeConfigController@index');
            Route::any('add', 'BarcodeConfigController@add');
            Route::any('edit/{id}', 'BarcodeConfigController@edit');
            Route::get('view/{id}', 'BarcodeConfigController@view');
            Route::any('delete/{id}', 'BarcodeConfigController@delete');
            Route::any('publish/{id}', 'BarcodeConfigController@isActive');
            Route::get('destroy/{id}', 'BarcodeConfigController@destroy');
        });

        ## Installment Packages Route
        Route::group(['prefix' => 'pinstallpackage'], function () {

            Route::any('/', 'PInstPackageController@index')->name('pInstPkgDatatable');
            Route::any('add', 'PInstPackageController@add');
            Route::any('edit/{id}', 'PInstPackageController@edit');
            Route::get('view/{id}', 'PInstPackageController@view');
            Route::any('delete/{id}', 'PInstPackageController@delete');
            Route::any('publish/{id}', 'PInstPackageController@isActive');
            Route::any('destroy/{id}', 'PInstPackageController@destroy');
        });

        ## Processing Fee Route
        Route::group(['prefix' => 'proFee'], function () {

            Route::any('/', 'ProFeeController@index')->name('proFeeDatatable');
            Route::any('add', 'ProFeeController@add');
            Route::any('edit/{id}', 'ProFeeController@edit');
            Route::any('view/{id}', 'ProFeeController@view');

            Route::any('delete/{id}', 'ProFeeController@delete');
            Route::any('publish', 'ProFeeController@isActive');
            Route::any('destroy/{id}', 'ProFeeController@destroy');
            Route::post('getData', 'ProFeeController@getData');
        });

        ## Supplier  Route C
        Route::group(['prefix' => 'supplier'], function () {
            Route::any('/', 'SupplierController@index')->name('supplierDatatable');
            Route::any('add', 'SupplierController@add');
            Route::any('edit/{id}', 'SupplierController@edit');
            Route::get('view/{id}', 'SupplierController@view');
            Route::any('delete/{id}', 'SupplierController@delete');
            Route::any('publish/{id}', 'SupplierController@isActive');
            Route::any('destroy/{id}', 'SupplierController@destroy');
        });

        // // ## MIS Configaration Route
        Route::group(['prefix' => 'mis_config'], function () {

            Route::any('/', 'MisConfigController@index')->name('misConfigDatatable');
            Route::any('add', 'MisConfigController@add');
            Route::any('edit/{id}', 'MisConfigController@edit');
            Route::get('view/{id}', 'MisConfigController@view');
            Route::any('delete', 'MisConfigController@delete');
            Route::any('publish/{id}', 'MisConfigController@isActive');
        });

        // // ## AutoVoucher Route  working on hold
        Route::group(['prefix' => 'auto_v_config'], function () {

            Route::any('/', 'AutoVoucherConfigController@index')->name('autoVConfigDatatable');
            Route::any('add', 'AutoVoucherConfigController@add');
            Route::any('edit/{id}', 'AutoVoucherConfigController@edit');
            Route::get('view/{id}', 'AutoVoucherConfigController@view');
            Route::any('delete', 'AutoVoucherConfigController@delete');
            Route::any('publish/{id}', 'AutoVoucherConfigController@isActive');
            Route::post('getData', 'AutoVoucherConfigController@getData');
        });
    });

    ## Product Configaration --------------------------
    Route::group(['namespace' => 'ProductConfigaration'], function () {
        ## Group Route
        Route::group(['prefix' => 'group'], function () {
            Route::any('/', 'PGroupController@index')->name('pGroupDatatable');
            Route::any('add', 'PGroupController@add');
            Route::any('edit/{id}', 'PGroupController@edit');
            Route::get('view/{id}', 'PGroupController@view');
            Route::any('delete/{id}', 'PGroupController@delete');
            Route::any('publish/{id}', 'PGroupController@isActive');
            Route::any('destroy/{id}', 'PGroupController@destroy');
        });

        ## category Route
        Route::group(['prefix' => 'category'], function () {

            Route::any('/', 'PCategoryController@index')->name('pCatDatatable');
            Route::any('add', 'PCategoryController@add');
            Route::any('edit/{id}', 'PCategoryController@edit');
            Route::get('view/{id}', 'PCategoryController@view');
            Route::any('delete/{id}', 'PCategoryController@delete');
            Route::any('publish/{id}', 'PCategoryController@isActive');
            Route::any('destroy/{id}', 'PCategoryController@destroy');

            Route::any('add_other_cost/{id}', 'PCategoryController@fnaddOther');
            Route::post('addOtherCost', 'PCategoryController@addOtherCost');

            Route::any('edit_other_cost/{id}', 'PCategoryController@fneditOther');
            Route::post('editOtherCost', 'PCategoryController@editOtherCost');

            Route::any('delete_other_cost/{id}', 'PCategoryController@DeleteOther');

            // Route::any('edit_other_cost/{id}', function($id){
            //     return view ('POS.ProductConfigaration.Category.cost_edit',compact('id'));
            // });
        });

        ## Subcategory Route
        Route::group(['prefix' => 'subcategory'], function () {

            Route::any('/', 'PSubCategoryController@index')->name('PSubCatDatatable');
            Route::any('add', 'PSubCategoryController@add');
            Route::any('edit/{id}', 'PSubCategoryController@edit');
            Route::get('view/{id}', 'PSubCategoryController@view');
            Route::any('delete/{id}', 'PSubCategoryController@delete');
            Route::any('publish/{id}', 'PSubCategoryController@isActive');
            Route::any('destroy/{id}', 'PSubCategoryController@destroy');
        });

        ## Brand Route
        Route::group(['prefix' => 'brand'], function () {
            Route::any('/', 'PBrandController@index')->name('pBraDatatable');
            Route::any('add', 'PBrandController@add');
            Route::any('edit/{id}', 'PBrandController@edit');
            Route::get('view/{id}', 'PBrandController@view');
            Route::any('delete/{id}', 'PBrandController@delete');
            Route::any('publish/{id}', 'PBrandController@isActive');
            Route::any('destroy/{id}', 'PBrandController@destroy');
        });

        ## Model Route
        Route::group(['prefix' => 'model'], function () {
            Route::any('/', 'PModelController@index')->name('pModelDatatable');
            Route::any('add', 'PModelController@add');
            Route::any('edit/{id}', 'PModelController@edit');
            Route::get('view/{id}', 'PModelController@view');
            Route::any('delete/{id}', 'PModelController@delete');
            Route::any('publish/{id}', 'PModelController@isActive');
            Route::any('destroy/{id}', 'PModelController@destroy');
        });

        ## Size Route C
        Route::group(['prefix' => 'size'], function () {
            Route::any('/', 'ProductSizeController@index')->name('proSizeDatatable');
            Route::any('add', 'ProductSizeController@add');
            Route::any('edit/{id}', 'ProductSizeController@edit');
            Route::get('view/{id}', 'ProductSizeController@view');
            Route::any('delete/{id}', 'ProductSizeController@delete');
            Route::any('publish/{id}', 'ProductSizeController@isActive');
            Route::any('destroy/{id}', 'ProductSizeController@destroy');

            Route::any('loadModelSize', 'ProductSizeController@ajaxSelectModelLoad');
        });

        ## Color Route C
        Route::group(['prefix' => 'color'], function () {

            Route::any('/', 'ProductColorController@index')->name('pColDatatable');
            Route::any('add', 'ProductColorController@add');
            Route::any('edit/{id}', 'ProductColorController@edit');
            Route::get('view/{id}', 'ProductColorController@view');
            Route::any('delete/{id}', 'ProductColorController@delete');
            Route::any('publish/{id}', 'ProductColorController@isActive');
            Route::any('destroy/{id}', 'ProductColorController@destroy');

            Route::any('loadModelColor', 'ProductColorController@ajaxSelectModelLoad');
        });

        ##  Product UOM Route C
        Route::group(['prefix' => 'uom'], function () {

            Route::any('/', 'ProductUOMController@index')->name('pUOMDatatable');
            Route::any('add', 'ProductUOMController@add');
            Route::any('edit/{id}', 'ProductUOMController@edit');
            Route::get('view/{id}', 'ProductUOMController@view');
            Route::any('delete', 'ProductUOMController@delete');
            Route::any('publish/{id}', 'ProductUOMController@isActive');
            Route::any('destroy/{id}', 'ProductUOMController@destroy');
        });

        ## Carrat Route
        Route::group(['prefix' => 'carat'], function () {

            Route::any('/', 'PCaratController@index');
            Route::any('add', 'PCaratController@add');
            Route::any('edit/{id}', 'PCaratController@edit');
            Route::get('view/{id}', 'PCaratController@view');
            Route::any('delete/{id}', 'PCaratController@delete');
            Route::any('publish/{id}', 'PCaratController@isActive');
            Route::any('destroy/{id}', 'PCaratController@destroy');
        });
    });

    ## Product --------------------------
    Route::group(['namespace' => 'Product'], function () {

        ## Barcoe --------------------------
        Route::group(['namespace' => 'Barcode'], function () {
            ## barcode print Route
            Route::group(['prefix' => 'barcode'], function () {

                Route::any('/', 'BarcodeController@index');
                // Route::any('/', 'BarcodeController@index');
                Route::post('getData', 'BarcodeController@getData');
            });
            ## barcode reprint  Route
            Route::group(['prefix' => 'barcode_re'], function () {

                Route::any('/', 'BarcodeController@reindex');
                // Route::any('/', 'BarcodeController@index');
                Route::post('getData', 'BarcodeController@getData');
            });
        });

        ## Product Route
        Route::group(['prefix' => 'product'], function () {
            Route::any('/', 'ProductController@index')->name('productDatatable');
            Route::any('add', 'ProductController@add');
            Route::any('add_bulk', 'ProductController@addbulk');
            Route::any('edit/{id}', 'ProductController@edit');
            Route::get('view/{id}', 'ProductController@view');
            Route::any('delete/{id}', 'ProductController@delete');
            Route::any('approve/{id}', 'ProductController@isActive');
            Route::any('destroy/{id}', 'ProductController@destroy');

            // Gold Route
            // Route::any('add_gold', 'ProductController@add_gold');
            // Route::any('edit_gold/{id}', 'ProductController@edit');

            Route::any('duplicate_product', function () {
                //(1);dd
                return view('POS.Transaction.Purchase.Popup.product_duplicate_modal');
            });
            // Route::any('excel_import', 'ProductImportController@excel_import');
            Route::any('excel_import', 'ProductImportController@excel_import_product');
            Route::any('excel_pob', 'ProductImportController@excel_import_pob');
            Route::any('/downloadExampleFile', 'ProductImportController@exampleFileDownload');

            Route::any('view_all_list', 'ProductImportController@view'); // this route for make example data for excel import

            Route::post('getData', 'ProductController@getData');

            // Route::any('loadModelProduct', 'ProductController@ajaxSelectModelLoad');

            Route::any('loadModelForProduct', 'ProductController@ajaxLoadModelFP');
            // ->withoutMiddleware(['permission'])
            Route::any('loadSizeForProduct', 'ProductController@ajaxLoadSizeFP');
            Route::any('loadColorForProduct', 'ProductController@ajaxLoadColorFP');
        });

        ## Product_gold Route
        Route::group(['prefix' => 'product_gold'], function () {
            Route::any('/', 'ProductController@index_gold')->name('productDatatable');
            Route::any('add', 'ProductController@add_gold');
            Route::any('edit/{id}', 'ProductController@edit');
            Route::get('view/{id}', 'ProductController@view');

            Route::any('delete/{id}', 'ProductController@delete');
            Route::any('publish/{id}', 'ProductController@isActive');
            Route::any('destroy/{id}', 'ProductController@destroy');
            Route::post('getData', 'ProductController@getData');
            Route::any('loadModelForProduct', 'ProductController@ajaxLoadModelFP');
            Route::any('loadSizeForProduct', 'ProductController@ajaxLoadSizeFP');
            Route::any('loadColorForProduct', 'ProductController@ajaxLoadColorFP');
        });

        ## Product OB Route C
        Route::group(['prefix' => 'product_ob'], function () {

            Route::any('/', 'POpeningBalanceController@index');
            Route::any('add', 'POpeningBalanceController@add');
            Route::any('edit/{id}', 'POpeningBalanceController@edit');
            Route::get('view/{id}', 'POpeningBalanceController@view');
            Route::any('delete/{id}', 'POpeningBalanceController@delete');
            Route::any('publish/{id}', 'POpeningBalanceController@isActive');
            Route::any('destroy/{id}', 'POpeningBalanceController@destroy');
        });

        ## Customer OB Route C
        Route::group(['prefix' => 'customer_ob'], function () {
            Route::any('/', 'PCustomerOBController@index');
            Route::any('add', 'PCustomerOBController@add');
            Route::any('edit/{id}', 'PCustomerOBController@edit');
            Route::get('view/{id}', 'PCustomerOBController@view');
            Route::any('delete/{id}', 'PCustomerOBController@delete');
            Route::any('destroy/{id}', 'PCustomerOBController@destroy');
            // Route::any('publish/{id}', 'PCustomerOBController@isActive');
        });

        ## Price Updating  Route
        Route::group(['prefix' => 'price_updating'], function () {

            Route::any('/', 'PriceUpdatingController@index')->name('price_updating_datatable');
            Route::any('add', 'PriceUpdatingController@add');
            Route::any('edit/{id}', 'PriceUpdatingController@edit');
            Route::get('view/{id}', 'PriceUpdatingController@view');
            Route::any('delete', 'PriceUpdatingController@delete');
            Route::any('publish/{id}', 'PriceUpdatingController@isActive');
            Route::get('product_list', 'PriceUpdatingController@getProductList');
            Route::get('product_list_edit/{id}', 'PriceUpdatingController@edit');
        });

        ## Discount  Route
        Route::group(['prefix' => 'discount_set'], function () {

            Route::any('/', 'DiscountController@index');
            Route::any('add', 'DiscountController@add');
            Route::any('edit/{id}', 'DiscountController@edit');
            Route::get('view/{id}', 'DiscountController@view');
            Route::any('delete/{id}', 'DiscountController@delete');
            Route::any('publish/{id}', 'DiscountController@isActive');
        });

        ## GoldPrice  Route
        Route::group(['prefix' => 'gold_price'], function () {

            Route::any('/', 'GoldPriceController@index');
            Route::any('add', 'GoldPriceController@add');
            Route::any('edit/{id}', 'GoldPriceController@edit');
            Route::get('view/{id}', 'GoldPriceController@view');
            Route::any('delete/{id}', 'GoldPriceController@delete');
            Route::any('publish/{id}', 'GoldPriceController@isActive');
        });

        ## GoldPrice  Route
        Route::group(['prefix' => 'gold_price_cal'], function () {
            Route::any('/', 'GoldPriceController@calculator');
        });
    });

    ## Process --------------------------
    Route::group(['namespace' => 'Process'], function () {

        ## Day End Route C
        Route::group(['prefix' => 'day_end'], function () {
            Route::any('/', 'DayEndController@index')->name('dayendDatatable');
            Route::post('execute', 'DayEndController@end');

            Route::get('/ajaxDeleteDayEnd', 'DayEndController@ajaxDeleteDayEnd');
        });

        ## Month End Route C
        Route::group(['prefix' => 'month_end'], function () {
            Route::any('/', 'MonthEndController@index');
            Route::post('execute', 'MonthEndController@executeMonthEnd');
            Route::get('checkDayEndData', 'MonthEndController@checkDayEndData');
            Route::get('delete', 'MonthEndController@isDelete');
        });

        ## Sales Transfer Route
        Route::group(['prefix' => 'sales_transfer'], function () {
            Route::any('/', 'SalesTransferController@index')->name('SalesTransferList');;
            Route::any('add', 'SalesTransferController@add');
            Route::any('edit/{id}', 'SalesTransferController@edit');
            Route::get('view/{id}', 'SalesTransferController@view');
            Route::any('delete/{id}', 'SalesTransferController@delete');
            Route::any('approve', 'SalesTransferController@approve');
        });

        ## Audit  Route
        Route::group(['prefix' => 'audit'], function () {
            Route::any('/', 'AuditController@index');
            Route::any('add', 'AuditController@add');
            Route::any('edit/{id}', 'AuditController@edit');
            Route::get('view/{id}', 'AuditController@view');
            Route::any('delete', 'AuditController@delete');
            Route::any('publish/{id}', 'AuditController@isActive');
            Route::post('getData', 'AuditController@getData');
        });

        ## Authrization Route
        Route::group(['prefix' => 'authorization'], function () {
            Route::any('/', 'AuthorizationController@index');
            Route::any('/loadData', 'AuthorizationController@getData');

            Route::any('/execute', 'AuthorizationController@authorizeOnebyOne');
            Route::any('/executeall', 'AuthorizationController@authorizeALL');
        });

        ## UnAuthrization Route
        Route::group(['prefix' => 'unauthorization'], function () {
            Route::any('/', 'UnauthorizationController@index');
            Route::any('/loadData', 'UnauthorizationController@getData');

            Route::any('/execute', 'UnauthorizationController@unauthorizeOnebyOne');
            Route::any('/executeall', 'UnauthorizationController@unauthorizeALL');
        });

        ## Day And Month Back Script
        Route::group(['prefix' => 'day_back_script'], function () {
            Route::any('/', 'DayBackController@index');
            Route::post('/getinfo', 'DayBackController@getInfo');
            Route::post('/day_back', 'DayBackController@dayBack');
            // Route::any('delete', 'DayEndController@delete')->name('deletePosdayend');
        });
    });

    ## Transactionn --------------------------
    Route::group(['middleware' => ['TxSchedule'], 'namespace' => 'Transaction'], function () {

        ## Purchase --------------------------
        Route::group(['namespace' => 'Purchase'], function () {
            ## Purchase Route
            Route::group(['prefix' => 'purchase'], function () {
                Route::any('/', 'PurchaseController@index')->name('PurchaseList');
                Route::any('add', 'PurchaseController@add');
                Route::any('edit/{id}', 'PurchaseController@edit');
                Route::get('view/{id}', 'PurchaseController@view');
                Route::any('delete', 'PurchaseController@delete');
                // Route::any('publish/{id}', 'PurchaseController@isActive');
                Route::post('popUpSupplierData', 'PurchaseController@popUpSupplierDataInsert');
                Route::post('popUpProductDuplicateData', 'PurchaseController@popUpProductDuplicateDataInsert');
                Route::post('popUpProductNewData', 'PurchaseController@popUpProductNewDataInsert');
                Route::post('getData', 'PurchaseController@getData')->name('getPurchaseData');

                // Gold Route
                // Route::any('add_gold', 'PurchaseController@addGold');
                // Route::any('edit_gold/{id}', 'PurchaseController@editGold');

                Route::any('supplier_new', function () {
                    return view('POS.Transaction.Purchase.Popup.purchase_modal');
                });
                Route::any('new_product', function () {
                    return view('POS.Transaction.Purchase.Popup.product_new_modal');
                });
                Route::any('duplicate_product', function () {
                    return view('POS.Transaction.Purchase.Popup.product_duplicate_modal');
                });
            });

            ## Purchase Gold Route
            Route::group(['prefix' => 'purchase_gold'], function () {
                Route::any('/', 'PurchaseController@index_gold');
                Route::any('add', 'PurchaseController@addGold');
                Route::any('edit/{id}', 'PurchaseController@editGold');
                Route::get('view/{id}', 'PurchaseController@viewGold');
                Route::any('delete', 'PurchaseController@delete');

                Route::any('supplier_new', function () {
                    return view('POS.Transaction.Purchase.Popup.purchase_modal');
                });
            });

            ## Purchases Return Route C
            Route::group(['prefix' => 'purchase_return'], function () {

                Route::any('/', 'PurchaseReturnController@index')->name('purReturnIndexDtable');
                Route::any('add', 'PurchaseReturnController@add');
                Route::any('edit/{id}', 'PurchaseReturnController@edit');
                Route::get('view/{id}', 'PurchaseReturnController@view');
                Route::any('delete/{id}', 'PurchaseReturnController@delete');
                Route::any('publish/{id}', 'PurchaseReturnController@isActive');
                Route::post('getData', 'PurchaseReturnController@getData')->name('getPurchaseRData');
                Route::any('invoicepop/{id}', 'PurchaseReturnController@invoiceModal');

                // Gold Route
                // Route::any('add_gold', 'PurchaseReturnController@addGold');
                // Route::any('edit_gold/{id}', 'PurchaseReturnController@editGold');

            });

            ## Purchases Return Gold Route C
            Route::group(['prefix' => 'purchase_return_gold'], function () {

                Route::any('/', 'PurchaseReturnController@index_gold');

                Route::get('view/{id}', 'PurchaseReturnController@viewGold');
                Route::any('delete/{id}', 'PurchaseReturnController@delete');
                Route::any('publish/{id}', 'PurchaseReturnController@isActive');
                Route::post('getData', 'PurchaseReturnController@getData');
                // Gold Route
                Route::any('add', 'PurchaseReturnController@addGold');
                Route::any('edit/{id}', 'PurchaseReturnController@editGold');
            });

            ## Product Order Route
            Route::group(['prefix' => 'product_order'], function () {
                Route::any('/', 'OrderController@index');
                Route::any('add', 'OrderController@add');
                Route::any('edit/{id}', 'OrderController@edit');
                Route::get('view/{id}', 'OrderController@view');
                Route::any('delete/{id}', 'OrderController@delete');
                Route::any('approve/{id}', 'OrderController@isApprove');
                Route::post('popUpProductNewData', 'OrderController@popUpProductNewDataInsert');
                Route::post('getData', 'OrderController@getData')->name('getOrderData');

                Route::get('work_order/{id}', 'OrderController@workOrder');


                Route::any('new_product', function () {
                    return view('POS.Transaction.Purchase.Popup.product_new_modal');
                });

                Route::any('duplicate_product', function () {
                    return view('POS.Transaction.Purchase.Popup.product_duplicate_modal');
                });

                Route::any('order_add_branch', function () {
                    //(1);dd
                    return view('POS.Transaction.Purchase.Popup.order_branch_modal');
                });

                Route::any('order_add_branch_view', function () {
                    //(1);dd
                    return view('POS.Transaction.Purchase.Popup.order_branch_modal_view');
                });
            });
        });

        ## Issue --------------------------
        Route::group(['namespace' => 'Issue'], function () {
            ## issue Route
            Route::group(['prefix' => 'issue'], function () {
                Route::any('/', 'IssueController@index');
                Route::any('add', 'IssueController@add');
                Route::any('edit/{id}', 'IssueController@edit');
                Route::get('view/{id}', 'IssueController@view');
                Route::any('delete', 'IssueController@delete');
                Route::any('publish/{id}', 'IssueController@isActive');
                Route::post('getData', 'IssueController@getData')->name('getIssueData');
                Route::any('approve/{id}', 'IssueController@isApprove');
                Route::any('invoicepop/{id}', 'IssueController@invoiceModal');
            });

            ## Issue Return Route C
            Route::group(['prefix' => 'issue_return'], function () {

                Route::any('/', 'IssueReturnController@index');
                Route::any('add', 'IssueReturnController@add');
                Route::any('edit/{id}', 'IssueReturnController@edit');
                Route::get('view/{id}', 'IssueReturnController@view');
                Route::any('delete/{id}', 'IssueReturnController@delete');
                Route::any('publish/{id}', 'IssueReturnController@isActive');
                Route::post('getData', 'IssueReturnController@getData')->name('getIssueRData');
                Route::any('invoicepop/{id}', 'IssueReturnController@invoiceModal');
            });

            ## issue Gold Route
            Route::group(['prefix' => 'issue_gold'], function () {
                Route::any('/', 'IssueController@index_gold');
                Route::any('add', 'IssueController@addGold');
                Route::any('edit/{id}', 'IssueController@editGold');
                Route::get('view/{id}', 'IssueController@viewGold');
                Route::any('delete', 'IssueController@delete');
                Route::any('publish/{id}', 'IssueController@isActive');
                Route::post('getData', 'IssueController@getData');
                Route::any('approve/{id}', 'IssueController@isApprove');
            });

            ## Issue Return Gold Route C
            Route::group(['prefix' => 'issue_return_gold'], function () {

                Route::any('/', 'IssueReturnController@index_gold');
                Route::any('add', 'IssueReturnController@addGold');
                Route::any('edit/{id}', 'IssueReturnController@editGold');
                Route::get('view/{id}', 'IssueReturnController@viewGold');
                Route::any('delete/{id}', 'IssueReturnController@delete');
                Route::any('publish/{id}', 'IssueReturnController@isActive');
                Route::post('getData', 'IssueReturnController@getData');
            });
        });

        ## Sales --------------------------
        Route::group(['namespace' => 'Sales'], function () {

            ## Cash Sales Route C
            Route::group(['prefix' => 'sales_cash'], function () {

                Route::any('/', 'SalesController@index');
                Route::any('add', 'SalesController@add');
                Route::any('edit/{id}', 'SalesController@edit');
                Route::any('view/{id}', 'SalesController@view');
                Route::any('delete/{id}', 'SalesController@delete');
                Route::any('invoice/{id}', 'SalesController@invoice');
                Route::any('invoicepop/{id}', 'SalesController@invoiceModal');
                Route::post('getData', 'SalesController@getData')->name('getSalesData');
                // Gold Route
                // Route::any('add_gold', 'SalesController@addGold');
                // Route::any('edit_gold/{id}', 'SalesController@editGold');
                // Route::any('publish/{id}', 'SalesController@isActive');
            });

            ## Cash Sales  Gold Route C
            Route::group(['prefix' => 'sales_cash_gold'], function () {

                Route::any('/', 'SalesController@index_gold');

                Route::any('view/{id}', 'SalesController@view');
                Route::any('view/{id}', 'SalesController@viewGold');
                Route::any('delete/{id}', 'SalesController@delete');
                Route::any('invoice/{id}', 'SalesController@invoice');
                Route::any('invoicepop/{id}', 'SalesController@invoiceModal');
                Route::post('getData', 'SalesController@getData');
                // Gold Route
                Route::any('add', 'SalesController@addGold');
                Route::any('edit/{id}', 'SalesController@editGold');
                // Route::any('publish/{id}', 'SalesController@isActive');
            });



            Route::post('/popUpCustomerData', 'SalesController@popUpCustomerDataInsert');
            ## Installment Sales Route C
            Route::group(['prefix' => 'sales_installment'], function () {

                Route::any('/', 'SalesController@instIndex')->name('InstallmentSalesList');
                Route::any('add', 'SalesController@instAdd');
                Route::any('edit/{id}', 'SalesController@instEdit');
                Route::any('view/{id}', 'SalesController@view');
                Route::any('delete/{id}', 'SalesController@delete');
                Route::any('invoice/{id}', 'SalesController@invoice');
                Route::post('getData', 'SalesController@getData');
                // Route::any('publish/{id}', 'SalesController@isActive');
                Route::any('invoicepop/{id}', 'SalesController@invoiceModal');
                Route::any('inst_pass', 'SalesController@passbook')->name('InstallmentPassBook');
            });

            ##Shop Sales Route C
            Route::group(['prefix' => 'sales'], function () {

                Route::any('/', 'ShopSalesController@index');
                Route::any('add', 'ShopSalesController@add');
                Route::any('edit/{id}', 'ShopSalesController@edit');
                Route::any('view/{id}', 'ShopSalesController@view');
                Route::any('delete/{id}', 'ShopSalesController@delete');
                Route::post('/getData', 'ShopSalesController@getData')->name('getShopSaleData');
                Route::any('invoice/{id}', 'ShopSalesController@invoice');
                Route::any('invoiceshop/{id}', 'ShopSalesController@invoiceForShop');
                Route::any('invoicepaper/{id}', 'ShopSalesController@invoiceForA4');
                Route::post('/popUpCustomerData', 'ShopSalesController@popUpCustomerDataInsert');
                // testing route
                Route::any('invoiceTest/{id}', 'ShopSalesController@invoiceShopTest');

                Route::any('customer_new', function () {
                    return view('POS.Transaction.Sales.Popup.customer_modal');
                });
            });


            ## Due Sales Route C
            Route::group(['prefix' => 'due_sales'], function () {

                Route::any('/', 'DueSalesController@index');
                Route::any('add', 'DueSalesController@add');
                Route::any('edit/{id}', 'DueSalesController@edit');
                Route::any('view/{id}', 'DueSalesController@view');
                Route::any('delete/{id}', 'DueSalesController@delete');
                Route::any('invoice/{id}', 'DueSalesController@invoice');
                Route::any('invoicepop/{id}', 'DueSalesController@invoiceModal');
                Route::post('getData', 'DueSalesController@getData')->name('getDueData');
                // Route::any('publish/{id}', 'DueSalesController@isActive');

                Route::any('customer_new', function () {
                    return view('POS.Transaction.Sales.Popup.customer_modal');
                });
            });
        });

        ## SalesReturn --------------------------
        Route::group(['namespace' => 'SalesReturn'], function () {
            ## cash sales Return Route C
            Route::group(['prefix' => 'sales_return'], function () {

                Route::any('/', 'SaleReturnController@index')->name('CashSalesRList');
                Route::any('add', 'SaleReturnController@add');
                Route::any('edit/{id}', 'SaleReturnController@edit');
                Route::get('view/{id}', 'SaleReturnController@view');
                Route::any('delete/{id}', 'SaleReturnController@delete');
                Route::any('publish/{id}', 'SaleReturnController@isActive');
                Route::post('getData', 'SaleReturnController@getData')->name('getSalesReturnData');
            });

            ## Shop Sales Return Route C
            Route::group(['prefix' => 's_return'], function () {

                Route::any('/', 'ShopSaleReturnController@index')->name('ShopSalesRList');
                Route::any('add', 'ShopSaleReturnController@add');
                Route::any('edit/{id}', 'ShopSaleReturnController@edit');
                Route::get('view/{id}', 'ShopSaleReturnController@view');
                Route::any('delete/{id}', 'ShopSaleReturnController@delete');
                Route::any('/getData', 'ShopSaleReturnController@getData');

                Route::any('invoice/{id}', 'ShopSaleReturnController@invoice');
                Route::any('invoiceshop/{id}', 'ShopSaleReturnController@invoiceForShop');
                Route::any('invoicepaper/{id}', 'ShopSaleReturnController@invoiceForA4');
            });
        });

        ## InstCollection --------------------------
        Route::group(['namespace' => 'InstCollection'], function () {
            ## Collection Route
            Route::group(['prefix' => 'collection'], function () {

                Route::any('/', 'CollectionController@index')->name('CollectionList');
                Route::any('add', 'CollectionController@add');
                Route::any('edit/{id}', 'CollectionController@edit');
                Route::get('view/{id}', 'CollectionController@view');
                Route::get('delete/{id}', 'CollectionController@delete');
                Route::any('/getData', 'CollectionController@getData');
                // Route::get('destroy/{id}', 'CollectionController@destroy');
                // ////// collection auto process
                Route::any('/auto_process', 'CollectionController@autoProcess');
                Route::any('ajaxColCustList', 'CollectionController@ajaxCollCustList');
                Route::any('ajaxColEmpList', 'CollectionController@ajaxCollEmpList');
            });
            ## Collection auto process
            Route::any('/col_auto_process', 'CollectionController@autoProcess');
        });

        ## Transfer Route
        Route::group(['prefix' => 'transfer'], function () {

            Route::any('/', 'TransferController@index')->name('transferDatatable');
            Route::any('add', 'TransferController@add');

            Route::any('edit/{id}', 'TransferController@edit');
            Route::get('view/{id}', 'TransferController@view');
            Route::any('delete/{id}', 'TransferController@delete');
            Route::any('publish/{id}', 'TransferController@isActive');
            Route::post('getData', 'TransferController@getData')->name('getTransferData');
            Route::any('approve/{id}', 'TransferController@isApprove');
            Route::any('invoicepop/{id}', 'TransferController@invoiceModal');

            // Gold Route
            // Route::any('add_gold', 'TransferController@addGold');
            // Route::any('edit_gold/{id}', 'TransferController@editGold');

        });

        ## Transfer Gold Route
        Route::group(['prefix' => 'transfer_gold'], function () {

            Route::any('/', 'TransferController@index_gold');
            Route::any('add', 'TransferController@addGold');

            Route::any('edit/{id}', 'TransferController@editGold');
            Route::get('view/{id}', 'TransferController@viewGold');
            Route::any('delete/{id}', 'TransferController@delete');
            Route::any('publish/{id}', 'TransferController@isActive');
            Route::post('getData', 'TransferController@getData');

            Route::any('approve/{id}', 'TransferController@isApprove');
        });

        ## Supplier Payment Route C
        Route::group(['prefix' => 'supplier_payment'], function () {
            Route::any('/', 'SupplierPaymentController@index')->name('SupplierPaymentList');
            Route::any('add', 'SupplierPaymentController@add');
            Route::any('edit/{id}', 'SupplierPaymentController@edit');
            Route::get('view/{id}', 'SupplierPaymentController@view');
            Route::get('delete/{id}', 'SupplierPaymentController@delete');
            Route::any('publish/{id}', 'SupplierPaymentController@isActive');

            Route::post('getData', 'SupplierPaymentController@getData')->name('getSupplierData');
        });

        ##Requisition Route
        Route::group(['prefix' => 'requisition'], function () {
            Route::any('/', 'RequisitionController@index');
            Route::any('add', 'RequisitionController@add');
            Route::any('edit/{id}', 'RequisitionController@edit');
            Route::get('view/{id}', 'RequisitionController@view');
            Route::any('delete/{id}', 'RequisitionController@delete');
            Route::any('approve/{id}', 'RequisitionController@isApprove');
            Route::post('getData', 'RequisitionController@getData')->name('getRequData');
        });

        ##Product Waiver/Gift Route
        Route::group(['prefix' => 'waiver_product'], function () {
            Route::any('/', 'WaiverProductController@index')->name('WaiverProductList');
            Route::any('add', 'WaiverProductController@add');
            Route::any('edit/{id}', 'WaiverProductController@edit');
            Route::get('view/{id}', 'WaiverProductController@view');
            Route::any('delete', 'WaiverProductController@delete');
            Route::any('invoicepop/{id}', 'WaiverProductController@invoiceModal');
            Route::post('getData', 'WaiverProductController@getData')->name('getWaiverData');
        });
    });

    // // ## Report Route C
    Route::group(['prefix' => 'report'], function () {

        /* start ---------------------- Collection Reports */

        // Route::any('collection_sheet', 'Reports\CollectionSheetController@allCollectionSheet');
        // Route::any('monthly_collection_sheet', 'Reports\CollectionSheetController@allCollectionSheet');
        // Route::any('weekly_collection_sheet', 'Reports\CollectionSheetController@allCollectionSheet');
        // Route::any('daily_collection_sheet', 'Reports\CollectionSheetController@allCollectionSheet');

        Route::any('collection_sheet/{sheetFor}', function ($sheetFor = null) {
            return view('POS/Reports/CollectionSheet/index', compact('sheetFor'));
        });
        Route::any('collection_sheet/{sheetFor}/api', 'Reports\CollectionSheetController@allCollectionSheet');
        Route::any('collection_sheet/{sheetFor}/checkWorkingDay', 'Reports\CollectionSheetController@getWorkingDays');
        /* end ---------------------- Collection Reports */

        Route::group(['namespace' => 'Reports'], function () {

            /* start ---------------------- Collection Reports */
            Route::any('collections', 'CollectionReportController@getCollectionAll')->name('allcollectionDataTable');
            Route::any('collection_with_profit', 'CollectionReportController@getCollectionWithProfit')->name('collectionProfitDatatable');
            Route::any('recoverable_register', 'CollectionReportController@getRecoverableRegisterReport')->name('recoverRegisterDataTable');
            /* start ---------------------- Collection Reports */

            /* start ---------------------- Due Reports */
            Route::any('current_due', 'DueReportController@getCurrentDue');
            Route::any('over_due', 'DueReportController@getOverDue');
            Route::any('current_n_over_due', 'DueReportController@getCurrentnOverDue');
            Route::any('regular_due', 'DueReportController@getRegularDue');
            Route::any('regular_due2', 'DueReportController@getRegularDueTest'); // test perpous
            Route::any('customer_due', 'DueReportController@customerDue')->name('customerDueDatatable');
            /* end ---------------------- Due Reports */

            /* start ---------------------- Stock Reports */
            Route::any('stock_branch', 'StockReportController@stockBranch');
            Route::any('stock_branch_load', 'StockReportController@stockBranchLoad');

            // start Demof outlet route
            Route::any('stock_outlet', 'StockReportController@stockOutlet');
            Route::any('stock_outlet_load', 'StockReportController@stockOutletLoad');

            Route::group(['prefix' => 'stock_inv_outlet'], function () {
                Route::any('/', 'StockReportController@stockInvOutlet');
            });
            // end Demof outlet route

            // Route::any('stock_ho', 'StockReportController@stockHO');
            // Route::any('stock_ho_load', 'StockReportController@stockHOLoad');


            // Route::any('stock_inv_branch', 'StockReportController@stockInvBranch');
            // Route::any('stock_inv_ho', 'StockReportController@stockInvHO');
            Route::any('prod_stock_branch', 'StockReportController@stockProdWiseBranch');
            Route::any('prod_stock_branch_s', 'StockReportController@stockProdWiseBranch');
            // Route::any('stock_branch_model_wise', 'StockReportController@stockModelWiseBranchView');
            Route::group(['prefix' => 'stock_inv_ho'], function () {
                Route::any('/', 'StockReportController@stockInvHO');
                Route::any('/loadData', 'StockReportController@stockInvHO');
            });

            Route::group(['prefix' => 'stock_inv_branch'], function () {
                Route::any('/', 'StockReportController@stockInvBranch');
                Route::any('/loadData', 'StockReportController@stockInvBranch');
            });

            // Route::any('stock_branch_wise', 'StockReportController@stockAllBranch');

            Route::group(['prefix' => 'stock_ho'], function () {
                Route::any('/', 'StockReportController@stockHO');
                Route::any('/loadData', 'StockReportController@stockHO');
            });

            Route::group(['prefix' => 'stock_branch_wise'], function () {
                Route::any('/', 'StockReportController@stockAllBranch');
                Route::any('/loadData', 'StockReportController@stockAllBranch');
            });

            /* Start ---------------------- At a glance stock report */
            Route::group(['prefix' => 'at_a_glance_stock'], function () {
                Route::any('/', 'AtAGlanceStockReportController@index');
                Route::any('/loadData', 'AtAGlanceStockReportController@index');

                // Route::any('sub_cat_wise', 'AtAGlanceStockReportController@subCatWiseAllBranchStock');
                // Route::any('model_wise', 'AtAGlanceStockReportController@modelWiseAllBranchStock');
                // Route::any('product_wise', 'AtAGlanceStockReportController@productWiseAllBranchStock');
            });
            /* End ---------------------- At a glance stock report */

            /* Start Cross check stock Report */

            Route::group(['prefix' => 'cross_check_stock_report'], function () {
                Route::any('/', 'StockReportController@crossStockCheck');
                Route::any('/loadData', 'StockReportController@crossStockCheck');
            });

            /* End Cross check stock Report */



            /* end ---------------------- Stock Reports */

            /* start ---------------------- Sales Reports */
            Route::any('salesr', 'SalesReport\SalesNgoReportController@getsale')->name('saleRDatatable');
            Route::any('sales_profit', 'SalesReport\SalesNgoReportController@getsalesprofit')->name('saleProfitDatatable');
            Route::any('sales_details', 'SalesReport\SalesNgoReportController@getsaleDetails');
            Route::any('sales_details_summary', 'SalesReport\SalesNgoReportController@getsaleDetailsSummary');
            // Route::any('tsales_summary', 'SalesReport\SalesNgoReportController@getTSalesSummary')->name('TSalesSummaryDatatable');
            Route::any('tsales_summary', 'SalesReport\SalesNgoReportController@getTSalesSummaryBranch');

            Route::any('salesr_s', 'SalesReport\SalesReportController@getsale')->name('saleRSDatatable');
            Route::any('sales_profit_s', 'SalesReport\SalesReportController@getsalesprofit')->name('saleProfitSDatatable');
            Route::any('sales_details_s', 'SalesReport\SalesReportController@getsaleDetails')->name('saleDetailsWRDatatable');
            Route::any('sales_details_summary_s', 'SalesReport\SalesReportController@getsaleSummaryDetails')->name('saleDetailsSUMDatatable');
            Route::any('product_w_sales_s', 'SalesReport\SalesReportController@getsaleDetailsProductWise');

            /* end ---------------------- Sales Reports */

            /* start ---------------------- Payment Register Reports */
            Route::any('sup_payment', 'PaymentRegisterReportController@getPaymentAll')->name('paymentreportDatatable');
            Route::any('supplier_due', 'PaymentRegisterReportController@supplierDue')->name('supplierDueDatatable');
            Route::any('customer_sales_due', 'PaymentRegisterReportController@customerDue')->name('customerDueDatatable');
            /* end ---------------------- Payment Register Reports */

            /* start ---------------------- Sales Return Reports */
            Route::any('sales_return', 'SalesReturnReportController@getSalesReturn')->name('SalesReturnDataTable');
            Route::any('sales_return_s', 'SalesReturnReportController@getSalesReturn')->name('SalesReturnDataTable');
            /* end ---------------------- Sales Return Reports */

            /* start ---------------------- Issue Register Reports */
            Route::any('issue', 'IssueRegisterReportController@getIssueAll')->name('issueDataTable');
            // Route::any('issue', 'IssueRegisterReportController@getIssueAll')->name('issueDataTable');
            Route::any('issue_return', 'IssueRegisterReportController@getIssueReturnAll')->name('issueReturnDataTable');
            /* end ---------------------- Issue Register Reports */

            /* start ---------------------- Purchase Register Reports */
            Route::any('product_order', 'PurchaseRegisterReportController@getProdOrder');
            Route::any('prod_order/{selectedid?}', 'PurchaseRegisterReportController@getProdOrderDetails');
            Route::any('purchase', 'PurchaseRegisterReportController@getPurchaseAll')->name('purchasereportDatatable');
            Route::any('purchase_summary', 'PurchaseRegisterReportController@getSummaryPurchaseAll')->name('purchasereportDatatable');
            Route::any('purchase_return', 'PurchaseRegisterReportController@getPurchaseReturnAll')->name('PurReturnDataTable');

            Route::any('gold_purchase_amount_diff', 'PurchaseRegisterReportController@getGoldPurchaseAmountDiff')->name('PurAmntDiffDataTable');
            /* start ---------------------- Purchase Register Reports */

            /* start ---------------------- Transfer Reports */
            Route::any('transferin', 'TransferReportController@getTransferIn')->name('transferinDataTable');
            Route::any('transferout', 'TransferReportController@getTransferOut')->name('transferoutDataTable');
            /* start ---------------------- Transfer Reports */

            /* start ---------------------- Mis Reports */
            Route::any('mis_report-1', 'MisReportController@getMISReportFirst');
            /* end ---------------------- Mis Reports */

            /* start ---------------------- Requisition Reports */
            Route::any('requisition', 'RequisitionReportController@getRequisition');
            /* start ---------------------- Requisition Reports */

            /* start ---------------------- Register Reports */
            Route::any('customer_report_date_wise', 'RegisterReportController@customerReportDateWise');
            Route::any('customer_wise_sales_report', 'RegisterReportController@customerWiseSalesReport');
            Route::any('area_sales', 'RegisterReportController@getAreaWiseSales');
            Route::any('zone_sales', 'RegisterReportController@getZoneSales');
            Route::any('branch_customer', 'RegisterReportController@getbranchcustomer')->name('branchcustomerDatatable');
            /* end ---------------------- Register Reports */

            /* start ---------------------- Waiver Reports */
            Route::any('waiver_details_r', 'WaiverReportController@getWaiverDetails');
            Route::any('waiver_summary_r', 'WaiverReportController@getWaiverSummary');
            /* start ---------------------- Waiver Reports */

            /* start ---------------------- Product Reports */
            Route::any('price_list', 'ProductReportController@getPriceList');
            Route::any('price_updating_report', 'ProductReportController@getPriceUpdating');
            Route::any('product_register', 'ProductReportController@getProductRegister');
            /* start ---------------------- Product Reports */

            /* start ---------------------- Gold Reports */
            Route::any('gold_purchase_report', 'GoldProductReportController@getGoldPurchaseAll');
            Route::any('gold_purchase_return_report', 'GoldProductReportController@getGoldPurchaseReturnAll');

            Route::any('gold_issue_report', 'GoldProductReportController@getGoldIssueAll');
            Route::any('gold_issue_return_report', 'GoldProductReportController@getGoldIssueReturnAll');

            Route::any('gold_price_list', 'GoldProductReportController@getPriceList');


            // Route::any('gold_sales_report', 'GoldProductReportController@getGoldSalesAll');
            Route::any('gold_sales_details_report', 'GoldProductReportController@getGoldSalesDetails');
            // Route::any('sales_details', 'GoldProductReportController@getsaleDetails');
            // Route::any('sales_details_summary', 'GoldProductReportController@getsaleDetailsSummary');
            // Route::any('tsales_summary', 'GoldProductReportController@getTSalesSummary');

            Route::any('gold_transfer_in_report', 'GoldProductReportController@getGoldTransferInAll');
            Route::any('gold_transfer_out_report', 'GoldProductReportController@getGoldTransferOutAll');

            /* end ---------------------- Gold Reports */

            /* start ---------------------- Statement Reports */
            Route::group(['prefix' => 'customer_st'], function () {
                Route::any('/', 'StatementReportController@getCutomerStatement');
                Route::any('/loadData', 'StatementReportController@getCutomerStatementLoad');
            });

            Route::group(['prefix' => 'supplier_st'], function () {
                Route::any('/', 'StatementReportController@getSupplierStatement');
                Route::any('/loadData', 'StatementReportController@getSupplierStatementLoad');
            });
            /* end ---------------------- Statement Reports */
        });

        // Under COnstruction
        // Route::any('region_sales', 'ReportController@getRegionSales');
        Route::any('customer_details', 'ReportController@getCustomerDetails')->name('CustDetailsDataTable');
        // ai report ta upore kora hoyeche, customern wise sales report


        // Under Construction
        // Route::any('mis_report-2', 'ReportController@getMISReportSecond');

        // Route::any('incentive', 'ReportController@getincentive')->name('incentiveDatatable');
        // Route::any('collection_register', 'ReportController@getcollregister')->name('collregisterDatatable');

        // Route::any('branchr', 'ReportController@getbranch')->name('branchRDatatable');
        // Route::any('arear', 'ReportController@getarea')->name('areaRDatatable');
        // Route::any('zoner', 'ReportController@getzone')->name('zoneRDatatable');

        // Route::any('installment_receivable', 'ReportController@getInstallmentReceivable');
    });

    // // ## Necissity File  Route C
    Route::group(['prefix' => 'file_management'], function () {
        Route::any('/', 'FileController@index')->name('fileDatatable');
        Route::any('add', 'FileController@add');
        Route::any('edit/{id}', 'FileController@edit');
        Route::get('view/{id}', 'FileController@download');
        Route::any('delete', 'FileController@delete');
        Route::any('publish/{id}', 'FileController@isActive');
        Route::any('destroy/{id}', 'FileController@destroy');
    });

    ## START CRM (Customer relationship management)  Route C
    Route::group(['prefix' => 'crm'], function () {

        Route::group(['namespace' => 'CRM'], function () {

            /* start ---------------------- Loyalty type */
            Route::group(['prefix' => 'loyalty_type'], function () {
                Route::any('/', 'membershipSettingController@index_loyalty_type');
                Route::any('add', 'membershipSettingController@add_loyalty_type');
                Route::any('edit/{id}', 'membershipSettingController@edit_loyalty_type');
                Route::get('view/{id}', 'membershipSettingController@view_loyalty_type');
                Route::any('delete/{id}', 'membershipSettingController@delete_loyalty_type');
            });
            /* end ---------------------- Loyalty type */

            /* start ---------------------- Cash Member */
            Route::group(['prefix' => 'cash_member'], function () {
                Route::any('/', 'membershipSettingController@index_cash_member');
                Route::any('add', 'membershipSettingController@add_cash_member');
                Route::any('edit/{id}', 'membershipSettingController@edit_cash_member');
                Route::get('view/{id}', 'membershipSettingController@view_cash_member');
                Route::any('delete/{id}', 'membershipSettingController@delete_cash_member');
                Route::post('getData', 'membershipSettingController@getData');
            });
            /* end ---------------------- Cash Member */

            /* start ---------------------- Installment Member */
            Route::group(['prefix' => 'inst_member'], function () {
                Route::any('/', 'membershipSettingController@index_inst_member');
                Route::any('add', 'membershipSettingController@add_inst_member');
                Route::any('edit/{id}', 'membershipSettingController@edit_inst_member');
                Route::get('view/{id}', 'membershipSettingController@view_inst_member');
                Route::any('delete/{id}', 'membershipSettingController@delete_inst_member');
            });
            /* end ---------------------- Installment Member */

            /* start ---------------------- Credit Member */
            Route::group(['prefix' => 'credit_member'], function () {
                Route::any('/', 'membershipSettingController@index_credit_member');
                Route::any('add', 'membershipSettingController@add_credit_member');
                Route::any('edit/{id}', 'membershipSettingController@edit_credit_member');
                Route::get('view/{id}', 'membershipSettingController@view_credit_member');
                Route::any('delete/{id}', 'membershipSettingController@delete_credit_member');
            });
            /* end ---------------------- Credit Member */
        });
    });
    ## END CRM (Customer relationship management)  Route C

});
