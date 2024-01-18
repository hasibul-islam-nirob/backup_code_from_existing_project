
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">

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
        <b>A=</b>Approved,
        <b>P=</b>Pending,
    </span>

    @php

        
        // dd($searchByLeaveType, $allLeaveCategoryData);
        if ($searchByLeaveType == null) {
            $isNull = $allLeaveCategoryData->groupBy('short_form');
            $countColspan = count($isNull);
        }
        elseif ($searchByLeaveType == 1) {
            $isPay = $allLeaveCategoryData->where('leave_type_uid', 1)->groupBy('short_form');
            $countColspan = count($isPay);

        }elseif ($searchByLeaveType == 2) {
            $isNonPay = $allLeaveCategoryData->where('leave_type_uid', 2)->groupBy('short_form');
            $countColspan = count($isNonPay);
        }
        elseif ($searchByLeaveType == 3) {
            $isNonPay = $allLeaveCategoryData->where('leave_type_uid', 3)->groupBy('short_form');
            $countColspan = count($isNonPay);
        }
        elseif ($searchByLeaveType == 4) {
            $isNonPay = $allLeaveCategoryData->where('leave_type_uid', 4)->groupBy('short_form');
            $countColspan = count($isNonPay);
        }
        else{
            $countColspan = count($allLeaveCategoryData);
        }

        $forOpening = 1;
        $opening = (!empty($employeeData) && count($employeeData) > 0) ? $employeeData[0]->opening : [];
        foreach ($opening as $key => $value) {
            $forOpening += count($value);
        }

        // dd($countColspan, $leave_cat);
        // dd($opening, $allLeaveCategoryData);
    @endphp


    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">
            <tr>

                <th width="2%"  rowspan="4" class="text-center" >SL</th>
                <th width="12%"  rowspan="4">Employee [Code]</th>
                <th width="11%"  rowspan="4">Department</th>
                <th width="25%"  colspan="{{ $countColspan }}">Opening</th>
                <th width="25%"  colspan="{{ $countColspan }}">During Period</th>
                <th width="25%"  colspan="{{ $countColspan }}">Closing</th>
            </tr>



            <tr>
                @for ($i=0; $i<3; $i++)
                @foreach ($opening as $openingKey => $openingData)
                    @if (count($openingData) > 0)
                        <th colspan="{{count($openingData)}}">{{ $openingKey }}</th>
                    @elseif($openingKey == "Non Pay" && count($openingData) > 0)
                        <th rowspan="2" colspan="1">{{ $openingKey }}</th>
                    @endif
                @endforeach
                @endfor
            </tr>

            <tr>
                @for ($i=0; $i<3; $i++)
                @foreach ($opening as $openingKey => $openingData)
                    @foreach ($openingData as $oKey => $oData)
                        <th colspan="">{{ $oKey }}</th>

                        @php
                            // dd($openingData, $oKey, $oData);
                        @endphp
                    @endforeach
                @endforeach
                @endfor
            </tr>

        </thead>

        <tbody>

            @php
                $i = 0;
            @endphp

            @foreach ($employeeData as $employee)
                @php

                    if (in_array($employee->designation_id, $leave_bypass_arr)){
                        continue;
                    }

                    $i++;
                    $opening = !empty($employee->opening) ? $employee->opening : [];
                    $during = !empty($employee->during) ? $employee->during : [];
                    $closing = !empty($employee->closing) ? $employee->closing : [];
                @endphp

                <tr>
                    <td class="text-center" >{{$i}}</td>
                    <td> {{$employee->emp_name}} </td>
                    <td> {{$employee->emp_department}} </td>

                    @foreach ($opening as $opnKey => $openData)

                        @if (!empty($openData))
                            @foreach ($openData as $singOData)
                                @if (!empty($singOData))
                                    <td  class="text-center"  > {{$singOData}} </td>
                                @else
                                    <td  class="text-center"  > - </td>
                                @endif
                            @endforeach

                        @elseif ( $opnKey == "Non Pay" && count($opening["Non Pay"]) > 0)
                            <td  class="text-center"  > - </td>
                        @endif
                    @endforeach


                    @foreach ($during as $duKey => $duData)

                        @if (!empty($duData))
                            @foreach ($duData as $singDuData)
                                @if (!empty($singDuData))
                                    <td  class="text-center"  > {{$singDuData}} </td>
                                @else
                                    <td  class="text-center"  > - </td>
                                @endif
                            @endforeach

                        @elseif ( $duKey == "Non Pay" && count($during[$duKey]) > 0)
                            <td  class="text-center"  > - </td>
                        @endif

                    @endforeach

                    @foreach ($closing as $clKey => $clData)

                        @if (!empty($clData))
                            @foreach ($clData as $singClData)
                                @if (!empty($singClData))
                                    <td  class="text-center"  > {{$singClData}} </td>
                                @else
                                    <td  class="text-center"  > - </td>
                                @endif
                            @endforeach
                        @elseif ( $clKey == "Non Pay" && count($closing[$clKey])  > 0)
                            <td  class="text-center"  > - </td>
                        @endif

                    @endforeach

                </tr>

            @endforeach

        </tbody>
    </table>

    @include('elements.signature.approvalSet')

</div>
