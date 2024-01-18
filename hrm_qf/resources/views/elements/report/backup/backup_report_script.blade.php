@php
use App\Services\CommonService as Common;
$requestData = Request::all();

@endphp

@if((isset($monthYear) && $monthYear) || (isset($month) && $month))
<style>
    /* .ui-datepicker-calendar {
        display: none;
    } */
</style>
@endif

<script type="text/javascript"> // // For Global variable
    var zoneG = "{{ (isset($zone) && $zone) ? 1 : 0 }}";
    var areaG = "{{ (isset($area) && $area) ? 1 : 0 }}";
    var projectG = "{{ (isset($project) && $project) ? 1 : 0 }}";
    var projectTypeG = "{{ (isset($projectType) && $projectType) ? 1 : 0 }}";
    var branchG = "{{ (isset($branch) && $branch) ? 1 : 0 }}";
    var branchFromG = "{{ (isset($branchFrom) && $branchFrom) ? 1 : 0 }}";
    var branchToG = "{{ (isset($branchTo) && $branchTo) ? 1 : 0 }}";
    var deliveryPlaceG = "{{ (isset($deliveryPlace) && $deliveryPlace) ? 1 : 0 }}";

    var groupG = "{{ (isset($group) && $group) ? 1 : 0 }}";
    var modelG = "{{ (isset($model) && $model) ? 1 : 0 }}";
    var productG = "{{ (isset($product) && $product) ? 1 : 0 }}";
    var supplierG = "{{ (isset($supplier) && $supplier) ? 1 : 0 }}";

    var groupGInv = "{{ (isset($groupInv) && $groupInv) ? 1 : 0 }}";
    var modelGInv = "{{ (isset($modelInv) && $modelInv) ? 1 : 0 }}";
    var productGInv = "{{ (isset($productInv) && $productInv) ? 1 : 0 }}";
    var supplierGInv = "{{ (isset($supplierInv) && $supplierInv) ? 1 : 0 }}";

    var purchaseNoG = "{{ (isset($purchaseNo) && $purchaseNo) ? 1 : 0 }}";
    var orderNoG = "{{ (isset($orderNo) && $orderNo) ? 1 : 0 }}";
    var invoiceNoG = "{{ (isset($invoiceNo) && $invoiceNo) ? 1 : 0 }}";

    var customerG = "{{ (isset($customer) && $customer) ? 1 : 0 }}";
    var employeeG = "{{ (isset($employee) && $employee) ? 1 : 0 }}";
    var salesTypeG = "{{ (isset($salesType) && $salesType) ? 1 : 0 }}";
    var salesBillNoG = "{{ (isset($salesBillNo) && $salesBillNo) ? 1 : 0 }}";
    var issueRetBillNoG = "{{ (isset($issueRetBillNo) && $issueRetBillNo) ? 1 : 0 }}";
    var stockG = "{{ (isset($stock) && $stock) ? 1 : 0 }}";

    var ledgerG = "{{ (isset($ledger) && $ledger) ? 1 : 0 }}";
    var ledgerCashG = "{{ (isset($ledgerCash) && $ledgerCash) ? 1 : 0 }}";
    var ledgerBankG = "{{ (isset($ledgerBank) && $ledgerBank) ? 1 : 0 }}";

    var voucherG = "{{ (isset($voucher) && $voucher) ? 1 : 0 }}";
    var voucherTypeG = "{{ (isset($voucherType) && $voucherType) ? 1 : 0 }}";

    var branchAccG = "{{ (isset($branchAcc) && $branchAcc) ? 1 : 0 }}";
    var vTypeReceiptG = "{{ (isset($vTypeReceipt) && $vTypeReceipt) ? 1 : 0 }}";
    var depthLevelG = "{{ (isset($depthLevel) && $depthLevel) ? 1 : 0 }}";
    var roundUpG = "{{ (isset($roundUp) && $roundUp) ? 1 : 0 }}";
    var zeroBalanceG = "{{ (isset($zeroBalance) && $zeroBalance) ? 1 : 0 }}";


    var startDateG = "{{ (isset($startDate) && $startDate) ? 1 : 0 }}";
    var endDateG = "{{ (isset($endDate) && $endDate) ? 1 : 0 }}";
    var monthYearG = "{{ (isset($monthYear) && $monthYear) ? 1 : 0 }}";
    var searchByG = "{{ (isset($searchBy) && $searchBy) ? 1 : 0 }}";

    var currentYearG = "{{ (isset($currentYear) && $currentYear) ? 1 : 0 }}";
    var dateRangeG = "{{ (isset($dateRange) && $dateRange) ? 1 : 0 }}";
    var monthG = "{{ (isset($month) && $month) ? 1 : 0 }}";

    var allWithWihoutHO = "{{ (isset($allWithWihoutHO) && $allWithWihoutHO) ? 1 : 0 }}";

    if(allWithWihoutHO == 1){
        $(document).ready(function () {
            var newOption = '<option value="-1" data-select2-id="-1" selected>All (With HO)</option>';
            newOption += '<option value="-2" data-select2-id="-2">All (Without HO)</option>';
            // Append it to the select
            $('#branch_id').prepend(newOption).trigger('change');
        });
    }

    /////////////////////////////
    var catId = "{{ isset($requestData['cat_id']) ? $requestData['cat_id'] : '' }}";
    var subCatId = "{{ isset($requestData['sub_cat_id']) ? $requestData['sub_cat_id'] : '' }}";
    var productId = "{{ isset($requestData['product_id']) ? $requestData['product_id'] : '' }}";
    var modelId = "{{ isset($requestData['model_id']) ? $requestData['model_id'] : '' }}";
