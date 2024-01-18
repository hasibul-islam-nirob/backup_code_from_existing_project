@extends('Layouts.erp_master')
@section('content')

<?php
use App\Services\CommonService as Common;

$divisionData = Common::ViewTableOrder('gnl_divisions',
    [['is_delete', 0], ['is_active', 1]],
    ['id', 'division_name'],
    ['division_name', 'ASC']);
?>
<!-- Page -->

    <!-- Search Option Start -->
    <div class="row align-items-center pb-10 d-print-none">
        <div class="col-lg-2">
            <label class="input-title">Division</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="division_id" id="division_id">
                    <option value="">Select All</option>
                    @foreach ($divisionData as $row)
                    <option value="{{ $row->id }}">{{ $row->division_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        @include('elements.button.common_button', [
                        'search' => [
                            'action' => true,
                            'title' => 'search',
                            'id' => 'searchButton',
                            'exClass' => 'float-right'
                        ]
                    ]) 
    </div>
    <!-- Search Option End -->

    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable" >
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Name</th>
                        <th>Division</th>
                        <th style="width:15%;" class="text-center">Action</th>
                    </tr>
                </thead>
            
            </table>
        </div>
    </div>
<!-- End Page -->
<script>
    function ajaxDataLoad(divisionID = null){

        $('.clsDataTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            stateSave: true,
            stateDuration: 1800,
            // ordering: false,
            // lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
            "ajax":{
                     "url": "{{route('DistrictDatatable')}}",
                     "dataType": "json",
                     "type": "post",
                     "data":{
                         _token: "{{csrf_token()}}",
                         divisionID: divisionID,
                     }
                   },
            columns: [

                  { data: 'id', name: 'id', className: 'text-center',targets:[1], width: '5%' },
                  { data: 'district_name', name: 'district_name'},
                  { data: 'division_name', name: 'division_name', orderable: false },
                  {data: 'action', name: 'action', orderable: false, className: 'text-center', width: '15%'},

            ],
            'fnRowCallback': function(nRow, aData, Index) {
                var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name, aData.action.action_link);
                $('td:last', nRow).html(actionHTML);
            }

        });
    }

    $(document).ready( function () {
        ajaxDataLoad();
        $('#searchButton').click(function() {
            var divisionID = $('#division_id').val();

            ajaxDataLoad(divisionID);
        });
    });

    function fnDelete(RowID) {
        /**
         * para1 = link to delete without id
         * para 2 = ajax check link same for all
         * para 3 = id of deleting item
         * para 4 = matching column
         * para 5 = table 1
         * para 6 = table 2
         * para 7 = table 3
         */

        fnDeleteCheck(
            "{{url('gnl/district/delete/')}}",
            "{{url('/ajaxDeleteCheck')}}",
            RowID,
            "{{base64_encode('district_id')}}",
            "",
            "{{base64_encode('gnl_upazilas')}}"
        );
    }
 </script>
@endsection
