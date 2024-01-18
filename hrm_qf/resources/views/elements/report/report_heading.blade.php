
<?php
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
// dd(Request::all());
if(isset(Request::all()['branch_id'])){

    $BranchID = Request::all()['branch_id'];
}elseif(isset(Request::all()['branch_to'])){
    $BranchID = Request::all()['branch_to'];

}elseif(isset(Request::all()['branch_from'])){
    $BranchID = Request::all()['branch_from'];
}

if (!empty($BranchID) && $BranchID > 0) {
    $StartDate = $EndDate = Common::systemCurrentDate($BranchID);
} else {
    $BranchID = Common::getBranchId();
    $StartDate = $EndDate = Common::systemCurrentDate();
}


$branchInfo = Common::ViewTableFirst('gnl_branchs', [['is_delete', 0], ['is_active', 1], ['id', $BranchID]], ['id', 'branch_name','branch_addr', 'branch_code']);
$groupInfo = Common::ViewTableFirst('gnl_groups', [['is_delete', 0], ['is_active', 1]], ['id', 'group_name','group_addr']);
$companyInfo = Common::ViewTableFirst('gnl_companies', [['is_delete', 0], ['is_active', 1]], ['id', 'comp_name','comp_addr','comp_logo']);
$ledgerId      = (!empty(Request::input('ledger_id'))) ?  Request::input('ledger_id') : ((!empty(Request::input('ledger_cash'))) ?  Request::input('ledger_cash') : ( (!empty(Request::input('ledger_bank'))) ?  Request::input('ledger_bank') : null ));
// dd($branchInfo);

## This is for Collection Sheet Period
if(!empty(Request::input('month_year'))){
    $monthYear = Request::input('month_year');
    $StartDate = new DateTime($monthYear);
    $StartDate = ($StartDate->modify('first day of this month'))->format('d-m-Y');

    $EndDate = new DateTime($monthYear);
    $EndDate = ($EndDate->modify('last day of this month'))->format('d-m-Y');
}

if(!empty($ledgerId)){
    $ledgerData = DB::table('acc_account_ledger')->where([['is_delete',0], ['is_active',1],['id',$ledgerId]])->first();
}
else {
    $route = Route::current()->uri();
    if($route == "acc/report/cash_book"){
        $ledgerTitle = "All Cash";
    }
    else if($route == "acc/report/bank_book"){
        $ledgerTitle = "All Bank";
    }
}
?>
<style type="text/css">
    .main {  
        position: relative;
        width: 100%;
    }  
    .bottom { 
        position:absolute;                  
        bottom:0;  
    }
</style>

