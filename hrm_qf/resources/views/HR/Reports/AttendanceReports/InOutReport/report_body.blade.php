@php
    // $isTime = 'yes';
@endphp
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">
    <table class="table table-bordered sticky-table clsDataTable" id="tableID" style="font-size:70%;">
        <thead class="text-center sticky-head" id="header_col">
            <tr>
                @php
                    $dayColSpan = 0;
                    $TotalColSpan = 7;
                    $dNone = '';
                    if ($withHoliday == 'no') {
                        $dayColSpan = count($monthDates);
                        $dNone = 'd-none';
                    } else {
                        $dayColSpan = count($monthDates);
                    }
                @endphp
                <th rowspan="2" width="2%">SL</th>
                <th rowspan="2" width="8%">Employee</th>
                <th rowspan="2" width="3%">Desig-<br>nation</th>
                <th rowspan="2" width="5%" title="Department">Dept.</th>

                <th colspan="{{ $dayColSpan }}" width="70%">Days</th>
                <th class="{{ $dNone }}" colspan="{{ $TotalColSpan }}">Total</th>
            </tr>

            <tr>

                @foreach ($monthDates as $date => $day)

                    {{-- @php $runningDate = $monthYear->format('Y-m-').$date; @endphp --}}
                    @php $runningDate = $date; @endphp

                    @if (in_array($runningDate, $holidays) == true && in_array($runningDate, $attendanceDates) != true)
                        @if ($withHoliday == 'yes')
                            {{-- <th>{{ $date }} {{ $day }}<br>(in/out)</th> --}}
                            <th>
                                {{ date('d', strtotime($date)) }} {{ $day }}
                                @if ($isTime == 'yes')
                                    <br>(in/out)
                                @endif
                            </th>
                        @endif
                    @else
                        {{-- <th>{{ $date }} {{ $day }}<br>(in/out)</th> --}}
                        <th>
                            {{ date('d', strtotime($date)) }} {{ $day }}
                            @if ($isTime == 'yes')
                                <br>(in/out)
                            @endif
                        </th>
                    @endif

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
            @foreach ($employeeData as $item)
                @php
                    $empId = $item->id;
                    $empAttendance = isset($item->attendance) ? $item->attendance : [];
                    
                    if ($withAbsent == 'no') {
                        if (count($empAttendance) < 1) {
                            continue;
                        }
                    }
                    
                    $totalLP = $totalLWP = $totalL = $countForLPMonthWise = $totalLPLeave = 0;

                    ## Ignored Attendance Bypass 
                    if (in_array($item->emp_designation_id, $attendance_bypass_arr)) {
                        continue;
                    }

                    $emp_join_date = new DateTime($item->join_date);
                    $emp_resign_date = new DateTime($item->closing_date);

                    // dd($employeeData, $empAttendance);
                    // dd($item, $emp_join_date, $emp_resign_date);
                @endphp

                <tr style="">
                    <td class="text-center">{{ ++$i }}</td>
                    <td>{{ $item->emp_name }}</td>
                    <td>{{ $item->emp_designation }}</td>
                    <td>{{ $item->emp_department }}</td>

                    @foreach ($monthDates as $date => $day)
                        @php
                        // dd($empAttendance);
                            $bgColor = '';
                            $textColor = '';
                            $attText = '-';
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
                            // $runningDate = $monthYear->format('Y-m-').$date;
                            $runningDate = $date;

                            if (in_array($item->emp_designation_id, $attendance_bypass_arr)) {
 
                                    if (in_array($runningDate, $holidays) == true) {
                                        $attText = 'H';
                                        $bgColor = in_array($runningDate, $holidays) ? 'font-weight:500; background-color:#dddddd; color:rgb(226, 96, 96);' : '';
                                        $resultMsg = "Holiday";
                                    }else{
                                        if ($isTime == 'yes') {
                                            $attText = "<span style='font-weight:500; color:rgb(9, 128, 9);'>P</span>";
                                            // $attText .= '<br> - ';
                                        } else {
                                            $attText = "<span style='font-weight:500; color:rgb(9, 128, 9);'>P</span>";
                                        }
                                        $resultMsg = "Present";
                                    }
                                    
                            }else{

                                $runningDateTmp = new DateTime($runningDate);
                                if ((date('Y-m-d h:i:s') < $runningDate) || $emp_join_date > $runningDateTmp ||  $emp_resign_date <= $runningDateTmp) {
                                    $attText = '';
                                    $bgColor = 'background-color:#dddddd; color:rgb(226, 96, 96);';

                                }elseif (isset($empAttendance[$runningDate])) {
                                
                                    if ($isTime == 'yes') {
                                        $attText = isset($empAttendance[$runningDate]['in']) ? $empAttendance[$runningDate]['in'] : '';
                                        $attText .= isset($empAttendance[$runningDate]['out']) ? '<br>' . $empAttendance[$runningDate]['out'] : '';
                                        $resultMsg = "Present";
                                    } else {
                                        if($empAttendance[$runningDate]['status'] == 'A'){
                                            $attText = "A";
                                            $bgColor = 'color:rgb(230, 245, 230); background-color:rgba(226, 96, 96); font-weight:500;';
                                            $resultMsg = "Absent";
                                            $totalLWP++;
                                        }else{
                                            $attText = "<span style='font-weight:500; color:rgb(9, 128, 9);'>P</span>";
                                            $resultMsg = "Present";
                                        }
                                    }
                            
                                    if ($empAttendance[$runningDate]['status'] == 'LP' && !in_array($runningDate, $holidays)) {

                                        if ( in_array($item->emp_designation_id, $late_bypass_arr) ) {
                                            $totalLP = 0;
                                        }else{
                                            $totalLP++;
                                        }
                                        
                                        $countForLPMonthWise++;
                                        $bgColor = 'background-color:rgb(240, 201, 74);';
                                        $resultMsg = "Late Present";
                            
                                        if ($isTime == 'no') {
                                            $attText = "<span style='font-weight:500;  '>" . $empAttendance[$runningDate]['status'] . '</span>';
                                        }
                                    }

                                    if ($empAttendance[$runningDate]['status'] == 'LP' && in_array($runningDate, $holidays)) {

                                        $bgColor = "background-color:#dddddd;";
                                        $attText = "<span style='font-weight:500; color:rgb(9, 128, 9);'>P</span>";
                                        $resultMsg = "Holiday But Present";
                                        
                                    }

                                    if ($empAttendance[$runningDate]['status'] == 'MP'){
                                        $bgColor = 'background-color:rgb(9, 128, 9); color:rgba(247, 240, 245, 0.966);';
                                        if ($isTime == 'no') {
                                            $attText = "<span style='font-weight:500; '>P</span>";
                                        }else{
                                            $attText = isset($empAttendance[$runningDate]['in']) ? $empAttendance[$runningDate]['in'] : "<span style='font-weight:500; '>P</span>";
                                            $attText .= isset($empAttendance[$runningDate]['out']) ? '<br>' . $empAttendance[$runningDate]['out'] : '';
                                        }
                                        $resultMsg = "Movement Present";
                                    }

                                    ## Employee Designation Late Bypass
                                    if ( in_array($item->emp_designation_id, $late_bypass_arr) ) {
                                        $bgColor = " ";
                                        if ($isTime == 'yes') {
                                            $attText = isset($empAttendance[$runningDate]['in']) ? $empAttendance[$runningDate]['in'] : "<span style='font-weight:500; '>P</span>";
                                            $attText .= isset($empAttendance[$runningDate]['out']) ? '<br>' . $empAttendance[$runningDate]['out'] : '';

                                            if ($empAttendance[$runningDate]['status'] == 'MP'){
                                                $bgColor = 'background-color:rgb(9, 128, 9); color:rgb(229, 235, 229)';
                                                $resultMsg = "Movement Present";
                                            }

                                        } else {
                                            $attText = "<span style='font-weight:500; '>P</span>";

                                            if ($empAttendance[$runningDate]['status'] == 'MP'){
                                                $bgColor = 'background-color:rgb(9, 128, 9); color:rgb(229, 235, 229)';
                                                $resultMsg = "Movement Present";
                                            }else{
                                                $attText = "<span style='font-weight:500; color:rgb(9, 128, 9);'>P</span>";
                                                $resultMsg = "Present";
                                            }
                                        }  
                                    }
                                    ## Employee Designation Late Bypass
                            
                                    if ($empAttendance[$runningDate]['status'] != 'LP' && $empAttendance[$runningDate]['status'] != 'P' && $empAttendance[$runningDate]['status'] != 'NaN' && $empAttendance[$runningDate]['status'] != 'MP' && $empAttendance[$runningDate]['status'] != 'A') {
                                        $totalL++;
                                        $bgColor = 'background-color:rgba(165, 4, 138, 0.966);';
                                        $attText = "<span style='font-weight:500; color:rgba(247, 240, 245, 0.966); '>" . $empAttendance[$runningDate]['status'] . '</span>';

                                        $leaveStatus = $empAttendance[$runningDate]['status'];

                                        if ($leaveStatus == 'AL') {
                                            $resultMsg = "Annual Leave";
                                        } elseif($leaveStatus == 'SL') {
                                            $resultMsg = "Sick Leave";
                                        }elseif ($leaveStatus == 'CL') {
                                            $resultMsg = "Casual Leave";
                                        }
                                        // elseif ($leaveStatus == 'CL/P' || $leaveStatus =='AL/P' || $leaveStatus == 'SL/P' || $leaveStatus == 'PL/P' || $leaveStatus == 'ML/P') {
                                        //     $attText = "<span style='font-weight:500; '>P</span>";
                                        //     $bgColor = 'background-color:rgb(165, 4, 138, 0.966); color:rgb(229, 235, 229)';
                                        //     $resultMsg = "Leave day but present";
                                        // }
                                    }

                                    if($empAttendance[$runningDate]['status'] == 'A'){
                                        $bgColor = 'background-color:rgba(226, 96, 96); font-weight:500; color:rgb(230, 245, 230);';
                                        $attText = 'A';
                                        $totalLWP++;
                                        $resultMsg = "Absent";
                                    }

                                } elseif (in_array($runningDate, $holidays) == true) {
                                    $attText = 'H';
                                    $bgColor = in_array($runningDate, $holidays) ? 'font-weight:500; background-color:#dddddd; color:rgb(226, 96, 96);' : '';
                                    $resultMsg = "Holiday";
                                } else {

                                    $bgColor = 'background-color:rgba(226, 96, 96); font-weight:500; color:rgb(230, 245, 230);';
                                    $attText = 'A';
                                    $totalLWP++;
                                    $resultMsg = "Absent";
                                }

                            }
                            
                            
                        @endphp

                        @if (in_array($runningDate, $holidays) == true && in_array($runningDate, $attendanceDates) != true)
                            @if ($withHoliday == 'yes')
                                <td class="text-center" style="{!! $bgColor !!}" title="{{$resultMsg}}"> {!! $attText !!} </td>
                            @endif
                        @else
                            <td class="text-center" style="{!! $bgColor !!} {!! $textColor !!}" title="{{$resultMsg}}">
                                {!! $attText !!} </td>
                        @endif
                    @endforeach

                    @php
                        // dd($totalLP);
                        if ( !empty($lpAccept) && $totalLP >= intval($lpAccept)) {
                            $totalLPLeave =  !empty($totalLP) ? intval($totalLP / intval($lpAccept)) : 0;
                        }else{
                            $totalLPLeave = 0;
                        }
                    @endphp

                    <td class="text-center" style="background:#dddddd">{{ $totalLP }}</td>
                    <td class="text-center" style="background:#dddddd">{{ $totalLWP + $totalLPLeave}}</td>
                    <td class="text-center" style="background:#dddddd">{{ $item->totalAL }}</td>
                    <td class="text-center" style="background:#dddddd">{{ $item->totalSL }}</td>
                    <td class="text-center" style="background:#dddddd">{{ $item->totalCL }}</td>
                    <td class="text-center" style="background:#dddddd">
                        {{ $item->totalAL + $item->totalSL + $item->totalCL + $totalLWP }}</td>
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