</script>

<script type="text/javascript"> // // function For request selected variable 
    async function fnForRequestedVariable(){
        if(zoneG == 1){
            let zoneId = "{{ isset($requestData['zone_id']) ? $requestData['zone_id'] : '' }}";
            if (zoneId != '') {
                $('#zone_id').val(zoneId).attr("selected", "selected");
            }
        }

        if(areaG == 1){
            let areaId = "{{ isset($requestData['area_id']) ? $requestData['area_id'] : '' }}";
            if (areaId != '') {
                $('#area_id').val(areaId).attr("selected", "selected");

                // reportBranchTxt = $('#area_id').find("option:selected").text();
            }
        }

        {
            let branchId = "{{ isset($requestData['branch_id']) ? $requestData['branch_id'] : '' }}";
            if (branchId != '') {
                $('#branch_id').val(branchId).attr("selected", "selected");

                // reportBranchTxt = $('#branch_id').find("option:selected").text();
            }
        }

        if(projectG == 1){
            let projectId = "{{ isset($requestData['project_id']) ? $requestData['project_id'] : '' }}";
            if (projectId != '') {
                $('#project_id').val(projectId).attr("selected", "selected");
            }
        }

        if(projectTypeG == 1){
            let projectTypeId = "{{ isset($requestData['project_type_id']) ? $requestData['project_type_id'] : '' }}";
            if (projectTypeId != '') {
                $('#project_type_id').val(projectTypeId).attr("selected", "selected");
            }
        }

        if(groupG == 1){
            let groupId = "{{ isset($requestData['group_id']) ? $requestData['group_id'] : '' }}";
            if (groupId != '') {
                $('#group_id').val(groupId).attr("selected", "selected");
            }

            let catId = "{{ isset($requestData['cat_id']) ? $requestData['cat_id'] : '' }}";
            if (catId != '') {
                $('#cat_id').val(catId).attr("selected", "selected");
            }

            let subCatId = "{{ isset($requestData['sub_cat_id']) ? $requestData['sub_cat_id'] : '' }}";
            if (subCatId != '') {
                $('#sub_cat_id').val(subCatId).attr("selected", "selected");
            }

            let brandId = "{{ isset($requestData['brand_id']) ? $requestData['brand_id'] : '' }}";
            if (brandId != '') {
                $('#brand_id').val(brandId).attr("selected", "selected");
            }
        }

        if(modelG == 1){
            let modelId = "{{ isset($requestData['model_id']) ? $requestData['model_id'] : '' }}";
            if (modelId != '') {
                $('#model_id').val(modelId).attr("selected", "selected");
            }
        }

        if(productG == 1){
            let productId = "{{ isset($requestData['product_id']) ? $requestData['product_id'] : '' }}";
            if (productId != '') {
                $('#product_id').val(productId).attr("selected", "selected");
            }
        }

        if(supplierG == 1){
            let supplierId = "{{ isset($requestData['supplier_id']) ? $requestData['supplier_id'] : '' }}";
            if (supplierId != '') {
                $('#supplier_id').val(supplierId).attr("selected", "selected");
            }
        }

        if(salesTypeG == 1){
            let sales_type = "{{ isset($requestData['sales_type']) ? $requestData['sales_type'] : '' }}";
            if (sales_type != '') {
                $('#sales_type').val(sales_type).attr("selected", "selected");
            }
        }

        if(purchaseNoG == 1){
            let purchaseId = "{{ isset($requestData['purchase_id']) ? $requestData['purchase_id'] : '' }}";
            if (purchaseId != '') {
                $('#purchase_id').val(purchaseId).attr("selected", "selected");
            }
        }

        if(orderNoG == 1){
            let orderId = "{{ isset($requestData['order_id']) ? $requestData['order_id'] : '' }}";
            if (orderId != '') {
                $('#order_id').val(orderId).attr("selected", "selected");
            }
        }

        if(salesBillNoG == 1){
            let salesBillNo = "{{ isset($requestData['sales_bill_no']) ? $requestData['sales_bill_no'] : '' }}";
            if (salesBillNo != '') {
                $('#sales_bill_no').val(salesBillNo).attr("selected", "selected");
            }
        }

        if(issueRetBillNoG == 1){
            let issueRBillNo = "{{ isset($requestData['issue_r_bill_no']) ? $requestData['issue_r_bill_no'] : '' }}";
            if (issueRBillNo != '') {
                $('#issue_r_bill_no').val(issueRBillNo).attr("selected", "selected");
            }
        }

        if(stockG == 1){
            let stockSearch = "{{ isset($requestData['stockSearch']) ? $requestData['stockSearch'] : '' }}";
            if (stockSearch != '') {
                $('#stockSearch').val(stockSearch).attr("selected", "selected");
            }
        }

        // // // ACC
        if(voucherTypeG == 1){
            let voucherType = "{{ isset($requestData['voucher_type']) ? $requestData['voucher_type'] : '' }}";
            if (voucherType != '') {
                $('#voucher_type').val(voucherType).attr("selected", "selected");
            }
        }

        if(depthLevelG == 1){
            let depthLevel = "{{ isset($requestData['depth_level']) ? $requestData['depth_level'] : '' }}";
            if (depthLevel != '') {
                $('#depth_level').val(depthLevel).attr("selected", "selected");
            }
        }

        if(roundUpG == 1){
            let roundUp = "{{ isset($requestData['round_up']) ? $requestData['round_up'] : '' }}";
            if (roundUp != '') {
                $('#round_up').val(roundUp).attr("selected", "selected");
            }
        }

        if(zeroBalanceG == 1){
            let zeroBalance = "{{ isset($requestData['zero_balance']) ? $requestData['zero_balance'] : '' }}";
            if (zeroBalance != '') {
                $('#zero_balance').val(zeroBalance).attr("selected", "selected");
            }
        }

        if(invoiceNoG == 1){
            let invoiceId = "{{ isset($requestData['invoice_id']) ? $requestData['invoice_id'] : '' }}";
            if (invoiceId != '') {
                $('#invoice_id').val(invoiceId).attr("selected", "selected");
            }
        }

        if(ledgerG == 1){
            let ledgerId = "{{ isset($requestData['ledger_id']) ? $requestData['ledger_id'] : '' }}";
            if (ledgerId != '') {
                $('#ledger_id').val(ledgerId).attr("selected", "selected");
            }
        }

        if(ledgerCashG == 1){
            let ledgerCash = "{{ isset($requestData['ledger_cash']) ? $requestData['ledger_cash'] : '' }}";
            if (ledgerCash != '') {
                $('#ledger_cash').val(ledgerCash).attr("selected", "selected");
            }
        }

        if(ledgerBankG == 1){

            let ledgerBank = "{{ isset($requestData['ledger_bank']) ? $requestData['ledger_bank'] : '' }}";
            if (ledgerBank != '') {
                $('#ledger_bank').val(ledgerBank).attr("selected", "selected");
            }
        }

        {
            let voucherTypeId = "{{ isset($requestData['voucher_type_id']) ? $requestData['voucher_type_id'] : '' }}";
            if (voucherTypeId != '') {
                $('#voucher_type_id').val(voucherTypeId).attr("selected", "selected");
            }
        }

        if(customerG == 1){
            let customerId = "{{ isset($requestData['customer_id']) ? $requestData['customer_id'] : '' }}";
            if (customerId != '') {
                $('#customer_id').val(customerId).attr("selected", "selected");
            }
        }

        if(employeeG == 1){
                let employeeId = "{{ isset($requestData['employee_id']) ? $requestData['employee_id'] : '' }}";
                if (employeeId != '') {
                    $('#employee_id').val(employeeId).attr("selected", "selected");
                }
        }

        if(monthYearG == 1){

            let monthYear = "{{ isset($requestData['month_year']) ? $requestData['month_year'] : '' }}";
            if (monthYear != '') {
                $('#month_year').val(monthYear);
            }
            else{
                let today = $.datepicker.formatDate('MM-yy', new Date());
                $('#month_year').val(today);
            }
        }

        if(searchByG == 1){
            let searchBy = "{{ isset($requestData['search_by']) ? $requestData['search_by'] : '' }}";
            if (searchBy != '') {
                $('#search_by').val(searchBy).attr("selected", "selected");
            }
        }

        {
            let fiscalYear = "{{ isset($requestData['fiscal_year']) ? $requestData['fiscal_year'] : '' }}";
            if (fiscalYear != '') {
                $('#fiscal_year').val(fiscalYear).attr("selected", "selected");
            }
        }

        if(currentYearG == 1){
            let startDateCy = "{{ isset($requestData['start_date_cy']) ? $requestData['start_date_cy'] : '' }}";
            if (startDateCy != '') {
                $('#start_date_cy').val(startDateCy);
            }

            let endDateCy = "{{ isset($requestData['end_date_cy']) ? $requestData['end_date_cy'] : '' }}";
            if (endDateCy != '') {
                $('#end_date_cy').val(endDateCy);
            }
        }

        // if(dateRangeG == 1)
        {
            let startDateDr = "{{ isset($requestData['start_date_dr']) ? $requestData['start_date_dr'] : '' }}";
            if (startDateDr != '') {
                $('#start_date_dr').val(startDateDr);
            }

            let endDateDr = "{{ isset($requestData['end_date_dr']) ? $requestData['end_date_dr'] : '' }}";
            if (endDateDr != '') {
                $('#end_date_dr').val(endDateDr);
            }
        }

        if(monthG == 1){

            let monthYr = "{{ isset($requestData['month_yr']) ? $requestData['month_yr'] : '' }}";
            if (monthYr != '') {
                $('#month_yr').val(monthYr);
            }
            else{
                let today = $.datepicker.formatDate('MM-yy', new Date());
                $('#month_year').val(today);
            }
        }

        {
            let startDate = "{{ isset($requestData['StartDate']) ? $requestData['StartDate'] : '' }}";
            if (startDate != '') {
                // $('#start_date_txt').html(startDate);
                $('#start_date').val(startDate);
            }

            let endDate = "{{ isset($requestData['EndDate']) ? $requestData['EndDate'] : '' }}";
            if (endDate != '') {
                // $('#end_date_txt').html(endDate);
                $('#end_date').val(endDate);
            }
        }

        {
            let reportFormatting = "{{ isset($requestData['report_formatting']) ? $requestData['report_formatting'] : '' }}";
            if (reportFormatting != '') {
                $('#report_formatting').val(reportFormatting).attr("selected", "selected");

                // reportBranchTxt = $('#branch_id').find("option:selected").text();
            }
        }
    }
