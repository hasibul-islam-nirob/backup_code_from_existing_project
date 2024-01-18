@php
    // dd($employeeData);

    $statusMapping = [
                    'all' => 'All',
                    'p' => 'Present (Regular)',
                    'lp' => 'Present (Late)',
                    'mp' => 'Present (Movement)',
                    'pl' => 'Present (Leave)',
                    'a' => 'Absent',
                    'al' => 'Annual Leave',
                    'sl' => 'Sick Leave',
                    'cl' => 'Casual Leave'
                ];
    $status = $statusMapping[$orderStatus];

@endphp


<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">

    <table class="table table-bordered sticky-table clsDataTable" id="tableID">
        <thead class="text-center sticky-head" id="header_col">
            <tr>
                <th width="2%">SL</th>
                <th >Date</th>
                <th >On Duty</th>
                <th >Off Duty</th>
                <th >Clock In</th>
                <th >Clock Out</th>
                <th >Late In <small>(H:M)</small> </th>
                <th >Early Out <small>(H:M)</small></th>
                {{-- <th >Absent</th> --}}
                <th >Over Time <small>(H:M)</small></th>
                <th >Work Time <small>(H:M)</small></th>
                <th width="9%" >Status</th>
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
                    $statusData = !empty($value['status']) ? $value['status'] : ' ';

                @endphp
              
                    <tr>
                        <tr style="background: #cac8c8;">
                            <td colspan="12">
                                &nbsp;&nbsp; <strong> {{ $item->emp_name }}</strong>  &nbsp;&nbsp; ({{ $item->emp_designation }}) &nbsp;&nbsp; ({{ $item->emp_department }})
                            </td>
                        </tr>

                        @foreach ($empStatus as $date => $value )
                            @php

                                $statusData = !empty($value['status']) ? $value['status'] : ' ';
                            @endphp
                            @if(($value['status'] == $status && $status != 'All'))
                            <tr>
                                <td class="text-center" >{{ ++$i }}</td>
                                <td class="px-4" > {{ date("d-M Y", strtotime($date)) }} - {{ date("D", strtotime($date)) }} </td>
                                <td class="text-center" > {{ !empty($value['on_duty']) ? $value['on_duty'] : '' }} </td>
                                <td class="text-center" > {{ !empty($value['off_duty']) ? $value['off_duty'] : '' }}</td>

                                <td class="text-center" > {{ !empty($value['clock_in']) ? $value['clock_in'] : '' }}</td>
                                <td class="text-center" > {{ !empty($value['clock_out']) ? $value['clock_out'] : '' }}</td>

                                <td class="text-center" style="" > 
                                    {{ !empty($value['late_time']) ? $value['late_time'] : '' }}
                                </td>
                                <td class="text-center" > {{ !empty($value['early_out']) ? $value['early_out'] : '' }}</td>
                                <td class="text-center" > {{ !empty($value['ot_time']) ? $value['ot_time'] : '' }}</td>
                                
                                <td class="text-center" > {{ !empty($value['work_time']) ? $value['work_time'] : '' }}</td>
                                <td  > {{$statusData}} </td>
                            </tr>
                            @elseif($status == 'All')
                            <tr>
                                <td class="text-center" >{{ ++$i }}</td>
                                <td class="px-4" > {{ date("d-M Y", strtotime($date)) }} - {{ date("D", strtotime($date)) }}</td>
                                <td class="text-center" > {{ !empty($value['on_duty']) ? $value['on_duty'] : '' }} </td>
                                <td class="text-center" > {{ !empty($value['off_duty']) ? $value['off_duty'] : '' }}</td>

                                <td class="text-center" > {{ !empty($value['clock_in']) ? $value['clock_in'] : '' }}</td>
                                <td class="text-center" > {{ !empty($value['clock_out']) ? $value['clock_out'] : '' }}</td>

                                <td class="text-center" style="" > 
                                    {{ !empty($value['late_time']) ? $value['late_time'] : '' }}
                                </td>
                                <td class="text-center" > {{ !empty($value['early_out']) ? $value['early_out'] : '' }}</td>
                                <td class="text-center" > {{ !empty($value['ot_time']) ? $value['ot_time'] : '' }}</td>
                                
                                <td class="text-center" > {{ !empty($value['work_time']) ? $value['work_time'] : '' }}</td>
                                <td  > {{$statusData}} </td>
                            </tr>
                            @endif
                    </tr>
                
                    @endforeach
                    

                
            @endforeach
            
        </tbody>
    </table>



    {{-- @include('../elements.signature.signatureSet', ['visible' => true]) --}}
    @include('elements.signature.approvalSet')

</div>
