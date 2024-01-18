@php
    use App\Services\CommonService as CS;

    $startDate = CS::systemCurrentDate();
    $endDate = CS::systemCurrentDate();

    $headerElements = $elements;

    $printIcon = isset($printIcon) ? $printIcon : true;
    $reportingDateShow = isset($reportingDateShow) ? $reportingDateShow : true;
    $ignoreElements = isset($ignoreElements) ? $ignoreElements : array();

    foreach($ignoreElements as $key){
        if(isset($headerElements[$key])){
            unset($headerElements[$key]);
        }
    }

    $firstElement = array();
    if(count($headerElements) > 0){
        $firstElement = array_slice($headerElements, 0, 1, true);
        $elementsTemp = $headerElements;
    }

    // dd($headerElements, $firstElement);

@endphp

<style type="text/css">
    .filterOptionView > table {
        color: #000;
        font-size: 10px;
    }

    @media print{
        .filterOptionView {
            margin: 0!important;
        }

        .filterOptionView > div {
            margin: 2px;
            padding: 2px;
            color: #000;
            font-size: 10px;
        }
    }
</style>

<div class="row reportHeading ExportHeading">
    <div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12 d-flex justify-content-center">

        <table style="width:100%; color:#000; margin:0 padding: 0; font-size: 12px;">

            @if(count($firstElement) > 0 || isset($reportTitle) || $printIcon == true)
                <tr class="p-0 m-0">
                    <td colspan="1" class="text-left p-0 m-0" width="30%" style="vertical-align: bottom;">

                        @if(count($firstElement) > 0)
                            @foreach($firstElement as $fKey => $rowData)

                                @if(isset($rowData['label']))
                                    <span>{{ $rowData['label'] }}:</span>
                                @endif

                                @if(isset($rowData['id']))
                                    <span>
                                        <span id="{{ $rowData['id'] }}_rptxt">&nbsp;</span>
                                    </span>
                                @endif
                                @php unset($elementsTemp[$fKey]); @endphp
                            @endforeach
                        @else
                            <p class="p-0 m-0">
                                Printed Date: {{ CS::viewDateFormat(now()) }}
                            </p>
                        @endif

                    </td>

                    <td colspan="2" class="text-center p-0 m-0" width="40%">
                        <p class="p-0 m-0" style="font-size: 14px; font-weight:bold;">
                            <span id="beforeTitle"></span>
                            <span id="reportTitleDiv">
                                @if(isset($reportTitle))
                                    {{ $reportTitle }}
                                @endif
                            </span>
                            <span id="afterTitle"></span>
                        </p>
                    </td>

                    <td colspan="1" class="text-right p-0 m-0" width="30%">
                        @if($printIcon == true)
                            <p class="d-print-none p-0 m-0">

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

                                @php $title_excel = (isset($title_excel)) ? $title_excel : 'report'; @endphp
                                <a href="javascript:void(0)" title="Download Excel"
                                    onclick="fnDownloadExcel('ExportHeading,ExportDiv', '{{ $title_excel }}_{{ (new Datetime())->format('d-m-Y') }}');">
                                    <i class="fa fa-file-excel-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                                </a>

                            </p>
                        @endif
                    </td>
                </tr>
            @endif

            @php
                $leftElements = array();
                $rightElements = array();

                if(isset($elementsTemp) && count($elementsTemp) > 0){
                    $elementsTemp = array_values($elementsTemp);

                    $leftElementCount = (int) ceil(count($elementsTemp) / 2);

                    $leftElements = array_slice($elementsTemp, 0, $leftElementCount);
                    $rightElements = array_slice($elementsTemp, $leftElementCount);
                }

            @endphp

            @foreach($leftElements as $key => $element)
                <tr class="p-0 m-0">
                    <td colspan="2" class="text-left p-0 m-0"  width="50%">
                        <span>{{ $element['label'] }}:</span>
                        <span>
                            <span id="{{ $element['id'] }}_rptxt">&nbsp;</span>
                        </span>
                    </td>

                    @if(isset($rightElements[$key]))
                        <td colspan="2" class="text-right p-0 m-0"  width="50%">
                            <span>{{ $rightElements[$key]['label'] }}:</span>
                            <span>
                                <span id="{{ $rightElements[$key]['id'] }}_rptxt">&nbsp;</span>
                            </span>
                        </td>

                    @elseif (count($leftElements) !=  count($rightElements) && !isset($rightElements[$key]))

                        <td colspan="2" class="text-right p-0 m-0" width="30%">
                            {{-- @if(isset($printDate) && $printDate) --}}
                                <p class="p-0 m-0">
                                    Printed Date: {{ CS::viewDateFormat(now()) }}
                                </p>
                            {{-- @endif --}}
                        </td>
                    @endif
                </tr>
            @endforeach

            <tr class="p-0 m-0">
                <td colspan="3" class="text-left p-0 m-0" id="reporting_date" width="70%">
                    @if($reportingDateShow == true)
                    <p class="p-0 m-0">
                        Reporting Date:
                        <span id="start_date_txt">{{ CS::viewDateFormat($startDate) }}</span>
                        <span id="text_to">to </span>
                        <span id="end_date_txt">{{ CS::viewDateFormat($endDate) }}</span>
                    </p>
                    @endif
                </td>
                @if (count($leftElements) ==  count($rightElements) && count($firstElement) > 0)
                <td colspan="1" class="text-right p-0 m-0" width="30%">
                    <p class="p-0 m-0">
                        Printed Date: {{ CS::viewDateFormat(now()) }}
                    </p>
                </td>
                @endif

            </tr>

        </table>
    </div>
</div>
