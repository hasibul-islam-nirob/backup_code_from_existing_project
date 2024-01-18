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

<!-- For Global variable -->
<script type="text/javascript">
    $(document).ready(function () {
        var requestMethod = "{{ Request::method() }}";
        if(requestMethod == "POST"){
            $('.show').show('slow');
        }
    });


    var zoneG = "{{ (isset($zone) && $zone) ? 1 : 0 }}";
    /*
        asob filter element or option old tai aikhane new modification er proyojon nai. New Filter option a modification kora hocche.
        jesob jaygay akhono use kora hocche sekhane jate error ba region load hoy sejonno zone er under a dewa holo.
        new common filter option sob jaygay implement kora hole ai code gulo gurbage hoye jabe. tai new modification dewar dorkar nai.
    */
    var regionG = "{{ (isset($zone) && $zone) ? 1 : 0 }}";
    var areaG = "{{ (isset($area) && $area) ? 1 : 0 }}";
    var projectG = "{{ (isset($project) && $project) ? 1 : 0 }}";
    var projectTypeG = "{{ (isset($projectType) && $projectType) ? 1 : 0 }}";
    var branchG = "{{ (isset($branch) && $branch) ? 1 : 0 }}";
    var branchFromG = "{{ (isset($branchFrom) && $branchFrom) ? 1 : 0 }}";
    var branchToG = "{{ (isset($branchTo) && $branchTo) ? 1 : 0 }}";
    var deliveryPlaceG = "{{ (isset($deliveryPlace) && $deliveryPlace) ? 1 : 0 }}";
    var branchWithoutHOG = "{{ (isset($branchWithoutHO) && $branchWithoutHO) ? 1 : 0 }}";

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
    var salesByOnlyPosReport = "{{ (isset($salesByOnlyPosReport) && $salesByOnlyPosReport) ? 1 : 0 }}";

    var salesTypeG = "{{ (isset($salesType) && $salesType) ? 1 : 0 }}";
    var installmentTypeG = "{{ (isset($installmentType) && $installmentType) ? 1 : 0 }}";
    var salesBillNoG = "{{ (isset($salesBillNo) && $salesBillNo) ? 1 : 0 }}";
    var issueRetBillNoG = "{{ (isset($issueRetBillNo) && $issueRetBillNo) ? 1 : 0 }}";
    var stockG = "{{ (isset($stock) && $stock) ? 1 : 0 }}";

    var ledgerG = "{{ (isset($ledger) && $ledger) ? 1 : 0 }}";
    var ledgerCashG = "{{ (isset($ledgerCash) && $ledgerCash) ? 1 : 0 }}";
    var ledgerBankG = "{{ (isset($ledgerBank) && $ledgerBank) ? 1 : 0 }}";
    var ledgerCashBankG = "{{ (isset($ledgerCashAndBank) && $ledgerCashAndBank) ? 1 : 0 }}";
    var typeCashBankG = "{{ (isset($typeCashAndBank) && $typeCashAndBank) ? 1 : 0 }}";

    var voucherG = "{{ (isset($voucher) && $voucher) ? 1 : 0 }}";
    var voucherTypeG = "{{ (isset($voucherType) && $voucherType) ? 1 : 0 }}";
    var accountTypeG = "{{ (isset($accountType) && $accountType) ? 1 : 0 }}";

    var branchAccG = "{{ (isset($branchAcc) && $branchAcc) ? 1 : 0 }}";
    var vTypeReceiptG = "{{ (isset($vTypeReceipt) && $vTypeReceipt) ? 1 : 0 }}";
    var depthLevelG = "{{ (isset($depthLevel) && $depthLevel) ? 1 : 0 }}";
    var roundUpG = "{{ (isset($roundUp) && $roundUp) ? 1 : 0 }}";
    var zeroBalanceG = "{{ (isset($zeroBalance) && $zeroBalance) ? 1 : 0 }}";

    var nameorcodeG = "{{ (isset($nameorcode) && $nameorcode) ? 1 : 0 }}";
    var customertextG = "{{ (isset($customertext) && $customertext) ? 1 : 0 }}";
    var employeetextG = "{{ (isset($employeetext) && $employeetext) ? 1 : 0 }}";



    var startDateG = "{{ (isset($startDate) && $startDate) ? 1 : 0 }}";
    var endDateG = "{{ (isset($endDate) && $endDate) ? 1 : 0 }}";
    var monthYearG = "{{ (isset($monthYear) && $monthYear) ? 1 : 0 }}";
    var searchByG = "{{ (isset($searchBy) && $searchBy) ? 1 : 0 }}";

    var currentYearG = "{{ (isset($currentYear) && $currentYear) ? 1 : 0 }}";
    var fiscalYearDateRangeG = "{{ (isset($fiscalYearDateRange) && $fiscalYearDateRange) ? 1 : 0 }}";
    var dateRangeG = "{{ (isset($dateRange) && $dateRange) ? 1 : 0 }}";
    var monthG = "{{ (isset($month) && $month) ? 1 : 0 }}";

    var reportFormattingG = "{{ (isset($reportFormatting) && $reportFormatting) ? 1 : 0 }}";
    var reportHeaderG = "{{ (isset($reportHeader) && $reportHeader) ? 1 : 0 }}";
    var reportViewOptionG = "{{ (isset($reportViewOption) && $reportViewOption) ? 1 : 0 }}";

    var allWithWihoutHO = "{{ (isset($allWithWihoutHO) && $allWithWihoutHO) ? 1 : 0 }}";

    if (allWithWihoutHO == 1) {
        $(document).ready(function () {

            let zoneId = "{{ isset($requestData['zone_id']) ? $requestData['zone_id'] : '' }}";
            let regionId = "{{ isset($requestData['region_id']) ? $requestData['region_id'] : '' }}";
            let areaId = "{{ isset($requestData['area_id']) ? $requestData['area_id'] : '' }}";

            var newOption = '<option value="-1" data-select2-id="-1" selected>All (With HO)</option>';
            newOption += '<option value="-2" data-select2-id="-2">All (Without HO)</option>';
            // Append it to the select

            let flag = true;
            if(zoneG == 1 && zoneId != ''){
                flag = false;
            }

            if(regionG == 1 && regionId != ''){
                flag = false;
            }

            if(areaG == 1 && areaId != ''){
                flag = false;
            }

            if($('#branch_id option').length > 1 && flag){
                $('#branch_id').prepend(newOption).trigger('change');
            }

        });
    }

    /////////////////////////////
    // var catId = "{{ isset($requestData['cat_id']) ? $requestData['cat_id'] : '' }}";
    // var subCatId = "{{ isset($requestData['sub_cat_id']) ? $requestData['sub_cat_id'] : '' }}";
    // var productId = "{{ isset($requestData['product_id']) ? $requestData['product_id'] : '' }}";
    // var modelId = "{{ isset($requestData['model_id']) ? $requestData['model_id'] : '' }}";

