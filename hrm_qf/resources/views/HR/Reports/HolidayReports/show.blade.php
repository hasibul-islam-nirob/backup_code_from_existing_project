@extends('Layouts.erp_master_full_width')
@section('content')

    @php
        use App\Services\CommonService as Common;

        // ## Convension mismatch thats why variable name change
        $elementArray = array();

        // $elementArray['company'] = ['label' => 'Company', 'type' => 'select', 'id' => 'company_id', 'name' => 'company_id'];
        $elementArray['zone'] = ['label' => 'Zone', 'type' => 'select', 'id' => 'zone_id', 'name' => 'zone_id'];
        $elementArray['region'] = ['label' => 'Region', 'type' => 'select', 'id' => 'region_id', 'name' => 'region_id'];
        $elementArray['area'] = ['label' => 'Area', 'type' => 'select', 'id' => 'area_id', 'name' => 'area_id'];
        // $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id'];
        
        $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id','onload' => '1', 'required' => false, 'withHeadOffice'=>true];

        $elementArray['startDate'] = ['label' => 'From Date', 'type'=>'text', 'id' => 'startDateHolidayR', 'name'=> 'startDate', 'required' => true];
        $elementArray['endDate'] = ['label' => 'To Date', 'type'=>'text', 'id' => 'endDateHolidayR', 'name'=> 'endDate', 'required' => true];


        $ignoreElements = [
           'company', 'zone', 'region', 'area', 'branch', 'startDate', 'endDate'
        ];

    @endphp

    <form enctype="multipart/form-data" method="post" id="filterFormId">
        @csrf
        @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])
    </form>

    <div class="w-full show" style="display: none;">
        <div class="panel">
            <div class="panel-body panel-search pt-2">

                @include('elements.report.company_header')

                @include('elements.report.reporting_header', [
                    'reportTitle' => 'Holiday Report',
                    'title_excel' => 'Holiday_Report',
                    'incompleteBranch' => false,
                    'elements' => $elementArray,
                    'ignoreElements' => $ignoreElements
                ])


                <div class="row ExportDiv" id="reportingDiv">&nbsp;</div>
            </div>
        </div>
    </div>


    <!-- End Page -->
    <script>

    $(document).ready(function(event){

        $("#startDateHolidayR, #endDateHolidayR").attr("placeholder", "dd-mm-yyyy");
        $("#startDateHolidayR").datepicker({
            dateFormat: 'dd-mm-yy',
            todayHighlight: true,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            todayButton: false,
            yearRange: '2000:+5',
            onClose: function(){
                $("#endDateHolidayR").datepicker("option", "minDate", $(this).datepicker('getDate'));
            }
        });

        $("#endDateHolidayR").datepicker({
            dateFormat: 'dd-mm-yy',
            todayHighlight: true,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            todayButton: false,
            yearRange: '2000:+5',
            
        });
      
        // $('#monthYear').prop('required', 'required');
        // $('#startDate, #endDate').prop('required', 'required');

        $('#filterFormId').submit(function (event) {
            event.preventDefault();

            $("#reportingDiv").empty();
            var flag = true;

            if(flag){
                $('.show').show('slow');
                $("#reportingDiv").load('{{ url()->current() }}/body'+'?'+$("#filterFormId").serialize());
            }

            // fnLoading(true);
        });

      
        // ========= Date Range Start ================
        // var startDate;
        // var endDate;

        // $('#startDate').change(function() {
        //     endDate = $(this).datepicker('getDate');
        //     endDate.setDate(endDate.getDate() + 31);

        //     $("#endDate").datepicker("option", "maxDate", endDate);
        //     $("#endDate").datepicker("option", "minDate", $(this).datepicker('getDate'));
        // })


        // $('#endDate').change(function() {
        //     startDate = $(this).datepicker('getDate');
        //     startDate.setDate(startDate.getDate() - 31);
        //     $("#startDate").datepicker("option", "minDate", startDate);
        //     $("#startDate").datepicker("option", "maxDate", $(this).datepicker('getDate')  );
        // })
        // ========= Date Range End ================

    });

    $(".page-header-actions").addClass("d-none");

    </script>
@endsection

