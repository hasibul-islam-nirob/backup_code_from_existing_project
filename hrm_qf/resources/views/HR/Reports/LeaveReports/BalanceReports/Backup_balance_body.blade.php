
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

    <span style="font-size:10px;font-style:italic;color:#000">
        <b>NB: </b>
        @foreach ($leave_cat as $lc)
        <b>{{ $lc->short_form }}=</b>{{ $lc->name }},
        @endforeach
    
    </span>

    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">

            <tr>
                @php
                //    dd($employeeData);
                $leaveConsumeData = !empty($employeeData) ?  $employeeData[0]->leaveConsumeData : [];
                    
                    if ($searchByLeaveType == 1) {
                        $rootRospan = 3;
                    }else{
                        $rootRospan = count($leaveConsumeData);
                    }
                @endphp

                <th width="2%" rowspan="{{$rootRospan}}" class="text-center"  >SL</th>
                <th width="10%" rowspan="{{$rootRospan}}">Employee [Code]</th>
                <th width="8%"  rowspan="{{$rootRospan}}">Department</th>

                @php
                    // dd($searchByLeaveType, $leaveConsumeData);
                @endphp

                @foreach ($leaveConsumeData as $leaveTypeName => $typeValues)

                    @if (empty($leaveConsumeData[$leaveTypeName]))
                    {{-- <th rowspan="3" >{{$leaveTypeName}}</th> --}}
                    @else

                        @php
                            if ($searchByLeaveType == 1) {
                                $topColspan = count($typeValues) * 4;
                            }else{
                                $topColspan = count($typeValues) * count($leaveConsumeData);
                            }

                            
                        @endphp

                    <th colspan="{{$topColspan}}" >{{$leaveTypeName}}</th>
                    @endif

                @endforeach

                <th width="6%" rowspan="3">TOTAL BALANCE</th>
            </tr>

            <tr>
                @foreach ($leaveConsumeData as $leaveTypeName => $typeValues)
                    @foreach ($typeValues as $leaveNames => $leavevalues)
                        <th colspan="{{count($leavevalues)}}" > {{$leaveNames}} </th>
                    @endforeach
                @endforeach
            </tr>

            <tr>
                @foreach ($leaveConsumeData as $leaveTypeName => $typeValues)
                    @foreach ($typeValues as $leaveNames => $leavevalues)

                        @foreach ($leavevalues as $name => $finalValues)
                            <th> {{$name}} </th>
                        @endforeach
                    @php
                        
                    @endphp
                        
                    @endforeach
                @endforeach
            </tr>

        </thead>


        <tbody>

            @php
                $i = 0;
            @endphp

            @foreach ($employeeData as $item)
                @php

                    if (in_array($item->id, $leave_bypass_arr)){
                        continue;
                    }

                    $leaveInformation = !empty($item->leaveConsumeData) ? $item->leaveConsumeData : [];
                    $i++;
                    $totalBalance = 0;
                @endphp

                <tr>
                    <td class="text-center" >{{$i}}</td>
                    <td> {{$item->emp_name}} </td>
                    <td> {{$item->emp_department}} </td>

                    @foreach ($leaveInformation as $key1 => $value1)
                        @foreach ($value1 as $key2 => $value2)
                            @foreach ($value2 as $key3 => $value3)
                                @if (empty($value3))
                                    <td class="text-center"  > {{Common::getDecimalValue(0)}} </td>
                                @else
                                <td class="text-center"  > 
                                    {{ ($value3 < 0) ? '('.Common::getDecimalValue($value3).')' : Common::getDecimalValue($value3) }} 
                                </td>
                                @endif
                            @endforeach

                            @php
                                $totalBalance += $value2['Balance'];
                            @endphp
                        @endforeach

                        @if (empty($leaveInformation[$key1]))
                        {{-- <td class="text-center"  > {{Common::getDecimalValue(0)}}  </td> --}}
                        @endif


                    @endforeach

                    <td class="text-center"  > {{ ($totalBalance < 0) ? '('.Common::getDecimalValue($totalBalance).')' : Common::getDecimalValue($totalBalance) }} </td>
                </tr>

                @php
                    // $totalBalance = 0;
                @endphp

            @endforeach
        
        </tbody>
    </table>

    @include('elements.signature.approvalSet')

</div>
