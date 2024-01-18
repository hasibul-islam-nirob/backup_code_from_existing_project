<table class="table w-full table-bordered table-striped table-responsive">
    <thead class="text-center">
        <tr>
            <th rowspan="3">SL</th>
            <th rowspan="3" width="10%">Employee</th>
            <th rowspan="3">Branch</th>
            <th rowspan="3">Designation</th>
            @foreach ($monthDates as $date)
                <th colspan="2">{{(new DateTime($date))->format('d') . ' ' . (new DateTime($date))->format('D') }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($monthDates as $date)
                <th>In</th>
                <th>Out</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($dataSet as $item)
            <tr>
                <td class="text-center">{{ $item['sl'] }}</td>
                <td>{!! $item['emp_name'] !!}</td>
                <td>{{ $item['branch'] }}</td>
                <td>{{ $item['designation'] }}</td>
                @foreach ($monthDates as $date)
                    <td class="text-center" style="{{ in_array($date, $holidays) ? 'background:#d1c6c6' : '' }}">{!! $item[$date]['in'] !!}</td>
                    <td class="text-center" style="{{ in_array($date, $holidays) ? 'background:#d1c6c6' : '' }}">{!! $item[$date]['out'] !!}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>