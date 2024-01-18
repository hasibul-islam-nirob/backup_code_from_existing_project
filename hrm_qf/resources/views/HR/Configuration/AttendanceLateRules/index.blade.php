
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
                        <th  width="5%">SL</th>
                        <th  width="30%">Title</th>
                        <th  width="20%">Effective Date</th>
                        <th  width="15%" class="text-center">Action</th>
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
                    
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        className: 'text-center',
                    },
                    {
                        data: 'title',
                        orderable: false,
                    },
                    {
                        data: 'eff_date_start',
                        orderable: false,
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