</script>

<script type="text/javascript">
    function showReportHeading(filter_div = null) {

        console.log('fn Report Heading');

        if(filter_div === 'close'){
            setTimeout(function () {
                $(".wb-minus").trigger('click');
            }, 10);
        }
        // $(".wb-minus").trigger('click');

        let reportBranchTxt = false;
        let reportForTxt = false;

        if (zoneG == 1) {
            if ($('#zone_id').val() != '' && typeof ($('#zone_id').val()) != 'undefined') {
                reportBranchTxt = $('#zone_id').find("option:selected").text();
                reportForTxt = "Zone:";
            }
        }

        if (regionG == 1) {
            if ($('#region_id').val() != '' && typeof ($('#region_id').val()) != 'undefined') {
                reportBranchTxt = $('#region_id').find("option:selected").text();
                reportForTxt = "Region:";
            }
        }

        if (areaG == 1) {
            if ($('#area_id').val() != '' && typeof ($('#area_id').val()) != 'undefined') {
                reportBranchTxt = $('#area_id').find("option:selected").text();
                reportForTxt = "Area:";
            }
        }

        if ($('#branch_id').val() != '' && typeof ($('#branch_id').val()) != 'undefined') {
            reportBranchTxt = $('#branch_id').find("option:selected").text();
            reportForTxt = "Branch:";

            // if($('#branch_id option').length > 2){

            // }

            $('#branchName').html($('#branch_id option:selected').text());

        }else if (typeof ($('#branch_id').val()) == 'undefined') {
            if($('#branch_to').val() != '' && typeof ($('#branch_to').val()) != 'undefined'){
                reportBranchTxt = $('#branch_to').find("option:selected").text();
                reportForTxt = "Branch:";

            }else if($('#branch_from').val() != '' && typeof ($('#branch_from').val()) != 'undefined'){
                reportBranchTxt = $('#branch_from').find("option:selected").text();
                reportForTxt = "Branch:";

            }else{
                if ($('#branch_id option').length < 2 || $('#branch_to option').length < 2 || $('#branch_from option').length < 2) {
                    reportBranchTxt = false;
                }else{
                    reportBranchTxt = "Head Office";
                    reportForTxt = "Branch:";
                }
            }

        }

        if (reportBranchTxt === false) {
            reportBranchTxt = "All Branch";
        }
        else if (reportBranchTxt == '') {
            reportBranchTxt = false;
            reportForTxt = false;
        }

        if(reportForTxt !== false){
            $('#reportFor').html(reportForTxt);
        }

        if(reportBranchTxt !== false) {
            $('#reportBranch').html(reportBranchTxt);
        }

        if (projectG == 1) {
            if ($('#project_id').val() != '' && typeof ($('#project_id').val()) != 'undefined') {
                $('#projectName').html($('#project_id option:selected').text());
            }
        }

        if (projectTypeG == 1) {
            if ($('#project_type_id').val() != '' && typeof ($('#project_type_id').val()) != 'undefined') {
                $('#projectTypeName').html($('#project_type_id option:selected').text());
            }
        }

        if ($('#start_date').val() != '' && typeof ($('#start_date').val()) != 'undefined') {
            $('#start_date_txt').html(viewDateFormat($('#start_date').val()));
        }
        // else if (typeof ($('#start_date').val()) == 'undefined' || $('#start_date').val() == '') {

        //     $('#start_date_txt').hide();
        //     // $('#text_to').hide();
        //     $('#text_to').html('Up to ');
        // }


        if ($('#end_date').val() != '' && typeof ($('#end_date').val()) != 'undefined') {
            $('#end_date_txt').html(viewDateFormat($('#end_date').val()));
        }
        // else if (typeof ($('#end_date').val()) == 'undefined' || $('#end_date').val() == '') {
        //     $('#end_date_txt').hide();
        //     $('#text_to').hide();
        // }

        if (monthYearG == 1) {
            let selectedMonth = new Date($('#month_year').val());

            let firstDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1);
            let lastDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth() + 1, 0);

            firstDayOfMonth = $.datepicker.formatDate('dd-mm-yy', firstDayOfMonth);
            lastDayOfMonth = $.datepicker.formatDate('dd-mm-yy', lastDayOfMonth);
            let selectedMonthT = $.datepicker.formatDate('MM-yy', selectedMonth);

            $('#start_date_txt').show();
            $('#end_date_txt').show();
            $('#text_to').show();

            $('#start_date_txt').html(viewDateFormat(firstDayOfMonth));
            $('#text_to').html(' to ');
            $('#end_date_txt').html(viewDateFormat(lastDayOfMonth));

            $('#afterTitle').html(" of " + selectedMonthT);
        }

        if (monthG == 1) {
            console.log('monthG');
            let selectedMonth = new Date($('#month_yr').val());
            let firstDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1);
            let lastDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth() + 1, 0);

            firstDayOfMonth = $.datepicker.formatDate('dd-mm-yy', firstDayOfMonth);
            lastDayOfMonth = $.datepicker.formatDate('dd-mm-yy', lastDayOfMonth);

            $('#start_date_txt').show();
            $('#end_date_txt').show();
            $('#text_to').show();

            $('#start_date_txt').html(viewDateFormat(firstDayOfMonth));
            $('#text_to').html(' to ');
            $('#end_date_txt').html(viewDateFormat(lastDayOfMonth));
        }
    }
</script>

