@php
    use App\Services\CommonService as Common;

    
@endphp

<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">

    <div class="d-print-none ">
        <span style="font-size:80%;"> RAL = EAL - (TAL + TLL)</span> //
        <span style="font-size:80%;"> RAL = ESL - TSL</span> //
        <span style="font-size:80%;"> RCL = ECL - TCL</span> //
        <span style="font-size:80%;"> RLWP = EAL - (TAL + TLL)</span> // 
        <span style="font-size:80%;"> RLWP = TLWP</span> 
    </div>

    <table class="table table-bordered sticky-table clsDataTable " id="tableID" style="font-size:70%;">
        <thead class="text-center sticky-head" id="header_col">

            <tr>
                @foreach ($getMonthDate as $monthName => $values)
                    @php
                        
                        // dd($getMonthDate, $monthName, $values, count($getMonthDate));

                        // if (count($getMonthDate) < 12) {
                        //     $totalRows = (count($getMonthDate) +1) * (count($leaveCategoryData) + 2);
                        //     $width = 73;
                        // }else{
                        //     $totalRows = (count($getMonthDate)  * (count($leaveCategoryData) + 2)) + (count($leaveCategoryData) + 2);
                        // }
                        $leaveCategoryDataForCount = $leaveCategoryData->where('short_form', '<>', 'LWP');
                        $categoryCount = count($leaveCategoryData) + 3;
                        $totalRows = ( (count($getMonthDate) + 1) * $categoryCount) - 1;
                    @endphp
                @endforeach
                <th rowspan="3" width="2%">SL</th>
                <th rowspan="3" width="10%">Employee Name</th>
                <th rowspan="3" width="5%">Desig</th>
                <th rowspan="3" width="5%">Dept.</th>
                <th colspan="{{$totalRows }}" >Leave Consumed</th>
                <th rowspan="2" colspan="{{count($leaveCategoryDataForCount) }}" width="5%">Eligible Leave (Yearly)</th>
                <th rowspan="2" colspan="{{( count($leaveCategoryData) ) }}" width="5%">Remaining Leave</th>
            </tr>
            <tr>
                @foreach ($getMonthDate as $monthName => $values)
                    <th colspan="{{count($leaveCategoryData) + 3}}">{{ $monthName }}</th>
                @endforeach

                <th  colspan="{{( count($leaveCategoryData) + 2) }}">Total</th>
            </tr>

            <tr>
                @for ($i = 1; $i <= count($getMonthDate); $i++)
                    @foreach ($leaveCategoryData as $leaveData)
                        <th>{{ $leaveData->short_form }}</th>
                    @endforeach

                    @if($i <= (count($getMonthDate)+1))
                        @if ($i <= (count($getMonthDate)))
                        <th>LP</th>
                        @endif
                        <th>LL</th>
                        <th>Adj</th>
                    @endif
                @endfor

                {{-- ## Total --}}
                @foreach ($leaveCategoryData as $leaveData)
                    <th> <strong style="color: #000">T{{ $leaveData->short_form }}</strong> </th>
                @endforeach
                <th> <strong style="color: #000">TLL</strong> </th>
                <th> <strong style="color: #000">TAdj</strong> </th>

                {{-- ## Eligible --}}
                @foreach ($leaveCategoryData as $leaveData)

                    @if ($leaveData->short_form !== 'LWP')
                    <th> <strong style="color: #000">E{{ $leaveData->short_form }}</strong> </th>
                    @endif
                @endforeach

                {{-- ## Remaining --}}
                @foreach ($leaveCategoryData as $leaveData)
                    <th> <strong style="color: #000">R{{ $leaveData->short_form }}</strong> </th>
                @endforeach
            </tr>
        </thead>


        <tbody>
            @php

                $lp_accept = 1;
                $acction_for_lp = 1;
                $i = 1;
            @endphp

            @foreach ($employeeData as $employee)
                @php
                    // dd($employee);
                    if (in_array($employee->designation_id, $leave_bypass_arr)){
                        continue;
                    }
                    // dd($employeeData);
                    $empResignDate = $employee->emp_resign_date;
                    $empJoinDate = $employee->join_date;

                    $totalLP = $totalLWP = $totalAL = $totalSL = $totalML = $totalPL = $totalCL = $totalLL = 0;
                    $assignedAL = isset($allocatedLeave['AL']) ? $allocatedLeave["AL"] : 0;
                    $assignedCL = isset($allocatedLeave['CL']) ? $allocatedLeave["CL"] : 0;
                    $assignedSL = isset($allocatedLeave['SL']) ? $allocatedLeave["SL"] : 0;
                    $assignedML = isset($allocatedLeave['ML']) ? $allocatedLeave["ML"] : 0;
                    $assignedPL = isset($allocatedLeave['PL']) ? $allocatedLeave["PL"] : 0;

                    $empMonthCounter = 0;

                    $attendance = isset($employee->attendance) ? $employee->attendance : [];
                @endphp

                <tr>
                    <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">{{ $i++ }}</td>
                    <td style="background-color:rgb(230, 153, 76, 0.5)"> {{ $employee->emp_name }} </td>
                    <td style="background-color:rgb(230, 153, 76, 0.5)"> {{ $employee->emp_designation }} </td>
                    <td style="background-color:rgb(230, 153, 76, 0.5)"> {{ $employee->emp_department }} </td>

                    @php
                        $monthOddEven = 0;
                        $bgColor = '';
                        
                        $totalLP  = $attendance['total_consume']['LP'];
                        $totalLL  = $attendance['total_consume']['LL'];
                        $totalLWP = $attendance['total_consume']['LWP'];
                        $totalAdj = $divLWP =0;
                    @endphp

                    @foreach ($getMonthDate as $monthName => $monthWiseValue)
                        @php

                            $leaveArray = isset($attendance[$monthName]['leave']) ? $attendance[$monthName]['leave'] : [];

                            $resignMonth = date('F', strtotime($empResignDate));
                            // dd($empResignDate, $getMonthDate, $position, $monthWiseValue);
                            // if ($empResignDate != null) {
                            //     // dd($empResignDate);
                            //     // continue;
                            // }


                            ##========================================================
                            if (isset($attendance[$monthName])) {
                                $empMonthCounter ++;
                            }
                            ##========================================================

                            $ll = 0;
                            $lp = $lwp = $al = $sl = $cl = $pl = $adj = 0;
                            $monthlyLP = $monthlyAL = $monthlySL = $monthlyCL = 0;
                            
                            if ($monthOddEven % 2 == 1) {
                                $bgColor = 'background-color:rgba(181, 181, 243, 0.6);';
                            } else {
                                $bgColor = '';
                            }
                            
                            $monthOddEven++;

                        @endphp

                        @foreach ($attendance as $keyMonth => $value)
                            @php
                                if ($monthName == $keyMonth) {

                                    $lp = isset($value['LP']) ? $value['LP'] : 0;
                                    $ll = isset($value['LL']) ? $value['LL'] : 0;
                                    $lwp = isset($value['LWP']) ? $value['LWP'] : 0;
                                    $adj = isset($value['Adj']) ? $value['Adj'] : 0;
                                    $adj_for = isset($value['adj_for']) ? $value['adj_for'] : 0;

                                    if($adj_for == 1){
                                        // $totalLL -= $adj;
                                        $totalAdj += $adj;

                                    }elseif($adj_for == 2){
                                        // $totalLWP -= $adj;
                                        $totalAdj += $adj;
                                    }
                                    
                                    if (in_array($employee->emp_designation_id, $attendance_bypass_arr)) {
                                        $lp = 0;
                                        $lwp = 0;
                                        $totalLWP = $totalLP = 0;
                                    }
                                    if (in_array($employee->emp_designation_id, $late_bypass_arr)) {
                                        $lp = $totalLP = 0;
                                    }
                                    
                                }
                            @endphp
                        @endforeach

                        @php
                            $array_keys = array_keys($monthWiseValue);
                            $lastDateInMonth = end($array_keys);
                        @endphp


                        @foreach ($leaveCategoryData as $leaveData)

                            @php
                                $leaveVlu = 0;
                            @endphp

                            @foreach ($leaveArray as $leaveKey => $leaveValue)

                                @php
                                    $leaveVlu = 0;
                                    // dd($leaveCategoryData, $leaveData);
                                    if ($leaveData->short_form == $leaveKey) {
                                        $leaveVlu = $leaveValue;
                                    }

                                    if($leaveKey == "AL"){
                                        // $al = $leaveVlu;
                                        $totalAL += $leaveVlu;
                                    }
                                    elseif($leaveKey == "SL"){
                                        // $sl = $leaveVlu;
                                        $totalSL += $leaveVlu;
                                    }
                                    elseif($leaveKey == "CL"){
                                        // $cl = $leaveVlu;
                                        $totalCL += $leaveVlu ;
                                    }

                                    elseif($leaveKey == "LWP"){
                                        // $lwp = $leaveVlu;
                                        
                                    }

                                    elseif($leaveKey == "ML"){
                                        $totalML = $leaveVlu;
                                    }

                                    elseif($leaveKey == "PL"){
                                        $totalPL = $leaveVlu;
                                        
                                    }
                                    
                                @endphp
                            @endforeach
                            

                            @if ($leaveData->short_form == 'LWP')
                                <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($lwp) }} </td>

                            @elseif ($leaveData->short_form == 'AL' && isset($leaveArray[$leaveData->short_form]))
                                <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($leaveArray[$leaveData->short_form]) }} </td>

                            @elseif ($leaveData->short_form == 'CL' && isset($leaveArray[$leaveData->short_form]))
                                <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($leaveArray[$leaveData->short_form]) }} </td>

                            @elseif ($leaveData->short_form == 'SL' && isset($leaveArray[$leaveData->short_form]))
                                <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($leaveArray[$leaveData->short_form]) }} </td>

                            @elseif ($leaveData->short_form == 'PL' && isset($leaveArray[$leaveData->short_form]))
                                <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($leaveArray[$leaveData->short_form]) }} </td>

                            @elseif ($leaveData->short_form == 'ML' && isset($leaveArray[$leaveData->short_form]))
                                <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($leaveArray[$leaveData->short_form]) }} </td> 

                            @else
                                <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($leaveVlu) }} </td>
                            @endif

                        @endforeach

                        @if ( $lastDateInMonth <= date("Y-m-d") )
                            <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($lp) }} </td>
                            <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($ll) }}</td>
                            <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($adj) }}</td>
                        @else
                            <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue(0) }} </td>
                            <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue(0) }}</td>
                            <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue(0) }}</td>
                        @endif

                        {{-- <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($al) }} </td>
                        <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($cl) }} </td>
                        <td style="text-align:center; {{ $bgColor }}">{{ Common::getDecimalValue($sl) }} </td> --}}
                        
                    @endforeach
                    

                    {{-- Month Wise Leave --}}
                    @foreach ($leaveCategoryData as $leaveData)

                        @if ($leaveData->short_form == 'AL')
                        <td style="font-weight:600; text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                            {{ Common::getDecimalValue($totalAL) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'CL')
                        <td style="font-weight:600; text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                            {{ Common::getDecimalValue($totalCL) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'SL')
                        <td style="font-weight:600; text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                            {{ Common::getDecimalValue($totalSL) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'ML')
                        <td style="font-weight:600; text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                            {{ Common::getDecimalValue($totalML) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'PL')
                        <td style="font-weight:600; text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                            {{ Common::getDecimalValue($totalPL) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'LWP')
                        <td style="font-weight:600; text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                            {{ Common::getDecimalValue($totalLWP) }} 
                        </td>
                        @endif

                    @endforeach

                    {{-- <td style="font-weight:600; text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ Common::getDecimalValue($totalLP) }} 
                    </td> --}}
                    <td style="font-weight:600; text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ Common::getDecimalValue($totalLL) }} 
                    </td>
                    <td style="font-weight:600; text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                        {{ Common::getDecimalValue($totalAdj) }} 
                    </td>
                    
                    {{-- Entitled / Assign Leaves Calculation Start --}}
                    @php
                        if( (date("Y", strtotime($employee->join_date))) == ($startDate)->format('Y') ){
                            $divAL = round(($assignedAL / count($getMonthDate)) * $empMonthCounter) ;
                            $divCL = round(($assignedCL / count($getMonthDate)) * $empMonthCounter);
                            $divSL = round(($assignedSL / count($getMonthDate)) * $empMonthCounter);
                            $divML = ($employee->gender === 'Female') ? round($assignedML) : 0;
                            $divPL = ($employee->gender === 'Male') ? round($assignedPL) : 0;
                        }
                        else {
                            $divAL = $assignedAL;
                            $divCL = $assignedCL;
                            $divSL = $assignedSL;
                            $divML = ($employee->gender === 'Female') ? $assignedML : 0;
                            $divPL = ($employee->gender === 'Male') ? $assignedPL : 0;
                            $divLWP = 0;
                        }

                        
                    @endphp
                    
                    @foreach ($leaveCategoryData as $leaveData)

                        @if ($leaveData->short_form == 'AL')
                        <td style="text-align:center; "> {{ Common::getDecimalValue($divAL) }} </td>
                        @endif

                        @if ($leaveData->short_form == 'CL')
                        <td style="text-align:center; "> {{ Common::getDecimalValue($divCL) }} </td>
                        @endif

                        @if ($leaveData->short_form == 'SL')
                        <td style="text-align:center; "> {{ Common::getDecimalValue($divSL) }} </td>
                        @endif

                        @if ($leaveData->short_form == 'ML')
                        <td style="text-align:center; "> {{ Common::getDecimalValue($divML) }} </td>
                        @endif

                        @if ($leaveData->short_form == 'PL')
                        <td style="text-align:center; "> {{ Common::getDecimalValue($divPL) }} </td>
                        @endif

                        {{-- @if ($leaveData->short_form == 'LWP')
                        <td style="text-align:center; "> {{ Common::getDecimalValue(0) }} </td>
                        @endif --}}
                    @endforeach
                    {{-- Entitled / Assign Leaves Calculation End --}}

                    @php
                        
                        $remainAL = $divAL - ($totalAL + $totalLL);
                        // $remainAL = ($divAL - ($totalAL + $totalAdj));
                        $remainCL = $divCL - $totalCL;
                        $remainSL = $divSL - $totalSL;

                        $remainML = $divML - $totalML;
                        $remainPL = $divPL - $totalPL;

                        $remainLWP = $divLWP - $totalLWP;

                        $total = $remainAL + $remainCL + $remainSL + $remainML + $remainPL;

                        
                        // if($totalAdj > 0) {
                        //     $remainLWP = $remainLWP + $totalAdj;
                        //     if ($remainLWP > 0) {
                        //         $remainLWP = 0;
                        //     }
                        // }

                        if ($remainAL < 0) {
                            $remainLWP += ($remainAL);
                            $remainAL = 0;
                        }

                        if($totalAdj > 0) {
                            $remainLWP = $remainLWP + $totalAdj;
                            if ($remainLWP > 0) {
                                $remainLWP = 0;
                            }
                        }

                        if ($remainCL < 0) {
                            $remainLWP += ($remainCL);
                            $remainCL = 0;
                        }
                        if ($remainSL < 0) {
                            $remainLWP += ($remainSL);
                            $remainSL = 0;
                        }
                        

                    @endphp

                    {{-- Remaining Leave --}}
                    @foreach ($leaveCategoryData as $leaveData)
                        @if ($leaveData->short_form == 'AL')
                        <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                            {{ ($remainAL < 0) ? '('.abs(Common::getDecimalValue($remainAL)).')' : Common::getDecimalValue($remainAL) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'CL')
                        <td style="text-align:center; background-color:rgb(230, 153, 76, 0.5)">
                            {{ ($remainCL < 0) ? '('.abs(Common::getDecimalValue($remainCL)).')' : Common::getDecimalValue($remainCL) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'SL')
                        <td style="text-align:center; background-color:rgba(230, 153,76, 0.5)">
                            {{ ($remainSL < 0) ? '('.abs(Common::getDecimalValue($remainSL)).')' : Common::getDecimalValue($remainSL) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'ML')
                        <td style="text-align:center; background-color:rgba(230, 153,76, 0.5)">
                            {{ ($remainML < 0) ? '('.abs(Common::getDecimalValue($remainML)).')' : Common::getDecimalValue($remainML) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'PL')
                        <td style="text-align:center; background-color:rgba(230, 153,76, 0.5)">
                            {{ ($remainPL < 0) ? '('.abs(Common::getDecimalValue($remainPL)).')' : Common::getDecimalValue($remainPL) }} 
                        </td>
                        @endif

                        @if ($leaveData->short_form == 'LWP')
                        <td style="text-align:center; background-color:rgba(65, 13, 13, 0.789); color:rgba(245, 242, 241, 0.931)">
                            {{-- {{ Common::getDecimalValue($remainLWP) }}  --}}
                            {{ ($remainLWP < 0) ? '('.abs(Common::getDecimalValue($remainLWP)).')' : Common::getDecimalValue($remainLWP) }}
                        </td>
                        @endif
                    @endforeach

                    <td style="text-align:center;"><strong>
                        {{-- {{ ($total < 0) ? '('.abs(Common::getDecimalValue($total)).')' : Common::getDecimalValue($total) }}  --}}
                    </strong></td>

                    

                </tr>
            @endforeach


        </tbody>

    </table>

    <p style="font-style: oblique; font-size:60%;" class="d-print-text-dark">
        **
        <span style="font-size:10px;font-style:italic;color:#000">
            <b>NB: A = Absent, &nbsp; LWP = Leave Without Pay, &nbsp; LP = Late Present, &nbsp; </b>
            @foreach ($leaveCategoryData as $lc)
            <b>{{ $lc->short_form }}=</b>{{ $lc->name }},
            @endforeach
        </span>
        **
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

    @include('elements.signature.approvalSet')
    {{-- @include('../elements.signature.signatureSet', ['visible' => false]) --}}
</div>

<style>
    @media print {
        .d-print-text-dark {
            color: #000;
        }
    }
</style>
