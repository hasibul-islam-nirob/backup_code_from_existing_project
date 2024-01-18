
@php

    $benA = $allowanceInfo->where('benifit_type_uid', 1);
    $benB = $allowanceInfo->where('benifit_type_uid', 2);
    $benC = $allowanceInfo->where('benifit_type_uid', 3);
    $totBen = count($allowanceInfo->groupBy('benifit_type_uid'));
    $allowanceInfoArr = $allowanceInfo->pluck('short_name','id')->toArray();

    // dd($allowanceInfoArr);

    // dd($deductionDataArr);
    $deductionColspan = count($deductionDataArr);
    $deductionStyle = '';
    
    if ($deductionColspan == 0) {
        $deductionStyle = 'display:none;';
    }else{
        $deductionStyle = '';
    }

@endphp



<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12 ">
    <table class="table table-bordered sticky-table clsDataTable" id="tableID" >
        <thead class="text-center sticky-head" id="header_col">
            <tr>
                <th rowspan="2">SL</th>
                <th rowspan="2" width="10%">Name</th>
                <th rowspan="2" width="5%">Dept</th>
                <th rowspan="2" width="8%">Desi</th>
                <th rowspan="2">Grade</th>
                <th rowspan="2">Level</th>
                <th rowspan="2">Step</th>
                <th rowspan="2">Basic Salary</th>

                @if (count($benA) > 0)
                <th colspan="{{ count($benA) + 1 }}" >Benefit Type- A</th>
                @endif
                
                @if (count($benB) > 0)
                <th colspan="{{ count($benB) + 1 }}" >Benefit Type- B</th>
                @endif

                @if (count($benC) > 0)
                <th colspan="{{ count($benC) + 1 }}" >Benefit Type- C</th>
                @endif
                <th colspan="{{ count($deductionDataArr)  }}" >Self Deduction</th>

                <th rowspan="2" width="5%">Net Payable Salary</th>
            </tr>

            {{-- Ben A --}}
            @if (count($benA) > 0)
                @foreach ($benA as $b)
                    <th>{{ $b->short_name }}</th>
                @endforeach
                <th>Gross Salary</th>
            @endif
            
            {{-- Ben B --}}
            @if (count($benB) > 0)
                @foreach ($benB as $b)
                    <th >{{ $b->short_name }}</th>
                @endforeach
                <th >Gross Salary</th>
            @endif

            @if (count($benC) > 0)
                @foreach ($benC as $b)
                    <th>{{ $b->short_name }}</th>
                @endforeach
                <th>Gross Salary</th>
            @endif

            @foreach ($deductionDataArr as $itemName => $itemValue)
                        
                @if ($itemName == 'PF')
                <th title="PF - Provident Fund">PF</th> 
                @endif

                @if ($itemName == 'WF')
                <th title="WF - Welfare Fund">WF</th> 
                @endif

                @if ($itemName == 'EPS')
                <th title="EPS - Employee Pension Scheme">EPS</th> 
                @endif

                @if ($itemName == 'OSF')
                <th title="OSF - ....">OSF</th> 
                @endif

                @if ($itemName == 'INC')
                <th >INC</th> 
                @endif

            @endforeach
            <th >Total</th> 
            
           
        </thead>

        <tbody>

            @php
                // $salaryData
                // dd($salaryData);
                $i = 1;
            @endphp

            @foreach ($salaryData as $item)
                @php
                    $salaryDataDetailsEncoded = $item->salary_details;
                    $salaryDataDetailsDecoded = json_decode($salaryDataDetailsEncoded);
                    $salaryDataDetails = $salaryDataDetailsDecoded[0];
                    // dd($item, $salaryDataDetails);
                @endphp

                @foreach ($salaryDataDetails as $salaryData)
                    @php
                        // dd($salaryData);
                    @endphp
                    <tr>
                        <td>{{$i++}}</td>
                        <td> {{$salaryData->emp_name}} </td>
                        <td> {{$salaryData->department_name}} </td>
                        <td> {{$salaryData->designation_name}} </td>
                        <td align="right"> {{$salaryData->grade}} </td>
                        <td align="right"> {{$salaryData->level}} </td>
                        <td align="right"> {{$salaryData->step}} </td>
                        <td align="right"> {{$salaryData->basic_salary}} </td>
                        
                        @php
                            $basicSalary = $salaryData->basic_salary;
                            $benifitInfo = $salaryData->benefit_info;
                            $benifitInfoArrTemp = [];
                            foreach ($benifitInfo as $key => $value) {
                                $explodeKey = explode("-", $key);
                                $benifitKey = isset($explodeKey[2]) ? $explodeKey[2] : null;
                                if (isset($allowanceInfoArr[$benifitKey])) {
                                    $benifitInfoArrTemp[$benifitKey] = $value;

                                }else{
                                    $newKey = $explodeKey[0];
                                    $benifitInfoArrTemp[$newKey] = $value;
                                }
                            }
                            // dd($benifitInfo, $benifitInfoArrTemp);
                        @endphp


                        {{-- Ben A --}}
                        @if (count($benA) > 0)
                            @php
                                $totalBenA = 0;
                            @endphp
                            @foreach ($benA as $b)
                                @if (isset($benifitInfoArrTemp[$b->id]))
                                    @php
                                        $totalBenA += $benifitInfoArrTemp[$b->id];
                                    @endphp

                                    <td align="right">{{$benifitInfoArrTemp[$b->id]}}</td>
                                @else
                                    <td align="right">-</td>
                                @endif
                            @endforeach
                            <td align="right">{{$totalBenA + $basicSalary}}</td>
                        @endif
                        
                        {{-- Ben B --}}
                        @if (count($benB) > 0)
                            @php
                                $totalBenB = 0;
                            @endphp
                            @foreach ($benB as $b)
                                @if (isset($benifitInfoArrTemp[$b->id]))
                                    @php
                                        $totalBenB += $benifitInfoArrTemp[$b->id];
                                    @endphp
                                    <td align="right">{{$benifitInfoArrTemp[$b->id]}}</td>
                                @else
                                    <td align="right">-</td>
                                @endif
                            @endforeach
                            <td align="right">{{$totalBenB + $totalBenA + $basicSalary}}</td>
                        @endif

                        {{-- Ben C --}}
                        @if (count($benC) > 0)
                            @php
                                $totalBenC = 0;
                            @endphp
                            @foreach ($benC as $b)
                                @if (isset($benifitInfoArrTemp[$b->id]))
                                    @php
                                        $totalBenC += $benifitInfoArrTemp[$b->id];
                                    @endphp
                                    <td align="right">{{$benifitInfoArrTemp[$b->id]}}</td>
                                @else
                                    <td align="right">-</td>
                                @endif
                            @endforeach
                            <td align="right">{{$totalBenC + $totalBenB + $totalBenA + $basicSalary}}</td>
                        @endif
                        
                        @if ('Self_Deduction')
                            @php
                                // dd($salaryData);
                                $pf = $salaryData->pf_self;
                                $wf = $salaryData->wf_self_non_refundable + $salaryData->wf_self_refundable;
                                $eps = $salaryData->eps;
                                $osf = $salaryData->osf_self;
                                $inc = $salaryData->insurance_self;
                                $totalDeduction = $pf + $wf + $eps + $osf + $inc;

                                $totalSalary = $totalBenC + $totalBenB + $totalBenA + $basicSalary;
                                $netPayableSalary = $totalSalary - $totalDeduction;
                            @endphp

                                @foreach ($deductionDataArr as $itemName => $itemValue)
                                                                
                                    @if ($itemName == 'PF')
                                        <td align="right"> {{$pf}} </td>
                                    @endif

                                    @if ($itemName == 'WF')
                                        <td align="right"> {{$wf}} </td>
                                    @endif

                                    @if ($itemName == 'EPS')
                                        <td align="right"> {{$eps}}</td>
                                    @endif

                                    @if ($itemName == 'OSF')
                                        <td align="right"> {{$osf}} </td>
                                    @endif

                                    @if ($itemName == 'INC')
                                        <td align="right"> {{$inc}} </td>
                                    @endif
                                @endforeach
                                <td align="right" > {{ $totalDeduction }} </td>
                        @endif
                                <td align="right" > {{ $netPayableSalary }} </td>

                    </tr>
                @endforeach
                
            @endforeach
            
        </tbody>
    </table>

 
    @include('../elements.signature.signatureSet', ['visible' => false])
</div>

<style>
    @media print {
        .d-print-text-dark {
            color: #000;
        }
    }
</style>


<script>
    $(document).ready(function(event){

        if($('#appl_status option:selected').text() === "All") {
            $('.hideColumn').show();
        }
        else {
            // $('.hideColumn').hide();
        }
    });
    
</script>