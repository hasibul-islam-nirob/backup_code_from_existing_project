@php
    use App\Services\CommonService as Common;
@endphp

<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">

    <table class="table table-bordered sticky-table clsDataTable " id="tableID" style="font-size:70%;">
        <thead class="text-center sticky-head" id="header_col">

            <tr>
                <th rowspan="3" width="2%" colspan="">SL</th>
                <th rowspan="3" width="10%" colspan="">Employee Name</th>
                <th rowspan="3" width="5%" colspan="">Desig</th>
                <th rowspan="3" width="5%" colspan="">Dept.</th>
                <th width="5%" rowspan="2" colspan="3">Probable Maximum Leave</th>
                <th width="73%" colspan="70">Leave Consumed</th>
            </tr>
            <tr>
                @php
                    $empYearlyAttendance = $employeeData[0]->yearlyAttendance;
                @endphp

                @foreach ($empYearlyAttendance as $monthName => $values)
                    <th width="4%" colspan="4">{{ $monthName }}</th>
                @endforeach

                <th width="4%" colspan="4">Total Consumed Leave</th>
                {{-- <th width="2%">Consider</th> --}}
                <th width="4%" colspan="3">Remaining Maximum Leave</th>
                <th width="2%" rowspan="20">Total</th>
            </tr>

            <tr>
                <th rowspan="16">AL</th>
                <th rowspan="16">SL</th>
                <th rowspan="16">CL</th>
            </tr>

            @php
                $colSpan = count($empYearlyAttendance) + 3;
            @endphp
            @for ($i = 0; $i < count($empYearlyAttendance); $i++)
                <tr>
                    <th rowspan="{{ $colSpan }}">LP</th>
                    <th rowspan="{{ $colSpan }}">AL</th>
                    <th rowspan="{{ $colSpan }}">SL</th>
                    <th rowspan="{{ $colSpan }}">CL</th>
                </tr>
                @php
                    $colSpan--;
                @endphp
            @endfor

            <tr>
                <th rowspan="{{ $colSpan }}">LP</th>
                <th rowspan="{{ $colSpan }}">AL</th>
                <th rowspan="{{ $colSpan }}">SL</th>
                <th rowspan="{{ $colSpan }}">CL</th>
            </tr>
            {{-- <tr>
                <th rowspan="{{ $colSpan }}">LP</th>
            </tr> --}}
            <tr>
                <th rowspan="{{ $colSpan }}">AL</th>
                <th rowspan="{{ $colSpan }}">SL</th>
                <th rowspan="{{ $colSpan }}">CL</th>
            </tr>
        </thead>


        <tbody>

            @php

                $lp_accept = !empty($LpEffectArray->lp_accept) ? $LpEffectArray->lp_accept : 0;
                $acction_for_lp = !empty($LpEffectArray->acction_for_lp) ? $LpEffectArray->acction_for_lp : 0;

                $i = 1;
                
            @endphp

            @foreach ($employeeData as $employee)
                @php
                    $considarLP = 0;
                    $constAL = $constSL = $constCL = 0;
                    
                    $empYearlyAttendance = isset($employee->yearlyAttendance) ? $employee->yearlyAttendance : [];

                    
                    $monthRemArr = array();
                    foreach ($empYearlyAttendance as $monKey => $monValue) {
                        array_push($monthRemArr, $monKey);
                    }
                    // dd($employeeData, $employee->join_date, $empYearlyAttendance);
                    // dd($fiscalYearData, $fiscalYearData->fy_start_date , $fiscalYearData->fy_end_date);

                    $fStartDate = $fiscalYearData->fy_start_date;
                    $fEndDate = $fiscalYearData->fy_end_date;
                    $empJoinDate = $employee->join_date;

                    // dd($fiscalYearData, $empYearlyAttendance);
                    $leaveDiv = $monthCount = 1;
                    $percentage = 0;
                    if( $empJoinDate > $fStartDate && $empJoinDate < $fEndDate ){

                        for ($i=1; $i <= count($monthRemArr); $i++) { 
                            
                            if ($monthRemArr[$i] == date("F", strtotime($empJoinDate))) {

                                $monthCount =  count($monthRemArr) - $i - 1;
                                // dd($monthCount, $employee, $empYearlyAttendance);
                                break;
                            }
                            
                        }
                        $leaveDiv = 2;

                    }elseif( count($empYearlyAttendance) <= 6 ){
                        $leaveDiv = $monthCount = 2;
                    }
                @endphp

                <tr>
                    <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">{{ $i++ }}</td>
                    <td style="background-color:rgb(230, 153, 76, 0.5)"> {{ $employee->emp_name }} </td>
                    <td style="background-color:rgb(230, 153, 76, 0.5)"> {{ $employee->emp_designation }} </td>
                    <td style="background-color:rgb(230, 153, 76, 0.5)"> {{ $employee->emp_department }} </td>


                    @foreach ($leaveCatAndDataArr as $key => $val)
                        @if (isset($val['AL']))

                            @php
                                $constAL = $val['AL'];

                                $monthCount = ($monthCount < 1) ? 1 : $monthCount;

                                if( $empJoinDate > $fStartDate && $empJoinDate < $fEndDate ){
                                    $percentage = ($monthCount / $constAL * 100) / $monthCount;

                                    $constAL = intval($percentage);
                                }else{

                                    $constAL = $constAL / $monthCount;
                                }

                                

                                // $al = $constAL;
                            @endphp
                            <td style="text-align:center;"> {{ $constAL }} </td>
                            
                        @elseif (isset($val['CL']))

                            @php
                                $constCL = $val['CL'];
                                $constCL = $constCL / $leaveDiv;
                            @endphp
                            <td style="text-align:center;"> {{ $constCL }} </td>
                            
                        @elseif (isset($val['SL']))

                            @php
                                $constSL = $val['SL'];
                                $constSL = $constSL / $leaveDiv;
                            @endphp
                            <td style="text-align:center;"> {{ $constSL }} </td>
                            
                        @endif
                    @endforeach


                    @php
                        $monthOddEven = 0;
                        $bgColor = '';
                        $totalAL = $totalSL = $totalCL = $totalLP = 0;
                    @endphp

                    @foreach ($empYearlyAttendance as $monthName => $monthWiseValue)
                        @php
                            $monthlyLP = $monthlyAL = $monthlySL = $monthlyCL = 0;
                            
                            if ($monthOddEven % 2 == 0) {
                                $bgColor = 'background-color:rgba(181, 181, 243, 0.6);';
                            } else {
                                $bgColor = '';
                            }
                            
                            $monthOddEven++;
                        @endphp



                        @php
                            $monthlyAL = 0;
                            $monthlySL = 0;
                            $monthlyCL = 0;
                            $monthlyLP = 0;

                            if (array_search('LP', $empYearlyAttendance[$monthName], true)) {
                                $counts = array_count_values($empYearlyAttendance[$monthName]);
                                $monthlyLP = $counts['LP'];
                            }
                            if (array_search('AL', $empYearlyAttendance[$monthName], true)) {
                                $counts = array_count_values($empYearlyAttendance[$monthName]);
                                
                                $monthlyAL = $counts['AL'];
                            }
                            if (array_search('SL', $empYearlyAttendance[$monthName], true)) {
                                $counts = array_count_values($empYearlyAttendance[$monthName]);
                                $monthlySL = $counts['SL'];
                            }
                            if (array_search('CL', $empYearlyAttendance[$monthName], true)) {
                                $counts = array_count_values($empYearlyAttendance[$monthName]);
                                $monthlyCL = $counts['CL'];
                            }

                            $totalAL += $monthlyAL;
                            $totalSL += $monthlySL;
                            $totalCL += $monthlyCL;

                            $lwp = 0;
                            if($monthlyLP >= $lp_accept){
                                $lwp = $monthlyLP / $lp_accept;
                            }
                            $totalLP += intval($lwp);
                            
                        @endphp

                        <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($monthlyLP) }} </td>
                        <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($monthlyAL) }} </td>
                        <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($monthlySL) }} </td>
                        <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($monthlyCL) }} </td>
                    @endforeach

                    <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ Common::getDecimalValue($totalLP) }} 
                    </td>
                    <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ Common::getDecimalValue($totalAL) }} 
                    </td>
                    <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ Common::getDecimalValue($totalSL) }} 
                    </td>
                    <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ Common::getDecimalValue($totalCL) }} 
                    </td>

                    {{-- <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ Common::getDecimalValue($considarLP) }} 
                    </td> --}}


                        @php
                            
                            if($acction_for_lp == "AL"){
                                $remainAL = $constAL - ($totalAL + $totalLP);
                            }else{
                                $remainAL = $constAL;
                            }

                            // $remainAL = $constAL - ($totalAL + $totalLP);
                            $remainSL = $constSL - $totalSL;
                            $remainCL = $constCL - $totalCL;

                            $total = $remainAL + $remainSL + $remainCL;
                        @endphp

                    <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ ($remainAL < 0) ? '('.abs(Common::getDecimalValue($remainAL)).')' : Common::getDecimalValue($remainAL) }} 
                    </td>

                    <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ ($remainSL < 0) ? '('.abs(Common::getDecimalValue($remainSL)).')' : Common::getDecimalValue($remainSL) }} 
                    </td>

                    <td style="text-align:center; background-color:rgba(230, 153,76, 0.5)">
                        {{ ($remainCL < 0) ? '('.abs(Common::getDecimalValue($remainCL)).')' : Common::getDecimalValue($remainCL) }} 
                    </td>

                    <td style="text-align:center;"><strong> {{ Common::getDecimalValue($total) }} </strong></td>

                </tr>
            @endforeach


        </tbody>

    </table>

    <p style="font-style: oblique; font-size:60%;" class="d-print-text-dark">
        ** A = Absent,&nbsp; LWP = Leave Without Pay,&nbsp; AL = Annual Leave,
        &nbsp; SL = Sick Leave,&nbsp; CL = Casual Leave **
    </p>

    <p class="d-print-none d-none" style="font-style: oblique; font-size:60%;">
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
