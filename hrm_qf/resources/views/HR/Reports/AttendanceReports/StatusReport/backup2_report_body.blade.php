
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">

    <table class="table table-bordered sticky-table clsDataTable" id="tableID">
        <thead class="text-center sticky-head" id="header_col">
            <tr>

                @php
                // dd($monthDates);
                    $dayColSpan = 0;
                    $TotalColSpan = 6;
                    $dNone = '';
                    if($withHoliday == 'no'){
                        $dayColSpan = count($monthDates);
                        $dNone = "d-none";
                        // $TotalColSpan = 6;
                    }else{
                        $dayColSpan = count($monthDates);
                        // $TotalColSpan = 2;
                    }

                @endphp

                <th rowspan="2" width="2%">SL</th>
                <th rowspan="2" width="10%">Employee</th>
                <th rowspan="2" width="5%">Designation</th>
                <th rowspan="2" width="8%">Department</th>
                <th colspan="{{ $dayColSpan }}" width="67%">Days</th>
                <th class="{{$dNone}}" colspan="{{ $TotalColSpan }}">Total</th>

            </tr>
            <tr>

                @foreach ($monthDates as $date => $day)
                    @php
                    // dd($monthDates);
                        $daysDate = '';
                        // $runningDate = $monthYear->format("Y-m-") . $date;
                        $runningDate = $date;
                        if(in_array($runningDate, $holidays) == true){

                            if($withHoliday == 'yes'){
                                $daysDate =  "<th width='2%'> ". date('d', strtotime($date)) ." <br> ".$day."</th>";
                            }else{
                                continue;
                            }

                        }
                        else {
                            $daysDate =  "<th width='2%'> ". date('d', strtotime($date))." <br> ".$day."</th>";
                        }
                    @endphp

                    {!! $daysDate !!}

                     {{-- <th width="2%">{{ $date }} <br> {{ $day }}</th>   --}}
                @endforeach


                <th width="2%">LP</th>
                <th width="2%">LWP</th>
                <th width="2%">AL</th>
                <th width="2%">SL</th>
                <th width="2%">CL</th>
                <th width="2%">Leave</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
                // dd($employeeData, $monthDates, $holidays);
            @endphp
            @foreach ($employeeData as $item)
                @php

                    $empId = $item->id;
                    $empAttendance = isset($item->attendance) ? $item->attendance : array();

                    $empHoliday = isset($holidays) ? $holidays : array();

                    // $withAbsent = "yes";

                    if($withAbsent == 'no'){
                        if(count($empAttendance) < 1){
                            continue;
                        }
                    }

                    if($withHoliday == 'no'){
                        if(count($empHoliday) < 1){
                            continue;
                        }
                    }

                    $totalLP = $totalLWP = $totalL = 0;
                @endphp
                <tr>
                    <td class="text-center">{{ ++$i }}</td>
                    <td>{{ $item->emp_name }}</td>
                    <td>{{ $item->emp_designation }}</td>
                    <td>{{ $item->emp_department }}</td>
                    {{-- <td>{{ $item->emp_branch }}</td> --}}

                    @foreach ($monthDates as $date => $day)
                        @php

                            $bgColor = '';
                            // $runningDate = $monthYear->format("Y-m-") . $date;
                            $runningDate =  $date;


                            // dd($employeeData, $runningDate,  $monthDates);
                            $attText = "-";

                            if(isset($empAttendance[$runningDate])){

                                if(gettype($empAttendance[$runningDate]) == "integer"){
                                    $bgColor = 'background-color:rgba(2, 117, 2);';
                                    $attText = "<span style='font-weight:500; color:rgb(230, 245, 230);'>MP</span>";
                                }
                                else {
                                    $attText = "<span style='font-weight:500; color:green;'>P</span>";

                                    if($empAttendance[$runningDate] == "LP"){
                                        $totalLP++;
                                        $bgColor = 'background-color:rgb(240, 201, 74);';
                                        $attText = "<span style='font-weight:500;  '>".$empAttendance[$runningDate]."</span>";
                                    }

                                    if($empAttendance[$runningDate] != "LP" && $empAttendance[$runningDate] != "P" && $empAttendance[$runningDate] != "NaN"){
                                        $totalL++;
                                        $bgColor = 'background-color:rgba(165, 4, 138, 0.966);';
                                        $attText = "<span style='font-weight:500; color:rgba(247, 240, 245, 0.966); '>".$empAttendance[$runningDate]."</span>";

                                    }

                                }
                            }
                            elseif(in_array($runningDate, $holidays) == true){

                                if($withHoliday == 'yes' && in_array($runningDate, $holidays) == true ){
                                    $bgColor = in_array($runningDate, $holidays) ? 'background-color:#dddddd' : '';
                                    $attText = "<span style='font-weight:500; color:rgb(226, 96, 96); '>H</span>";


                                }else{
                                    continue;
                                }

                                // $bgColor = in_array($runningDate, $holidays) ? 'background-color:#dddddd' : '';
                                // $attText = "<span style='font-weight:500; color:rgba(185, 25, 25, 0.699); '>H</span>";

                            }
                            else{


                                if ( date('Y-m-d h:i:s') < $runningDate ){
                                    $attText = " ";

                                }else{

                                    $bgColor = 'background-color:rgba(185, 25, 25, 0.699);';
                                    $attText = "<span style='font-weight:500; color:rgba(247, 240, 245, 0.966);'>A</span>";
                                    $totalLWP++;
                                }

                                // $bgColor = 'background-color:rgba(185, 25, 25, 0.699);';
                                // $attText = "<span style='font-weight:500; color:rgba(247, 240, 245, 0.966);'>A</span>";
                                // $totalLWP++;

                            }
                        @endphp


                        @if(in_array($runningDate, $holidays) == true && in_array($runningDate, $empAttendance) != true)
                            @if($withHoliday == 'yes')
                                <td class="text-center" style="{!! $bgColor !!}" > {!! $attText  !!} </td>
                            @endif
                        @else
                            <td class="text-center" style="{!! $bgColor !!}" > {!! $attText  !!} </td>
                        @endif
                        {{-- <td class="text-center" style="{!! $bgColor !!}" > {!! $attText  !!} </td> --}}
                    @endforeach

                    <td class="text-center" style="background:#dddddd">{{ $totalLP }}</td>
                    <td class="text-center" style="background:#dddddd">{{ $totalLWP }}</td>
                    <td class="text-center" style="background:#dddddd">{{ $item->totalAL }}</td>
                    <td class="text-center" style="background:#dddddd">{{ $item->totalSL }}</td>
                    <td class="text-center" style="background:#dddddd">{{ $item->totalCL }}</td>
                    <td class="text-center" style="background:#dddddd">{{ $item->totalAL + $item->totalSL +  $item->totalCL }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-style: oblique; font-size: 12px;">
        ** P = Present,&nbsp; LP = Late Present,&nbsp; A = Absent,&nbsp; LWP = Leave Without Pay,&nbsp; AL = Annual Leave,
        &nbsp;  SL = Sick Leave,&nbsp;  CL = Casual Leave,&nbsp;  MP = Movement Present **
    </p>

    <p class="d-print-none" style="font-style: oblique; font-size: 12px;">
        ** &nbsp;
            <input type="color" value="#dddddd" readonly disabled style="width:25px;"> For Holiday, &nbsp;
            <input type="color" value="#ffffff" readonly disabled style="width:25px;"> For Present,  &nbsp;
            <input type="color" value="#027502" readonly disabled style="width:25px;"> For Movement Present,  &nbsp;
            <input type="color" value="#f0c94a" readonly disabled style="width:25px;"> For Late Present,  &nbsp;
            <input type="color" value="#a5048a" readonly disabled style="width:25px;"> For Leave,  &nbsp;
            <input type="color" value="#b91919" readonly disabled style="width:25px;"> For Absent,  &nbsp;
        **
    </p>

    @include('../elements.signature.signatureSet',['visible' => true])

</div>


