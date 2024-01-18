
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12"> 

    @php
        use App\Services\CommonService as Common;
    @endphp
    <style>
        .table>thead th {
            /* padding: 2px 5px 2px 5px; */
            padding: 2px;
        }

        .table > tbody td {
            /* padding: 2px 5px 2px 5px; */
            padding: 2px;
        }
    </style>

    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">

            <tr>
                
                <th width="2%" rowspan="{{0}}" class="text-center"  >SL</th>
                <th width="10%" rowspan="{{0}}">Employee [Code]</th>
                <th width="8%"  rowspan="{{0}}">Department</th>


                @foreach ($leaveConsumeTable as $leaveTypeName => $typeValues)
                    @if($leaveTypeName == 'Non Pay')
                        <th colspan="{{ count($typeValues) }}" >{{$leaveTypeName}}</th>
                    @else
                        <th colspan="{{ count($typeValues) * 4 }}" >{{$leaveTypeName}}</th>
                    @endif
                @endforeach

                <th width="6%" rowspan="{{0}}">TOTAL BALANCE</th>
            </tr>

            <tr>
                @foreach ($leaveConsumeTable as $leaveTypeName => $typeValues)
                    @foreach ($typeValues as $leaveName => $values)
                   
                        @if($leaveTypeName == 'Non Pay')
                            <th colspan="{{ (count($values) > 0) ? count($values) : 0 }}" rowspan="2"  >{{$leaveName}}</th>
                        @else
                            <th colspan="{{ count($values) }}" >{{$leaveName}}</th>
                        @endif
                    @endforeach
                @endforeach
            </tr>

            <tr>
                @foreach ($leaveConsumeTable as $leaveTypeName => $typeValues)
                    @foreach ($typeValues as $leaveName => $values)
                        @foreach ($values as $valuesName => $finalVal)
                        
                            @if($leaveTypeName == 'Non Pay')
                                
                            @else
                                <th colspan="{{ 0 }}" >{{$valuesName}}</th>
                            @endif
                           
                        @endforeach
                    @endforeach
                @endforeach
            </tr>
           
        </thead>

        <tbody>

            @php
                $i = 0;
            @endphp

            @foreach ($employeeData as $employee)
                @php

                // dd($employee, $leave_bypass_arr);/

                    if (in_array($employee->designation_id, $leave_bypass_arr)){
                        continue;
                    }

                    $gender = $employee->gender;
                    $leaveInformation = !empty($employee->leaveConsumeData) ? $employee->leaveConsumeData : [];
                    $i++;
                    $totalBalance = 0;
                @endphp

                <tr>
                    <td class="text-center" >{{$i}}</td>
                    <td> {{$employee->emp_name}} </td>
                    <td> {{$employee->emp_department}} </td>

                    @foreach ($leaveInformation as $leaveTypeName => $typeValues)
                        @foreach ($typeValues as $leaveName => $values)
                            @foreach ($values as $valuesName => $finalVal)
                                @php
                                if ($leaveTypeName == 'Parental Leave') {
                                    if ($gender == 'Male' && $leaveTypeName == 'Parental Leave' && $leaveName == 'ML') {
                                        $finalVal = 0;
                                    }elseif ($gender == 'Female' && $leaveTypeName == 'Parental Leave' && $leaveName == 'PL'){
                                        $finalVal = 0;
                                    }
                                }
                                @endphp

                                <td class="text-center"  > {{Common::getDecimalValue($finalVal)}} </td>
                                @php
                                    if ($valuesName == 'Balance') {
                                        $totalBalance += $finalVal;
                                    }
                                @endphp
                            @endforeach
                        @endforeach
                    @endforeach

                    <td class="text-center"  > {{ ($totalBalance < 0) ? '('.Common::getDecimalValue($totalBalance).')' : Common::getDecimalValue($totalBalance) }} </td> 
                </tr>

            @endforeach
        
        </tbody>


    </table>


    <span style="font-size:10px;font-style:italic;color:#000">
        <b>NB: </b>
        @foreach ($leave_cat as $lc)
        <b>{{ $lc->short_form }}=</b>{{ $lc->name }},
        @endforeach
    </span>

    @include('elements.signature.approvalSet')

</div>
