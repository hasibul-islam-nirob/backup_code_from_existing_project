@php
    // $isTime = 'yes';
@endphp
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">

    @php
        // dd($employeeData);
    @endphp

    @foreach ($employeeData as $employee)

        @php
            $empYearlyAttendance = isset($employee->yearlyAttendance) ? $employee->yearlyAttendance : [];

        @endphp

        <table class="table table-bordered sticky-table clsDataTable" id="tableID" >
            <thead class="text-center " id="header_col">
                <tr>
                    @php
                        $dayColSpan = 0;
                        $TotalColSpan = 31;
                        $dNone = '';
                    @endphp
                    <th rowspan="2" width="2%">SL</th>
                    <th rowspan="2" width="8%">Month</th>
                    <th colspan="31" width="70%"> {{ $employee->emp_name }} &nbsp;- {{ $employee->emp_designation }} -&nbsp; {{ $employee->emp_department }}</th>

                    <th class="" colspan="8">Total</th>
                </tr>

                <tr>

                    @foreach ($const31DayForAllMonth as $key => $date)
                    <th> {{$date}} </th>
                    @endforeach 

                    <th width="2%">LP</th>
                    <th width="2%">LWP</th>
                    <th width="2%">AL</th>
                    <th width="2%">SL</th>
                    <th width="2%">CL</th>
                    <th width="2%" title="Total Leave">TL</th>
                </tr>
            </thead>

            <tbody>
                @php
                    
                    $i = 1;
                @endphp
                @foreach ($empYearlyAttendance as $monthName => $datesForAtt)
                <tr>
                    <td style="text-align:center;"> {{$i++}} </td>
                    <td> {{$monthName}} </td>

                    
                    @foreach ($const31DayForAllMonth as $key => $date)
                        
                        @foreach ($datesForAtt as $attKey => $val )

                            @if (date('d', strtotime($attKey)) == $date)

                                @php
                                    $bgColor = '';

                                    if($val == 'H'){
                                        $bgColor = 'background-color:#dddddd; color:rgb(226, 96, 96);font-weight:500; ';
                                    }
                                    if($val == 'LP'){
                                        $bgColor = 'background-color:rgb(240, 201, 74); color:rgb(226, 96, 96);font-weight:500; ';
                                    }
                                    if($val == 'AL' || $val == 'SL' || $val == 'CL'){
                                        $bgColor = 'background-color:rgba(165, 4, 138, 0.966); color:rgba(247, 240, 245, 0.966);font-weight:500; ';
                                    }
                                @endphp

                                <td style="{{$bgColor}} text-align:center;" > {{$val}} </td>
                            @endif
                            
                            
                        @endforeach

                        {{-- <td> {{$dataValue}} </td> --}}
                    @endforeach

                    

                    

                    <td style="text-align:center;">0</td>
                    <td style="text-align:center;">0</td>
                    <td style="text-align:center;">0</td>
                    <td style="text-align:center;">0</td>
                    <td style="text-align:center;">0</td>
                    <td style="text-align:center;">0</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="33"> <span style="margin-left: 80rem" >Total</span> </td>
                    <td style="text-align:center;" >2</td>
                    <td style="text-align:center;" >2</td>
                    <td style="text-align:center;" >2</td>
                    <td style="text-align:center;" >2</td>
                    <td style="text-align:center;" >2</td>
                    <td style="text-align:center;" >2</td>
                </tr>
                    


                {{-- <tr>
                    <td>2</td>
                    <td>Month Name 2</td>

                    @foreach ($const31DayForAllMonth as $key => $date)
                    <td> {{$date}} </td>
                    @endforeach

                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr> --}}
                
                

            </tbody>
        </table>
        
    @endforeach

    {{-- <table class="table table-bordered sticky-table clsDataTable" id="tableID" style="font-size:70%;">
        <thead class="text-center sticky-head" id="header_col">
            <tr>
                @php
                    $dayColSpan = 0;
                    $TotalColSpan = 31;
                    $dNone = '';
                    // if ($withHoliday == 'no') {
                    //     $dayColSpan = count($monthDates);
                    //     $dNone = 'd-none';
                    // } else {
                    //     $dayColSpan = count($monthDates);
                    // }
                @endphp
                <th rowspan="2" width="2%">SL</th>
                <th rowspan="2" width="8%">Month</th>
                <th colspan="31" width="70%">Days</th>

                <th class="" colspan="8">Total</th>
            </tr>

            <tr>

                @foreach ($const31DayForAllMonth as $key => $date)
                   <th> {{$date}} </th>
                @endforeach 

                <th width="2%">LP</th>
                <th width="2%">LWP</th>
                <th width="2%">AL</th>
                <th width="2%">SL</th>
                <th width="2%">CL</th>
                <th width="2%" title="Total Leave">TL</th>
            </tr>
        </thead>

        <tbody>
            @php
                $i = 0;
            @endphp
            

        </tbody>
    </table> --}}

    <p style="font-style: oblique; font-size:60%;" class="d-print-text-dark">
        ** A = Absent,&nbsp; LWP = Leave Without Pay,&nbsp; AL = Annual Leave,
        &nbsp; SL = Sick Leave,&nbsp; CL = Casual Leave **
    </p>

    <p class="d-print-none" style="font-style: oblique; font-size:60%;">
        ** &nbsp;
        <input type="color" value="#dddddd" readonly disabled style="width:25px;"> For Holiday, &nbsp;
        <input type="color" value="#ffffff" readonly disabled style="width:25px;"> For Present, &nbsp;
        <input type="color" value="#027502" readonly disabled style="width:25px;"> For Movement Present, &nbsp;
        <input type="color" value="#f0c94a" readonly disabled style="width:25px;"> For Late Present, &nbsp;
        <input type="color" value="#a5048a" readonly disabled style="width:25px;"> For Leave, &nbsp;
        <input type="color" value="#b91919" readonly disabled style="width:25px;"> For Absent, &nbsp;
        **
    </p>

    @include('../elements.signature.signatureSet', ['visible' => false])
