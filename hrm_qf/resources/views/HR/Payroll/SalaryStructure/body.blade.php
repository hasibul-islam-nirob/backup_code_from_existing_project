@php
    $benA = $allowance->where('benifit_type_uid', 1);
    $benB = $allowance->where('benifit_type_uid', 2);
    $benC = $allowance->where('benifit_type_uid', 3);

    $totBen = count($allowance->groupBy('benifit_type_uid'));


  
    $deductionColspan = count($deductionDataArr);
    $deductionStyle = '';
    
    if ($deductionColspan == 0) {
        $deductionStyle = 'display:none;';
    }else{
        $deductionStyle = '';
    }
    
    // dd($deductionDataArr, $deductionColspan);
@endphp

<style>
    @media print {
        th{
            font-weight: normal;
        }
    }
</style>




<div class="row">

    <div class="col-xl-4 col-lg-6 col-sm-6 col-md-6 col-6">
        <table class="table table-hover table-bordered table-striped">
            <tbody>
                <tr style="background-color: #A5A5A5;">
                    <td rowspan="{{ count($incrementData) + 1 }}" align="center">Salary Structure</td>
                    <td align="center">Basic</td>
                    <td align="center">Rate Of Increment</td>
                    <td align="center">Increment Amount</td>
                    <td align="center">No Of Increment</td>
                    <td align="center">Total Basic</td>
                </tr>
                @foreach ($incrementData as $row)
                    <tr style="background-color: #A5A5A5;">
                        <td>{{ $headerData['basic'] }}</td>
                        <td align="middle">{{ $row->inc_percentage }}</td>
                        <td align="right">{{ $row->amount }}</td>
                        <td align="middle">{{ $row->no_of_inc }}</td>
                        <td align="center">{{ $data[count($data)]['total_basic'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @php
    // dd($headerData);
    @endphp

    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6 col-6">
        <table class="table table-hover table-bordered table-striped clsDataTable">
            <tbody>
                <tr style="background-color: #A5A5A5;">
                    <td rowspan="2" align="center">{{ $headerData['recruitment_types'] }}</td>
                    <td align="center">Grade - {{ $headerData['grade'] }}</td>
                    <td rowspan="2" align="center">{{ $headerData['designations'] }}</td>
                </tr>
                <tr style="background-color: #A5A5A5;">
                    <td align="center">Level - {{ $headerData['level'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<br>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead>
                <tr>
                    <th rowspan="2" style="width:5%; background-color: #81b847;">Step</th>
                    <th rowspan="2" style="background-color: #81b847;">Basic</th>
                    <th rowspan="2" style="background-color: #81b847;">Increment</th>
                    <th rowspan="2" style="background-color: #81b847;">Total Basic</th>

                    @if (count($benA) > 0)
                    <th colspan="{{ count($benA) + 1 }}" style="background-color: #41a4c9;">Benefit Type- A</th>
                    @endif
                    
                    @if (count($benB) > 0)
                    <th colspan="{{ count($benB) + 1 }}" style="background-color: #a396b1;">Benefit Type- B</th>
                    @endif

                    @if (count($benC) > 0)
                    <th colspan="{{ count($benC) + 1 }}" style="background-color: #D99795;">Benefit Type- C</th>
                    @endif
                       
                    <th colspan="{{ count($deductionDataArr) + 1 }}" style="background-color: #e0995f; {{$deductionStyle}}">Deduction</th>
                    <th colspan="{{ $totBen }}" style="background-color: #81b847;">Net Salary</th>
                </tr>
                <tr>
                    

                    {{-- Ben A --}}
                    @if (count($benA) > 0)
                        @foreach ($benA as $b)
                            <th style="background-color: #41a4c9;">{{ $b->short_name }}</th>
                        @endforeach
                    <th style="background-color: #41a4c9;">Gross Salary</th>
                    @endif
                    
                    {{-- Ben B --}}
                    @if (count($benB) > 0)
                        @foreach ($benB as $b)
                            <th style="background-color: #a396b1;">{{ $b->short_name }}</th>
                        @endforeach
                    <th style="background-color: #a396b1;">Gross Salary</th>
                    @endif

                    @if (count($benC) > 0)
                        @foreach ($benC as $b)
                            <th style="background-color: #D99795;">{{ $b->short_name }}</th>
                        @endforeach
                    <th style="background-color: #D99795;">Gross Salary</th>
                    @endif

                    
                    @foreach ($deductionDataArr as $itemName => $itemValue)
                        
                        @if ($itemName == 'PF')
                        <th style="background-color: #e0995f;" title="PF - Provident Fund">PF</th> 
                        @endif

                        @if ($itemName == 'WF')
                        <th style="background-color: #e0995f;" title="WF - Welfare Fund">WF</th> 
                        @endif

                        @if ($itemName == 'EPS')
                        <th style="background-color: #e0995f;" title="EPS - Employee Pension Scheme">EPS</th> 
                        @endif

                        @if ($itemName == 'OSF')
                        <th style="background-color: #e0995f;" title="OSF - ....">OSF</th> 
                        @endif

                        @if ($itemName == 'INC')
                        <th style="background-color: #e0995f;">INC</th> 
                        @endif

                    @endforeach
                    


                    {{-- <th style="background-color: #e0995f;">PF</th>
                    <th style="background-color: #e0995f;">WF</th>
                    <th style="background-color: #e0995f;">EPS</th> --}}
                    <th  style="background-color: #e0995f; {{$deductionStyle}}">Total</th>

                    @if (count($benA) > 0)
                    <th style="background-color: #41a4c9;">Type- A</th>
                    @endif
                    
                    @if (count($benB) > 0)
                    <th style="background-color: #a396b1;">Type- B</th>
                    @endif

                    @if (count($benC) > 0)
                    <th style="background-color: #D99795;">Type- C</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td align="center">{{ $row['year'] }}</td>
                        <td align="right">{{ $row['basic'] }}</td>
                        <td align="right">{{ $row['increment'] }}</td>
                        <td align="right">{{ $row['total_basic'] }}</td>

                        {{-- Ben A --}}
                        @if (count($benA) > 0)
                            @foreach ($benA as $b)
                                <td align="right">{{ $row['allowance'][1][$b->id] }}</td>
                            @endforeach
                        <td align="right">{{ $row['total_gross_a'] }}</td>
                        @endif
                        
                        {{-- Ben B --}}
                        @if (count($benB) > 0)
                            @foreach ($benB as $b)
                            <td align="right">{{ $row['allowance'][2][$b->id] }}</td>
                            @endforeach
                        <td align="right">{{ $row['total_gross_b'] }}</td>
                        @endif

                        {{-- Ben C --}}
                        @if (count($benC) > 0)
                            @foreach ($benC as $b)
                            <td align="right">{{ $row['allowance'][3][$b->id] }}</td>
                            @endforeach
                        <td align="right">{{ $row['total_gross_c'] }}</td>
                        @endif

                        @foreach ($deductionDataArr as $itemName => $itemValue)
                            
                            @if ($itemName == 'PF')
                            <td align="right">  {{ !empty($row['deduction'][$itemName]) ? $row['deduction'][$itemName] : 0 }} </td>
                            @endif

                            @if ($itemName == 'WF')
                            <td align="right">  {{ !empty($row['deduction'][$itemName]) ? $row['deduction'][$itemName] : 0 }} </td>
                            @endif

                            @if ($itemName == 'EPS')
                            <td align="right"> {{ !empty($row['deduction'][$itemName]) ? $row['deduction'][$itemName] : 0 }} </td>
                            @endif

                            @if ($itemName == 'OSF')
                            <td align="right"> {{ !empty($row['deduction'][$itemName]) ? $row['deduction'][$itemName] : 0 }} </td>
                            @endif
    
                            @if ($itemName == 'INC')
                            <td align="right"> {{ !empty($row['deduction'][$itemName]) ? $row['deduction'][$itemName] : 0 }} </td>
                            @endif
                        @endforeach

                        <td align="right" style="{{$deductionStyle}}"> {{ $row['totalDeduction'] }} </td>
                        
                        @if (count($benA) > 0)
                        <td align="right">{{ $row['total_gross_a'] - $row['totalDeduction']}}</td>
                        @endif
                        
                        @if (count($benB) > 0)
                        <td align="right">{{ $row['total_gross_b'] - $row['totalDeduction']}}</td>
                        @endif

                        @if (count($benC) > 0)
                        <td align="right">{{ $row['total_gross_c'] - $row['totalDeduction']}}</td>
                        @endif

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>