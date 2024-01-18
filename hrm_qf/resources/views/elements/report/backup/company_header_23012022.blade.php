@php
use App\Services\CommonService as Common;

$branchInfo = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_active', 1], ['id', Common::getBranchId()]])
                ->select('id', 'branch_name','branch_addr', 'branch_code')
                ->first();

$companyInfo = DB::table('gnl_companies')
                ->where([['is_delete', 0], ['is_active', 1], ['id', Common::getCompanyId()]])
                ->select('id', 'comp_name','comp_addr','comp_logo')
                ->first();
@endphp

<style type="text/css">
    .reportHeading > div > p {
        margin:0; 
        padding:0;
        color: #000;
    }

    .companyLogo {
        width:50%;
    }

    @media print{
        .companyLogo {
            width:100px;
        }
    }
</style>
{{-- d-print-block ### if use this class header alignement broken in print page  --}}
<div class="row reportHeading ExportHeading">

    <div class="col-xl-2 col-lg-2 col-sm-2 col-md-2 col-2 headerLogo">
        @if(!empty($companyInfo->comp_logo) && file_exists($companyInfo->comp_logo))
        <img src="{{ asset($companyInfo->comp_logo)}}" class="companyLogo">
        @endif
    </div>

    <div class="col-xl-8 col-lg-8 col-sm-8 col-md-8 col-8 headerTitle" style="text-align: center; color:#000;">

        <p style="margin:0; padding:0; text-transform: uppercase; font-weight:bold; font-size: 15px;">{{ $companyInfo->comp_name }}</p>
        <p style="margin:0; padding:0; font-size: 11px;">{{ $companyInfo->comp_addr }}</p>

        <p style="margin:0; padding:0 0 5px 0; font-size: 14px; font-weight:bold;">
            <span id="reportBranch">
                @if(isset($branchName) && $branchName)
                    {{ $branchName }}
                @else
                {{ $branchInfo->branch_name . " (" . $branchInfo->branch_code . ")" }}
                @endif
            </span>
        </p>

        @if(isset($reportTitle) && $reportTitle)
        <span style="border:1px solid #000; padding:3px; text-align:center; font-weight:bold; font-size: 14px;" id="reportTitleDiv">
            <span id="beforeTitle"></span> {{ $reportTitle }} <span id="afterTitle"></span>
        </span>
        @endif
    </div>

    @if(isset($printIcon) && $printIcon)
        <div class="col-xl-2 col-lg-2 col-sm-2 col-md-2 col-2" style="text-align: right;">
            <span class="d-print-none">

                @if(isset($incompleteBranch) && $incompleteBranch)
                <a href="javascript:void(0)" title="Branch List for Incomplete Data" onClick="incompleteBranchList();"
                class="btnIncompleList">
                    <i class="fa fa-file-text-o fa-lg" style="font-size:20px; margin-right: 5px;"></i>
                </a>
                @endif

                {{-- style="background-color:transparent;border:none;" --}}
                <a href="javascript:void(0)" title="Print Document" onClick="window.print();"
                class="btnPrint" >
                    <i class="fa fa-print fa-lg" style="font-size:20px;"></i>
                </a>

                <a href="javascript:void(0)" title="Download PDF" onclick="getDownloadPDF();">
                    <i class="fa fa-file-pdf-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                </a>

                <a href="javascript:void(0)" title="Download Excel" 
                    onclick="fnDownloadExcel('ExportHeading,ExportDiv', '{{ $title_excel }}_{{ (new Datetime())->format('d-m-Y') }}');">
                    <i class="fa fa-file-excel-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                </a>
            </span>
        </div>
    @endif

    @if(isset($printDate) && $printDate)
    <div class="col-xl-2 col-lg-2 col-sm-2 col-md-2 col-2 headerRight" style="text-align: right;">
        <span class="" style="color:#000;font-size:8px;"><b>Printed Date:</b> {{ (new Datetime())->format('d-m-Y') }}</span>
    </div>
    @endif
</div>
