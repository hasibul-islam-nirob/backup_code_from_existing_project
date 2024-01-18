@extends('Layouts.erp_master')
@section('content')


    <!-- Search Option Start -->
    @include('elements.common_filter_options', [
        'branch' => true,
         'textField' => [
            'field_text' => 'Feedback Code',
           'field_id' => 'se_f_code',
         'field_name' => 'se_f_code',
         'field_value' => null
         ],
        'applicationStatus' => true,
    ])
    <!-- Search Option End -->

    {{-- Datatable --}}
    <div class="row">
        
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Feedback Title</th>
                        <th>Feedback Code</th>
                        <th>Date</th>
                        <th>Status</th>
                        @if(Auth::user()->branch_id == 1)
                            <th>Button</th>
                        @endif
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
            $('.ajaxRequest').show();
            $('.httpRequest').hide(); //Hide new entry button
            ajaxDataLoad();
        });

        $('#searchFieldBtn').click(function(){
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
                        "date": $('date').val(),
                        "branch_id": $('#branch_id').val(),
                        "appl_code": $('#se_f_code').val(),
                        "appl_status": $('#appl_status').val(),
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'f_title',
                        orderable: true,
                    },
                    {
                        data: 'f_code',
                        orderable: true,
                    },
                    // {
                    //     data: 'f_description',
                    //     orderable: true,
                    // },
                    {
                        data: 'date',
                        orderable: true,
                        className: 'text-center'
                    },
                    // {
                    //     data: 'branch',
                    //     orderable: true,
                    // },
                   
                    {
                        data: 'status',
                        orderable: true,
                        className: 'text-center'
                    },
                    @if(Auth::user()-> branch_id == 1)
                    {  
                            data: 'button',
                            orderable: false,
                            className: 'text-center d-print-none'
                       
                    },
                    @endif
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none'
                    },
                ],
                'fnRowCallback': function(nRow, aData, Index) {

                    var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action
                        .action_name, aData.action.action_link, aData.id);
                    $('td:last', nRow).html(actionHTML);
                },
            });
        }

        $(document).on('click', '.approve-reject-btn', function(){
            var formData = new FormData();
            formData.append('id', $(this).attr('data-id'));
            
            if($(this).attr('data-btn') == 'approve') {
                formData.append('status',1);
            }
            else {
                formData.append('status', 0);
            }

            callApi("{{url()->current()}}/updateStatus", 'post', formData,
                function(response, textStatus, xhr) {
                    showApiResponse(xhr.status, '');
                    ajaxDataLoad();
                },
                function(response) {
                    showApiResponse(response.status, JSON.parse(response.responseText).message);
                }
            )
        });

        $(document).on('click', '.complete-btn', function(){
            var formData = new FormData();
            formData.append('id', $(this).attr('data-id'));

            callApi("{{url()->current()}}/updateAction", 'post', formData,
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                ajaxDataLoad();
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            })
        });
    </script>
@endsection
