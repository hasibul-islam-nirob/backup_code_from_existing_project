@php
    // dd($employeeData);
    // dd($statusCountArray);

    $statusMapping = [
                    'all' => 'All',
                    'p' => 'Present (Regular)',
                    'lp' => 'Present (Late)',
                    'mp' => 'Present (Movement)',
                    'pl' => 'Present (Leave)',
                    'a' => 'Absent',
                ];
    $status = $statusMapping[$orderStatus];

    $totalPLCount = 0;
    $presentLeaveArr = [];
    foreach($allLeaveCategoryData as $allLeaveCat){
        $lowercaseString = strtolower($allLeaveCat->short_form); 
        $totalPLCount += $statusCountArray[$lowercaseString];
        
        $presentLeaveArr[$lowercaseString] = $allLeaveCat->name;
        
    }

    $customTableClass = "row col-sm-6  justify-content-center mx-auto";
    if ($status != 'All') {
        $customTableClass = "row col-sm-4";
    }

    $colorCodeArr =[
                'p'  => '#000000',
                'lp' => '#db7d09',
                'mp' => '#048054',
                'pl' => '#7a0466',
                'a'  => '#c90007',
            ];
@endphp


<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12 ">
    <div class="{{$customTableClass}}">

        {{-- <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="70%">Attendance Status</th>
                    <th >Total</th>
                </tr>
            </thead>
            <tbody>
                @if($status == 'All')
                    <!-- Present (Regular) -->
                    <tr>
                        <td>Present (Regular)</td>
                        <td class="text-center">{{$statusCountArray['p']}}</td>
                    </tr>
                    <!-- Present (Late) -->
                    <tr>
                        <td>Present (Late)</td>
                        <td class="text-center">{{$statusCountArray['lp']}}</td>
                    </tr>
                    <!-- Present (Movement) -->
                    <tr>
                        <td>Present (Movement)</td>
                        <td class="text-center">{{$statusCountArray['mp']}}</td>
                    </tr>
                    
                    <!-- Absent -->
                    <tr>
                        <td>Absent</td>
                        <td class="text-center">{{$statusCountArray['a']}}</td>
                    </tr>

                    <!-- For Leave Start-->
                    <tr>
                        <td>Present (Leave)</td>
                        <td class="text-center">{{$totalPLCount}}</td>
                    </tr>
                    <!-- For Leave End-->

                @else
                    <tr>
                        <td>{{$status}}</td>
                        @if ($orderStatus == 'pl')
                        <td class="text-center">{{$totalPLCount}}</td>
                        @else
                            <td class="text-center">{{$statusCountArray[$orderStatus]}}</td>
                        @endif
                    </tr>
                @endif
                <!-- Repeat this block for each status -->
            </tbody>
        </table> --}}

        <table class="table table-bordered">
            <tr>
                <tr>
                    <th width="15%" class="px-2" style="background-color: #17B3A3; color:#ffff;">  Attendance Status</th> 
                    @if($status == 'All')
                        <td width="15%" class="text-center">Present (Regular)</td>
                        <td width="15%" class="text-center">Present (Late)</td>
                        <td width="15%" class="text-center">Present (Movement)</td>
                        <td width="15%" class="text-center">Absent</td>
                        <td width="15%" class="text-center">Present (Leave)</td>
                    @else
                        <td width="15%" class="text-center">{{$status}}</td>
                    @endif
                </tr>
                <tr>
                    <th class="px-2" style="background-color: #17B3A3; color:#ffff;">  Total</th>
                    @if($status == 'All')
                        <td class="text-center">{{$statusCountArray['p']}}</td> 
                        <td class="text-center">{{$statusCountArray['lp']}}</td> 
                        <td class="text-center">{{$statusCountArray['mp']}}</td> 
                        <td class="text-center">{{$statusCountArray['a']}}</td> 
                        <td class="text-center">{{$totalPLCount}}</td> 
                    @else
                        @if ($orderStatus == 'pl')
                            <td class="text-center">{{$totalPLCount}}</td>
                        @else
                            <td class="text-center">{{$statusCountArray[$orderStatus]}}</td>
                        @endif
                    @endif
                </tr>
            </tr>
        </table>

    </div>
    <table class="table table-bordered sticky-table clsDataTable" id="tableID">
        <thead class="text-center sticky-head" id="header_col">
           
            <tr>
                <th width="2%">SL</th>
                <th >Name</th>
                <th >Designation</th>
                <th >Department</th>
                <th> Work Station </th>
                <th >Duty In</th>
                <th >Duty Out</th>
                <th >First In</th>
                <th >Last Out</th>
                <th >Late Hour <small>(H:M)</small></th>
                {{-- <th >Absent</th> --}}
                <th width="9%" >Status</th>
                <th> Remarks</th>
            </tr>

        </thead>
        <tbody>

            @php
                $i = 0;
                // $t0talI = 0;
                
                // dd($employeeData);
            @endphp

            @foreach ($employeeData as $item)
                
                @php

                     $empStatus = isset($item->attendance) ? $item->attendance : array();

                     ksort($empStatus, 2);

                     if($withAbsent == 'no'){
                        if(count($empStatus) < 1){
                            continue;
                        }
                    }

                    ## Ignored Attendance Bypass 
                    if (in_array($item->emp_designation_id, $attendance_bypass_arr)) {
                        continue;
                    }

                @endphp

                <tr>
                    @foreach ($empStatus as $date => $value )
                        @php
                            // dd($colorCodeArr['pl']);
                            $statusData = !empty($value['status']) ? $value['status'] : ' ';
                        @endphp
                        @if(($value['status'] == $status && $status != 'All'))
                            <tr>
                                <td style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" >{{ ++$i }}</td>
                                <td style="color:{{$colorCodeArr[$orderStatus]}};"> {{ $item->emp_name }}</td>
                                <td style="color:{{$colorCodeArr[$orderStatus]}};"> {{ $item->emp_designation }}</td>
                                <td style="color:{{$colorCodeArr[$orderStatus]}};"> {{ $item->emp_department }}</td>
                                <td style="color:{{$colorCodeArr[$orderStatus]}};"> {{ $item->emp_branch }}</td>
                                <td style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" > {{ !empty($value['on_duty']) ? $value['on_duty'] : '' }} </td>
                                <td style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" > {{ !empty($value['off_duty']) ? $value['off_duty'] : '' }}</td>

                                <td style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" > {{ !empty($value['clock_in']) ? $value['clock_in'] : '' }}</td>
                                <td style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" > {{ !empty($value['clock_out']) ? $value['clock_out'] : '' }}</td>

                                <td style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" style="" > 
                                    {{ !empty($value['late_time']) ? $value['late_time'] : '' }}
                                </td>
                                <td style="color:{{$colorCodeArr[$orderStatus]}};"  > {{$statusData}} </td>
                                <td> </td>
                            </tr>
                        @elseif($status == 'All')

                            @php
                                $txtColorCode = '';
                                if ($statusData == 'Present (Regular)') {
                                    $txtColorCode = '#000000;';
                                }elseif ($statusData == 'Present (Late)') {
                                    $txtColorCode = '#db7d09;';

                                }
                                elseif ($statusData == 'Present (Movement)') {
                                    $txtColorCode = '#048054;';

                                }
                                elseif ($statusData == 'Absent') {
                                    $txtColorCode = '#c90007;';

                                }
                                elseif ( in_array($statusData, $presentLeaveArr) ) {
                                    $txtColorCode = '#7a0466';
                                }
                                
                            @endphp

                            <tr>
                                <td style="color:{{$txtColorCode}};" class="text-center" >{{ ++$i }}</td>
                                <td style="color:{{$txtColorCode}};" > {{ $item->emp_name }}</td>
                                <td style="color:{{$txtColorCode}};" > {{ $item->emp_designation }}</td>
                                <td style="color:{{$txtColorCode}};" > {{ $item->emp_department }}</td>
                                <td style="color:{{$txtColorCode}};" > {{ $item->emp_branch }}</td>
                                <td style="color:{{$txtColorCode}};" class="text-center" > {{ !empty($value['on_duty']) ? $value['on_duty'] : '' }} </td>
                                <td style="color:{{$txtColorCode}};" class="text-center" > {{ !empty($value['off_duty']) ? $value['off_duty'] : '' }}</td>

                                <td style="color:{{$txtColorCode}};" class="text-center" > {{ !empty($value['clock_in']) ? $value['clock_in'] : '' }}</td>
                                <td style="color:{{$txtColorCode}};" class="text-center" > {{ !empty($value['clock_out']) ? $value['clock_out'] : '' }}</td>

                                <td style="color:{{$txtColorCode}};" class="text-center" style="" > 
                                    {{ !empty($value['late_time']) ? $value['late_time'] : '' }}
                                </td>
                                <td style="color:{{$txtColorCode}};"> {{$statusData}} </td>
                                <td> </td>
                            </tr>
                        @endif

                        @if ($orderStatus == 'pl')
                            @foreach ($presentLeaveArr as $leaveKey => $leaveArr)
                                @if ($value['status'] == $leaveArr)
                                    <tr>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" >{{ ++$i }}</td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};" > {{ $item->emp_name }}</td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};"> {{ $item->emp_designation }}</td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};"> {{ $item->emp_department }}</td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};"> {{ $item->emp_branch }}</td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" > {{ !empty($value['on_duty']) ? $value['on_duty'] : '' }} </td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" > {{ !empty($value['off_duty']) ? $value['off_duty'] : '' }}</td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" > {{ !empty($value['clock_in']) ? $value['clock_in'] : '' }}</td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" > {{ !empty($value['clock_out']) ? $value['clock_out'] : '' }}</td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};" class="text-center" style="" >  {{ !empty($value['late_time']) ? $value['late_time'] : '' }}</td>
                                        <td  style="color:{{$colorCodeArr[$orderStatus]}};" > {{$statusData}} </td>
                                        <td> </td>
                                    </tr>

                                @endif
                            @endforeach
                        @endif

                       
                    @endforeach
                    

                </tr>
            @endforeach
            
        </tbody>
    </table>



    {{-- @include('../elements.signature.signatureSet', ['visible' => true]) --}}
    @include('elements.signature.approvalSet')

</div>

<script>
    
    $("#text_to").html('');
    $("#end_date_txt").html('');

    // Custom Date Formating Start
    var dateStringCustom = $("#start_date_txt").html();
    if (dateStringCustom != '') {
        // Parse the input date string into a Date object
        let dateParts = dateStringCustom.split('/');
        let day = parseInt(dateParts[0], 10);
        let month = parseInt(dateParts[1], 10) - 1; // Months are zero-based in JavaScript
        let year = parseInt(dateParts[2], 10);
        let inputDate = new Date(year, month, day);
        // Define an array of month names
        let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        // Define an array of day names
        let dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        // Get the formatted date
        let formattedDate = day + " " + monthNames[month] + " " + year + " (" + dayNames[inputDate.getDay()] + ")";

        // $("#reportTitleDiv").html("Daily Attendance Report - "+formattedDate);
        $("#reportTitleDiv").html("<strong>Daily Attendance Report - "+formattedDate+"</strong>");
    }
    // Custom Date Formating End
    
    function capitalizeWords(inputString) {
        return inputString.replace(/\b\w/g, function(match) {
            return match.toUpperCase();
        });
    }

    
</script>
