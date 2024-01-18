@php
    use App\Services\CommonService as Common;

    $branchInfo = DB::table('gnl_branchs')
                    ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1], ['id', Common::getBranchId()]])
                    ->select('id', 'branch_name','branch_addr', 'branch_code', 'branch_phone')
                    ->first();

    $companyInfo = DB::table('gnl_companies')
                    ->where([['is_delete', 0], ['is_active', 1], ['id', Common::getCompanyId()]])
                    ->select('id', 'comp_name', 'comp_addr', 'comp_logo', 'bill_logo', 'logo_view_bill',
                        'logo_view_report', 'logo_report_width', 'logo_bill_width', 'logo_bill_width_pos', 'name_view_bill', 'name_view_report', 'br_add_view_bill')
                    ->first();
@endphp

<style type="text/css">
    .companyLogo {
        width:40px;
    }

    @media print{
        .companyLogo {
            width:40px;
        }
    }
</style>

<div class="row reportHeading ExportHeading">
    <div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12 d-flex justify-content-center">
        <table style="color:#000;">
            <tr>
                @if($companyInfo->logo_view_report == 1)
                    <td class="headerLogo p-0 m-0 {{ ($companyInfo->name_view_bill == 1) ? 'text-right' : 'text-center' }}">
                        @if(!empty($companyInfo->comp_logo) && file_exists($companyInfo->comp_logo))
                            <img class="companyLogo" src="{{ asset($companyInfo->comp_logo)}}"  width="{{ $companyInfo->logo_report_width > 0 ? $companyInfo->logo_report_width : '5'}}%">
                        @endif
                    </td>
                @endif

                @if($companyInfo->name_view_report == 1)
                    <td class="{{ ($companyInfo->logo_view_report == 1) ? '' : 'text-center' }}">
                        <p class="p-0 m-0 f-25 text-black" style="text-transform: uppercase; font-size: 14px; font-weight:bold;">
                            {{ $companyInfo->comp_name }}
                        </p>
                    </td>
                @endif
            </tr>

            <tr class="p-0 m-0 text-center">
                <td colspan="2" class="address text-black p-0 m-0" style="font-size: 12px; font-weight: bold;">

                    <span id="reportBranch">
                        @if(isset($branchName) && $branchName)
                            {{ $branchName }}
                        @else
                            {{ $branchInfo->branch_name . " [" . $branchInfo->branch_code . "]" }}
                        @endif
                    </span>
                </td>
            </tr>

            @if ($companyInfo->br_add_view_bill == 1)
                <tr class="p-0 m-0 text-center">
                    <td colspan="2" class="address text-black p-0 m-0" style="font-size: 10px;">
                        <span>
                            {{ $branchInfo->branch_addr }}
                        </span>
                    </td>
                </tr>
            @endif

            @if((isset($title) && $title) || (isset($reportTitle) && $reportTitle))
                <tr class="p-0 m-0 text-center">
                    <td colspan="2" class="text-black p-0 m-0" style="border:1px solid #000; padding:3px; text-align:center; font-size: 12px; font-weight: bold;">
                        <span  id="reportTitleDiv">
                            <span id="beforeTitle"></span> {{ $reportTitle }} <span id="afterTitle"></span>
                        </span>
                    </td>
                </tr>
            @endif

        </table>
    </div>
</div>
