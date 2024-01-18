@extends('Layouts.erp_master')
@section('content')
<?php 
use App\Services\HtmlService as HTML;
?>
<!-- Page -->
<div class="row">
    <div class="col-lg-9 offset-3 mb-2">
        <!-- Html View Load  -->
        {!! HTML::forCompanyFeild($EmployeeData->company_id,'disabled') !!}
    </div>
</div>

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
                <td width="20%">BRANCH NAME</td>
                <td width="20%">{{(!empty($EmployeeData->branch['branch_name']))? $EmployeeData->branch['branch_name']: ''}}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td width="20%">EMPLOYEE NAME</td>
                <td width="20%">{{$EmployeeData->emp_name}}</td>

                <td width="20%">EMPLOYEE CODE</td>
                <td width="20%">{{sprintf('%04d',$EmployeeData->emp_code)}}</td>
            </tr>
            <tr>
            <tr>
                <td width="20%">FATHER'S NAME </td>
                <td width="20%"> {{$empPersonalDetails->father_name_en}}</td>

                <td width="20%"> MOTHER'S NAME</td>
                <td width="20%">{{$empPersonalDetails->mother_name_en}}</td>
            </tr>
            <tr>
                <td width="20%">DATE OF BIRTH</td>
                <td width="20%">{{date('m-d-y', strtotime($empPersonalDetails->dob))}}</td>

                <td width="20%">Personal EMAIL</td>
                <td width="20%">{{$empPersonalDetails->email}}</td>
            </tr>
            <tr>
                <td width="20%">MOBILE</td>
                <td width="20%">{{$empPersonalDetails->mobile_no}}</td>

                <td width="20%">NATIONAL ID</td>
                <td width="20%">{{$empPersonalDetails->nid_no}}</td>
                
            </tr>
            <tr>
                <td width="20%">Passport</td>
                <td width="20%">{{$empPersonalDetails->passport_no}}</td>

                <td width="20%">Driving License</td>
                <td width="20%">{{$empPersonalDetails->driving_license_no}}</td>
                
            </tr>
            <tr>
                <td width="20%">Birth Certificate</td>
                <td width="20%">{{$empPersonalDetails->birth_certificate_no}}</td>

                <td width="20%">GENDER</td>
                <td width="20%">{{$EmployeeData->gender}}</td>
            </tr>
            <tr>
                <td width="20%">DESIGNATION</td>
                <td width="20%">{{$EmployeeData->designation['name']}}</td>
                <td width="20%">PRESENT ADDRESS</td>
                <td width="20%">{{$empPersonalDetails->pre_addr_street}}</td>
            </tr>
            <tr>
                <td width="20%">DEPARTMENT</td>
                <td width="20%">{{$EmployeeData->department['dept_name']}}</td>
                <td width="20%">PARMANENT ADDRESS</td>
                <td width="20%">{{$empPersonalDetails->par_addr_street}}</td>
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