<!-- function For request selected variable -->
<script type="text/javascript">

    var showRequestVarForOneTime = false;

    async function fnForRequestedVariable() {

        console.log('fn Requested Var');

        let requestArr = "{{ (count($requestData) > 0) ? 1 : 0 }}";

        if(requestArr == 0){
            return;
        }
        /*
        * Request variable ekbar view hoye gele this flag true hoye jabe,
        * noyto every on change event a ajax complete hole ai function call hoy
        */
        showRequestVarForOneTime = true;

        if (zoneG == 1) {
            let zoneId = "{{ isset($requestData['zone_id']) ? $requestData['zone_id'] : '' }}";
            if (zoneId != '') {
                $('#zone_id').val(zoneId);
                // $('#zone_id').val(zoneId).attr("selected", "selected");
            }
        }

        if (regionG == 1) {
            let regionId = "{{ isset($requestData['region_id']) ? $requestData['region_id'] : '' }}";
            if (regionId != '') {
                $('#region_id').val(regionId);
                // $('#region_id').val(regionId).attr("selected", "selected");
            }
        }

        if (areaG == 1) {
            let areaId = "{{ isset($requestData['area_id']) ? $requestData['area_id'] : '' }}";
            if (areaId != '') {
                // $('#area_id').val(areaId).attr("selected", "selected");
                $('#area_id').val(areaId);

                console.log(areaId);

                // reportBranchTxt = $('#area_id').find("option:selected").text();
            }
        }

        {
            let branchId = "{{ isset($requestData['branch_id']) ? $requestData['branch_id'] : '' }}";
            if (branchId != '') {
                // $('#branch_id').val(branchId).attr("selected", "selected");
                $('#branch_id').val(branchId);

                // reportBranchTxt = $('#branch_id').find("option:selected").text();
            }else{

                let branchId = "{{ isset($requestData['branch_to']) ? $requestData['branch_to'] : '' }}";
                let branchIdF = "{{ isset($requestData['branch_from']) ? $requestData['branch_from'] : '' }}";

                if(branchId != ''){
                    $('#branch_to').val(branchId);

                }else if(branchIdF != ''){
                    $('#branch_from').val(branchIdF);

                }
            }

        }

        if (projectG == 1) {
            let projectId = "{{ isset($requestData['project_id']) ? $requestData['project_id'] : '' }}";
            if (projectId != '') {
                $('#project_id').val(projectId);
            }
        }

        if (projectTypeG == 1) {
            let projectTypeId =
                "{{ isset($requestData['project_type_id']) ? $requestData['project_type_id'] : '' }}";
            if (projectTypeId != '') {
                $('#project_type_id').val(projectTypeId);
            }
        }

        if (groupG == 1 || groupGInv == 1) {
            let groupId = "{{ isset($requestData['group_id']) ? $requestData['group_id'] : '' }}";
            if (groupId != '') {

                // if ($('#group_id option').length < 2) {
                //     $('#group_id').append(new Option(groupId, groupId, false, true));
                // }

                $('#group_id').val(groupId);
            }

            let catId = "{{ isset($requestData['cat_id']) ? $requestData['cat_id'] : '' }}";
            if (catId != '') {

                if ($('#cat_id option').length < 2) {
                    $('#cat_id').append(new Option(catId, catId, false, true));
                }

                $('#cat_id').val(catId);
            }

            let subCatId = "{{ isset($requestData['sub_cat_id']) ? $requestData['sub_cat_id'] : '' }}";
            if (subCatId != '') {

                if ($('#sub_cat_id option').length < 2) {
                    $('#sub_cat_id').append(new Option(subCatId, subCatId, false, true));
                }

                $('#sub_cat_id').val(subCatId);
            }

            let brandId = "{{ isset($requestData['brand_id']) ? $requestData['brand_id'] : '' }}";
            if (brandId != '') {

                if ($('#brand_id option').length < 2) {
                    $('#brand_id').append(new Option(brandId, brandId, false, true));
                }

                $('#brand_id').val(brandId);
            }
        }

        if (modelG == 1 || modelGInv == 1) {
            let modelId = "{{ isset($requestData['model_id']) ? $requestData['model_id'] : '' }}";
            if (modelId != '') {

                if ($('#model_id option').length < 2) {
                    $('#model_id').append(new Option(modelId, modelId, false, true));
                }

                $('#model_id').val(modelId);
            }
        }

        if (productG == 1 || productGInv == 1) {
            let productId = "{{ isset($requestData['product_id']) ? $requestData['product_id'] : '' }}";
            if (productId != '') {

                if ($('#product_id option').length < 2) {
                    $('#product_id').append(new Option(productId, productId, false, true));
                }

                $('#product_id').val(productId);
            }
        }

        if (supplierG == 1 || supplierGInv == 1) {
            let supplierId = "{{ isset($requestData['supplier_id']) ? $requestData['supplier_id'] : '' }}";
            if (supplierId != '') {
                $('#supplier_id').val(supplierId);
            }
        }

        if (salesTypeG == 1) {
            let sales_type = "{{ isset($requestData['sales_type']) ? $requestData['sales_type'] : '' }}";
            if (sales_type != '') {
                $('#sales_type').val(sales_type);
            }
        }

        if (installmentTypeG == 1) {
            let installment_type = "{{ isset($requestData['installment_type']) ? $requestData['installment_type'] : '' }}";
            if (installment_type != '') {
                $('#installment_type').val(installment_type);
            }
        }

        if (purchaseNoG == 1) {
            let purchaseId = "{{ isset($requestData['purchase_id']) ? $requestData['purchase_id'] : '' }}";
            if (purchaseId != '') {
                $('#purchase_id').val(purchaseId);
            }
        }

        if (orderNoG == 1) {
            let orderId = "{{ isset($requestData['order_id']) ? $requestData['order_id'] : '' }}";
            if (orderId != '') {
                $('#order_id').val(orderId);
            }
        }

        if (salesBillNoG == 1) {
            let salesBillNo = "{{ isset($requestData['sales_bill_no']) ? $requestData['sales_bill_no'] : '' }}";
            if (salesBillNo != '') {
                $('#sales_bill_no').val(salesBillNo);
            }
        }

        if (nameorcodeG == 1) {
            let nameorcode = "{{ isset($requestData['nameorcode']) ? $requestData['nameorcode'] : '' }}";
            if (nameorcode != '') {
                $('#nameorcode').val(nameorcode);
            }
        }

        if (customertextG == 1) {
            let customer_tx = "{{ isset($requestData['customer_tx']) ? $requestData['customer_tx'] : '' }}";
            if (customer_tx != '') {
                $('#customer_tx').val(customer_tx);
            }
        }

        if (employeetextG == 1) {
            let employee_tx = "{{ isset($requestData['employee_tx']) ? $requestData['employee_tx'] : '' }}";
            if (employee_tx != '') {
                $('#employee_tx').val(employee_tx);
            }
        }

        if (issueRetBillNoG == 1) {
            let issueRBillNo =
                "{{ isset($requestData['issue_r_bill_no']) ? $requestData['issue_r_bill_no'] : '' }}";
            if (issueRBillNo != '') {
                $('#issue_r_bill_no').val(issueRBillNo);
            }
        }

        if (stockG == 1) {
            let stockSearch = "{{ isset($requestData['stockSearch']) ? $requestData['stockSearch'] : '' }}";
            if (stockSearch != '') {
                $('#stockSearch').val(stockSearch);
            }
        }

        // // // ACC
        if (voucherTypeG == 1) {
            let voucherType = "{{ isset($requestData['voucher_type']) ? $requestData['voucher_type'] : '' }}";
            if (voucherType != '') {
                $('#voucher_type').val(voucherType);
            }
        }

        if (voucherG == 1) {
            let voucherType = "{{ isset($requestData['v_generate_type']) ? $requestData['v_generate_type'] : '' }}";
            if (voucherType != '') {
                $('#v_generate_type').val(voucherType);
            }
        }

        if (accountTypeG == 1) {
            let accountType = "{{ isset($requestData['account_type']) ? $requestData['account_type'] : '' }}";
            if (accountType != '') {
                $('#account_type').val(accountType);
            }
        }



        if(vTypeReceiptG == 1){
            let voucherTypeReceipt = "{{ isset($requestData['voucher_type']) ? $requestData['voucher_type'] : '' }}";
            if (voucherTypeReceipt != '') {
                $('#voucher_type').val(voucherTypeReceipt);
            }
        }

        if (depthLevelG == 1) {
            let depthLevel = "{{ isset($requestData['depth_level']) ? $requestData['depth_level'] : '' }}";
            if (depthLevel != '') {
                $('#depth_level').val(depthLevel);
            }
        }

        if (roundUpG == 1) {
            let roundUp = "{{ isset($requestData['round_up']) ? $requestData['round_up'] : '' }}";
            if (roundUp != '') {
                $('#round_up').val(roundUp);
            }
        }

        if (zeroBalanceG == 1) {
            let zeroBalance = "{{ isset($requestData['zero_balance']) ? $requestData['zero_balance'] : '' }}";
            if (zeroBalance != '') {
                $('#zero_balance').val(zeroBalance);
            }
        }

        if (invoiceNoG == 1) {
            let invoiceId = "{{ isset($requestData['invoice_id']) ? $requestData['invoice_id'] : '' }}";
            if (invoiceId != '') {
                $('#invoice_id').val(invoiceId);
            }
        }

        if (ledgerG == 1) {
            let ledgerId = "{{ isset($requestData['ledger_id']) ? $requestData['ledger_id'] : '' }}";
            if (ledgerId != '') {
                $('#ledger_id').val(ledgerId);
            }
        }

        if (ledgerCashG == 1) {
            let ledgerCash = "{{ isset($requestData['ledger_cash']) ? $requestData['ledger_cash'] : '' }}";
            if (ledgerCash != '') {
                $('#ledger_cash').val(ledgerCash);
            }
        }

        if (ledgerBankG == 1) {

            let ledgerBank = "{{ isset($requestData['ledger_bank']) ? $requestData['ledger_bank'] : '' }}";
            if (ledgerBank != '') {
                $('#ledger_bank').val(ledgerBank);
            }
        }

        if (ledgerCashBankG == 1) {

            let ledgerCashBankG = "{{ isset($requestData['ledger_cash_bank']) ? $requestData['ledger_cash_bank'] : '' }}";
            if (ledgerCashBankG != '') {
                $('#ledger_cash_bank').val(ledgerCashBankG);
            }
        }

        if (typeCashBankG == 1) {

            let typeCashBankG = "{{ isset($requestData['type_cash_bank']) ? $requestData['type_cash_bank'] : '' }}";
            if (typeCashBankG != '') {
                $('#type_cash_bank').val(typeCashBankG);
                if(typeCashBankG == 1) $('#reportTitleDiv').html('Cash Book');
                else if(typeCashBankG == 2) $('#reportTitleDiv').html('Bank Book');
            }
        }

        {
            let voucherTypeId =
                "{{ isset($requestData['voucher_type_id']) ? $requestData['voucher_type_id'] : '' }}";
            if (voucherTypeId != '') {
                $('#voucher_type_id').val(voucherTypeId);
            }
        }

        if (customerG == 1) {
            let customerId = "{{ isset($requestData['customer_id']) ? $requestData['customer_id'] : '' }}";
            if (customerId != '') {
                $('#customer_id').val(customerId);
            }
        }

        if (employeeG == 1) {
            let employeeId = "{{ isset($requestData['employee_id']) ? $requestData['employee_id'] : '' }}";
            if (employeeId != '') {
                $('#employee_id').val(employeeId);
            }
        }
        if (salesByOnlyPosReport == 1) {
            let salesById = "{{ isset($requestData['salesBy_id']) ? $requestData['salesBy_id'] : '' }}";
            if (salesById != '') {
                $('#salesBy_id').val(salesById);
            }
        }

        if (reportFormattingG == 1) {
            let reportFormatting =
                "{{ isset($requestData['report_formatting']) ? $requestData['report_formatting'] : '' }}";
            if (reportFormatting != '') {
                $('#report_formatting').val(reportFormatting);
            }
        }

        if (reportViewOptionG == 1) {
            let reportViewOption =
                "{{ isset($requestData['view_option']) ? $requestData['view_option'] : '' }}";
            if (reportViewOption != '') {
                $('#view_option').val(reportViewOption);
            }
        }

        if (reportHeaderG == 1) {
            let reportHeader =
                "{{ isset($requestData['report_header']) ? $requestData['report_header'] : '' }}";
            if (reportHeader != '') {
                $('#report_header').val(reportHeader);
            }
        }

        if (monthYearG == 1) {

            let monthYear = "{{ isset($requestData['month_year']) ? $requestData['month_year'] : '' }}";
            if (monthYear != '') {
                // console.log(monthYear);
                $('#month_year').val(monthYear);
            } else {
                let today = $.datepicker.formatDate('MM-yy', new Date());
                $('#month_year').val(today);
            }
        }

        if (searchByG == 1) {
            let searchBy = "{{ isset($requestData['search_by']) ? $requestData['search_by'] : '' }}";
            if (searchBy != '') {
                $('#search_by').val(searchBy);
            }
        }

        {
            let fiscalYear = "{{ isset($requestData['fiscal_year']) ? $requestData['fiscal_year'] : '' }}";
            if (fiscalYear != '') {
                $('#fiscal_year').val(fiscalYear);
            }
        }

        if (currentYearG == 1 || fiscalYearDateRangeG == 1) {
            let startDateCy = "{{ isset($requestData['start_date_cy']) ? $requestData['start_date_cy'] : '' }}";
            if (startDateCy != '') {
                $('#start_date_cy').val(startDateCy);
                $('#start_date').val(startDateCy);
            }

            let endDateCy = "{{ isset($requestData['end_date_cy']) ? $requestData['end_date_cy'] : '' }}";
            if (endDateCy != '') {
                $('#end_date_cy').val(endDateCy);
                $('#end_date').val(endDateCy);
            }
        }

        // if(dateRangeG == 1)
        {
            let startDateDr = "{{ isset($requestData['start_date_dr']) ? $requestData['start_date_dr'] : '' }}";
            if (startDateDr != '') {
                $('#start_date_dr').val(startDateDr);
                $('#start_date').val(startDateDr);
            }

            let endDateDr = "{{ isset($requestData['end_date_dr']) ? $requestData['end_date_dr'] : '' }}";
            if (endDateDr != '') {
                $('#end_date_dr').val(endDateDr);
                $('#end_date').val(endDateDr);
            }
        }

        if (monthG == 1) {

            let monthYr = "{{ isset($requestData['month_yr']) ? $requestData['month_yr'] : '' }}";
            if (monthYr != '') {
                $('#month_yr').val(monthYr);
            } else {
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



        $('.clsSelect2').select2({
            'width': '100%'
        });

        $('#fiscal_year').select2({
            'width': '100%'
        });

        // showReportHeading('close');
    }
</script>

<!-- functions For Zone Area -->
<script type="text/javascript">

    function fnAjaxGetRegion() {
        var zoneId = $('#zone_id').val();

        let selectedValue = $('#region_id').val();

        // if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
        //     if(regionG['exist'] == true && typeof regionG['selected'] != "undefined" && regionG['selected'] != ''){
        //         selectedValue = regionG['selected'];
        //     }
        // }

        $("#region_id").empty().append($('<option>', {
            value: "",
            text: "All"
        }));

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetRegion') }}",
            dataType: "json",
            async:false,
            data: {
                zoneId: zoneId,
                returnType: 'json',
                returnFor: 'search'
            },
            success: function (response) {
                if (response['status'] == 'success') {
                    let result_data = response['result_data'];
                    let idArr = [];   // New code for zone,area

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $("#region_id").append($('<option>', {
                            value: item.id,
                            text: item.region_name + " [" + item.region_code + "]",
                            // defaultSelected: false,
                            // selected: true
                        }));
                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $("#region_id").val(selectedValue);
                    }else{
                        $("#region_id").val();
                    }
                    // // console.log('fn 3');
                }
            }
        });
    }

    function fnAjaxGetArea() {
        var zoneId = $('#zone_id').val();
        var regionId = $('#region_id').val();

        let selectedValue = $('#area_id').val();

        $('#area_id').empty().append($('<option>', {
            value: "",
            text: "All"
        }));

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetArea') }}",
            dataType: "json",
            data: {
                zoneId: zoneId,
                regionId: regionId,
                returnType: 'json',
                returnFor: 'search'
            },
            success: function (response) {
                if (response['status'] == 'success') {
                    let result_data = response['result_data'];
                    let idArr = [];   // New code for zone,area

                    // $('#area_id').empty().append($('<option>', {
                    //     value: "",
                    //     text: "All"
                    // }));

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $('#area_id').append($('<option>', {
                            value: item.id,
                            text: item.area_name + " [" + item.area_code + "]",
                            // defaultSelected: false,
                            // selected: true
                        }));
                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $('#area_id').val(selectedValue);
                    }else{
                        $('#area_id').val();
                    }
                    console.log('fn 3');
                }
            }
        });
    }

    function fnAjaxGetBranch() {

        var zoneId = $('#zone_id').val();
        var regionId = $('#region_id').val();
        var areaId = $('#area_id').val();

        // if (areaId != null || zoneId != null) {
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetBranch') }}",
            dataType: "json",
            data: {
                areaId: areaId,
                zoneId: zoneId,
                regionId: regionId,
                ignorHO: branchWithoutHOG,
                returnType: 'json',
                returnFor: 'search'
            },
            success: function (response) {
                if (response['status'] == 'success') {
                    let result_data = response['result_data'];
                    let selectedValue = $('#branch_id').val();
                    let idArr = [];   // New code for zone,area

                    $('#branch_id').empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    if (branchWithoutHOG == 1) {
                        $('#branch_id').empty().append($('<option>', {
                            value: "",
                            text: "Select One"
                        }));
                    }

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $('#branch_id').append($('<option>', {
                            value: item.id,
                            text: item.branch_name + " [" + item.branch_code + "]"
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $('#branch_id').val(selectedValue);
                    }else{
                        $('#branch_id').val();
                    }
                    console.log('fn 4');
                }
            }
        });
        // }
    }
