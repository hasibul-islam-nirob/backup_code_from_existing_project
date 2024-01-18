@extends('Layouts.erp_master')
@section('content')


<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th colspan="4" >
                    Terms and Conditions Information
                </th>
            </tr>
        </thead>
        <tbody style="color: #000;">
            <tr>
                <td width="10%">Term Type</td>
                <td width="20%">{{ $termType->type_title }}</td>

                <td width="20%">Terms & Conditions</td>
                <td width="50%">{!! $TCData->tc_name !!}</td>
            </tr>

        </tbody>
    </table>
</div>

@endsection
