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

@php
    //dd($lv_bal);
    
    $month[1] = 'January';
    $month[2] = 'February';
    $month[3] = 'March';
    $month[4] = 'April';
    $month[5] = 'May';
    $month[6] = 'June';
    $month[7] = 'July';
    $month[8] = 'August';
    $month[9] = 'September';
    $month[10] = 'October';
    $month[11] = 'November';
    $month[12] = 'December';

    $req_month = [];
    $flag = 0;
    
    for($m = $m_start; $m <= 12; $m++){
        $req_month[$m] = $month[$m];
        if ($m == $m_end) {
            $flag = 1;
            break;
        }
    }

    if ($flag == 0) {
        for($m = 1; $m <= $m_end; $m++){
            $req_month[$m] = $month[$m];
        }
    }

@endphp


<table class="table w-full table-hover table-bordered table-striped">
    <thead class="text-center sticky-head">
        <tr>
            <th rowspan="3">Sl</th>
            <th rowspan="3">Employee Name</th>
            <th rowspan="3">Designation</th>
            <th colspan="{{ count($leave_cat) }}">Probalable Maximum</th>
            <th colspan="{{ (count($req_month) + 2) *  count($leave_cat)}}">Leave Consumed</th>
        </tr>

        <tr>
            @foreach ($leave_cat as $lc)
                <th rowspan="2">{{ $lc->short_form }}</th>
            @endforeach

            @foreach ($req_month as $key => $rm)
                <th colspan="{{ count($leave_cat) }}">{{ $rm }}</th>
            @endforeach

            <th colspan="{{ count($leave_cat) }}">Total Consumed Leave</th>
            <th colspan="{{ count($leave_cat) }}">Remaining Maximum Leave</th>
        </tr>

        <tr>
            @foreach ($req_month as $key => $rm)
            @foreach ($leave_cat as $lc)
                <th>{{ $lc->short_form }}</th>
            @endforeach
            @endforeach

            @foreach ($leave_cat as $lc)
                <th>{{ $lc->short_form }}</th>
            @endforeach

            @foreach ($leave_cat as $lc)
                <th>{{ $lc->short_form }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @php
            $total = [];
        @endphp
        @foreach ($data as $empId => $info)

        @php
            foreach ($leave_cat as $lc) {
                $total[$empId][$lc->id] = 0;
            }
        @endphp

        <tr>
            <td>{{ $empId }}</td>
            <td>{{ $info['emp_info']['name'] }}</td>
            <td>{{ $info['emp_info']['designation_name'] }}</td>
            @foreach ($leave_cat as $lc)
                <td>{{ (isset($max_leave[$lc->id][$info['emp_info']['rec_type_id']]) ? $max_leave[$lc->id][$info['emp_info']['rec_type_id']][0]->allocated_leave : '-')  }}</td>
            @endforeach

            @foreach ($req_month as $monNo => $rm)
                @foreach ($leave_cat as $lc)

                    @if (isset($info['consume_info'][$monNo][$lc->id]))
                        
                        <td>{{ $info['consume_info'][$monNo][$lc->id] }}</td>
                        @php
                            $total[$empId][$lc->id] += $info['consume_info'][$monNo][$lc->id];
                        @endphp
                    @else
                    <td>0</td>
                    @endif

                @endforeach
            @endforeach

            @foreach ($leave_cat as $lc)
                <td>{{ (isset($total[$empId][$lc->id])) ? $total[$empId][$lc->id] : '' }}</td>
            @endforeach

            @foreach ($leave_cat as $lc)
            @if (isset($total[$empId][$lc->id]) && isset($max_leave[$lc->id][$info['emp_info']['rec_type_id']]))
            <td>{{ $max_leave[$lc->id][$info['emp_info']['rec_type_id']][0]->allocated_leave -  $total[$empId][$lc->id]}}</td>
            @else
            <td>-</td>
            @endif
            @endforeach
        </tr>
        @endforeach
        
    </tbody>

</table>