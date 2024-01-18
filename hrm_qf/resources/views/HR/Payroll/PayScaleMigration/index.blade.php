@extends('Layouts.erp_master')
@section('content')

    @php
        // use App\Services\CommonService as Common;

        // ## Convension mismatch thats why variable name change
        $elementArray = array();
        $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id'];
        $elementArray['department'] = ['label' => 'Department', 'type' => 'select', 'id' => 'department_id', 'name' => 'department_id', 'onload' => '1'];
        $elementArray['designation'] = ['label' => 'Designation', 'type' => 'select', 'id' => 'designation_id', 'name' => 'designation_id', 'onload' => '1'];
 
    @endphp
    @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Employee</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Branch</th>
                        <th>Rectuitment Type</th>
                        <th>Grade</th>
                        <th>Level</th>
                        <th>Step</th>
                        <th>Structure ID</th>
                        <th>Current Payscale</th>
                        <th>To Migration Payscale</th>
                        <th>Effective Date</th>
                        <th style="width: 10%" class="text-center">Action</th>
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
            // end hide toggle btn and panel border

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
                        "branch_id": $('#branch_id').val(),
                        "designation_id": $('#designation_id').val(),
                        "department_id": $('#department_id').val(),
                    }
                },
                columns: [
                    {data: 'id', className: 'text-center', orderable: false, width: '5%'},
                    {data: 'emp_name', orderable: false},
                    {data: 'designation', orderable: false},
                    {data: 'department', orderable: false},
                    {data: 'branch', orderable: false},

                    {data: 'recruitment',orderable: false},
                    {data: 'grade',className: 'text-center',orderable: false},
                    {data: 'level',className: 'text-center',orderable: false},
                    {data: 'step',className: 'text-center',orderable: false},
                    {data: 'salary_structure_id', className: 'text-center',orderable: false},
                    
                    {data: 'oldPayScale', orderable: false,  className: 'text-center'},
                    {data: 'newPayScale', orderable: false,  className: 'text-center'},
                    {data: 'effective_date', orderable: false,  className: 'text-center'},
                    
                    {data: 'action', orderable: false, className: 'text-center d-print-none'},
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