</script>

<script type="text/javascript"> // // function For Zone Area 

    function fnAjaxGetArea() {
        var zoneId = $('#zone_id').val();

        if (zoneId != null) {
            $.ajax({
                method: "GET",
                url: "{{ url('ajaxGetArea') }}",
                dataType: "text",
                data: {
                    zoneId: zoneId,
                },
                success: function (data) {
                    if (data) {
                        $('#area_id').empty().html(data);
                    }
                }
            });
        }
    }

    function fnAjaxGetBranch() {

        var zoneId = $('#zone_id').val();
        var areaId = $('#area_id').val();

        if (areaId != null || zoneId != null) {
            $.ajax({
                method: "GET",
                url: "{{ url('ajaxGetBranch') }}",
                dataType: "text",
                data: {
                    areaId: areaId,
                    zoneId: zoneId,
                },
                success: function (data) {
                    if (data) {
                        $('#branch_id').empty().html(data);
                    }
                }
            });
        }
    }
</script>

<script type="text/javascript"> // // function for product  

    function fnAjaxGetCategory(moduleName = null, onchange = 1) {
        var groupId = $('#group_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetCategory') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#cat_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#cat_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    // Change = 1 means is the function calling is coming from Onchange
                    if (onchange != 1) {
                        $('#cat_id').val(catId).attr("selected", "selected");
                    }

                }
            }
        });
    }

    function fnAjaxGetSubCat(moduleName = null, onchange = 1) {
        var groupId = $('#group_id').val();
        var categoryId = $('#cat_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetSubCat') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#sub_cat_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#sub_cat_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    // Change = 1 means is the function calling is coming from Onchange
                    if (onchange != 1) {
                        $('#sub_cat_id').val(subCatId).attr("selected", "selected");
                    }

                }
            }
        });
    }

    function fnAjaxGetModel(moduleName =null, onchange = 1) {
        var groupId = $('#group_id').val();
        var categoryId = $('#cat_id').val();
        var subCatId = $('#sub_cat_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetModel') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#model_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#model_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    // Change = 1 means is the function calling is coming from Onchange
                    if (onchange != 1) {
                        $('#model_id').val(modelId).attr("selected", "selected");
                    }

                }
            }
        });
    }

    function fnAjaxGetProduct(moduleName = null, onchange = 1) {
        var groupId = $('#group_id').val();
        var categoryId = $('#cat_id').val();
        var subCatId = $('#sub_cat_id').val();
        var brandId = $('#brand_id').val();
        var modelId = $('#model_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProduct') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                brandId: brandId,
                modelId: modelId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#product_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#product_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    // Change = 1 means is the function calling is coming from Onchange
                    if (onchange != 1) {
                        $('#product_id').val(productId).attr("selected", "selected");
                    }


                }
            }
        });
    }
