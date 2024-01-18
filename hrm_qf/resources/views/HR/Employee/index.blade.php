@extends('Layouts.erp_master')
@section('content')

@php
    // dd(11);
@endphp


    @php
        $elementArray = array();

        $elementArray['zone'] = ['label' => 'Zone', 'type' => 'select', 'id' => 'zone_id', 'name' => 'zone_id'];
        $elementArray['region'] = ['label' => 'Region', 'type' => 'select', 'id' => 'region_id', 'name' => 'region_id'];
        $elementArray['area'] = ['label' => 'Area', 'type' => 'select', 'id' => 'area_id', 'name' => 'area_id'];
        $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id'];

        $elementArray['district'] = ['label' => 'District', 'type' => 'select', 'id' => 'district_id_index', 'name' => 'district_id_index', 'exClass'=> 'c'];
        $elementArray['upazila'] = ['label' => 'Upazila', 'type' => 'select', 'id' => 'upazila_id_index', 'name' => 'upazila_id_index', 'exClass'=> 'c'];

        $elementArray['department'] = ['label' => 'Department', 'type' => 'select', 'id' => 'department_id', 'name' => 'department_id', 'onload' => '1'];
        $elementArray['designation'] = ['label' => 'Designation', 'type' => 'select', 'id' => 'designation_id', 'name' => 'designation_id', 'onload' => '1'];
        // $elementArray['employee'] = ['label' => 'Employee', 'type' => 'select', 'id' => 'employee_id', 'name' => 'employee_id', 'onload' => '1'];


        $elementArray['select_box2'] = [
            'label'=>"Employee Code",
            'type'=>'text',
            'id'=> 'se_emp_code',
            'name' => 'se_emp_code',
        ];

        $elementArray['select_box1'] = [
            'label'=>"Gender",
            'type'=>'select',
            'id'=> 'emp_gender',
            'name' => 'emp_gender',
            'selected_value' => ' ',
            'options'=>[''=>'Both', 'Male'=>'Male', 'Female'=>'Female']
        ];

        $elementArray['startDate'] = ['label' => 'From Date', 'type'=>'startDate', 'id' => 'startDate', 'name'=> 'startDate', 'required' => false];
        $elementArray['endDate'] = ['label' => 'To Date', 'type'=>'endDate', 'id' => 'endDate', 'name'=> 'endDate', 'required' => false];

        $elementArray['select_box4'] = [
            'label'=>"Status",
            'type'=>'select',
            'id'=> 'emp_status',
            'name' => 'emp_status',
            'selected_value' => ' ',
            'options'=>[
                ''=>'All',
                '1'=>'Present',
                '2'=>'Resigned',
                '3'=>'Dismissed',
                '4'=>'Terminated',
                '5'=>'Retired',
            ]
        ];

        // $elementArray['statusBox'] = ['label' => 'Status', 'type' => 'status', 'id' => 'status', 'name' => 'status', 'module' => 'HR','onload' => '1'];

        $ignoreElements = ['company', 'zone', 'region', 'area', 'branch', 'startDate', 'endDate' ];
    @endphp
    @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])
    <!-- Search Option End -->

    {{-- <select class="js-data-example-ajax" id="test" style="width:100%">
        <option> Select One</option>
    </select> --}}

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                    <thead>
                        <tr>
                            <th style="width:4%;">SL</th>
                            <th>Name</th>
                            <th>Emp. Code</th>
                            <th>Gender</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Branch</th>
                            <th>Mobile</th>
                            <th>Joining Date</th>
                            <th>Login Username</th>
                            <th>Status</th>
                            <th style="width:10%;" class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- End Page -->
    <script>

        $(document).ready(function() {
            // start hide toggle btn and panel border
            $('.panel-heading').hide();
            $('.filterDiv').css('box-shadow', 'none');
            // end hide toggle btn and panel border

            $('.ajaxRequest').hide();
            // $('.httpRequest').hide(); //Hide new entry button

            $('#emp_status').val(1);
            ajaxDataLoad();


        });

        $('#searchButton').click(function(){
            ajaxDataLoad();
        });

        function ajaxDataLoad() {

            $('.clsDataTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                stateDuration: 1800,
                order: [
                    [2, "ASC"]
                ],
                "ajax": {
                    "url": "{{ route('employeeDatatableHR') }}",
                    "dataType": "json",
                    "type": "post",
                    "data": {
                        "start_date": $('#startDate').val(),
                        "end_date": $('#endDate').val(),
                        "zone_id": $('#zone_id').val(),
                        "area_id": $('#area_id').val(),
                        "branch_id": $('#branch_id').val(),
                        "designation_id": $('#designation_id').val(),
                        "department_id": $('#department_id').val(),
                        "emp_gender": $('#emp_gender').val(),
                        "emp_code": $('#se_emp_code').val(),
                        "emp_status": $('#emp_status').val(),
                        "district_id": $('#district_id_index').val(),
                        "upazila_id": $('#upazila_id_index').val(),
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'emp_name',
                        orderable: false,
                    },
                    {
                        data: 'emp_code',
                        className: 'text-center',
                        orderable: true,
                    },
                    {
                        data: 'emp_gender',
                        // className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'designation',
                        orderable: false,
                    },
                    {
                        data: 'department',
                        orderable: false,
                    },
                    {
                        data: 'branch',
                        orderable: false,
                    },
                    {
                        data: 'phone_number',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'join_date',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'username',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'status',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none'
                    },

                ],
                'fnRowCallback': function(nRow, aData, Index) {
                    var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name,
                        aData
                        .action.action_link);
                    $('td:last', nRow).html(actionHTML);
                }

            });
        }

        function fnDelete(RowID) {
            fnAjaxDeleteReloadTable("{{ url()->current() }}/delete", RowID, "clsDataTable");
        }
        
        $('#district_id_index').select2({
            ajax: {
                url: "{{ url()->current() }}/getData",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                        context: 'searchDistrict'
                    };
                },
                processResults: function (data, params) {
                    console.log(data);
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: 'Search for a district',
            minimumInputLength: 2
        });


        $('#upazila_id_index').select2({
            ajax: {
                url: "{{ url()->current() }}/getData",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                        context: 'searchUpazila'
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: 'Search for a upazila',
            minimumInputLength: 2
        });


    </script>
@endsection
