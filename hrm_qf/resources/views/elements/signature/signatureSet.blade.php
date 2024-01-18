<?php

use App\Services\CommonService as Common;
$QuerryData = Common::getSignatureSettings();
$generatedUser = Common::fnForEmployeeData([Auth::user()->emp_id]);

## formId 14 = signator  name show or not
$signatorShow =  (!empty(DB::table('gnl_company_config')->where([['company_id',Common::getCompanyId()],['form_id',14]])->first()))? 1 : 0;


?>

@if(isset($visible) && $visible)
<br><br><br><br>
<!-- <div class="" style="display: flex!important; justify-content: space-around!important;"> -->
<div class="divSignature" style="width: 100%;">
    <div class="d-flex justify-content-end justify-content-around">

        @if(count($QuerryData) > 0)
        <div class="p-2">
            <div class="card">
                <div class="card-body">
                    <p class="card-text" style="color: black;">_______________</p>
                    <h5 class="card-title" style="color: black; font-size: 15px;">Generated By</h5>

                    @if($signatorShow)
                    <p class="card-text" style="color: black;padding-bottom: 0px;margin-bottom: 0px;">
                        {{ (isset($generatedUser[Auth::user()->emp_id])) ?  $generatedUser[Auth::user()->emp_id] : Auth::user()->full_name }}
                    </p>
                    @endif

                    <p class="card-text" style="color: black;padding-bottom: 0px;margin-bottom: 0px;">
                    <small>
                        {{Common::getSignatureEmployee(null,null,Auth::user()->emp_id)}}
                    </small>
                    </p>
                </div>
            </div>
        </div>
        @endif

        @foreach ($QuerryData as $item)
        <div class="p-2">
            <div class="card">
                <div class="card-body">
                    <p class="card-text" style="color: black;">_______________</p>
                    <h5 class="card-title" style="color: black; font-size: 15px;">{{$item->title}}</h5>
                    @if($signatorShow)
                    <p class="card-text" style="color: black;padding-bottom: 0px;margin-bottom: 0px;">
                        {{(isset($item->signatorEmployeeId)? $item->employee->emp_name.' ('.$item->employee->emp_code.')' : Common::getSignatureEmployee(null,$item->signatorDesignationId))}}
                    </p>
                    @endif
                    <p class="card-text" style="color: black;padding-bottom: 0px;margin-bottom: 0px;"><small>{{$item->designation->name}}</small></p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif