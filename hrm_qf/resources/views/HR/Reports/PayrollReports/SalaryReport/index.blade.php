@extends('Layouts.erp_master_full_width')
@section('content')

    @php
        use App\Services\HrService as HRS;
        $months = DB::table('hr_months')->pluck('name', 'id')->toArray();
        $fiscalYear = DB::table('gnl_fiscal_year')->where([['is_delete', 0],['is_active', 1]])->orderBy('fy_name','desc')->first();

        $companies = DB::table('gnl_companies')->where([['is_active', 1],['is_delete', 0]])->get(); //comp_name
        $companies = $companies->pluck('comp_name','id')->toArray();
        $projects = DB::table('gnl_projects')->where([['is_active', 1],['is_delete', 0]])->get(); //project_name
        $projects = $projects->pluck('project_name','id')->toArray();
        $groups = DB::table('gnl_groups')->where([['is_active', 1],['is_delete', 0]])->get();
        $groups = $groups->pluck('group_name','id')->toArray();

        $salaryMonthArr = [];
        $getMonthNameDatesDaysData = HRS::getMonthNameDatesDaysData($fiscalYear);
        foreach ($months as $key => $value) {
            foreach($getMonthNameDatesDaysData as $keyMonth => $monthDateArr){
                if($keyMonth == $value){
                    $monthDatesKeysArr = array_keys($monthDateArr);
                    $monthStartDate = reset($monthDatesKeysArr);
                    $monthEndDate = end($monthDatesKeysArr);
                    $tmpTargetYear = intval((date("Y", strtotime($monthEndDate))));
                    // $identityName = $keyMonth.'-'.$tmpTargetYear;
                    $identityName = $keyMonth;
                    $salaryMonthArr[$identityName] = $monthEndDate;
                }
            }
        }
        $salaryMonthArr = array_flip($salaryMonthArr);

        // dd($salaryMonthArr);

        use App\Services\CommonService as Common;

        // ## Convension mismatch thats why variable name change
        $elementArray = array();

        $elementArray['zone'] = ['label' => 'Zone', 'type' => 'select', 'id' => 'zone_id', 'name' => 'zone_id'];
        $elementArray['region'] = ['label' => 'Region', 'type' => 'select', 'id' => 'region_id', 'name' => 'region_id'];
        $elementArray['area'] = ['label' => 'Area', 'type' => 'select', 'id' => 'area_id', 'name' => 'area_id'];

        $elementArray['select_box1'] = [ 'required' => true, 'label'=>"Companie", 'type'=>'select', 'id'=> 'companie_id', 'name' => 'companie_id', 'options'=> $companies];
        $elementArray['select_box2'] = [ 'required' => true, 'label'=>"Project", 'type'=>'select', 'id'=> 'project_id', 'name' => 'project_id', 'options'=> $projects];
        $elementArray['select_box3'] = [ 'required' => true, 'label'=>"Group", 'type'=>'select', 'id'=> 'groups_id', 'name' => 'groups_id', 'options'=> $groups];

        $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id','onload' => '1', 'required' => true, 'withHeadOffice'=>true];

        
        $elementArray['select_box4'] = [
            'required' => true,
            'label'=>"Salary Month",
            'type'=>'select',
            'id'=> 'salary_month',
            'name' => 'salary_month',
            'selected_value' => ' ',
            'options'=> $salaryMonthArr
        ];

        $elementArray['select_box5'] = ['label'=>"Approved By", 'type'=>'text', 'id'=> 'approved_by', 'name' => 'approved_by'];
        $elementArray['ApprovedDate'] = ['label' => 'Approved Date', 'type'=>'startDate', 'id' => 'approved_date', 'name'=> 'approved_date', 'required' => false];
        $elementArray['PaymentDate'] = ['label' => 'Payment Date', 'type'=>'startDate', 'id' => 'payment_date', 'name'=> 'payment_date', 'required' => false];

        $elementArray['select_box6'] = ['label'=>"Create By", 'type'=>'text', 'id'=> 'create_by', 'name' => 'create_by'];

        $elementArray['statusBox'] = ['label' => 'Status', 'type' => 'status', 'id' => 'status', 'name' => 'status', 'module' => 'HR','onload' => '1'];




        $ignoreElements = ['company', 'zone', 'region', 'area', 'branch', 'startDate', 'endDate' ];

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
                    'reportTitle' => 'Salary Report',
                    'title_excel' => 'Salary_Report',
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
        $('.httpRequest').hide(); //Hide new entry button
        $(document).ready(function(event){


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

            // $('#searchButton').click(function () {
            //     $('#filterFormId').submit(function (event) {
            //         event.preventDefault();
            //         // $("#reportingDiv").empty();
            //         var flag = true;

            //         if(flag){
            //             $('.show').show('slow');
            //             //$("#reportingDiv").load('{{URL::to("hr/reports/salary_report/body")}}'+'?'+$("#filterFormId").serialize());

            //             $("#reportingDiv").load('{{ url()->current() }}/body'+'?'+$("#filterFormId").serialize());
            //         }
            //     });
            // })

            
            
        });
        
    </script>
@endsection