</script>

<!-- functions For product -->
<script type="text/javascript">

    function fnAjaxGetCategory(moduleName = null) {
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
                    let result_data = response['result_data'];
                    let selectedValue = $('#cat_id').val();

                    $('#cat_id').empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#cat_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#cat_id').val(selectedValue).select2({'width': '100%'});
                        $('#cat_id').val(selectedValue);
                    }

                    console.log('fn 5');

                }
            }
        });
    }

    function fnAjaxGetSubCat(moduleName = null) {
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
                    let result_data = response['result_data'];
                    let selectedValue = $('#sub_cat_id').val();

                    $('#sub_cat_id').empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#sub_cat_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#sub_cat_id').val(selectedValue).select2({'width': '100%'});
                        $('#sub_cat_id').val(selectedValue);
                    }

                    console.log('fn 6');

                }
            }
        });
    }

    function fnAjaxGetModel(moduleName = null) {
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

                    let result_data = response['result_data'];
                    let selectedValue = $('#model_id').val();

                    $('#model_id').empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#model_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#model_id').val(selectedValue).select2({'width': '100%'});
                        $('#model_id').val(selectedValue);
                    }

                    console.log('fn 7');
                }
            }
        });
    }

    function fnAjaxGetProduct(moduleName = null) {
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

                    let result_data = response['result_data'];
                    let selectedValue = $('#product_id').val();

                    $('#product_id').empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#product_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#product_id').val(selectedValue).select2({'width': '100%'});
                        $('#product_id').val(selectedValue);
                    }

                    console.log('fn 8');

                }
            }
        });
    }

    function fnAjaxGetProductType(moduleName = null) {
        var groupId = $('#group_id').val();
        var categoryId = $('#cat_id').val();
        var subCatId = $('#sub_cat_id').val();
        var brandId = $('#brand_id').val();
        var modelId = $('#model_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProductType') }}",
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

                    let result_data = response['result_data'];
                    let selectedValue = $('#type_id').val();

                    $('#type_id').empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#type_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#type_id').val(selectedValue).select2({'width': '100%'});
                        $('#type_id').val(selectedValue);
                    }

                    console.log('fn 9');

                }
            }
        });
    }

    function fnAjaxGetProductName(moduleName = null) {
        var groupId = $('#group_id').val();
        var categoryId = $('#cat_id').val();
        var subCatId = $('#sub_cat_id').val();
        var typeId = $('#type_id').val();
        var modelId = $('#model_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProductName') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                typeId: typeId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];
                    let selectedValue = $('#name_id').val();

                    $('#name_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#name_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#name_id').val(selectedValue).select2({'width': '100%'});
                        $('#name_id').val(selectedValue);
                    }

                    console.log('fn 10');

                }
            }
        });
    }
