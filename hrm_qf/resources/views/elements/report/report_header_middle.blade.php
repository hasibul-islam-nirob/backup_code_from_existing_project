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

$serviceCharge = (!empty(Request::input('withServiceCharge'))) ?  Request::input('withServiceCharge') : null;

// dd($serviceCharge);

@endphp

<style type="text/css">
    .reportHeading > div > p {
        margin:0; 
        padding:0;
        color: #000;
    }

    .companyLogo {
        width:20%;
    }

    @media print{
        .companyLogo {
            /* width:70px; */
            width:20%;
        }
    }
</style>
{{-- d-print-block ### if use this class header alignement broken in print page  --}}
<div class="reportHeading ExportHeading">
    <div class="headerTitle  text-center" style="color:#000;">

        <div class="row">
            <div class="col-xl-5 col-lg-5 col-md-5 col-sm-5 col-5 text-right" style="padding-right: 0">
                @if(!empty($companyInfo->comp_logo) && file_exists($companyInfo->comp_logo))
                    <img src="{{ asset($companyInfo->comp_logo)}}" class="companyLogo">
                @endif
            </div>

            <div class="col-xl-7 col-lg-7 col-md-7 col-sm-7 col-7" style="padding-left: 10px">
                <table>
                    <tr>
                        <td style="margin:0; padding-top:4px;text-transform: uppercase; font-weight:bold; font-size: 12px;">
                            {{ $companyInfo->comp_name }}
                        </td>
                    </tr>
                    @if(isset($companyAddShow) && $companyAddShow)
                    <tr>
                        <td style="margin:0; padding:0; font-size: 11px;">{{ $companyInfo->comp_addr }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="margin:0; padding:0; font-size: 12px;">
                            <span id="reportBranch">
                                @if(isset($branchName) && $branchName)
                                    {{ $branchName }}
                                @else
                                {{ $branchInfo->branch_name . " [" . $branchInfo->branch_code . "]" }}
                                @endif
                            </span>
                        </td>
                    </tr>
                </table>
                
            </div>
        </div>

        @if(isset($reportTitle) && $reportTitle)
            <div class="row">
                <div class="col-md-12 text-center">
                    <span style="font-size: 12px;" id="reportTitleDiv">
                        <span id="beforeTitle"></span> {{ $reportTitle }} <span id="afterTitle"></span>
                        @if($serviceCharge == 'yes')
                            <span> - With Service Charge</span>
                        @elseif($serviceCharge == 'no')
                            <span> - Without Service Charge</span>
                        @endif
                    </span>
                </div>
            </div>
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
            @if(isset($printDate) && $printDate)
                <br style="display: none"><br style="display: none" class="d-print-block">
                <div class="headerRight" style="text-align: right;">
                    <span class="" style="color:#000;font-size:8px;"><b>Printed Date:</b> {{ (new Datetime())->format('d-m-Y') }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- @if(isset($printDate) && $printDate)
        <div class="col-xl-2 col-lg-2 col-sm-2 col-md-2 col-2 headerRight" style="text-align: right;">
            <span class="" style="color:#000;font-size:8px;"><b>Printed Date:</b> {{ (new Datetime())->format('d-m-Y') }}</span>
        </div>
    @endif --}}
</div>
