<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">
    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">
            <tr>
                <th style="width:4%;" rowspan="2">SL</th>
                <th rowspan="2">Branch Name</th>
                @foreach ($designationData as $designation)

                    @if ($designation->uid == '2')
                        <th colspan="4">{{$designation->name}}</th>   
                    @else
                        @if($designation->uid == '6' || ($designation->uid == '7') || ($designation->uid == '8'))
                        @continue                    
                        @else
                            <th colspan="3">{{$designation->name}}</th>
                        @endif
                    @endif
                            
                @endforeach
                <th rowspan="2">Cook</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>Per.</th>
                <th>Act.</th>
                <th>Total</th>
                <th>Per.</th>
                <th>Act.</th>
                <th>Total</th>
                <th>Per.</th>
                <th>Act.</th>
                <th>Total</th>
                <th>Per.</th>
                <th>Act.</th>
                <th>Tr.</th>
                <th>Total</th>
                <th>CO.</th>
                <th>CO-TR.</th>
                <th>Total</th>
            </tr>
            
        </thead>

        <tbody>
            @php
                $i=0;
                
            @endphp
            
            @foreach ($branchData as $branchName)
                @php
                    $brEmp = isset($employeeDataBranchWise[$branchName->id]) ? $employeeDataBranchWise[$branchName->id] : '';
                    $total =0;
                @endphp
                <tr>
                        
                    <td class="text-center">{{ ++$i }}</td>
                    <td>{{ $branchName->branch_name }}[{{ $branchName->branch_code }}]</td>           

                    @foreach ($designationData as $designation)
                        @php
                            $totalAct =0;
                            $trCredit =0;
                            $trAcc =0;
                                                                                                                        
                        @endphp
                        @if ($designation->uid == '2')                        
                            @if(isset($brEmp) && $brEmp != '')
                                                    
                                @foreach($brEmp as $emp)
                                
                                    @if(in_array($emp->designation_id,$test[$designation->uid]))
                                        @php
                                            $totalAct = $totalAct + $emp->total;
                                            
                                        @endphp
                                    @endif   
                                @endforeach
                                
                            @endif
                            
                            @php
                                $accAct = isset($accAct[$branchName->id]) ? $accAct[$branchName->id] : 0;
                                $accTrainee = isset($accTrainee[$branchName->id]) ? $accTrainee[$branchName->id] : 0;
                            @endphp
                            <td class="text-center col_{{$designation->uid}}">{{$totalAct}}</td>
                            <td class="text-center accAct">{{ $accAct }}</td>  
                            <td class="text-center accTrainee">{{ $accTrainee }}</td>  
                            <td class="text-center">{{ $totalAct+ $accAct + $accTrainee }}
                                @php
                                    $total = $total + $totalAct + $accAct + $accTrainee;
                                    
                                @endphp
                            </td>  
                        @else
                            @if($designation->uid == '6' || ($designation->uid == '7') || ($designation->uid == '8'))                        
                            @continue
                            @endif
                            @if(isset($brEmp) && $brEmp != '')
                                                
                                @foreach($brEmp as $emp)
                            
                                    @if(in_array($emp->designation_id,$test[$designation->uid]))
                                        @php
                                            $totalAct = $emp->total; 
                                        @endphp
                                    @endif   
                                @endforeach
                            @endif
                            <td class="text-center col_{{$designation->uid}}">{{$totalAct}}</td>
                            @if($designation->uid == '1')
                            <td class="text-center crTrainee">{{$creditTrainee[$branchName->id]}}</td>
                            @else
                                @if($designation->uid == '3')
                                    <td class="text-center">{{$branchAct[$branchName->id]}}</td>
                                @elseif($designation->uid == '4')
                                    <td class="text-center">{{$areaAct[$branchName->id]}}</td>
                                @elseif($designation->uid == '5')
                                    <td class="text-center">{{$zonalAct[$branchName->id]}}</td>
                                @endif     
                            @endif
                            @if($designation->uid == '1')
                            <td class="text-center acc_ttl_{{$designation->uid}}">{{$creditTrainee[$branchName->id]+$totalAct}}
                                @php
                                $total = $total + $creditTrainee[$branchName->id]+$totalAct;
                                
                                @endphp
                            </td>
                            @else
                                @if($designation->uid == '3')
                                    <td class="text-center">{{ $totalAct + $branchAct[$branchName->id] }}
                                        @php
                                        $total = $total + $totalAct + $branchAct[$branchName->id];
                                        @endphp
                                    </td>
                                @elseif($designation->uid == '4')
                                    <td class="text-center">{{ $totalAct + $areaAct[$branchName->id] }}
                                        @php
                                        $total = $total  + $totalAct + $areaAct[$branchName->id];
                                        @endphp
                                    </td>
                                @elseif($designation->uid == '5')
                                    <td class="text-center">{{ $totalAct + $zonalAct[$branchName->id] }}
                                        @php
                                        $total = $total  + $totalAct + $zonalAct[$branchName->id];
                                        @endphp
                                    </td>
                                @endif
                            @endif                      
                        @endif                        
                    @endforeach

                    @php
                        $cook = isset($cook[$branchName->id]) ? $cook[$branchName->id] : 0;
                    @endphp
                    <td class="text-center cook">{{$cook}}</td>
                    <td class="text-center">{{ $total + $cook }}</td>
                </tr>       
            @endforeach
            <tr style="font-weight:bold;" class="text-right">
                <td colspan="2">Total</td>
                @foreach ($designationData as $designation)

                    @if ($designation->uid == '2')
                        <td class="text-center ttl_col_{{$designation->uid}}"></td>
                        <td class="text-center ttl_account_act"></td>  
                        <td class="text-center ttl_accTrainee"></td>  
                        <td class="text-center"></td>  
                    @else
                        @if($designation->uid == '6' || ($designation->uid == '7') || ($designation->uid == '8'))
                            @continue;                    
                        @else

                            <td class="text-center ttl_col_{{$designation->uid}}"></td>
                                @if($designation->uid == '1')
                                <td class="text-center ttl_crTrainee"></td>
                                @else  
                                <td class="text-center">-</td>
                                @endif
                                @if($designation->uid == '1')
                                <td class="text-center ttl_acc_all"></td>
                                @else  
                                <td class="text-center">-</td>
                                @endif

                        @endif
                    @endif                    
                @endforeach
                    
                    <td  class="text-center ttl_cook">-</td>
                    <td class="text-center"></td>
            </tr>
        </tbody>
    </table>
    @include('elements.signature.approvalSet')
