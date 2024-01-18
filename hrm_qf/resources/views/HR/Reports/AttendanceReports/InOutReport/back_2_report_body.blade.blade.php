
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">

    <table class="table table-bordered sticky-table clsDataTable" id="tableID">
        <thead class="text-center sticky-head" id="header_col">
            <tr>
                <th width="2%">SL</th>
                <th width="8%">Employee</th>
                <th width="3%">Desig-<br>nation</th>
                <th width="5%" title="Department">Dept.</th>
                {{-- <th rowspan="2" width="5%">Branch</th> --}}
               
                {{-- <th rowspan="3">Time</th> --}}
                @foreach ($monthDates as $date => $day)

                    @php $runningDate = $monthYear->format('Y-m-').$date; @endphp

                    @if(in_array($runningDate, $holidays) == true && in_array($runningDate, $attendanceDates) != true)
                        @if($withHoliday == 'yes')
                            <th>{{ $date }} {{ $day }}<br>(in/out)</th>
                        @endif
                    @else
                        <th>{{ $date }} {{ $day }}<br>(in/out)</th>
                    @endif

                @endforeach
    
                {{-- @foreach ($attendanceDates as $date)
                    @if(in_array($date, $holidays) == true)
                        @if($withHoliday == 'yes')
                            <th>{{ date('d D', strtotime($date)) }}<br>(in/out)</th>
                        @endif
                    @else
                        <th>{{ date('d D', strtotime($date)) }}<br>(in/out)</th>
                    @endif
                    
                @endforeach --}}
            </tr>
        </thead>

        <tbody>
            @php
                $i = 0;

                // dd($employeeData);
              
            @endphp
            @foreach ($employeeData as $item)

                @php
                    $empId = $item->id;
                    $empAttendance = isset($item->attendanceTime) ? $item->attendanceTime : array();


                    if($withAbsent == 'no'){
                        if(count($empAttendance) < 1){
                            continue;
                        }
                    }
                @endphp
    
                <tr style="" >
                    <td class="text-center">{{ ++$i }}</td>
                    <td>{{ $item->emp_name }}</td>
                    <td>{{ $item->emp_designation }}</td>
                    <td>{{ $item->emp_department }}</td>
                    {{-- <td>{{ $item->emp_branch }}</td> --}}

                    @foreach ($monthDates as $date => $day)
                        @php
                            $bgColor = '';
                            $attText = "-";
                            $runningDate = $monthYear->format('Y-m-').$date;
                            

                            if(isset($empAttendance[$runningDate])){
                                $attText = $empAttendance[$runningDate]['in']."<br>".$empAttendance[$runningDate]['out'];
                            }
                            elseif(in_array($runningDate, $holidays) == true){
                                $bgColor =  in_array($runningDate, $holidays) ? 'background-color:#dddddd' : '';;
                                $attText = "H";
                            }
                            else{
                                $bgColor =  "background-color:rgba(226, 96, 96);";
                                $attText = "A";
                            }
                        @endphp

                        @if(in_array($runningDate, $holidays) == true && in_array($runningDate, $attendanceDates) != true)
                            @if($withHoliday == 'yes')
                            <td class="text-center" style="{!! $bgColor !!}" > {!! $attText  !!} </td>
                            @endif
                        @else
                        <td class="text-center" style="{!! $bgColor !!}" > {!! $attText  !!} </td>
                        @endif

                    @endforeach

                    {{-- @foreach ($attendanceDates as $date)
                        @php
                            $bgColor = '';
                            $runningDate = $date;
                            $attText = "-";

                            if(isset($empAttendance[$runningDate])){
                                // <hr style='padding: 0; margin:0;'>
                                $attText = $empAttendance[$runningDate]['in']."<br>".$empAttendance[$runningDate]['out'];
                            }
                            elseif(in_array($runningDate, $holidays) == true){
                                if($withHoliday == 'yes'){
                                    $bgColor =  in_array($runningDate, $holidays) ? 'background-color:#dddddd' : '';;
                                    $attText = "H";
                                } 
                            }
                            else{
                                $bgColor =  "background-color:rgba(226, 96, 96);";
                                $attText = "-";
                            }
                        @endphp

                        @if(in_array($date, $holidays) == true)
                            @if($withHoliday == 'yes')
                                <td class="text-center" style="{!! $bgColor !!}" > {!! $attText  !!} </td>
                            @endif
                        @else
                            <td class="text-center" style="{!! $bgColor !!}" > {!! $attText  !!} </td>
                        @endif

                    @endforeach --}}
    
                </tr>
            @endforeach

        </tbody>
    </table>
</div>