</script>

<script type="text/javascript"> // // function for ledger 

    function ajaxLedgerLoad (branch_id, project_id, acc_type = null){
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetLedgerForBranch') }}",
            dataType: "text",
            data: {
                branch_id: branch_id,
                project_id: project_id,
                acc_type: acc_type,
            },
            success: function (data) {
                if (data) {
                    if(acc_type == 4){
                        $('#ledger_cash').empty().html(data);
                    }
                    else if(acc_type == 5){
                        $('#ledger_bank').empty().html(data);
                    }
                    else{
                        $('#ledger_id').empty().html(data);
                    }

                }
            }
        });
    }
</script>

<script type="text/javascript"> // // function for Fiscal / Current / serchby 
    
    function fnAjaxFiscalYear() {
        let branchId = $('#branch_id').val();

        if(branchId === "-1" || branchId === "-2" || branchId === -1 || branchId === -2){
            branchId = 1;
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxCurrentFY') }}",
            dataType: "json",
            data: {
                branchId: branchId,
                moduleName: "{{ Common::getModuleByRoute() }}",
                currentFY: false
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let brOpeningDate = response['brOpeningDate'];
                    let loginSystemDate = response['loginSystemDate'];

                    $("#fiscal_year option").each(function()
                    {
                        let startDate = $(this).attr('data-startdate');
                        let endDate = $(this).attr('data-enddate');

                        if(startDate === undefined && endDate === undefined){
                            return true;
                        }

                        let startArr = startDate.split("-");
                        let endArr = endDate.split("-");

                        /////////////// Y-m-d
                        startDate = startArr[2]+"-"+startArr[1]+"-"+startArr[0];
                        endDate = endArr[2]+"-"+endArr[1]+"-"+endArr[0];

                        if(brOpeningDate >= startDate && brOpeningDate <= endDate){
                            startDate = brOpeningDate;
                        }

                        if(loginSystemDate >= startDate && loginSystemDate <= endDate){
                            endDate = loginSystemDate;
                        }

                        startDate = $.datepicker.formatDate('dd-mm-yy', new Date(startDate));
                        endDate = $.datepicker.formatDate('dd-mm-yy', new Date(endDate));

                        $(this).attr('data-startdate', startDate);
                        $(this).attr('data-enddate', endDate);

                        $(this).data('startdate', startDate);
                        $(this).data('enddate', endDate);
                    });

                    fnForSearchBy();

                } else if (response['status'] == 'error'){
                    swal({
                        icon: 'warning',
                        title: 'Warning',
                        text: response['message'],
                        timer: 3000
                    }).then(function() {
                        // window.location = "{{url('/acc')}}";
                    });
                }
            }
        });
    }

    function fnAjaxCurrentFY() {
        let branchId = $('#branch_id').val();

        if(branchId === "-1" || branchId === "-2" || branchId === -1 || branchId === -2){
            branchId = 1;
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxCurrentFY') }}",
            dataType: "json",
            data: {
                branchId: branchId,
                moduleName: "{{ Common::getModuleByRoute() }}",
            },
            success: function (response) {
                if (response['status'] == 'success') {
                    var result_data = response['result_data'];

                    // let tempStartDate = $.datepicker.formatDate('dd-mm-yy', new Date(result_data['fy_start_date']));

                    let tempStartDate = new Date(result_data['fy_start_date']);
                    let tempEndDate = new Date(result_data['fy_end_date']);

                    let start_date = tempStartDate;
                    let start_date_ex = tempStartDate;

                    let loginBranchOpenDate = response['brOpeningDate'];

                    // let loginBranchOpenDate = '<?php echo $branchOpenDate ?>';
                    loginBranchOpenDate = new Date(loginBranchOpenDate);
                    if(loginBranchOpenDate >= tempStartDate && loginBranchOpenDate <= tempEndDate){
                        start_date = loginBranchOpenDate;
                        start_date_ex = loginBranchOpenDate;
                    }

                    start_date = $.datepicker.formatDate('dd-mm-yy', new Date(start_date));

                    $("#start_date_cy").val(start_date);
                    $("#fy_name_cy").val(result_data['fy_name']);

                    // $("#end_date_cy").datepicker("option", "minDate", new Date(result_data['fy_start_date']));
                    $("#end_date_cy").datepicker("option", "minDate", start_date_ex);
                    $("#end_date_cy").datepicker("option", "maxDate", new Date(result_data['fy_end_date']));


                    fnForSearchBy();

                } else if (response['status'] == 'error'){
                    swal({
                        icon: 'warning',
                        title: 'Warning',
                        text: response['message'],
                        timer: 3000
                    }).then(function() {
                        // window.location = "{{url('/acc')}}";
                    });
                }
            }
        });
    }

    function fnForSearchBy(){
        let selected = $('#search_by').val();
        let start_date_txt = "";
        let end_date_txt = "";

        if (selected == 1) { // fiscal year
            $('#endDateDivCY,#startDateDivDR,#endDateDivDR').hide('fast');
            $('#fyDiv').show('slow');

            if($('#fiscal_year').val() != ''){
                let start_date_fy = $('#fiscal_year :selected').data('startdate');
                let end_date_fy = $('#fiscal_year :selected').data('enddate');

                $('#start_date_fy').val(start_date_fy);
                $('#end_date_fy').val(end_date_fy);

                let startArr = start_date_fy;
                let endArr = end_date_fy;

                startArr = startArr.split("-");
                endArr = endArr.split("-");

                /////////////// Y-m-d
                startDate = startArr[2]+"-"+startArr[1]+"-"+startArr[0];
                endDate = endArr[2]+"-"+endArr[1]+"-"+endArr[0];

                let pre_fiscal_start = (Number(startArr[2]) - 1);
                let pre_fiscal_end = startArr[2];

                let cur_fiscal_start = startArr[2];
                let cur_fiscal_end = endArr[2];

                $('#prev_year').html(pre_fiscal_start + "-" + pre_fiscal_end);
                $('#current_year').html(cur_fiscal_start + "-" + cur_fiscal_end);


                start_date_txt = start_date_fy;
                end_date_txt = end_date_fy;
            }

        } else if (selected == 2) { // current year
            $('#fyDiv,#startDateDivDR,#endDateDivDR').hide('fast');
            $('#endDateDivCY').show('slow');

            start_date_txt = $('#start_date_cy').val();
            end_date_txt = $('#end_date_cy').val();

            let startArr = start_date_txt;
            let endArr = end_date_txt;

            startArr = startArr.split("-");
            endArr = endArr.split("-");

            /////////////// Y-m-d
            startDate = startArr[2]+"-"+startArr[1]+"-"+startArr[0];
            endDate = endArr[2]+"-"+endArr[1]+"-"+endArr[0];

            let pre_fiscal_start = (Number(startArr[2]) - 1);
            let pre_fiscal_end = startArr[2];

            let cur_fiscal_start = startArr[2];
            let cur_fiscal_end = (Number(startArr[2]) + 1);

            $('#prev_year').html(pre_fiscal_start + "-" + pre_fiscal_end);
            $('#current_year').html(cur_fiscal_start + "-" + cur_fiscal_end);

        } else if (selected == 3) { // date range
            $('#fyDiv,#endDateDivCY').hide('fast');
            $('#startDateDivDR,#endDateDivDR').show('slow');

            start_date_txt = $('#start_date_dr').val();
            end_date_txt = $('#end_date_dr').val();
        } else {
            $('#fyDiv,#endDateDivCY').hide('');
        }

        if(start_date_txt != ""){
            $('#start_date_txt').html(start_date_txt);
        }
        if(end_date_txt != ""){
            $('#end_date_txt').html(end_date_txt);
            $('.title_date').html(end_date_txt);
        }
    }