<div class="text-dark ExportHeading">
    {{-- <div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12" style="text-align:center;"> --}}
        <div class="row pb-2">
            <div class="main col-xl-4 col-lg-4 col-sm-4 col-md-4 col-4">
                <span class="bottom" style="font-size: 12px;color: #000">
                    <br>
                    <?php
                        $count = 1;
                        $max = 4;
                    ?>

                    @if(isset($customerDesig) && $customerDesig)
                        <?php $count++; ?>
                    @endif

                    @if(isset($ledgerHead) && $ledgerHead)
                        <?php $count++; ?>
                    @endif

                    @if(isset($projectName) && $projectName)
                        <?php $count++; ?>
                    @endif

                    @if(isset($projectTypeName) && $projectTypeName)
                        <?php $count++; ?>
                    @endif

                    <?php $count = $max - $count; ?>
                    @if($count > 0)
                    @for ($i = 0; $i <= $count; $i++)
                        <br>
                    @endfor
                    @endif

                    
                    @if(isset($companyInfo->comp_logo) && $companyInfo->comp_logo && file_exists($companyInfo->comp_logo))
                    <img src="{{ asset($companyInfo->comp_logo)}}" style="height: 65PX; width: 65PX;">
                    <br>
                    @endif

                    @if(isset($customerDesig) && $customerDesig)
                    <?php $count++; ?>
                    <span>
                        <strong><span id="designation">{{ (isset($designationName) && $designationName) ? $designationName : '' }}</span> Name:</strong> 
                        <span id="empName">{{ (isset($employeeName) && $employeeName) ? $employeeName : '' }}</span>
                    </span>
                    <br>
                    @endif

                    @if(isset($ledgerHead) && $ledgerHead)
                    <?php $count++; ?>
                    <span>
                        <strong>Ledger Head: </strong>
                        @if(isset($ledgerTitle) && $ledgerTitle)
                        <span id="ledgerHead">{{ $ledgerTitle }}</span>
                        @else
                        <span id="ledgerHead">{{ isset($ledgerData) ? $ledgerData->name . ' (' . $ledgerData->code. ')' : '' }}</span>
                        @endif
                    </span>
                    <br>
                    @endif

                    @if(isset($projectName) && $projectName)
                    <?php $count++; ?>
                    <span>
                        <strong>Project Name: </strong>
                        <span id="projectName"></span>
                    </span>
                    <br>
                    @endif

                    @if(isset($projectTypeName) && $projectTypeName)
                    <?php $count++; ?>
                    <span>
                        <strong>Project Type: </strong>
                        <span id="projectTypeName"></span>
                    </span>
                    <br>
                    @endif

                    <strong>Period :</strong>
                    <span id="start_date_txt">{{ $StartDate }}</span>
                    <span id="text_to">to</span>
                    <span id="end_date_txt">{{ $EndDate }}</span>
                </span>
            </div>

            <div class="col-xl-4 col-lg-4 col-sm-4 col-md-4 col-4" style="text-align: center;">
                <strong class="text-uppercase">{{ $companyInfo->comp_name }}</strong><br>
                {{-- <span style="font-size: 11px; color:#000;">{{ $companyInfo->comp_addr }}</span><br> --}}
                <strong style="font-size: 13px">
                    {{-- <span id="reportFor">Branch (Code):</span>  --}}
                    <span id="reportBranch">
                        {{ $branchInfo->branch_name . " (" . $branchInfo->branch_code . ")" }}
                    </span>
                </strong>
                <br>
                
                @if(isset($reportTitle) && $reportTitle)
                <span style="color:#000; border:1px solid #000;padding:3px; text-align:center;" 
                id="reportTitle">
                    <b>
                        <span id="beforeTitle"></span>
                        {{ $reportTitle }} 
                        <span id="afterTitle"></span>
                    </b>
                </span>
                @else
                <span style="color:#000; text-align:center;" id="reportTitleDiv">
                    <b>
                        <span id="beforeTitle"></span>
                        {{ $title }} 
                        <span id="afterTitle"></span>
                    </b>
                </span>
                @endif
            </div>

            <div class="col-xl-4 col-lg-4 col-sm-4 col-md-4 col-4" style="text-align: right;">
                <span class="d-print-none">

                    @if(isset($incompleteBranch) && $incompleteBranch)

                    <a href="javascript:void(0)" title="Branch List for Incomplete Data" onClick="incompleteBranchList();"
                    style="background-color:transparent;border:none;" class="btnIncompleList">
                        <i class="fa fa-file-text-o fa-lg" style="font-size:20px; margin-right: 5px;"></i>
                    </a>

                    @endif

                    <a href="javascript:void(0)" title="Print Document" onClick="window.print();"
                    style="background-color:transparent;border:none;" class="btnPrint" >
                        <i class="fa fa-print fa-lg" style="font-size:20px;"></i>
                    </a>

                    <a href="javascript:void(0)" title="Download PDF" style="background-color:transparent;border:none;"
                    onclick="getDownloadPDF();">
                        <i class="fa fa-file-pdf-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                    </a>

                    <a href="javascript:void(0)" title="Download Excel" style="background-color:transparent;border:none;"
                        onclick="fnDownloadExcel('ExportHeading,ExportDiv', '{{ $title_excel }}_{{ (new Datetime())->format('d-m-Y') }}');">
                        <i class="fa fa-file-excel-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                    </a>
                </span>
                <br><br>
                @if(isset($totalCustomer) && $totalCustomer)
                    <span  style="font-size: 12px;color: #000"><strong>Total Customer:</strong> <span id="totalRowDiv">0</span><span><br>
                @else 
                    <br>
                @endif
                <span style="color:#000;font-size:12px;"><b>Printed Date:</b> {{ (new Datetime())->format('d-m-Y') }}</span>
            </div>
         
            
        </div>
    {{-- </div> --}}
</div>