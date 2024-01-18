@extends('Layouts.erp_master')
@section('content')

    
    @php
        // use App\Services\CommonService as Common;

        // ## Convension mismatch thats why variable name change
        $elementArray = array();
        $elementArray['payscale'] = ['label' => 'Pay Scale', 'type' => 'select', 'id' => 'filter_payscale_id', 'name' => 'payscale_id', 'onload' => '1'];

        $elementArray['grade'] = ['label' => 'Grade', 'type' => 'select', 'id' => 'filter_grade', 'name' => 'grade', 'onload' => '1'];
        $elementArray['level'] = ['label' => 'Level', 'type' => 'select', 'id' => 'filter_level', 'name' => 'level', 'onload' => '1'];
        $elementArray['recruitment_type'] = ['label' => 'Recruitment Type', 'type' => 'select', 'id' => 'filter_recruitment_type_id', 'name' => 'recruitment_type_id', 'onload' => '1'];
 
    @endphp
    @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])


    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Grade</th>
                        <th>Level</th>
                        <th>Basic Salary</th>
                        <th>Pay Scale</th>
                        <th>Company</th>
                        <th width="35%" >Designations</th>
                        <th>Project</th>
                        <th>Recruitment Type</th>
                        <th>Status</th>
                        <th style="width:15%;" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- Datatable --}}

    <!-- End Page -->
    <script>
        $(document).ready(function() {

            // start hide toggle btn and panel border
            $('.panel-heading').hide();
            $('.filterDiv').css('box-shadow', 'none');
            // end hide toggle btn and panel border

            $('.ajaxRequest').show();
            $('.httpRequest').hide(); //Hide new entry button
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
                "ajax": {
                    "url": "{{ url()->current() }}",
                    "dataType": "json",
                    "type": "post",
                    "data": {
                        "designation_id": $('#filter_designation_id').val(),
                        "payscale_id": $('#filter_payscale_id').val(),
                        "grade": $('#filter_grade').val(),
                        "level": $('#filter_level').val(),
                        "recruitment_type_id": $('#filter_recruitment_type_id').val(),
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'grade',
                        orderable: false,
                    },
                    {
                        data: 'level',
                        orderable: false,
                    },
                    {
                        data: 'basic',
                        orderable: false,
                    },
                    {
                        data: 'pay_scale',
                        orderable: false,
                    },
                    {
                        data: 'company',
                        orderable: false,
                    },
                    {
                        data: 'designations',
                        orderable: false,
                    },
                    {
                        data: 'project',
                        orderable: false,
                    },
                    {
                        data: 'recruitment_type',
                        orderable: false,
                    },
                    {
                        data: 'status',
                        orderable: false,
                    },
                    
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none',
                        width: '10%'
                    },
                ],
                'fnRowCallback': function(nRow, aData, Index) {

                    var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action
                        .action_name, aData.action.action_link, aData.id);
                    $('td:last', nRow).html(actionHTML);
                },
            });
        }
    </script>
@endsection
