@php
// use DateTime;
use App\Model\GNL\Branch;
    $companyData = Branch::where('gnl_branchs.id',Auth::user()->branch_id)
            ->select('gnl_companies.comp_name','gnl_companies.comp_addr', 'gnl_companies.comp_logo')
            ->leftJoin('gnl_companies', 'gnl_companies.id', 'gnl_branchs.company_id')->first();
@endphp

@if (Auth::user()->branch_id != 1)
    @php
        $brnach= DB::table('gnl_branchs')
                ->where([['is_delete',0],['id', Auth::user()->branch_id]])
                ->first();
    @endphp
    <span style="display: none" id="branch_name">{{$brnach->branch_name}} ({{$brnach->branch_code}})</span>
@endif


<div id="headerContent" class="mb-2 ExportHeading">
    
    <div class="row">       
        <div class="col-lg-4 col-4">
            <table>
                <tbody class="reportLeftSide"></tbody>
            </table>
        </div>

        <div class="col-lg-4 col-4">
            <div class="row text-center  d-print-block">
                <div class="col-lg-12" style="color:#000;font-size: 13px">
                    @if(!empty($companyData->comp_logo) && file_exists($companyData->comp_logo))
                        <img src="{{ asset($companyData->comp_logo)}}" class="companyLogo" height="60px" width="60px">
                    @endif
                    <p style="margin:0; padding:0; text-transform: uppercase; font-weight:bold; font-size: 12px;">
                        {{ $companyData->comp_name }}
                    </p>
                    {{-- <strong style="font-size: 15px">{{ strtoupper($companyData->comp_name) }}</strong><br> --}}
                    
                    {{-- <span>{{$companyData->comp_addr}}</span><br> --}}
                    <p id="title"></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-4">
            <div class="row d-print-none text-right" data-html2canvas-ignore="true">
                <div class="col-lg-12">
                    <a href="javascript:void(0)" title="Print Document" onClick="window.print();"
                    style="background-color:transparent;border:none;" class="btnPrint" >
                        <i class="fa fa-print fa-lg" style="font-size:20px;"></i>
                    </a>

                    <a href="javascript:void(0)" title="Download PDF" onclick="getDownloadPDF();"style="background-color:transparent;border:none;">
                        {{-- class="mr-2" --}}
                        <i class="fa fa-file-pdf-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                    </a>

                    @php $title_excel = ""; @endphp

                    <a href="javascript:void(0)" title="Download Excel"" style="background-color:transparent;border:none;"
                        onclick="fnDownloadExcel('ExportHeading,ExportDiv', '{{ $title_excel }}_{{ (new Datetime())->format('d-m-Y') }}');">
                        <i class="fa fa-file-excel-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                    </a>
                </div>
            </div>

            <table style="margin-left: auto">
                <tbody class="reportRightSide">
                    
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    function loadHeaderData(){
        $('.reportRightSide').html("");
        $('.reportLeftSide').html("");

        if ($("#samityId") != '' && $("#samityId") != 'All') {
            excludedReportingElemnts = excludedReportingElemnts.concat(['zoneId', 'areaId']);
        }
        else if ($("#branchId") != '' && $("#branchId") != 'All') {
            excludedReportingElemnts = excludedReportingElemnts.concat(['zoneId', 'areaId']);
        }

        var searchingElements = {};
        $("#title").html($("#pageName").text());
        $("#filterFormId select option:selected").each(function(){
            if(jQuery.inArray($(this).closest('select').attr('id'), excludedReportingElemnts) !== -1){
                return;
            }
            var label = $(this).closest('div').find('label').text();
            if(label=="")
            {
                label = $(this).parents('div').eq(1).find('label').text();
            }
            
            searchingElements[label.toUpperCase()] = $(this).text();
            
        });
        
        $("#filterFormId input:text").each(function(){
            // if ($(this).attr('id') == 'date' || $(this).attr('id') == 'startDate' || $(this).attr('id') == 'endDate') {
            //     return;
            // }
            if(jQuery.inArray($(this).attr('id'), excludedReportingElemnts) !== -1){
                return;
            }

            var label = $(this).closest('div').find('label').text();
            if(label=="")
             {
                 label = $(this).parents('div').eq(1).find('label').text();
             }
            
            searchingElements[label.toUpperCase()] = $(this).val();
        });

        if ($('#month').length == 1) {
            searchingElements['REPORTING MONTH'] = $('#month option:selected').text() + ', ' + $('#year').val();
        }
        else if ($('#date').length == 1) {
            searchingElements['REPORTING DATE'] = $('#date').val();
        }
        else{
            searchingElements['REPORTING DATE'] = $('#startDate').val() + ' to '+ $('#endDate').val();
        }

        searchingElements['PRINT AT'] = new Date().toLocaleString('es-CL', {'hour12':true});
        var count = Object.keys(searchingElements).length;
        var current = 0;
        $.each(searchingElements, function (label, value) {
            
            label = label.charAt(0)+label.slice(1).toLowerCase();
            
            if(Math.ceil(count/2)>current){
                $(".reportLeftSide").append('<tr><td><span style="color: black; font-size:13px" class="float-left"><span style="font-weight: bold;">'+ label +': </span><span>'+ value +'</span></span></td></tr>');
                current++;
            }
            else
            {
                $(".reportRightSide").append('<tr><td><span style="color: black; font-size:13px" class="float-right"><span style="font-weight: bold;">'+ label +': </span><span>'+ value +'</span></span></td></tr>');
            }
             
        });

        let html = '<tr><td style="visibility: hidden"> &nbsp; </td></tr>';
        //if logged in from branch -> need to show branch name
        @if (Auth::user()->branch_id != 1)
            let branch_name = $('#branch_name').html();
            html += `<tr><td><span style="color: black; font-size:13px"><span style="font-weight: bold;">BRANCH: </span><span> ${branch_name} </span></span></td></tr>`;
        @endif

        html += $('.reportLeftSide').html();
        $('.reportLeftSide').html(html);

        html = '<tr><td class="d-none d-print-block"> &nbsp; </td></tr>'+$('.reportRightSide').html();
        $('.reportRightSide').html(html);
        
    }
    $(document).ready(function(){
        loadHeaderData();
    });
    
</script>
