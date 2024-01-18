@php
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use App\Services\HtmlService as HTML;

$StartDate = Common::systemCurrentDate();
$EndDate = Common::systemCurrentDate();


$branchArr = array();
$branchID = isset(Request::all()['branch_id']) ? Request::all()['branch_id'] : null;

if (!empty($branchID) && $branchID > 0) {
    $branchArr[] = $branchID;

    $StartDate = $EndDate = Common::systemCurrentDate($branchID);
    $branchOpenDate = Common::getBranchSoftwareStartDate($branchID);
} else {
    $StartDate = $EndDate = Common::systemCurrentDate();
    // $EndDate = Common::systemCurrentDate();
    $branchOpenDate = Common::getBranchSoftwareStartDate();
}

// dd($elements);
$definedElements = [
    'division',
    'district',
    'upozila',
    'union',
    'village',
    'zone',
    'area',
    'branch',
    'samity',
    'monthYear',
    'day',
    'month',
    'year',
    'startDate',
    'endDate',
    'date',
    'activeInactiveStatus',
    'loanStatus',
    'fieldofficer',
    'fieldofficerdropdown',
    'creditofficerdropdown',
    'fundingOrg',
    'loanaccount',
    'loanCode',
    'member',
    'nameorcode',
    'optiondropdown',
    'productFrom',
    'product',
    'productCategory',
    'productOrCategoryOptions',
    'producttype',
    'savingsaccount',
    'savingsproduct',
    'yesnodropdown',
    'loanRepaymentFrequency',
    'viewas',
    'viewtype',
    'loanType',
    'gender' ,
    'posgroup',
    'poscategory',
    'possubcategory',
    'posbrand',
    'posmodel',
    'possupplier',
    'posproduct',
    'emptyEvidence',
    'transactionType',
    'emptySavings',

];
@endphp

<div class="w-full d-print-none">
    <div class="panel filterDiv">

        <div class="panel-heading">
            <h3 class="panel-title"></h3>
            <div class="panel-actions">
                <a class="panel-action icon wb-minus" data-toggle="panel-collapse" aria-expanded="true"
                    aria-hidden="true"></a>
            </div>
        </div>

        <div class="panel-body panel-search">

            <div class="row align-items-center pb-10">
                @foreach ($elements as $name => $element)

                    @if(in_array($name, $definedElements))

                        @include('elements.report.mfn.filtering_elements.'.$name,['element'=>$element, 'allElements'=>$elements])
                    @else
                        @if(isset($element['type']) && $element['type']=='text')
                            @include('elements.report.mfn.filtering_elements.customtextbox',['element'=>$element, 'name'=>$name])
                        @else
                            @include('elements.report.mfn.filtering_elements.customdropdown',['element'=>$element, 'name'=>$name])
                        @endif
                    @endif
                @endforeach

                <div class="col-lg-2 mt-1 ml-auto">
                    <button type='submit' class="btn btn-primary btn-round text-uppercase float-right mt-4" id="searchButton"
                        style="font-size:16px;">
                        @if (strpos(Request::path(), 'reports') == true)
                            Show
                        @else
                            <i class="fa fa-search" aria-hidden="true"></i>&nbsp; Search
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function showReportHeadingForMfn(filter_div = null) {

        console.log('fn Report Heading');

        if(filter_div === 'close'){
            setTimeout(function () {
                $(".wb-minus").trigger('click');
            }, 10);
        }
        // $(".wb-minus").trigger('click');

        let reportBranchTxt = false;
        let reportForTxt = false;

        // if (zoneG == 1) {
            if ($('#zoneId').val() != '' && typeof ($('#zoneId').val()) != 'undefined') {
                reportBranchTxt = $('#zoneId').find("option:selected").text();
                reportForTxt = "Zone:";
            }
        // }

        // if (areaG == 1) {
            if ($('#areaId').val() != '' && typeof ($('#areaId').val()) != 'undefined') {
                reportBranchTxt = $('#areaId').find("option:selected").text();
                reportForTxt = "Area:";
            }
        // }

        if ($('#branchId').val() != '' && typeof ($('#branchId').val()) != 'undefined') {
            reportBranchTxt = $('#branchId').find("option:selected").text();
            reportForTxt = "Branch:";

            $('#branchName').html($('#branchId option:selected').text());
        }
        else if (typeof ($('#branchId').val()) == 'undefined') {
            reportBranchTxt = "Head Office";
            reportForTxt = "Branch:";
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

        // if (projectG == 1) {
            if ($('#projectId').val() != '' && typeof ($('#projectId').val()) != 'undefined') {
                $('#projectName').html($('#projectId option:selected').text());
            }
        // }

        // if (projectTypeG == 1) {
            if ($('#projectTypeId').val() != '' && typeof ($('#projectTypeId').val()) != 'undefined') {
                $('#projectTypeName').html($('#projectTypeId option:selected').text());
            }
        // }

        if ($('#startDate').val() != '' && typeof ($('#startDate').val()) != 'undefined') {
            $('#start_date_txt').html(viewDateFormat($('#startDate').val()));
        }
        else if (typeof ($('#startDate').val()) == 'undefined' || $('#startDate').val() == '') {

            $('#start_date_txt').hide();
            // $('#text_to').hide();
            $('#text_to').html('Up to ');
        }


        if ($('#endDate').val() != '' && typeof ($('#endDate').val()) != 'undefined') {
            $('#end_date_txt').html(viewDateFormat($('#endDate').val()));
        }
        else if (typeof ($('#endDate').val()) == 'undefined' || $('#endDate').val() == '') {
            $('#end_date_txt').hide();
            $('#text_to').hide();
        }
    }

    $("#filterFormId").submit(function (event) {
        if ($("#filterFormId").length) {
            // fnLoading(true);
        }

        showReportHeadingForMfn('close');
        // $("#filterFormId").submit();
    });

    // $('#searchButton').click(function (event) {

    //     // $(".wb-minus").trigger('click');
    //     event.preventDefault();

    //     if ($("#filterFormId").length) {
    //         // fnLoading(true);
    //     }

    //     showReportHeadingForMfn('close');
    //     $("#filterFormId").submit();

    //     // console.log($("#filterFormId").submit())

    // });
</script>

@include('elements/report/mfn/report_filter_option_script')
{{-- @include('elements/report/report_script') --}}

@if (isset($excludedReportingElemnts))
<script>
    var excludedReportingElemnts = <?php echo json_encode($excludedReportingElemnts); ?>;
</script>
@else
<script>
    var excludedReportingElemnts = [];
</script>
@endif

<script>
    var defaultExcludedEelements = ['year', 'month', 'date', 'startDate', 'endDate'];
    excludedReportingElemnts = excludedReportingElemnts.concat(defaultExcludedEelements);


</script>


