
@extends('Layouts.erp_master')
@section('content')
<link href="{{ asset('assets/css-js/datetimepicker-master/jquery.datetimepicker.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/css-js/datetimepicker-master/build/jquery.datetimepicker.full.min.js') }}"></script>

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:2%;">SL</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        {{-- <th>Extendent Entry Time</th> --}}
                        <th>Late Accepted</th>
                        <th>Early Accepted</th>
                        <th>Over Time Cycle</th>

                        {{-- <th>Leave Accept Each Month</th>
                        <th>LP Accept Each Month</th>
                        <th>Acction For LP</th> --}}

                        <th>Effective Date</th>
                        <th style="width:10%;" class="text-center">Action</th>
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
                        "start_time": $('#start_time').val(),
                        "end_time": $('#end_time').val(),
                        "ext_start_time": $('#ext_start_time').val(),
                        "eff_date_start": $('#eff_date_start').val(),
                        "eff_date_end": $('#eff_date_end').val(),
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        // width: '5%'
                    },
                    {
                        data: 'start_time',
                        orderable: true,
                        className: 'text-center',
                    },
                    {
                        data: 'end_time',
                        orderable: true,
                        className: 'text-center',
                    },
                    // {
                    //     data: 'ext_start_time',
                    //     orderable: true,
                    //     className: 'text-center',
                    // },
                    {
                        data: 'late_accept_minute',
                        orderable: true,
                        className: 'text-center',
                    },
                    {
                        data: 'early_accept_minute',
                        orderable: true,
                        className: 'text-center',
                    },
                    {
                        data: 'ot_cycle_minute',
                        orderable: true,
                        className: 'text-center',
                    },

                    // {
                    //     data: 'leave_allow',
                    //     orderable: true,
                    //     className: 'text-center',
                    // },
                    // {
                    //     data: 'lp_accept',
                    //     orderable: true,
                    //     className: 'text-center',
                    // },
                    // {
                    //     data: 'acction_for_lp',
                    //     orderable: true,
                    //     className: 'text-center',
                    // },


                    {
                        data: 'eff_date_start',
                        orderable: true,
                        className: 'text-center',
                    },
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none'
                    },
                ],
                'fnRowCallback': function(nRow, aData, Index) {

                    var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action.action_name, aData.action.action_link, aData.id);
                    $('td:last', nRow).html(actionHTML);
                },
            });
        }
    </script>

@endsection