</script>

<!-- functions For ledger -->
<script type="text/javascript">

    function ajaxLedgerLoad(branch_id, project_id, acc_type = null) {
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetLedgerForBranch') }}",
            dataType: "json",
            data: {
                branch_id: branch_id,
                project_id: project_id,
                acc_type: acc_type,
                returnType: 'json'
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    if (acc_type == 4) {
                        // $('#ledger_cash').empty().html(data);

                        let selectedValue = $('#ledger_cash').val();

                        $('#ledger_cash').empty().append($('<option>', {
                            value: "",
                            text: "All"
                        }));

                        $.each(result_data, function (i, item) {

                            $('#ledger_cash').append($('<option>', {
                                value: item.id,
                                text: item.code + ' [' + item.name + ']'
                            }));

                        });

                        if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                            // $('#ledger_cash').val(selectedValue).select2({'width': '100%'});
                            $('#ledger_cash').val(selectedValue);
                        }

                    } else if (acc_type == 5) {
                        // $('#ledger_bank').empty().html(data);

                        let selectedValue = $('#ledger_bank').val();

                        $('#ledger_bank').empty().append($('<option>', {
                            value: "",
                            text: "All"
                        }));

                        $.each(result_data, function (i, item) {

                            $('#ledger_bank').append($('<option>', {
                                value: item.id,
                                text: item.code + ' [' + item.name + ']'
                            }));

                        });

                        if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                            // $('#ledger_bank').val(selectedValue).select2({'width': '100%'});
                            $('#ledger_bank').val(selectedValue);
                        }
                    } else {
                        // $('#ledger_id').empty().html(data);

                        let selectedValue = $('#ledger_id').val();

                        $('#ledger_id').empty().append($('<option>', {
                            value: "",
                            text: "All"
                        }));

                        $.each(result_data, function (i, item) {

                            $('#ledger_id').append($('<option>', {
                                value: item.id,
                                text: item.code + ' [' + item.name + ']'
                            }));

                        });

                        if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                            // $('#ledger_id').val(selectedValue).select2({'width': '100%'});
                            $('#ledger_id').val(selectedValue);
                        }
                    }

                    console.log('fn 5');

                }
            }
        });
    }