</div>

<style>
    @media print {
        .d-print-text-dark {
            color: #000;
        }
    }
</style>






{{-- Every Month Value Looping Start --}}
@foreach ($const31DayForAllMonth as $key => $date)

@foreach ($attMonthValue as $attDate => $attResult)

    @php
        $tmpDate = date('Y-m', strtotime($attDate));
        $createFullDate = $tmpDate.'-'.$date;

        $countDayForEachMonth = count($attMonthValue);
    @endphp

    @if ($createFullDate == $attDate && $date == date('d', strtotime($attDate)))
        <td style="text-align:center;"> {{$attResult}} </td>
    @endif

    {{-- @if ($createFullDate == $attDate && $date == date('d', strtotime($attDate)))
        <td style="text-align:center;"> {{$attResult}} </td>
    @endif --}}

@endforeach
{{-- <td style="text-align:center;"> {{$date}} </td> --}}
@endforeach
{{-- Every Month Value Looping End --}}



<tbody>

    @php
        $febVal = 0;
        date('L',strtotime("$targetYear-01-01")) ? $febVal = 29 : $febVal = 28;
        $month_days = array("January" => 31,"February" => $febVal,"March" => 31,"April" => 30,"May" => 31,"June" => 30,"July" => 31,"August" => 31,"September" => 30,"October" => 31,"November" => 30,"December" => 31);
    @endphp
    
    @foreach ($employeeData as $employee)
        <tr>
            <tr style="background: #cac8c8;">
                <td colspan="39">
                    &nbsp;&nbsp; <strong> {{$employee->emp_name}}</strong>&nbsp;&nbsp; - {{$employee->emp_designation}} &nbsp;&nbsp;- {{$employee->emp_department}}
                </td>
            </tr>
            {{-- Every Month Looping Start --}}
            @php
                $empYearlyAttendance = isset($employee->yearlyAttendance) ? $employee->yearlyAttendance : array();

                $i=1;
                $tLP = $tAL = $tSL = $tCL = $ttl = $tlwp = 0;
                $totalLP = $totalLWP = 0;
            @endphp

            @foreach ($empYearlyAttendance as $monthName => $attMonthValue )

                @php
                    
                    if(array_search("A", $empYearlyAttendance[$monthName] ,true) ){
                        $counts = array_count_values($empYearlyAttendance[$monthName]);
                        $totalLWP = $counts["A"];
                    }

                    if(array_search("LP", $empYearlyAttendance[$monthName] ,true) ){
                        $counts = array_count_values($empYearlyAttendance[$monthName]);
                        $totalLP = $counts["LP"];
                    }
                    
                @endphp
                
                <tr>
                    <td style="text-align:center;">{{$i++}}</td>
                    <td>{{$monthName}}</td>

                    {{-- Every Month Value Looping Start --}}
                    @foreach ($const31DayForAllMonth as $key => $date)
                        @foreach ($attMonthValue as $attDate => $attResult)

                            @php

                                $tmpDate = date('Y-m', strtotime($attDate));
                                $createFullDate = $tmpDate.'-'.$date;

                                $bgColor = '';

                                $resultValue = $attResult;

                                if($attResult == 'P'){
                                    $bgColor = 'font-weight:500; color:green;';
                                }

                                if($attResult == 'LP'){
                                    $bgColor = 'background-color:rgb(240, 201, 74); color:rgb(226, 96, 96);font-weight:500; ';
                                    $empYearlyAttendance[$monthName]['totalLP'] += 1;
                                }elseif($attResult == 'H'){
                                    $bgColor = 'background-color:#dddddd; color:rgb(226, 96, 96);font-weight:500; ';

                                }elseif($attResult == 'MP'){
                                    $resultValue = "P";
                                    $bgColor = 'background-color:#0B610B; font-weight:500; color:rgb(230, 245, 230);font-weight:500; ';
                                    
                                }elseif($attResult == 'AL' || $attResult == 'SL' || $attResult == 'CL'){
                                    $bgColor = 'background-color:rgba(165, 4, 138, 0.966); color:rgba(247, 240, 245, 0.966);font-weight:500; ';

                                }elseif($attResult == 'A' && $monthName == date('F', strtotime($createFullDate))){
                                    $bgColor = 'background-color:rgba(226, 96, 96); color:rgba(247, 240, 245, 0.966);font-weight:500; ';

                                }
                                

                            @endphp

                            @if ($createFullDate == $attDate && $date == date('d', strtotime($attDate)))
                                <td width="2%" style="{{$bgColor}} text-align:center;"> {{$resultValue}} </td>
                            @endif 

                        @endforeach
                    {{-- <td style="text-align:center;"> {{$date}} </td> --}}
                    @endforeach
                    {{-- Every Month Value Looping End --}}

                    @foreach ($month_days as $mont => $totalD)
                        @if ($monthName == $mont)
                            @php
                                $emptyRow = 31 - $totalD;
                                $totalLWP -= $emptyRow;
                                if ($totalLWP < 1) {
                                    $totalLWP = 0;
                                }
                            @endphp
                            @for ($er = 0; $er<$emptyRow; $er++)
                                <td></td>
                            @endfor
                        @endif
                    @endforeach

                    <td style="text-align:center;">{{$totalLP}}</td>
                    <td style="text-align:center;">{{ $totalLWP }}</td>
                    <td style="text-align:center;">{{$attMonthValue['totalAL']}}</td>
                    <td style="text-align:center;">{{$attMonthValue['totalSL']}}</td>
                    <td style="text-align:center;">{{$attMonthValue['totalCL']}}</td>
                    <td style="text-align:center;">{{ $attMonthValue['totalAL'] + $attMonthValue['totalSL'] + $attMonthValue['totalCL']}}</td>
                </tr> 

                @php
                    $tlwp += $totalLWP;
                    $tLP += $totalLP;
                    $totalLWP = $totalLP = 0;
                    $tAL += $attMonthValue['totalAL'];
                    $tSL += $attMonthValue['totalSL'];
                    $tCL += $attMonthValue['totalCL'];
                    $ttl = $tAL + $tSL + $tCL;
                @endphp
            @endforeach
            
            {{-- Every Month Looping End --}}

            <tr>
                <td colspan="33" > <strong style="margin-left: 85rem" >Sub Total</strong></td>
                <td style="text-align:center;"><strong> {{$tLP}} </strong></td>
                <td style="text-align:center;"><strong> {{$tlwp}}</strong></td>
                <td style="text-align:center;"><strong> {{$tAL}} </strong></td>
                <td style="text-align:center;"><strong> {{$tSL}} </strong></td>
                <td style="text-align:center;"><strong> {{$tCL}} </strong></td>
                <td style="text-align:center;"><strong> {{$ttl}} </strong></td>
            </tr>
            
        </tr>
    @endforeach
    

</tbody>