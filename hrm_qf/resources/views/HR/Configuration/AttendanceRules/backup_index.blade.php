@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\RoleService as Role;
?>

<!-- Page -->
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable" data-plugin="dataTable">
                <thead class="text-center">
                    <tr>
                        <th style="width:3%;">SL</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Extended Start Time</th>
                        <th style="width:15%;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($attendanceRules as $Row)
                        <tr class="text-center">
                            <td>{{ ++$i }}</td>
                            <td>{{ (new DateTime($Row->start_time))->format('d-m-Y') }}</td>
                            <td>{{ (new DateTime($Row->end_time))->format('d-m-Y') }}</td>
                            <td>{{ (new DateTime($Row->ext_start_time))->format('d-m-Y') }}</td>
                            <td class="text-center">
                                <!-- Action Calling Role Wise -->
                                {!! Role::roleWisePermission($GlobalRole, $Row->id) !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
<!-- End Page -->
<script>
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
            "{{url('gnl/fiscal_year/delete/')}}",
            "{{url('/ajaxDeleteCheck')}}",
            RowID
        );
    }
</script>



@endsection
