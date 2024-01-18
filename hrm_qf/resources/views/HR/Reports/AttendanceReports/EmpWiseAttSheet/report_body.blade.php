@php
    // $isTime = 'yes';
@endphp
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">

    @php
        // dd($employeeData);
        // dd($attendance_bypass_arr, $late_bypass_arr);
    @endphp

    

        <table class="table table-bordered sticky-table clsDataTable" id="tableID" >
            <thead class="text-center sticky-head" id="header_col">
                <tr>
                    @php
                        $dayColSpan = 0;
                        $TotalColSpan = 31;
                        $dNone = '';
                    @endphp
                    <th rowspan="2" width="2%">SL</th>
                    <th rowspan="2" width="4%">Month</th>
                    <th colspan="31" width="60%"> Date </th>

                    <th class="" width="10%" colspan="8">Total</th>
                </tr>

                <tr>

                    @foreach ($const31DayForAllMonth as $key => $date)
                    <th width="2%" > {{$date}} </th>
                    @endforeach 

                    <th width="2%">LP </th>
                    <th width="2%">LWP</th>
                    <th width="2%">AL </th>
                    <th width="2%">SL </th>
                    <th width="2%">CL </th>
                    <th width="2%" title="Total Leave">TL </th>
                </tr>
            </thead>

            
                <tbody>

                    @php
                        $febVal = 28;
                        $month_days = array("January" => 31,"February" => $febVal,"March" => 31,"April" => 30,"May" => 31,"June" => 30,"July" => 31,"August" => 31,"September" => 30,"October" => 31,"November" => 30,"December" => 31);
                    @endphp
                    
                    @foreach ($employeeData as $employee)
                        @php
                            ## Ignored Attendance Bypass 
                            if (in_array($employee->emp_designation_id, $attendance_bypass_arr)) {
                                continue;
                            }

                            $empYearlyAttendance = isset($employee->yearlyAttendance) ? $employee->yearlyAttendance : array();
                            if (count($empYearlyAttendance) < 1) {
                                continue;
                            }
                            // dd($employee);
                            $empResignDate = new DateTime($employee->emp_resign_date);
                            $empJoinDate = new DateTime($employee->join_date);

                            // dd($empResignDate, $empJoinDate);
                        @endphp
                        <tr>
                            <tr style="background: #cac8c8;">
                                <td colspan="39">
                                    &nbsp;&nbsp; <strong> {{$employee->emp_name}}</strong>&nbsp;&nbsp; - {{$employee->emp_designation}} &nbsp;&nbsp;- {{$employee->emp_department}}
                                </td>
                            </tr>
                           
                            @php
                                
                                
                                $i=1;
                                $tLP = $tAL = $tSL = $tCL = $ttl = $tlwp = 0;
                                $totalLP = 0;
                                $totalLWP  = 0;
                                $lpToLWP = 0;

                                
                            @endphp

                            @foreach ($empYearlyAttendance as $monthName => $attMonthValue )

                                @php

                                    // dd($attMonthValue);
                                    $haveResign = 0;
                                    $totalAL = $totalCL = $totalSL = $lpToLWPeachMonth = 0;
                                    
                                    if(array_search("A", $empYearlyAttendance[$monthName] ,true) ){
                                        $counts = array_count_values($empYearlyAttendance[$monthName]);
                                        $totalLWP = $counts["A"];
                                    }

                                    if(array_search("LP", $empYearlyAttendance[$monthName] ,true) ){
                                        $counts = array_count_values($empYearlyAttendance[$monthName]);
                                        $totalLP = $counts["LP"];

                                        // $lpAccept = intval($employee->lpAccept);
                                        // if($totalLP >= $lpAccept ){
                                        //     $lpToLWP += intval($totalLP / $lpAccept);
                                        //     $lpToLWPeachMonth = intval($totalLP / $lpAccept);
                                        // }
                                    }

                                    if(array_search("AL", $empYearlyAttendance[$monthName] ,true) ){
                                        $counts = array_count_values($empYearlyAttendance[$monthName]);
                                        $totalAL = $counts["AL"];

                                        // dd($totalAL);
                                    }

                                    if(array_search("SL", $empYearlyAttendance[$monthName] ,true) ){
                                        $counts = array_count_values($empYearlyAttendance[$monthName]);
                                        $totalSL = $counts["SL"];
                                    }

                                    if(array_search("CL", $empYearlyAttendance[$monthName] ,true) ){
                                        $counts = array_count_values($empYearlyAttendance[$monthName]);
                                        $totalCL = $counts["CL"];
                                    }
                                    
                                    ## get year start
                                    foreach ($attMonthValue as $attDate => $attResult){
                                        $tmpTargetYear = intval((date("Y", strtotime($attDate))));
                                        break;
                                    }
                                    $year = $targetYear;
                                    if ($tmpTargetYear > intval($targetYear)) {
                                        $year = $tmpTargetYear;
                                        
                                    }else{
                                        $year = $targetYear;
                                    }
                                    ## get year start
                                    
                                    $lpHolidayArr = array();
                                @endphp
                                
                                <tr>
                                    <td style="text-align:center;">{{$i++}}</td>
                                    <td>{{$monthName.'-'.$year}}</td>
            
                                    
                                    @foreach ($const31DayForAllMonth as $key => $date)
                                        @foreach ($attMonthValue as $attDate => $attResult)

                                            @php

                                                $tmpDate = date('Y-m', strtotime($attDate));
                                                $createFullDate = $tmpDate.'-'.$date;

                                                if (in_array($createFullDate, $holidays) && $attMonthValue[$createFullDate] == "LP") {
                                                    
                                                    if ($attResult == 'LP') {
                                                        array_push($lpHolidayArr, $createFullDate);
                                                    }
                                                }

                                                ## Catch Tartget Year
                                                $febVal = date('L',strtotime($createFullDate)) ? $febVal = 29 : $febVal = 28;
                                                ## Catch Tartget Year

                                                if (in_array($employee->emp_designation_id, $attendance_bypass_arr)) {

                                                   if (in_array($createFullDate, $holidays) == true) {
                                                        $attResult = 'H';
                                                        $bgColor = in_array($createFullDate, $holidays) ? 'font-weight:500; background-color:#dddddd; color:rgb(226, 96, 96);' : '';
                                                    }else{

                                                        if (date('Y-m-d') < $createFullDate) {
                                                            $attResult = ' ';
                                                        }else{
                                                            $attResult = "P";
                                                        }
                                                    }
                                                    
                                                    $totalLWP = 0;
                                                }

                                                $bgColor = '';

                                                $resultValue = $attResult;
                                                $resultMsg = " ";
                                                // $resultMsg = " ";
                                                // $resultMsg = "Present";
                                                // $resultMsg = "Late Present";
                                                // $resultMsg = "Holiday";
                                                // $resultMsg = "Movement Present";
                                                // $resultMsg = "Annual Leave";
                                                // $resultMsg = "Sick Leave";
                                                // $resultMsg = "Casual Leave";
                                                // $resultMsg = "Absent";
                                                   

                                                $countForLPMonthWise = 0;
                                                $totalLPLeave = 0;

                                                if($attResult == 'P'){
                                                    $bgColor = 'font-weight:500; color:green;';
                                                    $resultMsg = "Present";
                                                }

                                                if($attResult == 'LP' && (in_array($createFullDate, $holidays)== false)){

                                                    if ( in_array($employee->emp_designation_id, $late_bypass_arr) ) {
                                                        $resultValue = "P";
                                                        $bgColor = 'font-weight:500; color:green;';
                                                        $totalLP = $lpToLWPeachMonth = $lpToLWP = 0;
                                                        // $tLP = 0;
                                                        
                                                    }else{

                                                        $bgColor = 'background-color:rgb(240, 201, 74); color:#000000;font-weight:500; ';
                                                        $empYearlyAttendance[$monthName]['totalLP'] += 1;
                                                        $resultMsg = "Late Present";
                                                    }

                                                }
                                                
                                                if($attResult == 'LP' && (in_array($createFullDate, $holidays) == true)){

                                                    $resultValue = "P";
                                                    $bgColor = 'font-weight:500; background-color:#dddddd; color:green;';
                                                    $resultMsg = "Holiday But Present";
                                                }
                                                
                                                if($attResult == 'H'){

                                                    $bgColor = 'background-color:#dddddd; color:rgb(226, 96, 96);font-weight:500; ';
                                                    $resultMsg = "Holiday";

                                                }elseif($attResult == 'MP'){
                                                    $resultValue = "P";
                                                    $bgColor = 'background-color:#0B610B; font-weight:500; color:rgb(230, 245, 230);font-weight:500; ';
                                                    $resultMsg = "Movement Present";
                                                    
                                                }elseif($attResult == 'AL' || $attResult == 'SL' || $attResult == 'CL'){
                                                    $bgColor = 'background-color:rgba(165, 4, 138, 0.966); color:rgba(247, 240, 245, 0.966);font-weight:500; ';

                                                    if ($attResult == 'AL') {
                                                        $resultMsg = "Annual Leave";
                                                    } elseif($attResult == 'SL') {
                                                        $resultMsg = "Sick Leave";
                                                    }elseif ($attResult == 'CL') {
                                                        $resultMsg = "Casual Leave";
                                                    }elseif ($attResult == 'CL/P' || $attResult =='AL/P' || $attResult == 'SL/P' || $attResult == 'PL/P' || $attResult == 'ML/P') {
                                                        $resultValue = "<span style='font-weight:500; '>P</span>";
                                                        $bgColor = 'background-color:rgb(165, 4, 138, 0.966); color:rgb(229, 235, 229)';
                                                        $resultMsg = "Leave day but present";
                                                    }

                                                }elseif($attResult == 'A' && $monthName == date('F', strtotime($createFullDate))){
                                                    $bgColor = 'background-color:rgba(226, 96, 96); color:rgba(247, 240, 245, 0.966);font-weight:500; ';
                                                    $resultMsg = "Absent";
                                                }


                                                $tmpEmpResignDate = $empResignDate->format("Y-m-d");

                                                if ( ($empResignDate < (new DateTime($createFullDate)))  ) {
                                                    $resultValue = ' ';
                                                    $resultMsg = " ";
                                                    $bgColor = 'font-weight:500; background-color:#dddddd; color:rgb(226, 96, 96);';
                                                    $totalAL = $totalSL = $totalCL = $totalLWP = 0;
                                                }
                                                if ( $empJoinDate > (new DateTime($createFullDate)) ) {
                                                    $systemCurrentDate = new DateTime();
                                                    if ($empJoinDate > $startDate) {
                                                        $resultValue = ' ';
                                                        $resultMsg = " ";
                                                        $bgColor = 'font-weight:500; background-color:#dddddd; color:rgb(226, 96, 96);';
                                                        // $totalAL = $totalSL = $totalCL = 0;
                                                    }
                                                }
                                                

                                            @endphp

                                            @if ($createFullDate == $attDate && $date == date('d', strtotime($attDate)))
                                                <td width="2%" style="{{$bgColor}} text-align:center;" title="{{$resultMsg}}"> {{$resultValue}} </td>
                                            @endif 

                                        @endforeach
                                    
                                    @endforeach
                                    
                                    @php
                                        $uniqueValues = array_unique($lpHolidayArr);
                                        $totalLP = $totalLP - count($uniqueValues);
                                    @endphp
            
                                    @foreach ($month_days as $mont => $totalD)
                                        @if ($monthName == $mont)
                                            @php
                                                $emptyRow = 31 - $totalD;
                                                // $totalLWP -= $emptyRow;
                                                // if ($totalLWP < 1) {
                                                //     $totalLWP = 0;
                                                // }
                                            @endphp
                                            @for ($er = 0; $er<$emptyRow; $er++)
                                                <td></td>
                                            @endfor
                                        @endif
                                    @endforeach

                                    @php
                                        $resigndate = $employee->emp_resign_date;
                                        
                                    @endphp

                                   
                                    <td style="text-align:center;">{{$totalLP}}</td>
                                    <td style="text-align:center;">{{ $totalLWP + $lpToLWPeachMonth }}</td>
                                    <td style="text-align:center;">{{ $totalAL }}</td>
                                    <td style="text-align:center;">{{ $totalSL }}</td>
                                    <td style="text-align:center;">{{ $totalCL }}</td>
                                    <td style="text-align:center;">{{ $totalAL + $totalSL + $totalCL }}</td>

                                    
                                    {{-- <td style="text-align:center;">{{$attMonthValue['totalAL']}}</td>
                                    <td style="text-align:center;">{{$attMonthValue['totalSL']}}</td>
                                    <td style="text-align:center;">{{$attMonthValue['totalCL']}}</td>
                                    <td style="text-align:center;">{{$attMonthValue['totalAL'] + $attMonthValue['totalSL'] + $attMonthValue['totalCL']}}</td> --}}
                                </tr> 

                                @php
                                    $tlwp += $totalLWP;
                                    $tLP += $totalLP;
                                    $totalLWP = $totalLP = 0;
                                    // $tAL += $attMonthValue['totalAL'];
                                    // $tSL += $attMonthValue['totalSL'];
                                    // $tCL += $attMonthValue['totalCL'];
                                    $tAL += $totalAL;
                                    $tSL += $totalSL;
                                    $tCL += $totalCL;
                                    $ttl = $tAL + $tSL + $tCL;
                                @endphp
                            @endforeach
                            
                            
        
                            <tr>
                                <td colspan="33" > <strong style="margin-left: 85rem" >Sub Total</strong></td>
                                <td style="text-align:center;"><strong> {{$tLP}} </strong></td>
                                <td style="text-align:center;"><strong> {{$tlwp + $lpToLWP}}</strong></td>
                                <td style="text-align:center;"><strong> {{$tAL}} </strong></td>
                                <td style="text-align:center;"><strong> {{$tSL}} </strong></td>
                                <td style="text-align:center;"><strong> {{$tCL}} </strong></td>
                                <td style="text-align:center;"><strong> {{$ttl}} </strong></td>
                            </tr>
                            
                        </tr>
                    @endforeach
                    

                </tbody>
            
        </table>
        
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

    {{-- @include('../elements.signature.signatureSet', ['visible' => false]) --}}
    @include('elements.signature.approvalSet')
</div>

<style>
    @media print {
        .d-print-text-dark {
            color: #000;
        }
    }
</style>
