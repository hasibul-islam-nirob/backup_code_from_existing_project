@extends('Layouts.erp_master')
@section('content')

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th colspan="4" >
                    Employee Information
                </th>
            </tr>
        </thead>
        <tbody style="color: #000;">
            <tr>
                <td width="20%"><b>Employee Name</b></td>
                <td width="20%">{{ $employeeName }}</td>

                <td width="20%"><b>Employee Code</b></td>
                <td width="20%">{{ $employeeNo }}</td>
            </tr>
            <tr>
                <td width="20%"><b>Branch</b></td>
                <td width="20%">{{ $branchFrom }}</td>

                <td width="20%"><b>Terminate Date</b></td>
                <td width="20%">{{ $terminateDate }}</td>
            </tr>
            <tr>
                <td width="20%"><b>Is Approved?</b></td>
                <td width="20%"> {{ $isApproved }}</td>

                <td width="20%"><b>Approved By</b></td>
                <td width="20%">{{ $approvedBy }}</td>
            </tr>
            <tr>
                <td width="20%"><b>Entry By</b></td>
                <td width="20%"> {{ $createdBy }}</td>
            </tr>
        </tbody>
    </table>
</div>
@include('elements.button.common_button', [
                        'back' => true,
                        'print' => [
                            'action' => 'print',
                            'title' => 'Print',
                            'exClass' => 'float-right',
                            'jsEvent' => 'onclick= window.print()'
                        ]
                    ])
<!-- End Page -->

@endsection