</script>

<!-- functions For Fiscal / Current / serchby  -->
<script type="text/javascript">
    function fnAjaxFiscalYear() {
        let branchId = $('#branch_id').val();


        if (branchId === "-1" || branchId === "-2" || branchId === -1 || branchId === -2) {
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

                    // console.log("branch open-->", brOpeningDate);


                    $("#fiscal_year option").each(function () {
                        let startDate = $(this).attr('data-orginalStartdate');
                        let endDate = $(this).attr('data-orginalEnddate');

                        // console.log("test1 --> ",startDate, endDate);

                        if (startDate === undefined && endDate === undefined) {
                            return true;
                        }

                        let startArr = startDate.split("-");
                        let endArr = endDate.split("-");

                        /////////////// Y-m-d
                        startDate = startArr[2] + "-" + startArr[1] + "-" + startArr[0];
                        endDate = endArr[2] + "-" + endArr[1] + "-" + endArr[0];

                        if (brOpeningDate >= startDate && brOpeningDate <= endDate) {
                            startDate = brOpeningDate;
                        }
                        else if(brOpeningDate >= endDate){
                            // branch open date end date theke boro hole select kora jabe na.
                            startDate = brOpeningDate;
                        }

                        if (loginSystemDate >= startDate && loginSystemDate <= endDate) {
                            endDate = loginSystemDate;
                        }

                        if (startDate >= endDate) {
                            // start date end date theke boro hole select kora jabe na.
                            $(this).attr('disabled', true);
                        }
                        else {
                            $(this).removeAttr("disabled");
                        }

                        startDate = $.datepicker.formatDate('dd-mm-yy', new Date(startDate));
                        endDate = $.datepicker.formatDate('dd-mm-yy', new Date(endDate));

                        // console.log("test2 --> ",startDate, endDate);

                        $(this).attr('data-startdate', startDate);
                        $(this).attr('data-enddate', endDate);

                        $(this).data('startdate', startDate);
                        $(this).data('enddate', endDate);



                    });

                    fnForSearchBy();

                    console.log('fn 10');

                } else if (response['status'] == 'error') {
                    swal({
                        icon: 'warning',
                        title: 'Warning',
                        text: response['message'],
                        timer: 3000
                    }).then(function () {
                        // window.location = "{{url('/acc')}}";
                    });
                }
            }
        });
    }

    function fnAjaxCurrentFY() {
        let branchId = $('#branch_id').val();

        if (branchId === "-1" || branchId === "-2" || branchId === -1 || branchId === -2) {
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

                    loginBranchOpenDate = new Date(loginBranchOpenDate);
                    if (loginBranchOpenDate >= tempStartDate && loginBranchOpenDate <= tempEndDate) {
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

                    console.log('fn 11');


                } else if (response['status'] == 'error') {
                    swal({
                        icon: 'warning',
                        title: 'Warning',
                        text: response['message'],
                        timer: 3000
                    }).then(function () {
                        // window.location = "{{url('/acc')}}";
                    });
                }
            }
        });
    }

    function fnForSearchBy() {
        let selected = $('#search_by').val();
        let start_date_txt = "";
        let end_date_txt = "";

        if (selected == 1 || selected == 5) { // fiscal year
            $('#endDateDivCY,#startDateDivDR,#endDateDivDR').hide('fast');
            $('#fyDiv').show('slow');

            if ($('#fiscal_year').val() != '') {
                let start_date_fy = $('#fiscal_year :selected').data('startdate');
                let end_date_fy = $('#fiscal_year :selected').data('enddate');

                $('#start_date_fy').val(start_date_fy);
                $('#end_date_fy').val(end_date_fy);

                let startArr = start_date_fy;
                let endArr = end_date_fy;

                startArr = startArr.split("-");
                endArr = endArr.split("-");

                /////////////// Y-m-d
                startDate = startArr[2] + "-" + startArr[1] + "-" + startArr[0];
                endDate = endArr[2] + "-" + endArr[1] + "-" + endArr[0];

                let pre_fiscal_start = (Number(startArr[2]) - 1);
                let pre_fiscal_end = startArr[2];

                let cur_fiscal_start = startArr[2];
                let cur_fiscal_end = endArr[2];

                if(cur_fiscal_start === cur_fiscal_end) {
                    cur_fiscal_end = Number(cur_fiscal_end) + 1;
                }

                $('#prev_year').html(pre_fiscal_start + "-" + pre_fiscal_end);
                $('#current_year').html(cur_fiscal_start + "-" + cur_fiscal_end);

                start_date_txt = start_date_fy;
                end_date_txt = end_date_fy;

                if(selected == 5){

                    let fyNameCy = $('#fiscal_year :selected').text();

                    $("#start_date_cy").val(start_date_fy);
                    $("#fy_name_cy").val(fyNameCy);

                    $("#end_date_cy").datepicker("option", "minDate", start_date_fy);
                    $("#end_date_cy").datepicker("option", "maxDate", end_date_fy);

                    $('#endDateDivCY').show('slow');

                    end_date_txt = $("#end_date_cy").val();
                }


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
            startDate = startArr[2] + "-" + startArr[1] + "-" + startArr[0];
            endDate = endArr[2] + "-" + endArr[1] + "-" + endArr[0];

            let pre_fiscal_start = (Number(startArr[2]) - 1);
            let pre_fiscal_end = startArr[2];

            let cur_fiscal_start = startArr[2];
            let cur_fiscal_end = (Number(startArr[2]) + 1);

            if(cur_fiscal_start === cur_fiscal_end) {
                cur_fiscal_end = Number(cur_fiscal_end) + 1;
            }

            $('#prev_year').html(pre_fiscal_start + "-" + pre_fiscal_end);
            $('#current_year').html(cur_fiscal_start + "-" + cur_fiscal_end);

        }
        else if (selected == 3) { // date range
            $('#fyDiv,#endDateDivCY').hide('fast');
            $('#startDateDivDR,#endDateDivDR').show('slow');

            start_date_txt = $('#start_date_dr').val();
            end_date_txt = $('#end_date_dr').val();

        }
        else {
            $('#fyDiv,#endDateDivCY').hide('');
        }

        if (start_date_txt != "") {
            $('#start_date').val(start_date_txt);
            $('#start_date_txt').html(viewDateFormat(start_date_txt));
        }

        if (end_date_txt != "") {
            $('#end_date').val(end_date_txt);

            $('#end_date_txt').html(viewDateFormat(end_date_txt));
            $('.title_date').html(end_date_txt);
        }

        console.log('fn 12');
    }
</script>

<!-- ////////////////////// Function Declare End //////////////////////////// -->

<!-- on load function -->
<script type="text/javascript">

    async function onLoadFunctionCall(){

        if(zoneG == 1 || regionG == 1 || areaG == 1){
            // fnAjaxGetArea();
            // fnAjaxGetBranch();
            // && $('#branch_id').val() == ''
            let zone_flag = $('#zone_id').is("select") && $('#zone_id').val() != '';
            let region_flag = $('#region_id').is("select") && $('#region_id').val() != '';
            let area_flag = $('#area_id').is("select") && $('#area_id').val() != '';


            if(zone_flag || region_flag || area_flag){
                fnAjaxGetBranch();
            }
            // if($('#area_id').val() != ''){
            //     fnAjaxGetArea();
            // }

            fnAjaxGetRegion();
            fnAjaxGetArea();
        }


        /* for POS Product group */
        if (groupG == 1) {
            fnAjaxGetCategory('pos');
            fnAjaxGetSubCat('pos');
        }

        if(modelG == 1){
            fnAjaxGetModel('pos');
        }

        if(productG == 1){
            fnAjaxGetProduct('pos');
        }

        /* for INV Product group */
        if (groupGInv == 1) {
            fnAjaxGetCategory('inv');
            fnAjaxGetSubCat('inv');
        }

        if(modelGInv == 1){
            fnAjaxGetModel('inv');
        }

        if(productGInv == 1){
            fnAjaxGetProduct('inv');
        }


        /* for Fiscal / Current / serchby */
        if (searchByG == 1) {

            var searchByLoad = $('#search_by').val();

            if (searchByLoad == "1" || searchByLoad == "5") {
                fnAjaxFiscalYear();
            } else if (searchByLoad == "2") {
                fnAjaxCurrentFY();
            } else {
                fnForSearchBy();
            }
        }
    }

</script>

<!-- on load -->
<script type="text/javascript">

    $(document).ready(function () {

        /* Load Default */

        $('#fiscal_year').select2({
            'width': '100%'
        });

        if (monthYearG == 1) {
            $('#month_year').datepicker({
                dateFormat: 'MM-yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                todayButton: false,
                onClose: function (dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val($.datepicker.formatDate('MM-yy', new Date(year, month, 1)));
                }
            });

            $("#month_year").click(function () {
                $(".ui-datepicker-calendar").hide();
            });

            $("#month_year").focus(function () {
                $(".ui-datepicker-calendar").hide();
            });
        }

        if (monthG == 1) {

            $(".monthPicker").datepicker({
                dateFormat: 'MM yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                todayButton: false,
                onClose: function (dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();

                    $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                }
            });

            $(".monthPicker").click(function () {
                $(".ui-datepicker-calendar").hide();
            });

            $(".monthPicker").focus(function () {
                $(".ui-datepicker-calendar").hide();
                // $("#ui-datepicker-div").position({
                //     my: "center top",
                //     at: "center bottom",
                //     of: $(this)
                // });
            });
        }

        if (currentYearG == 1 || fiscalYearDateRangeG == 1) {
            $("#end_date_cy").datepicker({
                dateFormat: 'dd-mm-yy',
                orientation: 'bottom',
                autoclose: true,
                todayHighlight: true,
                changeMonth: true,
                changeYear: true,
            });
        }

        if (dateRangeG == 1) {
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
        }

        /* show notification for ignore branch data */
        {
            let messageHas = "{{ (Session::has('status')) ? 1 : 0}}";

            if (messageHas == 1) {
                let type = "{{ Session::get('status', 'error')}}";

                if (type == 'error') {

                    let messageText = "{!! Session::get('message') !!}";
                    const wrapper = document.createElement('div');
                    let html = "<p style='color:#000;'>" + messageText + "</p>";
                    // html += "<p style='color:#000;'>Please delete that transactions or get to that date </p>";
                    wrapper.innerHTML = html;

                    swal({
                        icon: 'warning',
                        title: 'Warning',
                        content: wrapper,
                        timer: 5000
                    }).then(function () {
                        // window.location = "{{url('/acc')}}";
                    });
                }
            }
        }

        /* Load Default End */

        /* For request selected variable load automatic */
        /*
        * ajaxStart jquery function custom-js initially call kora ache, tai aikhane call korle seti kaj korbe na.
        * ajaxstart hole ekta global variable (globalVarForAjaxStatus) true hocche tar opor depend kore function call hobe
        * find ajax call running console.log('ajax running', $.active);
        * stop ajax call ajx.abort();
        * console.log(globalVarForAjaxStatus);
        */
        {
            let requestArr = "{{ (count($requestData) > 0) ? 1 : 0 }}";

            if(requestArr == 1){

                if(globalVarForAjaxStatus === true){
                    $(document).ajaxComplete(function () {

                        if($.active > 1){
                            return;
                        }
                        else{
                            // console.log('calling test');
                            // console.log(showRequestVarForOneTime);
                            if(showRequestVarForOneTime === false){
                                fnForRequestedVariable().then(() => {


                                    onLoadFunctionCall();
                                    showReportHeading('close');
                                });
                            }
                            globalVarForAjaxStatus = false;
                            // console.log(showRequestVarForOneTime);
                        }
                    });
                }
                else{
                    fnForRequestedVariable().then(() => {

                        // showReportHeading('close');
                        onLoadFunctionCall();
                        showReportHeading('close');
                    });
                }
            }
            else{
                onLoadFunctionCall();
            }
        }
    });
</script>

<!-- on Change -->
<script type="text/javascript">

    $('#zone_id').change(function(e){
        // fnAjaxGetArea();
        // fnAjaxGetRegion();
        fnAjaxGetBranch();
    });

    $('#region_id').change(function(e){
        // fnAjaxGetArea();
        // fnAjaxGetRegion();
        fnAjaxGetBranch();
    });

    $('#area_id').change(function(e){
        fnAjaxGetBranch();
    });

    $('#branch_id').change(function () {

        if (employeeG == 1) {

            let posModule = "{{ (Common::getModuleByRoute() == 'pos') ? 1 : 0 }}";

            let selectOption = "{{ (isset($creditOfficer) && $creditOfficer) ? 'all' : 'one' }}";

            if(posModule == 1){
                fnAjaxSelectBox('employee_id',
                    $(this).val(),
                    '{{base64_encode("hr_employees")}}',
                    '{{base64_encode("branch_id")}}',
                    '{{base64_encode("employee_no,emp_name,emp_code")}}',
                    '{{url("/ajaxSelectBox")}}', null, 'isActiveOff',null,null,selectOption
                );
            }
            else{
                fnAjaxSelectBox('employee_id',
                    $(this).val(),
                    '{{base64_encode("hr_employees")}}',
                    '{{base64_encode("branch_id")}}',
                    '{{base64_encode("id,emp_name,emp_code")}}',
                    '{{url("/ajaxSelectBox")}}', null, 'isActiveOff',null,null,selectOption
                );
            }

        }


        if (salesByOnlyPosReport == 1) {

            let posModule = "{{ (Common::getModuleByRoute() == 'pos') ? 1 : 0 }}";


            $.ajax({
                method: "GET",
                url: "{{ url('/ajaxGetSalesByData') }}",
                dataType: "text",
                data: {
                    branch_id: $(this).val(),
                },
                success: function (data) {
                    if (data) {
                        // $('#salesBy_id').val(data);
                        $('#salesBy_id')
                        .find('option')
                        .remove()
                        .end()
                        .append(data[0]);
                        console.log(data[0]);
                    }
                }
            });


        }

        if (ledgerG == 1 || ledgerCashG == 1 || ledgerBankG == 1) {

            let branch_id = $(this).val();
            let project_id = $('#project_id').val();

            if ($('#ledger_id').attr('id') == 'ledger_id') {
                ajaxLedgerLoad(branch_id, project_id);
            }

            if ($('#ledger_cash').attr('id') == 'ledger_cash') {
                let acc_type = 4;

                ajaxLedgerLoad(branch_id, project_id, acc_type);
            }

            if ($('#ledger_bank').attr('id') == 'ledger_bank') {
                let acc_type = 5;

                ajaxLedgerLoad(branch_id, project_id, acc_type);
            }
        }

        if (searchByG == 1) {
            let searchBy = $('#search_by').val();

            if (searchBy == "1" || searchBy == "5") {
                fnAjaxFiscalYear();
            }

            if (searchBy == "2") {
                fnAjaxCurrentFY();
            }

            if (searchBy == "3" || searchBy == "4") {
                fnForSearchBy();
            }
        }
    });

    $("#project_id").change(function () {

        if (ledgerG == 1 || ledgerCashG == 1 || ledgerBankG == 1) {

            let branch_id = $('#branch_id').val();
            let project_id = $(this).val();

            if ($('#ledger_id').attr('id') == 'ledger_id') {
                ajaxLedgerLoad(branch_id, project_id);
            }

            if ($('#ledger_cash').attr('id') == 'ledger_cash') {
                let acc_type = 4;
                ajaxLedgerLoad(branch_id, project_id, acc_type);
            }

            if ($('#ledger_bank').attr('id') == 'ledger_bank') {
                let acc_type = 5;
                ajaxLedgerLoad(branch_id, project_id, acc_type);
            }
        }
    });

    /* for Fiscal / Current / serchby */
    if (searchByG == 1) {
        $('#search_by').change(function () {
            // 1, 2 er jonno tader ajax a load hocche fnForSearchBy function

            let searchBy = $('#search_by').val();

            if (searchBy == "1" || searchBy == "5") {
                fnAjaxFiscalYear();
            }

            if (searchBy == "2") {
                fnAjaxCurrentFY();
            }

            if (searchBy == "3" || searchBy == "4") {
                fnForSearchBy();
            }
        });

        $("#fiscal_year").change(function () {
            fnForSearchBy();
        });
    }

</script>

<!-- on Submit / click  -->
<script type="text/javascript">

    $('#refreshButton').click(function(event){
        window.location.href = window.location.href.split('#')[0];
    });

    $('#searchButton').click(function (event) {

        // $(".wb-minus").trigger('click');

        if ($("#filterFormId").length) {
            fnLoading(true);
        }

        showReportHeading('close');
        $("#filterFormId").submit();
    });
</script>

<script>
    function incompleteBranchList() {
        $("#incomplete_list_modal").modal('show');
    }
</script>
