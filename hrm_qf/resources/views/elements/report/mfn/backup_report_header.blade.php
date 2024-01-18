@php
use App\Model\GNL\Branch;
    $companyData = Branch::where('gnl_branchs.id',Auth::user()->branch_id)
            ->select('gnl_companies.comp_name','gnl_companies.comp_addr')
            ->leftJoin('gnl_companies', 'gnl_companies.id', 'gnl_branchs.company_id')->first();
@endphp

@if (Auth::user()->branch_id != 1)
    @php
        $brnach= DB::table('gnl_branchs')
                ->where([['is_delete',0],['id', Auth::user()->branch_id]])
                ->first();
    @endphp
    <span style="display: none" id="branch_name">{{$brnach->branch_code}} - {{$brnach->branch_name}}</span>
@endif
<div id="headerContent">
    <div class="row text-center  d-print-block">
        <div class="col-lg-12" style="color:#000;">
            <strong>{{$companyData->comp_name}}</strong><br>
            
            <span>{{$companyData->comp_addr}}</span><br>
            <strong id="title"></strong><br>
        </div>
    </div>
    <div class="row d-print-none text-right" data-html2canvas-ignore="true">
        <div class="col-lg-12">
            <a href="javascript:void(0)" onClick="window.print();"
                style="background-color:transparent;border:none;" class="btnPrint mr-2">
                <i class="fa fa-print fa-lg" style="font-size:20px;"></i>
            </a>
            <a href="javascript:void(0)" style="background-color:transparent;border:none;"
                onclick="getPDF();">
                <i class="fa fa-file-pdf-o fa-lg" style="font-size:20px;"></i>
            </a>
            <a href="javascript:void(0)" style="background-color:transparent;border:none;"
                onclick="fnDownloadXLSX();">
                <i class="fa fa-file-excel-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                {{-- <i class="fa fa-file-pdf-o fa-lg" style="font-size:20px;"></i> --}}
            </a>
        </div>
    </div>
    <div class="row">       
    
        <div class="col-lg-12" style="font-size: 12px;">
            <span style="color: black; font-size:13px float: right;">
                <table style="border-collapse:separate;
                border-spacing:10px 10px;">
                    
                    <tbody class="reportRightSide">
                        
                        
                    </tbody>
                </table>
               
            </span>
    
            <table style="border-collapse:separate;
            border-spacing:10px 10px;">
                <tbody class="reportLeftSide">
                    
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

        // console.log(excludedReportingElemnts);
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
            if(Math.ceil(count/2)>current){
                $(".reportLeftSide").append('<tr><td><span style="color: black; font-size:13px" class="float-left"><span style="font-weight: bold;">'+ label +': </span><span>'+ value +'</span></span></td></tr>');
                current++;
            }
            else
            {
                $(".reportRightSide").append('<tr><td><span style="color: black; font-size:13px" class="float-right"><span style="font-weight: bold;">'+ label +': </span><span>'+ value +'</span></span></td></tr>');
            }
             
        });

        //if logged in from branch -> need to show branch name
        @if (Auth::user()->branch_id != 1)
            let branch_name = $('#branch_name').html();
            let html = `<tr><td><span style="color: black; font-size:13px"><span style="font-weight: bold;">BRANCH: </span><span> ${branch_name} </span></span></td></tr>`+$('.reportLeftSide').html();
            $('.reportLeftSide').html(html);
        @endif
        
    }
    $(document).ready(function(){
        loadHeaderData();
    });
    
</script>
