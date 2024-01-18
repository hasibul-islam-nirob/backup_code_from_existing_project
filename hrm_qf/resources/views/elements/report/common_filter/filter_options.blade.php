@php
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use App\Services\HtmlService as HTML;

$StartDate = Common::systemCurrentDate();
$EndDate = Common::systemCurrentDate();


$branchArr = array();
$branchID = isset(Request::all()['branch_id']) ? Request::all()['branch_id'] : null;

if (!empty($branchID) && $branchID > 0) {
    $branchArr[] = $branchID;

    $StartDate = $EndDate = Common::systemCurrentDate($branchID);
    $branchOpenDate = Common::getBranchSoftwareStartDate($branchID);
} else {
    $StartDate = $EndDate = Common::systemCurrentDate();
    // $EndDate = Common::systemCurrentDate();
    $branchOpenDate = Common::getBranchSoftwareStartDate();
}
@endphp

<div class="w-full d-print-none">
    <div class="panel filterDiv">

        <div class="panel-heading">
            <h3 class="panel-title"></h3>
            <div class="panel-actions">
                <a class="panel-action icon wb-minus" data-toggle="panel-collapse" aria-expanded="true"
                    aria-hidden="true"></a>
            </div>
        </div>

        <div class="panel-body panel-search">

            <div class="row align-items-center pb-10">
                @foreach ($elements as $key => $element)

                    @if($element['type'] == 'select')
                        @include('elements.report.common_filter.filtering_components.selectBox',['element'=>$element, 'key' => $key])
                    @elseif($element['type'] == 'text')
                        @include('elements.report.common_filter.filtering_components.textBox',['element'=>$element])
                    @elseif($element['type'] == 'startDate')
                        @include('elements.report.common_filter.filtering_components.startDate',['element'=>$element])
                    @elseif($element['type'] == 'endDate')
                        @include('elements.report.common_filter.filtering_components.endDate',['element'=>$element])
                    @elseif($element['type'] == 'year')
                        @include('elements.report.common_filter.filtering_components.year',['element'=>$element])
                    @elseif($element['type'] == 'month')
                        @include('elements.report.common_filter.filtering_components.month',['element'=>$element])
                    @elseif($element['type'] == 'monthYear')
                        @include('elements.report.common_filter.filtering_components.monthYear',['element'=>$element])
                    @elseif($element['type'] == 'fromMonthYear')
                        @include('elements.report.common_filter.filtering_components.fromMonthYear',['element'=>$element])
                    @elseif($element['type'] == 'toMonthYear')
                        @include('elements.report.common_filter.filtering_components.toMonthYear',['element'=>$element])
                    @elseif($element['type'] == 'status')
                        @include('elements.report.common_filter.filtering_components.status',['element'=>$element])

                    @elseif($element['type'] == 'searchBy')
                        @include('elements.report.common_filter.filtering_components.searchBy',['element'=>$element])
                    @elseif($element['type'] == 'dateNotRange')
                        @include('elements.report.common_filter.filtering_components.startDate',['element'=>$element])
                    @else

                    {{-- if type not found do nothing --}}

                    @endif
                @endforeach

                <div class="col-lg-2 mt-1 ml-auto">
                    <button type='submit' class="btn btn-primary btn-round text-uppercase float-right mt-4" id="searchButton"
                        style="font-size:16px;">
                        @if (strpos(Request::path(), 'reports') == true)
                            Show
                        @else
                            <i class="fa fa-search" aria-hidden="true"></i>&nbsp;
                            {{ isset($btnName) && $btnName ? $btnName : 'Search' }}
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('elements/report/common_filter/filter_option_script')