</script>

<script type="text/javascript"> 

    function showReportHeading(monthYearG = null,monthG = null){

        $(".wb-minus").trigger('click');

        let reportBranchTxt = false;

        if(zoneG == 1){
            if($('#zone_id').val() != '' && typeof($('#zone_id').val()) != 'undefined'){
                reportBranchTxt = $('#zone_id').find("option:selected").text();
            }
        }

        if(areaG == 1){
            if($('#area_id').val() != '' && typeof($('#area_id').val()) != 'undefined'){
                reportBranchTxt = $('#area_id').find("option:selected").text();
            }
        }

        if($('#branch_id').val() != '' && typeof($('#branch_id').val()) != 'undefined'){
            reportBranchTxt = $('#branch_id').find("option:selected").text();
            $('#branchName').html($('#branch_id option:selected').text());
        }

        if(projectG == 1){
            if($('#project_id').val() != '' && typeof($('#project_id').val()) != 'undefined'){
                $('#projectName').html($('#project_id option:selected').text());
            }
        }

        if(projectTypeG == 1){
            if($('#project_type_id').val() != '' && typeof($('#project_type_id').val()) != 'undefined'){
                $('#projectTypeName').html($('#project_type_id option:selected').text());
            }
        }

        if(reportBranchTxt == false){
            reportBranchTxt = "All Branch";
        }

        $('#reportBranch').html(reportBranchTxt);

        if($('#start_date').val() != '' && typeof($('#start_date').val()) != 'undefined'){
            $('#start_date_txt').html($('#start_date').val());
        }

        if($('#end_date').val() != '' && typeof($('#end_date').val()) != 'undefined'){
            $('#end_date_txt').html($('#end_date').val());
        }

        if(monthYearG == 1){

            let selectedMonth = new Date($('#month_year').val());
            let firstDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1);
            let lastDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth()+1, 0);

            firstDayOfMonth = $.datepicker.formatDate('dd-mm-yy', firstDayOfMonth);
            lastDayOfMonth = $.datepicker.formatDate('dd-mm-yy', lastDayOfMonth);

            $('#start_date_txt').html(firstDayOfMonth);
            $('#end_date_txt').html(lastDayOfMonth);
        }

        if(monthG == 1){
            let selectedMonth = new Date($('#month_yr').val());
            let firstDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1);
            let lastDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth()+1, 0);

            firstDayOfMonth = $.datepicker.formatDate('dd-mm-yy', firstDayOfMonth);
            lastDayOfMonth = $.datepicker.formatDate('dd-mm-yy', lastDayOfMonth);

            $('#start_date_txt').html(firstDayOfMonth);
            $('#end_date_txt').html(lastDayOfMonth);
        }
    }