</div>
<script>

    $(document).ready(function(){
        let acc = 0;
        let credit = 0;
        let zonal = 0;
        let area = 0;
        let branch = 0;
        let cook =0;
        let accTrainee =0;
        let crTrainee =0;
        let accTotal =0;
        let accActTotal =0;

        $('.col_1').each(function(){
            credit += parseInt($(this).text());  
        });

        $('.col_2').each(function(){
            acc += parseInt($(this).text());  
        });

        $('.col_3').each(function(){
            branch += parseInt($(this).text());  
        });

        $('.col_4').each(function(){
            area += parseInt($(this).text());  
        });

        $('.col_5').each(function(){
            zonal += parseInt($(this).text());  
        });

        $('.cook').each(function(){
            cook += parseInt($(this).text());  
        });

        $('.crTrainee').each(function(){
            crTrainee += parseInt($(this).text());  
        });

        $('.accTrainee').each(function(){
            accTrainee += parseInt($(this).text());  
        });

        $('.acc_ttl_1').each(function(){
            accTotal += parseInt($(this).text());  
        });

        $('.accAct').each(function(){
            accActTotal += parseInt($(this).text());  
        });

        
        $('.ttl_col_1').text(credit);
        $('.ttl_col_2').text(acc);
        $('.ttl_col_3').text(branch);
        $('.ttl_col_4').text(area);
        $('.ttl_col_5').text(zonal);
        $('.ttl_cook').text(cook);
        $('.ttl_crTrainee').text(crTrainee);
        $('.ttl_accTrainee').text(accTrainee);
        $('.ttl_acc_all').text(accTotal);
        $('.ttl_account_act').text(accActTotal);
        
    });
 

</script>
