<table class="table w-full table-bordered table-striped table-responsive">
    <thead class="text-center">
        <tr>
            <th rowspan="2">SL</th>
            <th rowspan="2" width="10%">Employee</th>
            <th rowspan="2">Branch</th>
            <th rowspan="2">Designation</th>
            <th colspan="{{ $countDays }}">Days</th>
        </tr>
        <tr>
            @foreach ($monthDates as $date)
                <th>{{(new DateTime($date))->format('d') . ' ' . (new DateTime($date))->format('D') }}</th>
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
                    <td class="text-center" style="{{ in_array($date, $holidays) ? 'background:#D10921' : '' }}">{!! $item[$date] !!}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>