</script>

<!-- ////////////////////////////////////////////////// -->

<script type="text/javascript">  // // for Product group 

    $(document).ready(function () {
        if(groupG == 1){
            fnAjaxGetCategory('pos', 0);
            fnAjaxGetSubCat('pos', 0);
            fnAjaxGetModel('pos', 0);
            fnAjaxGetProduct('pos', 0);
        }

        if(groupGInv == 1){
            fnAjaxGetCategory('inv', 0);
            fnAjaxGetSubCat('inv', 0);
            fnAjaxGetModel('inv', 0);
            fnAjaxGetProduct('inv', 0);
        }
    });

    $('#branch_id').change(function () {
        
        if(employeeG == 1){
            fnAjaxSelectBox('employee_id',
                $(this).val(),
                '{{base64_encode("hr_employees")}}',
                '{{base64_encode("branch_id")}}',
                '{{base64_encode("employee_no,emp_name,emp_code")}}',
                '{{url("/ajaxSelectBox")}}',null,'isActiveOff'
            );
        }
    });

</script>

<script type="text/javascript"> // // For Ledger

    var branch_id = $('#branch_id').val();
    var project_id = $('#project_id').val();
    var acc_type = null;

    if(ledgerG == 1 ||  ledgerCashG == 1 || ledgerBankG == 1){

        if ($('#ledger_id').attr('id') == 'ledger_id')
        {
            $("#branch_id" ).change(function() {
                branch_id = $(this).val();

                ajaxLedgerLoad(branch_id, project_id);
            });

            $("#project_id" ).change(function() {
                branch_id = $('#branch_id').val();
                project_id = $(this).val();

                ajaxLedgerLoad(branch_id, project_id);
            });
        }

        if ($('#ledger_cash').attr('id') == 'ledger_cash')
        {
            acc_type = 4;

            $("#branch_id" ).change(function() {
                branch_id = $(this).val();

                ajaxLedgerLoad(branch_id, project_id, acc_type);
            });

            $("#project_id" ).change(function() {
                branch_id = $('#branch_id').val();
                project_id = $(this).val();

                ajaxLedgerLoad(branch_id, project_id, acc_type);
            });
        }

        if ($('#ledger_bank').attr('id') == 'ledger_bank')
        {
            acc_type = 5;

            $("#branch_id" ).change(function() {
                branch_id = $(this).val();

                ajaxLedgerLoad(branch_id, project_id, acc_type);
            });

            $("#project_id" ).change(function() {
                branch_id = $('#branch_id').val();
                project_id = $(this).val();

                ajaxLedgerLoad(branch_id, project_id, acc_type);
            });
        }
    }
</script>

<script type="text/javascript"> // // For request selected variable load automatic

    $(document).ready(function () {

        let requestPromise = new Promise(function(resolve, reject) {
            var requestArr = "{{ (count($requestData) > 0) ? 1 : 0 }}";

            if(requestArr == 1){
                resolve(1);
            }
            else{
                reject(0);
            }
        });

        requestPromise.then(
            function(result){
                fnForRequestedVariable().then(()=>{
                    setTimeout(function () {
                        showReportHeading();
                    }, 10);
                });
            },
            function(error) { 
                // console.log(error); 
            }
        );
    });
</script>

<script type="text/javascript"> // // for Fiscal / Current / serchby 

    $(document).ready(function () {

        if(monthYearG == 1){
            // $("#month_year").find('.ui-datepicker-calendar').css({"display":"none"});

            $('#month_year').datepicker({
                dateFormat: 'MM-yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                todayButton: false,
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val($.datepicker.formatDate('MM-yy', new Date(year, month, 1)));
                }
            });
        }

        if(monthG == 1){
            $(".monthPicker").datepicker({
                dateFormat: 'MM yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,

                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                }
            });

            $(".monthPicker").focus(function () {
                $(".ui-datepicker-calendar").hide();
                $("#ui-datepicker-div").position({
                    my: "center top",
                    at: "center bottom",
                    of: $(this)
                });
            });
        }

        if(searchByG == 1){

            var searchByLoad = $('#search_by').val();

            if(searchByLoad == "1"){
                fnAjaxFiscalYear();
            }

            if(currentYearG == 1){
                $("#end_date_cy").datepicker({
                    dateFormat: 'dd-mm-yy',
                    orientation: 'bottom',
                    autoclose: true,
                    todayHighlight: true,
                    changeMonth: true,
                    changeYear: true,
                });

                if(searchByLoad == "2"){
                    fnAjaxCurrentFY();
                }
            }

            if(dateRangeG == 1){
                $('#start_date_dr').datepicker({
                    dateFormat: 'dd-mm-yy',
                    orientation: 'bottom',
                    autoclose: true,
                    todayHighlight: true,
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '1900:+10',
                    onClose: function (selectedDate) {
                        $("#end_date_dr").datepicker("option", "minDate", selectedDate);
                    }
                });

                $("#end_date_dr").datepicker({
                    dateFormat: 'dd-mm-yy',
                    orientation: 'bottom',
                    autoclose: true,
                    todayHighlight: true,
                    changeMonth: true,
                    changeYear: true,
                    // yearRange: '1900:+10',
                    onClose: function (selectedDate) {
                        $("#start_date_dr").datepicker("option", "maxDate", selectedDate);
                    }
                });

                fnForSearchBy();
            }

            if(monthG == 1){
                fnForSearchBy();
            }
            
            $("#branch_id").change(function(){

                let searchBy = $('#search_by').val();

                if(searchBy == "1"){
                    fnAjaxFiscalYear();
                }

                if(searchBy == "2"){
                    fnAjaxCurrentFY();
                }

                if(searchBy == "3" || searchBy == "4"){
                    fnForSearchBy();
                }
            });

            $('#search_by').change(function () {
                // 1, 2 er jonno tader ajax a load hocche fnForSearchBy function

                let searchBy = $('#search_by').val();

                if(searchBy == "1"){
                    fnAjaxFiscalYear();
                }

                if(searchBy == "2"){
                    fnAjaxCurrentFY();
                }

                if(searchBy == "3" || searchBy == "4"){
                    fnForSearchBy();
                }
            });

            $("#fiscal_year").change(function(){
                fnForSearchBy();
            });
        }
    });

</script>

<script type="text/javascript">  // For Submit

    $(document).ready(function () {

        $('#fiscal_year').select2({
            'width': '100%'
        });

        $('#refreshButton').on('click', function (e) {
            window.location.href = window.location.href.split('#')[0];
        });

        $('#searchButton').click(function (event) {
            if ($("#filterFormId").length) {
                fnLoading(true);
            }

            showReportHeading();
            $("#filterFormId").submit();
        });
    });

</script>

<script>
    $(document).ready(function () {

        let messageHas = "{{ (Session::has('status')) ? 1 : 0}}";

        if(messageHas == 1){
            let type = "{{ Session::get('status', 'error')}}";

            if(type == 'error'){

                let messageText = "{!! Session::get('message') !!}";
                const wrapper = document.createElement('div');
                let html = "<p style='color:#000;'>"+ messageText +"</p>";
                // html += "<p style='color:#000;'>Please delete that transactions or get to that date </p>";
                wrapper.innerHTML = html;

                swal({
                    icon: 'warning',
                    title: 'Warning',
                    content: wrapper,
                    timer: 5000
                }).then(function() {
                    // window.location = "{{url('/acc')}}";
                });
            }
        }
    });

    function incompleteBranchList(){
        $("#incomplete_list_modal").modal('show');
    }
</script>